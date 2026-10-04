<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Audit\Auditor;
use App\Domain\Pengaturan\Saklar;
use App\Models\Sales\SalesOrder;
use App\Models\User;
use RuntimeException;

/**
 * The marketing approval every sales order waits for when the rule is on:
 * someone with the approve right who is not the person who entered the
 * order (segregation of duties), after the credit check passes.
 */
final class OrderApproval
{
    public function __construct(private readonly HakAkses $akses, private readonly CreditCheck $credit) {}

    public function initialStatus(): string
    {
        return Saklar::MarketingApprovalRequired->isOn() ? SalesOrder::AWAITING : SalesOrder::APPROVED;
    }

    public function approve(SalesOrder $order, User $approver): void
    {
        if ($order->approval_status === SalesOrder::APPROVED) {
            throw new RuntimeException("{$order->number} is already approved.");
        }
        if (! $this->akses->allowsSpecial($approver, HakKhusus::ApproveTransactions)) {
            throw new RuntimeException('Approving an order takes the "approve transactions" right.');
        }
        if (Saklar::SegregationOfDuties->isOn() && $order->created_by !== null && $order->created_by === $approver->id) {
            throw new RuntimeException('Segregation of duties: the person who entered the order cannot approve it.');
        }
        $this->credit->assert($order->customer, (int) $order->total, $order->id);

        $order->forceFill(['approval_status' => SalesOrder::APPROVED, 'approved_by' => $approver->id, 'approved_at' => now(), 'rejection_reason' => null])->saveQuietly();
        Auditor::log('approved', $order, $order->number);
    }

    public function reject(SalesOrder $order, User $approver, string $reason): void
    {
        if (! $this->akses->allowsSpecial($approver, HakKhusus::ApproveTransactions)) {
            throw new RuntimeException('Rejecting an order takes the "approve transactions" right.');
        }
        $order->forceFill(['approval_status' => SalesOrder::REJECTED, 'approved_by' => $approver->id, 'approved_at' => now(), 'rejection_reason' => $reason])->saveQuietly();
        Auditor::log('rejected', $order, $order->number, ['reason' => $reason]);
    }
}
