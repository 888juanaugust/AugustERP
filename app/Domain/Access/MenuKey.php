<?php

declare(strict_types=1);

namespace App\Domain\Access;

use App\Filament\Modul;

/**
 * Every screen of the product, one case each, in the order the standard's
 * menus list them. The value is the screen's stable key: the key of its rights
 * in the access matrix and of its names in lang/<locale>/menu.php. Never
 * rename a value; stored rights refer to it.
 */
enum MenuKey: string
{
    case Preferences = 'company__preferences';
    case AccessGroups = 'company__access-privilege';
    case Users = 'company__user-company';
    case Numbering = 'company__auto-number';
    case PrintLayouts = 'company__print-layout';
    case TransactionApprovers = 'company__user-approval';
    case AddOnStore = 'company__application';
    case FinancingProgram = 'company__capital-program';
    case Currencies = 'company__currency';
    case Branches = 'company__branch';
    case TaxCodes = 'company__tax';
    case PaymentTerms = 'company__payment-term';
    case ShippingMethods = 'company__shipment';
    case FOBTerms = 'company__freeonboard';
    case SalaryComponents = 'company__employee-fee';
    case Employees = 'company__employee';
    case RecurringTransactions = 'company__recurring';
    case MonthEndProcess = 'company__period-end';
    case Contacts = 'company__contact';
    case MemorizedTransactions = 'company__memorize-transaction';
    case Calendar = 'company__calendar';
    case ActivityLog = 'company__audit';
    case ChartOfAccounts = 'general-ledger__glaccount';
    case ExpenseAccruals = 'general-ledger__expense-accrual';
    case PayrollEntries = 'cash-bank__employee-payment';
    case JournalVouchers = 'general-ledger__journal-voucher';
    case BudgetMonitor = 'budget-target__accountbudget-monitor';
    case BudgetTransfers = 'budget-target__accountbudget-transfer';
    case Budgets = 'budget-target__accountbudget-target';
    case AccountHistory = 'general-ledger__account-history';
    case JournalActivityLog = 'company__audit-journal';
    case Payments = 'cash-bank__other-payment';
    case Receipts = 'cash-bank__other-deposit';
    case BankTransfers = 'cash-bank__bank-transfer';
    case InternetBanking = 'cash-bank__internet-banking';
    case BankStatements = 'cash-bank__bank-statement';
    case BankBook = 'cash-bank__bank-book';
    case BankReconciliation = 'cash-bank__bank-reconcile';
    case VirtualAccounts = 'company__application-virtual-account';
    case EPayment = 'company__application-epayment';
    case SalesQuotations = 'customer__sales-quotation';
    case SalesOrders = 'customer__sales-order';
    case DeliveryOrders = 'customer__delivery-order';
    case SalesDownPayments = 'customer__sales-downpayment';
    case SalesInvoices = 'customer__sales-invoice';
    case SalesReceipts = 'customer__sales-receipt';
    case SalesReturns = 'customer__sales-return';
    case InvoiceExchanges = 'customer__exchange-invoice';
    case CustomerCategories = 'customer__customer-category';
    case PriceCategories = 'customer__price-category';
    case Customers = 'customer__customer';
    case PriceAndDiscountAdjustments = 'inventory__sellingprice-adjustment';
    case SalesmanCommissions = 'company__salesman-commission';
    case SalesTargets = 'budget-target__sales-target';
    case ECommerceLinks = 'customer__ecommerce-setting';
    case CheckIns = 'customer__sales-check-in';
    case PurchaseOrders = 'vendor__purchase-order';
    case GoodsReceipts = 'vendor__receive-item';
    case PurchaseDownPayments = 'vendor__purchase-downpayment';
    case PurchaseInvoices = 'vendor__purchase-invoice';
    case PurchasePayments = 'vendor__purchase-payment';
    case PurchaseReturns = 'vendor__purchase-return';
    case VendorClaims = 'vendor__vendor-claim';
    case VendorPrices = 'inventory__vendor-price';
    case VendorCategories = 'vendor__vendor-category';
    case Vendors = 'vendor__vendor';
    case PaymentOrders = 'vendor__transfer-order';
    case VendorTransfers = 'vendor__multi-vendor-transfer';
    case PurchaseRequisitions = 'vendor__purchase-requisition';
    case ItemTransfers = 'inventory__item-transfer';
    case InventoryAdjustments = 'inventory__item-adjustment';
    case StockOpnameOrders = 'inventory__stock-opname-order';
    case StockOpnameResults = 'inventory__stock-opname-result';
    case ItemsAndServices = 'inventory__item';
    case Warehouses = 'inventory__warehouse';
    case Units = 'inventory__unit';
    case ItemCategories = 'inventory__item-category';
    case ItemBrands = 'inventory__item-brand';
    case OrderFulfilment = 'inventory__backorder-inquiry';
    case StockByWarehouse = 'inventory__stock-warehouse';
    case MinimumStock = 'inventory__minimum-stock-item';
    case FixedAssets = 'fixed-asset__fixed-asset';
    case AssetCategories = 'fixed-asset__fa-type';
    case FiscalAssetCategories = 'fixed-asset__fiscal-fa-type';
    case AssetChanges = 'fixed-asset__fixed-asset-edited';
    case AssetDisposals = 'fixed-asset__fixed-asset-disposed';
    case AssetTransfers = 'fixed-asset__asset-transfer';
    case AssetsByLocation = 'fixed-asset__asset-location';
    case ETaxInvoiceExport = 'company__efaktur-ctas';
    case EmailTaxInvoice = 'customer__efaktur-send';
    case LegacyETaxExport = 'company__efaktur-online';
    case ReportCatalogue = 'report__report';
    case VATReturn = 'report__spt-masa';
    case AIAnalysis = 'report-insight-analysis';
    case IncomeTaxArt21Return = 'report__formulir-1721-induk';
    case WithholdingSlips = 'report__formulir-1721-bukti-potong';

    public function modul(): Modul
    {
        return match ($this) {
            self::Preferences, self::AccessGroups, self::Users, self::Numbering, self::PrintLayouts, self::TransactionApprovers, self::AddOnStore, self::FinancingProgram => Modul::Settings,
            self::Currencies, self::Branches, self::TaxCodes, self::PaymentTerms, self::ShippingMethods, self::FOBTerms, self::SalaryComponents, self::Employees, self::RecurringTransactions, self::MonthEndProcess, self::Contacts, self::MemorizedTransactions, self::Calendar, self::ActivityLog => Modul::Company,
            self::ChartOfAccounts, self::ExpenseAccruals, self::PayrollEntries, self::JournalVouchers, self::BudgetMonitor, self::BudgetTransfers, self::Budgets, self::AccountHistory, self::JournalActivityLog => Modul::GeneralLedger,
            self::Payments, self::Receipts, self::BankTransfers, self::InternetBanking, self::BankStatements, self::BankBook, self::BankReconciliation, self::VirtualAccounts, self::EPayment => Modul::CashBank,
            self::SalesQuotations, self::SalesOrders, self::DeliveryOrders, self::SalesDownPayments, self::SalesInvoices, self::SalesReceipts, self::SalesReturns, self::InvoiceExchanges, self::CustomerCategories, self::PriceCategories, self::Customers, self::PriceAndDiscountAdjustments, self::SalesmanCommissions, self::SalesTargets, self::ECommerceLinks, self::CheckIns => Modul::Sales,
            self::PurchaseOrders, self::GoodsReceipts, self::PurchaseDownPayments, self::PurchaseInvoices, self::PurchasePayments, self::PurchaseReturns, self::VendorClaims, self::VendorPrices, self::VendorCategories, self::Vendors, self::PaymentOrders, self::VendorTransfers => Modul::Purchasing,
            self::PurchaseRequisitions, self::ItemTransfers, self::InventoryAdjustments, self::StockOpnameOrders, self::StockOpnameResults, self::ItemsAndServices, self::Warehouses, self::Units, self::ItemCategories, self::ItemBrands, self::OrderFulfilment, self::StockByWarehouse, self::MinimumStock => Modul::Inventory,
            self::FixedAssets, self::AssetCategories, self::FiscalAssetCategories, self::AssetChanges, self::AssetDisposals, self::AssetTransfers, self::AssetsByLocation => Modul::FixedAssets,
            self::ETaxInvoiceExport, self::EmailTaxInvoice, self::LegacyETaxExport => Modul::Tax,
            self::ReportCatalogue, self::VATReturn, self::AIAnalysis, self::IncomeTaxArt21Return, self::WithholdingSlips => Modul::Reports,
        };
    }

    /** The screen's English name, from lang/en/menu.php. */
    public function label(): string
    {
        return __('menu.screens.'.$this->value);
    }

    /** Position in the sidebar, across all modules. */
    public function sort(): int
    {
        return match ($this) {
            self::Preferences => 1,
            self::AccessGroups => 2,
            self::Users => 3,
            self::Numbering => 4,
            self::PrintLayouts => 5,
            self::TransactionApprovers => 6,
            self::AddOnStore => 7,
            self::FinancingProgram => 8,
            self::Currencies => 9,
            self::Branches => 10,
            self::TaxCodes => 11,
            self::PaymentTerms => 12,
            self::ShippingMethods => 13,
            self::FOBTerms => 14,
            self::SalaryComponents => 15,
            self::Employees => 16,
            self::RecurringTransactions => 17,
            self::MonthEndProcess => 18,
            self::Contacts => 19,
            self::MemorizedTransactions => 20,
            self::Calendar => 21,
            self::ActivityLog => 22,
            self::ChartOfAccounts => 23,
            self::ExpenseAccruals => 24,
            self::PayrollEntries => 25,
            self::JournalVouchers => 26,
            self::BudgetMonitor => 27,
            self::BudgetTransfers => 28,
            self::Budgets => 29,
            self::AccountHistory => 30,
            self::JournalActivityLog => 31,
            self::Payments => 32,
            self::Receipts => 33,
            self::BankTransfers => 34,
            self::InternetBanking => 35,
            self::BankStatements => 36,
            self::BankBook => 37,
            self::BankReconciliation => 38,
            self::VirtualAccounts => 39,
            self::EPayment => 40,
            self::SalesQuotations => 41,
            self::SalesOrders => 42,
            self::DeliveryOrders => 43,
            self::SalesDownPayments => 44,
            self::SalesInvoices => 45,
            self::SalesReceipts => 46,
            self::SalesReturns => 47,
            self::InvoiceExchanges => 48,
            self::CustomerCategories => 49,
            self::PriceCategories => 50,
            self::Customers => 51,
            self::PriceAndDiscountAdjustments => 52,
            self::SalesmanCommissions => 53,
            self::SalesTargets => 54,
            self::ECommerceLinks => 55,
            self::CheckIns => 56,
            self::PurchaseOrders => 57,
            self::GoodsReceipts => 58,
            self::PurchaseDownPayments => 59,
            self::PurchaseInvoices => 60,
            self::PurchasePayments => 61,
            self::PurchaseReturns => 62,
            self::VendorClaims => 63,
            self::VendorPrices => 64,
            self::VendorCategories => 65,
            self::Vendors => 66,
            self::PaymentOrders => 67,
            self::VendorTransfers => 68,
            self::PurchaseRequisitions => 69,
            self::ItemTransfers => 70,
            self::InventoryAdjustments => 71,
            self::StockOpnameOrders => 72,
            self::StockOpnameResults => 73,
            self::ItemsAndServices => 74,
            self::Warehouses => 75,
            self::Units => 76,
            self::ItemCategories => 77,
            self::ItemBrands => 78,
            self::OrderFulfilment => 79,
            self::StockByWarehouse => 80,
            self::MinimumStock => 81,
            self::FixedAssets => 82,
            self::AssetCategories => 83,
            self::FiscalAssetCategories => 84,
            self::AssetChanges => 85,
            self::AssetDisposals => 86,
            self::AssetTransfers => 87,
            self::AssetsByLocation => 88,
            self::ETaxInvoiceExport => 89,
            self::EmailTaxInvoice => 90,
            self::LegacyETaxExport => 91,
            self::ReportCatalogue => 92,
            self::VATReturn => 93,
            self::AIAnalysis => 94,
            self::IncomeTaxArt21Return => 95,
            self::WithholdingSlips => 96,
        };
    }

    /** What the screen is for: its tile colour in the module menu. */
    public function kind(): ScreenKind
    {
        return match ($this) {
            self::Preferences, self::AccessGroups, self::Users, self::Numbering, self::PrintLayouts, self::TransactionApprovers,
            self::Currencies, self::Branches, self::TaxCodes, self::PaymentTerms, self::ShippingMethods, self::FOBTerms, self::SalaryComponents, self::Employees,
            self::ChartOfAccounts, self::Budgets,
            self::CustomerCategories, self::PriceCategories, self::Customers, self::SalesmanCommissions, self::SalesTargets,
            self::VendorPrices, self::VendorCategories, self::Vendors,
            self::ItemsAndServices, self::Warehouses, self::Units, self::ItemCategories, self::ItemBrands,
            self::FixedAssets, self::AssetCategories, self::FiscalAssetCategories => ScreenKind::Setup,

            self::RecurringTransactions, self::MonthEndProcess,
            self::ExpenseAccruals, self::PayrollEntries, self::JournalVouchers, self::BudgetTransfers,
            self::Payments, self::Receipts, self::BankTransfers, self::BankStatements, self::BankReconciliation,
            self::SalesQuotations, self::SalesOrders, self::DeliveryOrders, self::SalesDownPayments, self::SalesInvoices, self::SalesReceipts, self::SalesReturns, self::InvoiceExchanges, self::PriceAndDiscountAdjustments, self::CheckIns,
            self::PurchaseOrders, self::GoodsReceipts, self::PurchaseDownPayments, self::PurchaseInvoices, self::PurchasePayments, self::PurchaseReturns, self::VendorClaims, self::PaymentOrders, self::VendorTransfers,
            self::PurchaseRequisitions, self::ItemTransfers, self::InventoryAdjustments, self::StockOpnameOrders, self::StockOpnameResults,
            self::AssetChanges, self::AssetDisposals, self::AssetTransfers,
            self::ETaxInvoiceExport, self::EmailTaxInvoice, self::LegacyETaxExport => ScreenKind::Work,

            self::AddOnStore, self::FinancingProgram,
            self::Contacts, self::MemorizedTransactions, self::Calendar, self::ActivityLog,
            self::BudgetMonitor, self::AccountHistory, self::JournalActivityLog,
            self::InternetBanking, self::BankBook, self::VirtualAccounts, self::EPayment,
            self::ECommerceLinks,
            self::OrderFulfilment, self::StockByWarehouse, self::MinimumStock,
            self::AssetsByLocation,
            self::ReportCatalogue, self::VATReturn, self::AIAnalysis, self::IncomeTaxArt21Return, self::WithholdingSlips => ScreenKind::Tool,
        };
    }

    /** Screens of the standard menu that this product does not reproduce (vendor services of the original product). */
    public function isReplicated(): bool
    {
        return ! in_array($this, [
            self::AddOnStore,
            self::FinancingProgram,
            self::InternetBanking,
            self::VirtualAccounts,
            self::EPayment,
            self::ECommerceLinks,
            self::AIAnalysis,
        ], true);
    }

    /** The URL slug of the screen's resource or page. */
    public function slug(): string
    {
        return str_replace('__', '/', $this->value);
    }
}
