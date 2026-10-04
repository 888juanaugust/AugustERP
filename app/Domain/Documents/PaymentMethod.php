<?php

declare(strict_types=1);

namespace App\Domain\Documents;

use Filament\Support\Contracts\HasLabel;

/** How money moved, as the reference system's payment forms list it. */
enum PaymentMethod: string implements HasLabel
{
    case Cash = 'cash';
    case Cheque = 'cheque';
    case BankTransfer = 'bank_transfer';
    case Edc = 'edc';
    case DebitCard = 'debit_card';
    case CreditCard = 'credit_card';
    case Qris = 'qris';
    case PaymentLink = 'payment_link';
    case VirtualAccount = 'virtual_account';
    case EWallet = 'e_wallet';
    case OtherNonCash = 'other_non_cash';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Cheque => 'Cheque / giro',
            self::BankTransfer => 'Bank transfer',
            self::Edc => 'EDC',
            self::DebitCard => 'Debit card',
            self::CreditCard => 'Credit card',
            self::Qris => 'QRIS',
            self::PaymentLink => 'Payment link',
            self::VirtualAccount => 'Virtual account',
            self::EWallet => 'E-wallet',
            self::OtherNonCash => 'Other non-cash',
        };
    }

    public function isCheque(): bool
    {
        return $this === self::Cheque;
    }
}
