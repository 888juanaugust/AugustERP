<?php

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
