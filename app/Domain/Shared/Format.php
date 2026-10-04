<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Models\Company\Currency;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Throwable;

/**
 * How numbers and dates read on screen: DESIGN.md's rules. Numbers keep the
 * Indonesian convention (18.450.000, decimals after a comma); dates read as
 * "17 Oct 2026" in tables and "17/10/2026" in inputs. Independent of the app
 * locale, which is English. Money carries the base currency's symbol.
 */
final class Format
{
    public const DATE_INPUT = 'd/m/Y';

    public const DATE_TABLE = 'j M Y';

    private const FALLBACK_SYMBOL = 'Rp';

    private static ?string $symbol = null;

    /** The base currency's symbol ("Rp" until a base currency is set), read once per request. */
    public static function symbol(): string
    {
        if (self::$symbol === null) {
            try {
                self::$symbol = (string) (Currency::query()->where('is_base', true)->value('symbol') ?: self::FALLBACK_SYMBOL);
            } catch (Throwable) {
                self::$symbol = self::FALLBACK_SYMBOL;
            }
        }

        return self::$symbol;
    }

    /** Forget the remembered symbol: after the base currency changes, and between tests. */
    public static function forgetSymbol(): void
    {
        self::$symbol = null;
    }

    /** An amount with the base currency's symbol: "Rp 18.450.000", "-Rp 500". */
    public static function money(?int $amount): string
    {
        if ($amount === null) {
            return '';
        }

        return ($amount < 0 ? '-' : '').self::symbol().' '.number_format(abs($amount), 0, ',', '.');
    }

    /** @deprecated kept for the call sites that grew up with it; the same as money() */
    public static function rupiah(?int $amount): string
    {
        return self::money($amount);
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
