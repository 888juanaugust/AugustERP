<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Audit\Auditor;
use App\Domain\Numbering\TransactionType;
use App\Domain\Pengaturan\BusinessRule;
use App\Models\Company\TransactionApprover;
use App\Models\Sales\SalesOrder;
use App\Models\User;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * The approval a sales order waits for when the Sales Order Approval rule is
 * on. Who may approve comes from the approval rules (Transaction Approvers)
 * that cover the order: its document type, branch, amount and the person who
 * entered it. Without a covering rule, the "approve transactions" right
 * decides. Never the person who entered the order (segregation of duties),
 * and only after the credit check passes.
 *
 * Every rule condition is honoured as "any one of the approvers" in this
 * release; the two-approver and in-order conditions are on the roadmap.
 */
final class OrderApproval
{
    public function __construct(private readonly HakAkses $akses, private readonly CreditCheck $credit) {}

    public function initialStatus(): string
    {
        return BusinessRule::SalesOrderApproval->isOn() ? SalesOrder::AWAITING : SalesOrder::APPROVED;
    }

    /** @return Collection<int, TransactionApprover> the active rules that cover this order */
    public function rulesFor(SalesOrder $order): Collection
    {
        return TransactionApprover::query()
            ->active()
            ->where('transaction_type', TransactionType::SalesOrder->value)
            ->where(fn ($q) => $q->whereNull('branch_id')->when($order->branch_id, fn ($q) => $q->orWhere('branch_id', $order->branch_id)))
            ->where('min_amount', '<=', (int) $order->total)
            ->with('requesters')
            ->get()
            ->filter(fn (TransactionApprover $rule) => $rule->requesters->isEmpty() || ($order->created_by !== null && $rule->requesters->contains('id', $order->created_by)))
            ->values();
    }

    /** Named by a rule that covers the order; or, when no rule covers it, holding the approve right. */
    public function canApprove(SalesOrder $order, ?User $user): bool
    {
        if ($user === null || ! $user->is_active) {
            return false;
        }
        $rules = $this->rulesFor($order);
        if ($rules->isNotEmpty()) {
            return $rules->contains(fn (TransactionApprover $rule) => $rule->allowsApprover($user));
        }

        return $this->akses->allowsSpecial($user, HakKhusus::ApproveTransactions);
    }

    public function approve(SalesOrder $order, User $approver): void
    {
        if ($order->approval_status === SalesOrder::APPROVED) {
            throw new RuntimeException("{$order->number} is already approved.");
        }
        if (! $this->canApprove($order, $approver)) {
            throw new RuntimeException('Approving this order takes an approval rule that names you, or the "approve transactions" right.');
        }
        if (BusinessRule::SegregationOfDuties->isOn() && $order->created_by !== null && $order->created_by === $approver->id) {
            throw new RuntimeException('Segregation of duties: the person who entered the order cannot approve it.');
        }
        $this->credit->assert($order->customer, (int) $order->total, $order->id);

        $order->forceFill(['approval_status' => SalesOrder::APPROVED, 'approved_by' => $approver->id, 'approved_at' => now(), 'rejection_reason' => null])->saveQuietly();
        Auditor::log('approved', $order, $order->number);
    }

    public function reject(SalesOrder $order, User $approver, string $reason): void
    {
        if (! $this->canApprove($order, $approver)) {
            throw new RuntimeException('Rejecting this order takes an approval rule that names you, or the "approve transactions" right.');
        }
        $order->forceFill(['approval_status' => SalesOrder::REJECTED, 'approved_by' => $approver->id, 'approved_at' => now(), 'rejection_reason' => $reason])->saveQuietly();
        Auditor::log('rejected', $order, $order->number, ['reason' => $reason]);
    }
}
