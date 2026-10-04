<?php

declare(strict_types=1);

namespace App\Domain\Access;

/** Rights that are not tied to one screen; granted to a group on its Special Rights tab. */
enum HakKhusus: string
{
    case SeeCost = 'see_cost';
    case ChangeSellingPrice = 'change_selling_price';
    case SeeCreditData = 'see_credit_data';
    case OverrideCreditLimit = 'override_credit_limit';
    case OpenClosedPeriod = 'open_closed_period';
    case BackdateTransactions = 'backdate_transactions';
    case EditOthersTransactions = 'edit_others_transactions';
    case DeletePostedTransactions = 'delete_posted_transactions';
    case ApproveTransactions = 'approve_transactions';
    case ExportData = 'export_data';

    public function label(): string
    {
        return match ($this) {
            self::SeeCost => 'See item cost and margins',
            self::ChangeSellingPrice => 'Change the selling price on a document',
            self::SeeCreditData => 'See customer credit data',
            self::OverrideCreditLimit => 'Save a document over the credit limit',
            self::OpenClosedPeriod => 'Reopen a closed period',
            self::BackdateTransactions => 'Save a transaction dated before today',
            self::EditOthersTransactions => "Edit other users' transactions",
            self::DeletePostedTransactions => 'Delete a posted transaction',
            self::ApproveTransactions => 'Approve transactions',
            self::ExportData => 'Export lists and reports',
        };
    }
}
