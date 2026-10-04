<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use App\Domain\Fulfilment\StatusDeriver;
use App\Domain\Settlement\SettlementService;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseInvoiceLine;
use App\Models\Purchasing\PurchaseOrderLine;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesInvoiceLine;
use App\Models\Sales\SalesOrderLine;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Sales (R-07, R-08, R-09) and purchasing (R-10, R-11, R-12) reports: what
 * was sold or bought and to whom, what is still open, and how old the debt is.
 */
final class TradeReports
{
    /** @return list<array{id: int|string, name: string, invoices: int, quantity: string, amount: int, tax: int, total: int}> */
    public static function salesBy(string $dimension, Period $period): array
    {
        return self::linesBy(SalesInvoiceLine::query(), 'sales_invoice', $dimension, $period, 'customer');
    }

    /** @return list<array{id: int|string, name: string, invoices: int, quantity: string, amount: int, tax: int, total: int}> */
    public static function purchasesBy(string $dimension, Period $period): array
    {
        return self::linesBy(PurchaseInvoiceLine::query(), 'purchase_invoice', $dimension, $period, 'vendor');
    }

    /** @param  'customer'|'vendor'  $party @param 'party'|'item'|'salesman' $dimension */
    private static function linesBy(Builder $lines, string $doc, string $dimension, Period $period, string $party): array
    {
        $partyTable = $party === 'customer' ? 'customers' : 'vendors';
        $docTable = "{$doc}s";
        $query = $lines
            ->join($docTable, "{$docTable}.id", '=', "{$doc}_lines.{$doc}_id")
            ->whereBetween("{$docTable}.trans_date", [$period->fromDate(), $period->untilDate()])
            ->when($period->branchId, fn (Builder $q) => $q->where("{$docTable}.branch_id", $period->branchId));
        [$keyExpr, $nameExpr] = match ($dimension) {
            'item' => ["{$doc}_lines.item_id", "items.number || ' · ' || items.name"],
            'salesman' => ["{$doc}_lines.salesman_id", 'employees.name'],
            default => ["{$docTable}.{$party}_id", "{$partyTable}.name"],
        };
        $query = match ($dimension) {
            'item' => $query->join('items', 'items.id', '=', "{$doc}_lines.item_id"),
            'salesman' => $query->leftJoin('employees', 'employees.id', '=', "{$doc}_lines.salesman_id"),
            default => $query->join($partyTable, "{$partyTable}.id", '=', "{$docTable}.{$party}_id"),
        };
        $rows = $query
            ->selectRaw("{$keyExpr} AS key_id, {$nameExpr} AS name, COUNT(DISTINCT {$docTable}.id) AS invoices, SUM({$doc}_lines.base_quantity) AS quantity, SUM({$doc}_lines.amount) AS amount, SUM({$doc}_lines.tax_amount) AS tax")
            ->groupByRaw("{$keyExpr}, {$nameExpr}")
            ->orderByRaw('SUM('.$doc.'_lines.amount) DESC')
            ->get();

        $out = [];
        $sum = ['invoices' => 0, 'quantity' => BigDecimal::zero(), 'amount' => 0, 'tax' => 0];
        foreach ($rows as $row) {
            $out[] = ['id' => $row->key_id ?? 'none', 'name' => $row->name ?? '(no salesperson)', 'invoices' => (int) $row->invoices, 'quantity' => (string) BigDecimal::of((string) $row->quantity)->toScale(4), 'amount' => (int) $row->amount, 'tax' => (int) $row->tax, 'total' => (int) $row->amount + (int) $row->tax];
            $sum['invoices'] += (int) $row->invoices;
            $sum['quantity'] = $sum['quantity']->plus((string) $row->quantity);
            $sum['amount'] += (int) $row->amount;
            $sum['tax'] += (int) $row->tax;
        }
        $out[] = ['id' => 'total', 'name' => 'Total', 'invoices' => $sum['invoices'], 'quantity' => (string) $sum['quantity']->toScale(4), 'amount' => $sum['amount'], 'tax' => $sum['tax'], 'total' => $sum['amount'] + $sum['tax'], 'is_total' => true];

        return $out;
    }

    /** Sales order lines not yet fully delivered or invoiced (R-08). @return list<array<string, mixed>> */
    public static function openSalesOrders(Period $period): array
    {
        return self::openOrderLines(SalesOrderLine::query()->with(['salesOrder.customer', 'item', 'unit']), 'salesOrder', 'customer', $period);
    }

    /** Purchase order lines not yet fully received (R-11). @return list<array<string, mixed>> */
    public static function openPurchaseOrders(Period $period): array
    {
        return self::openOrderLines(PurchaseOrderLine::query()->with(['purchaseOrder.vendor', 'item', 'unit']), 'purchaseOrder', 'vendor', $period);
    }

    private static function openOrderLines(Builder $lines, string $doc, string $party, Period $period): array
    {
        $table = $doc === 'salesOrder' ? 'sales_orders' : 'purchase_orders';
        $rows = $lines
            ->whereHas($doc, fn (Builder $q) => $q
                ->whereIn('status', [StatusDeriver::PENDING, StatusDeriver::PARTIAL])
                ->whereBetween('trans_date', [$period->fromDate(), $period->untilDate()])
                ->when($period->branchId, fn (Builder $b) => $b->where('branch_id', $period->branchId))
                ->when($doc === 'salesOrder', fn (Builder $b) => $b->where('approval_status', '!=', 'rejected')))
            ->whereColumn('processed_quantity', '<', 'base_quantity')
            ->get();
        $out = [];
        $amount = 0;
        foreach ($rows as $line) {
            $header = $line->{$doc};
            $remaining = BigDecimal::of((string) $line->base_quantity)->minus((string) $line->processed_quantity);
            $value = (int) BigDecimal::of((string) $line->unit_price)->multipliedBy($remaining)->toScale(0, RoundingMode::HalfUp)->toInt();
            $out[] = [
                'id' => $line->id, 'number' => $header->number, 'trans_date' => $header->trans_date, 'party' => $header->{$party}?->name,
                'item' => $line->item ? "{$line->item->number} · {$line->item->name}" : '', 'ordered' => (string) $line->base_quantity, 'processed' => (string) $line->processed_quantity,
                'remaining' => (string) $remaining->toScale(4), 'value' => $value, 'status' => $header->status,
            ];
            $amount += $value;
        }
        $out[] = ['id' => 'total', 'number' => 'Total', 'trans_date' => null, 'party' => '', 'item' => '', 'ordered' => '', 'processed' => '', 'remaining' => '', 'value' => $amount, 'status' => '', 'is_total' => true];

        return $out;
    }

    /** Receivables by age (R-09). @return list<array<string, mixed>> */
    public static function receivableAging(Period $period, string $basis = 'invoice_date'): array
    {
        return self::aging(SalesInvoice::query()->with('customer'), 'customer', $period, $basis);
    }

    /** Payables by age (R-12). @return list<array<string, mixed>> */
    public static function payableAging(Period $period, string $basis = 'invoice_date'): array
    {
        return self::aging(PurchaseInvoice::query()->with('vendor'), 'vendor', $period, $basis);
    }

    /** Buckets by days since the invoice (or its due) date, as at the period's end. */
    private static function aging(Builder $invoices, string $party, Period $period, string $basis): array
    {
        $settlement = app(SettlementService::class);
        $asOf = $period->until;
        $open = $invoices
            ->where('trans_date', '<=', $period->untilDate())
            ->where('payment_status', '!=', 'paid')
            ->when($period->branchId, fn (Builder $q) => $q->where('branch_id', $period->branchId))
            ->get();
        $buckets = ['current' => 0, '1_30' => 0, '31_60' => 0, '61_90' => 0, '91_120' => 0, 'over_120' => 0];
        $byParty = [];
        foreach ($open as $invoice) {
            $balance = $settlement->balance($invoice);
            if ($balance <= 0) {
                continue;
            }
            $reference = CarbonImmutable::parse($basis === 'due_date' && $invoice->due_date ? $invoice->due_date : $invoice->trans_date);
            $days = (int) $reference->diffInDays($asOf, false);
            $bucket = match (true) {
                $days <= 0 => 'current',
                $days <= 30 => '1_30',
                $days <= 60 => '31_60',
                $days <= 90 => '61_90',
                $days <= 120 => '91_120',
                default => 'over_120',
            };
            $name = $invoice->{$party}?->name ?? '—';
            $id = $invoice->{"{$party}_id"};
            $byParty[$id] ??= ['id' => $id, 'name' => $name, 'invoices' => 0] + $buckets + ['total' => 0, 'oldest_days' => 0];
            $byParty[$id]['invoices']++;
            $byParty[$id][$bucket] += $balance;
            $byParty[$id]['total'] += $balance;
            $byParty[$id]['oldest_days'] = max($byParty[$id]['oldest_days'], $days);
        }
        usort($byParty, fn ($a, $b) => $b['total'] <=> $a['total']);
        $total = ['id' => 'total', 'name' => 'Total', 'invoices' => 0] + $buckets + ['total' => 0, 'oldest_days' => 0, 'is_total' => true];
        foreach ($byParty as $row) {
            foreach (array_keys($buckets) as $k) {
                $total[$k] += $row[$k];
            }
            $total['invoices'] += $row['invoices'];
            $total['total'] += $row['total'];
            $total['oldest_days'] = max($total['oldest_days'], $row['oldest_days']);
        }
        $byParty[] = $total;

        return array_values($byParty);
    }
}
