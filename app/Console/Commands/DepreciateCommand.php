<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\FixedAssets\DepreciationRun;
use App\Domain\Shared\Format;
use App\Modules\ModuleRegistry;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/** Posts the monthly depreciation of every fixed asset in use; the schedule runs it on the month's last day. */
class DepreciateCommand extends Command
{
    protected $signature = 'erp:depreciate {until? : A month (YYYY-MM); the current month when omitted}';

    protected $description = 'Post the monthly depreciation of every fixed asset in use, up to the month given';

    public function handle(ModuleRegistry $modules, DepreciationRun $run): int
    {
        if (! $modules->isEnabled('fixed-assets')) {
            $this->warn('The Fixed assets module is switched off; nothing to depreciate.');

            return self::SUCCESS;
        }
        $until = $this->argument('until');
        $month = $until ? CarbonImmutable::createFromFormat('Y-m', $until) : CarbonImmutable::today();
        $result = $run->upTo($month);
        $this->info("Depreciation posted: {$result['posted']} month(s), ".Format::number($result['amount']).' in all.');
        foreach ($result['skipped'] as $note) {
            $this->warn("Skipped {$note}");
        }

        return self::SUCCESS;
    }
}
