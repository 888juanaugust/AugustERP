<?php

declare(strict_types=1);

namespace App\Domain\FixedAssets;

use Filament\Support\Contracts\HasLabel;

/** The depreciation methods the reference system offers (A-03). */
enum DepreciationMethod: string implements HasLabel
{
    case None = 'none';
    case StraightLine = 'straight_line';
    case DecliningBalance = 'declining_balance';
    case SumOfYears = 'sum_of_years';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Not depreciated',
            self::StraightLine => 'Straight line',
            self::DecliningBalance => 'Declining balance',
            self::SumOfYears => "Sum of the years' digits",
        };
    }

    public function depreciates(): bool
    {
        return $this !== self::None;
    }
}
