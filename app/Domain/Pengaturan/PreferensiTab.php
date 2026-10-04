<?php

declare(strict_types=1);

namespace App\Domain\Pengaturan;

/** The tabs of the Preferences screen, in the reference system's order, plus one for the rules this product keeps. */
enum PreferensiTab: string
{
    case Company = 'company';
    case Features = 'features';
    case Tax = 'tax';
    case Sales = 'sales';
    case Purchasing = 'purchasing';
    case Restrictions = 'restrictions';
    case Attachments = 'attachments';
    case ExtraAttributes = 'extra';
    case DefaultAccounts = 'accounts';
    case Other = 'other';
    case Rules = 'rules';

    public function label(): string
    {
        return match ($this) {
            self::Company => 'Company',
            self::Features => 'Features',
            self::Tax => 'Tax',
            self::Sales => 'Sales',
            self::Purchasing => 'Purchasing',
            self::Restrictions => 'Restrictions',
            self::Attachments => 'Attachments',
            self::ExtraAttributes => 'Extra Attributes',
            self::DefaultAccounts => 'Default Accounts',
            self::Other => 'Other',
            self::Rules => 'Business Rules',
        };
    }

    /** @return list<PreferensiKey> */
    public function keys(): array
    {
        return array_values(array_filter(PreferensiKey::cases(), fn (PreferensiKey $key) => $key->tab() === $this));
    }
}
