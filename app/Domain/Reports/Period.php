<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use Carbon\CarbonImmutable;

/** The dates a report runs over, with a branch when asked. */
final class Period
{
    public readonly CarbonImmutable $from;

    public readonly CarbonImmutable $until;

    public function __construct(CarbonImmutable|string $from, CarbonImmutable|string $until, public readonly ?int $branchId = null)
    {
        $this->from = CarbonImmutable::parse($from)->startOfDay();
        $this->until = CarbonImmutable::parse($until)->startOfDay();
    }

    public static function month(int $year, int $month, ?int $branchId = null): self
    {
        $start = CarbonImmutable::create($year, $month, 1);

        return new self($start, $start->endOfMonth(), $branchId);
    }

    public function fromDate(): string
    {
        return $this->from->toDateString();
    }

    public function untilDate(): string
    {
        return $this->until->toDateString();
    }

    public function label(): string
    {
        return $this->from->format('d M Y').' – '.$this->until->format('d M Y');
    }
}
