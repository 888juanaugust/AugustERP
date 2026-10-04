<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use Filament\Support\Contracts\HasLabel;

/** The kind of tax ID a party identifies with on a tax invoice. */
enum WpType: string implements HasLabel
{
    case Nik = 'nik';
    case Npwp = 'npwp';
    case Passport = 'passport';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nik => 'National ID (NIK)',
            self::Npwp => 'Tax ID (NPWP)',
            self::Passport => 'Passport',
            self::Other => 'Other',
        };
    }
}
