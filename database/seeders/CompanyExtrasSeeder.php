<?php

namespace Database\Seeders;

use App\Domain\Numbering\TransactionType;
use App\Models\Company\PrintLayout;
use Illuminate\Database\Seeder;

/** One default print layout per printable document. */
class CompanyExtrasSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([TransactionType::SalesQuotation, TransactionType::SalesOrder, TransactionType::DeliveryOrder, TransactionType::SalesInvoice, TransactionType::SalesReturn, TransactionType::PurchaseOrder, TransactionType::GoodsReceipt, TransactionType::PurchaseInvoice, TransactionType::PurchaseReturn, TransactionType::CashBankVoucher, TransactionType::BankTransfer, TransactionType::JournalVoucher, TransactionType::InventoryAdjustment, TransactionType::ItemTransfer] as $type) {
            PrintLayout::query()->firstOrCreate(['name' => 'Standard', 'transaction_type' => $type->value], ['is_default' => true, 'used_all_user' => true, 'settings' => PrintLayout::DEFAULTS]);
        }
    }
}
