<?php

namespace Database\Seeders;

use App\Domain\Numbering\TransactionType;
use App\Models\Company\PrintLayout;
use App\Models\Company\SalaryComponent;
use App\Models\Company\TransactionApprover;
use App\Models\GeneralLedger\Account;
use App\Models\Settings\AccessGroup;
use Illuminate\Database\Seeder;

/** Salary components on the seeded expense account, the marketing approval rule, one default print layout per printable document. */
class CompanyExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $salaries = Account::query()->where('no', '6100')->value('id');
        foreach ([['Basic salary', 'salary'], ['Overtime', 'overtime'], ['Holiday allowance (THR)', 'bonus'], ['Health insurance (employer)', 'health_premium_employer']] as [$name, $type]) {
            SalaryComponent::query()->firstOrCreate(['name' => $name], ['fee_type' => $type, 'expense_account_id' => $salaries, 'is_active' => true]);
        }

        // The marketing approval every sales order waits for, as a configured rule.
        if (! TransactionApprover::query()->where('transaction_type', TransactionType::SalesOrder->value)->exists()) {
            $rule = TransactionApprover::query()->create(['transaction_type' => TransactionType::SalesOrder->value, 'min_amount' => 0, 'rule' => 'any_one', 'is_active' => true]);
            $marketing = AccessGroup::query()->where('name', 'Marketing')->first();
            if ($marketing) {
                $rule->groups()->syncWithoutDetaching([$marketing->id]);
            }
        }

        foreach ([TransactionType::SalesQuotation, TransactionType::SalesOrder, TransactionType::DeliveryOrder, TransactionType::SalesInvoice, TransactionType::SalesReturn, TransactionType::PurchaseOrder, TransactionType::GoodsReceipt, TransactionType::PurchaseInvoice, TransactionType::PurchaseReturn, TransactionType::CashBankVoucher, TransactionType::BankTransfer, TransactionType::JournalVoucher, TransactionType::InventoryAdjustment, TransactionType::ItemTransfer] as $type) {
            PrintLayout::query()->firstOrCreate(['name' => 'Standard', 'transaction_type' => $type->value], ['is_default' => true, 'used_all_user' => true, 'settings' => PrintLayout::DEFAULTS]);
        }
    }
}
