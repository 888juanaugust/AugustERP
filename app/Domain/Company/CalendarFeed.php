<?php

declare(strict_types=1);

namespace App\Domain\Company;

use App\Models\CashBank\Giro;
use App\Models\Company\CalendarEvent;
use App\Models\Company\RecurringTransaction;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Sales\SalesInvoice;
use App\Modules\ModuleRegistry;
use Carbon\CarbonImmutable;

/**
 * What a stretch of days holds (a month, a week, the agenda): invoices
 * falling due, giros maturing, recurring transactions scheduled, month ends,
 * and the company's own notes.
 */
final class CalendarFeed
{
    /** @return array<string, list<array{kind: string, title: string, url: ?string}>> date → events */
    public static function month(int $year, int $month): array
    {
        $from = CarbonImmutable::create($year, $month, 1);

        return self::between($from, $from->endOfMonth());
    }

    /** @return array<string, list<array{kind: string, title: string, url: ?string}>> date → events, for any range (a week, the agenda) */
    public static function between(CarbonImmutable $from, CarbonImmutable $until): array
    {
        $events = [];
        $add = function (string $date, string $kind, string $title, ?string $url = null) use (&$events): void {
            $events[$date][] = ['kind' => $kind, 'title' => $title, 'url' => $url];
        };

        foreach (SalesInvoice::query()->with('customer')->where('payment_status', '!=', 'paid')->whereBetween('due_date', [$from->toDateString(), $until->toDateString()])->get() as $invoice) {
            $add($invoice->due_date->toDateString(), 'receivable', "Due: {$invoice->number} · ".($invoice->customer?->name ?? ''));
        }
        foreach (PurchaseInvoice::query()->with('vendor')->where('payment_status', '!=', 'paid')->whereBetween('due_date', [$from->toDateString(), $until->toDateString()])->get() as $invoice) {
            $add($invoice->due_date->toDateString(), 'payable', "Pay: {$invoice->number} · ".($invoice->vendor?->name ?? ''));
        }
        foreach (Giro::query()->outstanding()->whereBetween('due_date', [$from->toDateString(), $until->toDateString()])->get() as $giro) {
            $add($giro->due_date->toDateString(), 'giro', 'Giro '.($giro->isIncoming() ? 'in' : 'out').": {$giro->number} · ".($giro->party_name ?? ''));
        }
        foreach (RecurringTransaction::query()->where('status', 'active')->whereBetween('next_run_on', [$from->toDateString(), $until->toDateString()])->get() as $recurring) {
            $add($recurring->next_run_on->toDateString(), 'recurring', "Recurring: {$recurring->name}");
        }
        foreach (CalendarEvent::query()->whereBetween('starts_on', [$from->toDateString(), $until->toDateString()])->orderBy('starts_on')->get() as $event) {
            $add($event->starts_on->toDateString(), 'note', $event->title);
        }
        $monthEnd = app(ModuleRegistry::class)->isEnabled('fixed-assets') ? 'Month end: close the period and run depreciation' : 'Month end: close the period';
        for ($end = $from->endOfMonth()->startOfDay(); $end->lte($until); $end = $end->addDay()->endOfMonth()->startOfDay()) {
            $add($end->toDateString(), 'period', $monthEnd);
        }
        ksort($events);

        return $events;
    }
}
