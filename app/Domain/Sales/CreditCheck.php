<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Settlement\SettlementService;
use App\Domain\Shared\Format;
use App\Models\Sales\Customer;
use App\Models\Sales\SalesDownPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesReturn;
use Illuminate\Support\Carbon;
use RuntimeException;

/**
 * Whether a customer may take on more credit: the limit by amount (open
 * receivables plus open orders) and the limit by age (an unpaid invoice older
 * than N days), each only when the customer has it switched on; the 150-day
 * freeze of the business rules; and the override right that lifts the amount
 * limit. Derived arithmetic every time, never stored state.
 */
final class CreditCheck
{
    public const FREEZE_AFTER_DAYS = 150;

    public const NOTICE_AFTER_DAYS = 120;

    public function __construct(private readonly SettlementService $settlement, private readonly HakAkses $akses) {}

    /** Open receivables (invoices and down payments, less credits) of the customer or its parent group. */
    public function exposure(Customer $customer): int
    {
        $ids = $this->groupIds($customer);
        $open = 0;
        foreach (SalesInvoice::query()->whereIn('customer_id', $ids)->where('payment_status', '!=', 'paid')->get() as $invoice) {
            $open += $this->settlement->balance($invoice);
        }
        foreach (SalesDownPayment::query()->whereIn('customer_id', $ids)->where('payment_status', '!=', 'paid')->get() as $dp) {
            $open += $this->settlement->balance($dp);
        }
        foreach (SalesReturn::query()->whereIn('customer_id', $ids)->where('payment_status', '!=', 'paid')->get() as $return) {
            $open += $this->settlement->balance($return);
        }

        return $open;
    }

    /** Approved orders not yet invoiced, by their remaining value. */
    public function openOrders(Customer $customer, ?int $excludeOrderId = null): int
    {
        $ids = $this->groupIds($customer);
        $open = 0;
        foreach (SalesOrder::query()->whereIn('customer_id', $ids)->whereIn('status', ['pending', 'partial'])->where('approval_status', 'approved')->when($excludeOrderId, fn ($q) => $q->whereKeyNot($excludeOrderId))->with('lines')->get() as $order) {
            $open += $order->remainingValue();
        }

        return $open;
    }

    /** The oldest unpaid invoice's age in days, by the aging basis in Preferences. */
    public function oldestUnpaidDays(Customer $customer): int
    {
        $basis = app(Preferensi::class)->get(PreferensiKey::AgingBasis);
        $column = $basis === 'due_date' ? 'due_date' : 'trans_date';
        $oldest = SalesInvoice::query()->whereIn('customer_id', $this->groupIds($customer))->where('payment_status', '!=', 'paid')->min($column);

        return $oldest ? max(0, (int) Carbon::parse($oldest)->diffInDays(today(), false)) : 0;
    }

    /** @throws RuntimeException when the customer must not take on $newAmount more */
    public function assert(Customer $customer, int $newAmount, ?int $excludeOrderId = null): void
    {
        $limitHolder = $customer->credit_limit_mode === 'parent' && $customer->parentCustomer ? $customer->parentCustomer : $customer;
        $age = $this->oldestUnpaidDays($customer);

        if ($age > self::FREEZE_AFTER_DAYS) {
            throw new RuntimeException("{$customer->name} is frozen: an invoice has been unpaid for {$age} days (the limit is ".self::FREEZE_AFTER_DAYS.'). Settle it first.');
        }
        if ($limitHolder->credit_limit_age_enabled && $age > $limitHolder->credit_limit_age_days) {
            throw new RuntimeException("{$customer->name} has an invoice unpaid for {$age} days, over its {$limitHolder->credit_limit_age_days}-day limit.");
        }
        if ($limitHolder->credit_limit_amount_enabled) {
            $would = $this->exposure($customer) + $this->openOrders($customer, $excludeOrderId) + $newAmount;
            if ($would > $limitHolder->credit_limit_amount && ! $this->akses->allowsSpecial(auth()->user(), HakKhusus::OverrideCreditLimit)) {
                throw new RuntimeException(sprintf('%s would owe %s with this order, over its credit limit of %s.', $customer->name, Format::rupiah($would), Format::rupiah((int) $limitHolder->credit_limit_amount)));
            }
        }
    }

    public function needsNotice(Customer $customer): bool
    {
        return $this->oldestUnpaidDays($customer) > self::NOTICE_AFTER_DAYS;
    }

    /** @return list<int> */
    private function groupIds(Customer $customer): array
    {
        if ($customer->credit_limit_mode === 'parent' && $customer->parent_customer_id) {
            return Customer::query()->where('parent_customer_id', $customer->parent_customer_id)->orWhereKey($customer->parent_customer_id)->pluck('id')->all();
        }

        return [$customer->id];
    }
}
