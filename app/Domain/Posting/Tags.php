<?php

declare(strict_types=1);

namespace App\Domain\Posting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The department and project a journal line is booked to. A line takes its
 * own, else its document's (the header's are the default); reports filter
 * by them when the Departments or Projects module is on.
 */
final class Tags
{
    /** @var list<string> the tables whose rows carry a department_id and a project_id */
    public const TABLES = [
        'journal_lines',
        'journal_vouchers', 'journal_voucher_lines',
        'expense_accruals', 'expense_accrual_lines',
        'cash_payments', 'cash_payment_lines',
        'cash_receipts', 'cash_receipt_lines',
        'payroll_entries', 'payroll_entry_lines',
        'sales_quotations', 'sales_quotation_lines', 'sales_quotation_charges',
        'sales_orders', 'sales_order_lines', 'sales_order_charges',
        'deliveries', 'delivery_lines',
        'sales_invoices', 'sales_invoice_lines', 'sales_invoice_charges',
        'sales_returns', 'sales_return_lines', 'sales_return_charges',
        'sales_down_payments', 'sales_receipts',
        'purchase_orders', 'purchase_order_lines', 'purchase_order_charges',
        'goods_receipts', 'goods_receipt_lines',
        'purchase_invoices', 'purchase_invoice_lines', 'purchase_invoice_charges',
        'purchase_returns', 'purchase_return_lines', 'purchase_return_charges',
        'purchase_down_payments', 'purchase_payments',
        'vendor_claims', 'vendor_claim_lines',
        'inventory_adjustments', 'inventory_adjustment_lines',
    ];

    public function __construct(public readonly ?int $departmentId = null, public readonly ?int $projectId = null) {}

    public static function none(): self
    {
        return new self;
    }

    /** The first department and the first project found on the models given, in order: Tags::of($line, $document). */
    public static function of(?Model ...$sources): self
    {
        $department = null;
        $project = null;
        foreach ($sources as $source) {
            if ($source === null) {
                continue;
            }
            $department ??= $source->getAttribute('department_id') !== null ? (int) $source->getAttribute('department_id') : null;
            $project ??= $source->getAttribute('project_id') !== null ? (int) $source->getAttribute('project_id') : null;
        }

        return new self($department, $project);
    }

    /** These tags, with the fallback's where these have none. */
    public function orElse(?self $fallback): self
    {
        return $fallback === null ? $this : new self($this->departmentId ?? $fallback->departmentId, $this->projectId ?? $fallback->projectId);
    }

    /** @return array{department_id: ?int, project_id: ?int} */
    public function toArray(): array
    {
        return ['department_id' => $this->departmentId, 'project_id' => $this->projectId];
    }

    /** Whether any tagged row names the record in the column (department_id or project_id). */
    public static function used(string $column, int $id): bool
    {
        foreach (self::TABLES as $table) {
            if (DB::table($table)->where($column, $id)->exists()) {
                return true;
            }
        }

        return false;
    }
}
