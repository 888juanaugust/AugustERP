<?php

declare(strict_types=1);

namespace App\Domain\Pengaturan;

/**
 * The behaviours this product keeps beyond the reference system, each a switch
 * on the Business Rules tab of Preferences. Read them through isOn(), never
 * hard-code them.
 */
enum Saklar
{
    case SegregationOfDuties;
    case MarketingApprovalRequired;
    case AllowNegativeStock;
    case SplitAcrossWarehouses;
    case StoreVisits;
    case CommissionScheme;

    public function key(): PreferensiKey
    {
        return match ($this) {
            self::SegregationOfDuties => PreferensiKey::SegregationOfDuties,
            self::MarketingApprovalRequired => PreferensiKey::MarketingApprovalRequired,
            self::AllowNegativeStock => PreferensiKey::AllowNegativeStock,
            self::SplitAcrossWarehouses => PreferensiKey::SplitAcrossWarehouses,
            self::StoreVisits => PreferensiKey::StoreVisits,
            self::CommissionScheme => PreferensiKey::CommissionScheme,
        };
    }

    public function isOn(): bool
    {
        return (bool) app(Preferensi::class)->get($this->key());
    }
}
