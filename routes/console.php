<?php

use App\Domain\Company\RecurringRunner;
use App\Domain\FixedAssets\DepreciationRun;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('erp:depreciate {until? : A month (YYYY-MM); the current month when omitted}', function (?string $until = null) {
    $month = $until ? CarbonImmutable::createFromFormat('Y-m', $until) : CarbonImmutable::today();
    $result = app(DepreciationRun::class)->upTo($month);
    $this->info("Depreciation posted: {$result['posted']} month(s), ".number_format($result['amount'], 0, ',', '.').' in all.');
    foreach ($result['skipped'] as $note) {
        $this->warn("Skipped {$note}");
    }
})->purpose('Post the monthly depreciation of every fixed asset in use, up to the month given');

// The reference system posts depreciation by itself each month; so does this one, on the month's last day.
Schedule::command('erp:depreciate')->lastDayOfMonth('23:30');

Artisan::command('erp:recurring {on? : Run every schedule due on or before this date (YYYY-MM-DD); today when omitted}', function (?string $on = null) {
    $made = app(RecurringRunner::class)->runDue($on ? CarbonImmutable::parse($on) : null);
    $this->info(count($made).' document(s) made from recurring transactions.');
    foreach ($made as $line) {
        $this->line("  {$line}");
    }
})->purpose('Make the documents of every recurring transaction that is due');

Schedule::command('erp:recurring')->dailyAt('06:00');
