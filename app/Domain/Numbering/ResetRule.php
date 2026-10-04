<?php

declare(strict_types=1);

namespace App\Domain\Numbering;

use Carbon\CarbonInterface;
use Filament\Support\Contracts\HasLabel;

/** When a series' counter starts again from 1. */
enum ResetRule: string implements HasLabel
{
    case None = 'none';
    case Daily = 'daily';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Never reset',
            self::Daily => 'Reset every day',
            self::Monthly => 'Reset every month',
            self::Yearly => 'Reset every year',
        };
    }

    /** The counter row the date falls in: '' | YYYY | YYYYMM | YYYYMMDD. */
    public function periodKey(CarbonInterface $date): string
    {
        return match ($this) {
            self::None => '',
            self::Yearly => $date->format('Y'),
            self::Monthly => $date->format('Ym'),
            self::Daily => $date->format('Ymd'),
        };
    }
}
