<?php

declare(strict_types=1);

namespace App\Domain\Numbering;

use Filament\Support\Contracts\HasLabel;

/** The transaction types the Numbering screen offers, as the reference system lists them. */
enum TransactionType: string implements HasLabel
{
    case FixedAsset = 'fixed_asset';
    case Item = 'item';
    case WithholdingSlip15 = 'withholding_slip_15';
    case WithholdingSlip21 = 'withholding_slip_21';
    case WithholdingSlip4_2 = 'withholding_slip_4_2';
    case WithholdingSlip23 = 'withholding_slip_23';
    case CheckIn = 'check_in';
    case FixedAssetDisposal = 'fixed_asset_disposal';
    case DraftTransaction = 'draft_transaction';
    case PurchaseInvoice = 'purchase_invoice';
    case SalesInvoice = 'sales_invoice';
    case VendorPrice = 'vendor_price';
    case StockOpnameResult = 'stock_opname_result';
    case JournalVoucher = 'journal_voucher';
    case Employee = 'employee';
    case VendorClaim = 'vendor_claim';
    case CashBankVoucher = 'cash_bank_voucher';
    case Customer = 'customer';
    case Vendor = 'vendor';
    case ItemTransfer = 'item_transfer';
    case SalesQuotation = 'sales_quotation';
    case ExpenseAccrual = 'expense_accrual';
    case PayrollEntry = 'payroll_entry';
    case GoodsReceipt = 'goods_receipt';
    case DeliveryOrder = 'delivery_order';
    case PriceAdjustment = 'price_adjustment';
    case InventoryAdjustment = 'inventory_adjustment';
    case PaymentOrder = 'payment_order';
    case StockOpnameOrder = 'stock_opname_order';
    case PurchaseRequisition = 'purchase_requisition';
    case FixedAssetChange = 'fixed_asset_change';
    case PurchaseOrder = 'purchase_order';
    case SalesOrder = 'sales_order';
    case AssetTransfer = 'asset_transfer';
    case PurchaseReturn = 'purchase_return';
    case SalesReturn = 'sales_return';
    case VatReturn = 'vat_return';
    case BudgetTransfer = 'budget_transfer';
    case BankTransfer = 'bank_transfer';
    case InvoiceExchange = 'invoice_exchange';

    public function getLabel(): string
    {
        return match ($this) {
            self::FixedAsset => 'Fixed Asset',
            self::Item => 'Item & Service',
            self::WithholdingSlip15 => 'Withholding Slip Art. 15',
            self::WithholdingSlip21 => 'Withholding Slip Art. 21',
            self::WithholdingSlip4_2 => 'Withholding Slip Art. 4(2)',
            self::WithholdingSlip23 => 'Withholding Slip Art. 23',
            self::CheckIn => 'Check-in',
            self::FixedAssetDisposal => 'Fixed Asset Disposal',
            self::DraftTransaction => 'Draft Transaction',
            self::PurchaseInvoice => 'Purchase Invoice',
            self::SalesInvoice => 'Sales Invoice',
            self::VendorPrice => 'Vendor Price',
            self::StockOpnameResult => 'Stock Opname Result',
            self::JournalVoucher => 'Journal Voucher',
            self::Employee => 'Employee',
            self::VendorClaim => 'Vendor Claim',
            self::CashBankVoucher => 'Cash / Bank Voucher',
            self::Customer => 'Customer',
            self::Vendor => 'Vendor',
            self::ItemTransfer => 'Item Transfer',
            self::SalesQuotation => 'Sales Quotation',
            self::ExpenseAccrual => 'Expense Accrual',
            self::PayrollEntry => 'Payroll Entry',
            self::GoodsReceipt => 'Goods Receipt',
            self::DeliveryOrder => 'Delivery Order',
            self::PriceAdjustment => 'Price / Discount Adjustment',
            self::InventoryAdjustment => 'Inventory Adjustment',
            self::PaymentOrder => 'Payment Order',
            self::StockOpnameOrder => 'Stock Opname Order',
            self::PurchaseRequisition => 'Purchase Requisition',
            self::FixedAssetChange => 'Fixed Asset Change',
            self::PurchaseOrder => 'Purchase Order',
            self::SalesOrder => 'Sales Order',
            self::AssetTransfer => 'Asset Transfer',
            self::PurchaseReturn => 'Purchase Return',
            self::SalesReturn => 'Sales Return',
            self::VatReturn => 'VAT Return',
            self::BudgetTransfer => 'Budget Transfer',
            self::BankTransfer => 'Bank Transfer',
            self::InvoiceExchange => 'Invoice Exchange',
        };
    }

    /** The prefix of the seeded default series; DESIGN.md names the first five. */
    public function defaultPrefix(): string
    {
        return match ($this) {
            self::SalesOrder => 'SO',
            self::PurchaseOrder => 'PO',
            self::SalesInvoice => 'INV',
            self::DeliveryOrder => 'DO',
            self::GoodsReceipt => 'GR',
            self::JournalVoucher => 'JV',
            self::InventoryAdjustment => 'ADJ',
            self::ItemTransfer => 'TRF',
            self::CashBankVoucher => 'CB',
            self::PurchaseRequisition => 'PR',
            self::PurchaseInvoice => 'BILL',
            self::SalesQuotation => 'SQ',
            self::SalesReturn => 'SR',
            self::PurchaseReturn => 'PRT',
            self::InvoiceExchange => 'IX',
            self::VendorClaim => 'VC',
            self::VendorPrice => 'VP',
            self::PaymentOrder => 'PYO',
            self::StockOpnameOrder => 'SOO',
            self::StockOpnameResult => 'SOR',
            self::PriceAdjustment => 'PA',
            self::ExpenseAccrual => 'EA',
            self::PayrollEntry => 'PAY',
            self::BankTransfer => 'BT',
            self::BudgetTransfer => 'BGT',
            self::CheckIn => 'CI',
            self::FixedAsset => 'FA',
            self::FixedAssetDisposal => 'FAD',
            self::FixedAssetChange => 'FAC',
            self::AssetTransfer => 'FAT',
            self::DraftTransaction => 'DRAFT',
            self::Item => 'ITM',
            self::Customer => 'C',
            self::Vendor => 'V',
            self::Employee => 'EMP',
            self::VatReturn => 'VAT',
            self::WithholdingSlip15 => 'WH15',
            self::WithholdingSlip21 => 'WH21',
            self::WithholdingSlip4_2 => 'WH42',
            self::WithholdingSlip23 => 'WH23',
        };
    }

    /** Masters number without a period: C-00001, not C-2610-0001. */
    public function isMaster(): bool
    {
        return in_array($this, [self::Item, self::Customer, self::Vendor, self::Employee, self::FixedAsset], true);
    }
}
