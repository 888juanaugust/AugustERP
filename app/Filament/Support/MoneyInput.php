<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Shared\Format;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

/** A whole-amount input that groups thousands as Preferences say (18.450.000, or 18,450,000) and saves the plain number. */
final class MoneyInput
{
    public static function make(string $name): TextInput
    {
        $decimal = Format::decimalSeparator();
        $thousands = Format::thousandsSeparator();

        return TextInput::make($name)
            ->mask(RawJs::make("\$money(\$input, '{$decimal}', '{$thousands}', 0)"))
            ->stripCharacters($thousands)
            ->numeric();
    }
}
