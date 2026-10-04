<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;

/**
 * How numbers and dates read on screen: DESIGN.md's rules. Numbers keep the
 * Indonesian convention (18.450.000, decimals after a comma); dates read as
 * "17 Oct 2026" in tables and "17/10/2026" in inputs. Independent of the app
 * locale, which is English.
 */
final class Format
{
    public const DATE_INPUT = 'd/m/Y';

    public const DATE_TABLE = 'j M Y';

    public static function rupiah(?int $amount): string
    {
        return $amount === null ? '' : Money::rupiah($amount);
    }

    public static function number(?int $amount): string
    {
        return $amount === null ? '' : Money::format($amount);
    }

    /** A quantity with up to four decimals, trailing zeros dropped: 12 → "12", 2.5 → "2,5". */
    public static function quantity(string|int|float|null $qty, int $maxDecimals = 4): string
    {
        if ($qty === null || $qty === '') {
            return '';
        }
        $text = number_format((float) $qty, $maxDecimals, ',', '.');
        $text = rtrim(rtrim($text, '0'), ',');

        return $text === '' ? '0' : $text;
    }

    /** A percentage for display: "12", "2,5". */
    public static function percent(string|int|float|null $value): string
    {
        return $value === null || $value === '' ? '' : self::quantity($value, 2).'%';
    }

    public static function date(DateTimeInterface|string|null $date): string
    {
        return self::carbon($date)?->format(self::DATE_TABLE) ?? '';
    }

    public static function dateInput(DateTimeInterface|string|null $date): string
    {
        return self::carbon($date)?->format(self::DATE_INPUT) ?? '';
    }

    public static function dateTime(DateTimeInterface|string|null $date): string
    {
        return self::carbon($date)?->format('j M Y H:i') ?? '';
    }

    private static function carbon(DateTimeInterface|string|null $date): ?CarbonInterface
    {
        if ($date === null || $date === '') {
            return null;
        }

        return $date instanceof DateTimeInterface ? CarbonImmutable::instance($date) : CarbonImmutable::parse($date);
    }
}
