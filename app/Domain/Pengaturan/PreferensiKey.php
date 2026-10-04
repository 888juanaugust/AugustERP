<?php

declare(strict_types=1);

namespace App\Domain\Pengaturan;

/**
 * Every field of the Preferences screen, one case each: its tab, its type, its
 * default (the value the studied database had, docs/referensi/preferensi.md)
 * and its English label. The value is the key stored in the preferences table.
 */
enum PreferensiKey: string
{
    // Company
    case CompanyName = 'company.name';
    case CompanyPhone = 'company.phone';
    case CompanyFax = 'company.fax';
    case CompanyEmail = 'company.email';
    case CompanyAddress = 'company.address';
    case DataStartDate = 'company.data_start_date';
    case FiscalYearStartMonth = 'company.fiscal_year_start_month';

    // Features
    case MultiBranch = 'features.multi_branch';
    case MultiCurrency = 'features.multi_currency';
    case Tax = 'features.tax';
    case Approval = 'features.approval';
    case FixedAssets = 'features.fixed_assets';
    case BudgetTarget = 'features.budget_target';
    case Department = 'features.department';
    case Project = 'features.project';
    case FinancialCategory = 'features.financial_category';
    case EmployeeLoan = 'features.employee_loan';

    // Tax
    case TaxCompanyName = 'tax.company_name';
    case PkpDate = 'tax.pkp_date';
    case PkpNumber = 'tax.pkp_number';
    case BusinessType = 'tax.business_type';
    case CompanyNpwp = 'tax.npwp';
    case Klu = 'tax.klu';
    case Nitku = 'tax.nitku';

    // Sales
    case CogsSource = 'sales.cogs_source';
    case ReturnCostCharge = 'sales.return_cost_charge';
    case ReturnCostAccount = 'sales.return_cost_account';
    case UpdateCostOnReturnResave = 'sales.update_item_cost_on_return_resave';
    case NewCustomerInclusiveTax = 'sales.new_customer_inclusive_tax';

    // Purchasing
    case LastPriceUpdatedByBill = 'purchasing.last_price_updated_by_bill';
    case LastPriceCutoffDate = 'purchasing.last_price_cutoff_date';
    case TemporaryPaymentAccount = 'purchasing.temporary_payment_account';

    // Restrictions
    case AccessRestriction = 'restrictions.mode';
    case AccessFrom = 'restrictions.from';
    case AccessUntil = 'restrictions.until';

    // Attachments
    case AttachSalesQuotation = 'attachments.sales_quotation';
    case AttachSalesOrder = 'attachments.sales_order';
    case AttachDeliveryOrder = 'attachments.delivery_order';
    case AttachSalesInvoice = 'attachments.sales_invoice';
    case AttachSalesReceipt = 'attachments.sales_receipt';
    case AttachSalesReturn = 'attachments.sales_return';
    case AttachInvoiceExchange = 'attachments.invoice_exchange';
    case AttachCustomer = 'attachments.customer';
    case AttachPriceAdjustment = 'attachments.price_adjustment';

    // Extra attributes
    case TransactionExtraColumns = 'extra.transaction_columns';
    case ItemExtraColumns = 'extra.item_columns';
    case ExtraDateColumns = 'extra.date_columns';

    // Default accounts
    case ReceivableAccount = 'accounts.receivable';
    case CustomerDownPaymentAccount = 'accounts.customer_down_payment';
    case SalesDiscountAccount = 'accounts.sales_discount';
    case PayableAccount = 'accounts.payable';
    case VendorDownPaymentAccount = 'accounts.vendor_down_payment';
    case CostOfSalesAccount = 'accounts.cost_of_sales';
    case InventoryAccount = 'accounts.inventory';
    case GoodsInTransitAccount = 'accounts.goods_in_transit';
    case RoundingAccount = 'accounts.rounding';
    case GiroReceivableAccount = 'accounts.giro_receivable';
    case GiroPayableAccount = 'accounts.giro_payable';

    // Other
    case DecimalFormat = 'other.decimal_format';
    case QuantityDecimals = 'other.quantity_decimals';
    case PriceDecimals = 'other.price_decimals';
    case DateFormat = 'other.date_format';
    case AgingRangeDays = 'other.aging_range_days';
    case AgingBasis = 'other.aging_basis';
    case AgingIntervalDays = 'other.aging_interval_days';
    case CommissionBasis = 'other.commission_basis';

    // Business rules this product keeps (switches; off is audited)
    case SegregationOfDuties = 'rules.segregation_of_duties';
    case MarketingApprovalRequired = 'rules.marketing_approval_required';
    case AllowNegativeStock = 'rules.allow_negative_stock';
    case SplitAcrossWarehouses = 'rules.split_across_warehouses';
    case StoreVisits = 'rules.store_visits';
    case CommissionScheme = 'rules.commission_scheme';

    public function tab(): PreferensiTab
    {
        return PreferensiTab::from(explode('.', $this->value, 2)[0]);
    }

    public function type(): PreferensiType
    {
        return match ($this) {
            self::DataStartDate, self::PkpDate, self::LastPriceCutoffDate => PreferensiType::Date,
            self::AccessFrom, self::AccessUntil => PreferensiType::Time,
            self::FiscalYearStartMonth, self::CogsSource, self::ReturnCostCharge, self::AccessRestriction,
            self::DecimalFormat, self::QuantityDecimals, self::PriceDecimals, self::DateFormat,
            self::AgingBasis, self::CommissionBasis => PreferensiType::Select,
            self::ReturnCostAccount, self::TemporaryPaymentAccount, self::ReceivableAccount,
            self::CustomerDownPaymentAccount, self::SalesDiscountAccount, self::PayableAccount,
            self::VendorDownPaymentAccount, self::CostOfSalesAccount, self::InventoryAccount,
            self::GoodsInTransitAccount, self::RoundingAccount, self::GiroReceivableAccount, self::GiroPayableAccount => PreferensiType::Account,
            self::AgingRangeDays, self::AgingIntervalDays => PreferensiType::Int,
            self::TransactionExtraColumns, self::ItemExtraColumns, self::ExtraDateColumns => PreferensiType::TextList,
            default => match ($this->tab()) {
                PreferensiTab::Features, PreferensiTab::Attachments, PreferensiTab::Rules => PreferensiType::Bool,
                PreferensiTab::Sales, PreferensiTab::Purchasing => PreferensiType::Bool,
                default => PreferensiType::Text,
            },
        };
    }

    public function default(): mixed
    {
        return match ($this) {
            self::FiscalYearStartMonth => '1',
            self::MultiBranch, self::MultiCurrency, self::Tax, self::Approval, self::FixedAssets, self::BudgetTarget => true,
            self::Department, self::Project, self::FinancialCategory, self::EmployeeLoan => false,
            self::CogsSource => 'last_purchase_cost',
            self::ReturnCostCharge => 'item_cogs_account',
            self::UpdateCostOnReturnResave, self::NewCustomerInclusiveTax => true,
            self::LastPriceUpdatedByBill => true,
            self::AccessRestriction => 'none',
            self::AccessFrom => '08:00',
            self::AccessUntil => '17:00',
            self::TransactionExtraColumns => array_fill(0, 15, null),
            self::ItemExtraColumns => array_fill(0, 10, null),
            self::ExtraDateColumns => array_fill(0, 2, null),
            self::DecimalFormat => 'id',
            self::QuantityDecimals => '2',
            self::PriceDecimals => '0',
            self::DateFormat => 'd/m/Y',
            self::AgingRangeDays => 90,
            self::AgingBasis => 'invoice_date',
            self::AgingIntervalDays => 30,
            self::CommissionBasis => 'payment',
            self::SegregationOfDuties, self::MarketingApprovalRequired, self::SplitAcrossWarehouses, self::StoreVisits, self::CommissionScheme => true,
            self::AllowNegativeStock => false,
            default => match ($this->type()) {
                PreferensiType::Bool => false,
                default => null,
            },
        };
    }

    /** @return array<string, string> options of a Select preference */
    public function options(): array
    {
        return match ($this) {
            self::FiscalYearStartMonth => collect(range(1, 12))->mapWithKeys(fn (int $m) => [(string) $m => date('F', mktime(0, 0, 0, $m, 1))])->all(),
            self::CogsSource => ['last_purchase_cost' => 'Last purchase price / landed cost', 'sales_invoice_cogs' => 'Cost of sales on the sales invoice'],
            self::ReturnCostCharge => ['item_cogs_account' => "Charge to the item's cost of sales account", 'account' => 'Charge to a fixed account'],
            self::AccessRestriction => ['none' => 'Not restricted', 'all' => 'Restricted for everyone', 'time_window' => 'Access only within a time window'],
            self::DecimalFormat => ['id' => '1.234.567,89', 'en' => '1,234,567.89'],
            self::QuantityDecimals, self::PriceDecimals => ['0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4'],
            self::DateFormat => ['d/m/Y' => '17/10/2026', 'd-m-Y' => '17-10-2026', 'Y-m-d' => '2026-10-17'],
            self::AgingBasis => ['invoice_date' => 'Invoice date', 'due_date' => 'Due date'],
            self::CommissionBasis => ['invoice' => 'Invoiced amount', 'payment' => 'Amount actually paid'],
            default => [],
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::CompanyName => 'Company name',
            self::CompanyPhone => 'Phone',
            self::CompanyFax => 'Fax',
            self::CompanyEmail => 'Email',
            self::CompanyAddress => 'Address',
            self::DataStartDate => 'Data start date',
            self::FiscalYearStartMonth => 'Fiscal year starts in',
            self::MultiBranch => 'Multiple branches',
            self::MultiCurrency => 'Multiple currencies',
            self::Tax => 'Tax',
            self::Approval => 'Transaction approval',
            self::FixedAssets => 'Fixed assets',
            self::BudgetTarget => 'Budgets and targets',
            self::Department => 'Departments',
            self::Project => 'Projects',
            self::FinancialCategory => 'Financial categories',
            self::EmployeeLoan => 'Employee loans',
            self::TaxCompanyName => 'Registered company name',
            self::PkpDate => 'VAT registration date',
            self::PkpNumber => 'VAT registration number',
            self::BusinessType => 'Business type',
            self::CompanyNpwp => 'Company tax ID (NPWP)',
            self::Klu => 'Business classification (KLU)',
            self::Nitku => 'Business location ID (NITKU)',
            self::CogsSource => 'Cost of goods sold taken from',
            self::ReturnCostCharge => 'Sales return cost is charged',
            self::ReturnCostAccount => 'Sales return cost account',
            self::UpdateCostOnReturnResave => 'Update item cost when a sales return is saved again',
            self::NewCustomerInclusiveTax => 'New customers default to prices including tax',
            self::LastPriceUpdatedByBill => 'Last purchase price is updated by purchase invoices',
            self::LastPriceCutoffDate => 'Only for invoices dated from',
            self::TemporaryPaymentAccount => 'Temporary cash account for pending payments',
            self::AccessRestriction => 'Access restriction',
            self::AccessFrom => 'Access allowed from',
            self::AccessUntil => 'Access allowed until',
            self::AttachSalesQuotation => 'Sales quotations',
            self::AttachSalesOrder => 'Sales orders',
            self::AttachDeliveryOrder => 'Delivery orders',
            self::AttachSalesInvoice => 'Sales invoices',
            self::AttachSalesReceipt => 'Sales receipts',
            self::AttachSalesReturn => 'Sales returns',
            self::AttachInvoiceExchange => 'Invoice exchanges',
            self::AttachCustomer => 'Customers',
            self::AttachPriceAdjustment => 'Price and discount adjustments',
            self::TransactionExtraColumns => 'Extra text columns on transactions',
            self::ItemExtraColumns => 'Extra text columns on items',
            self::ExtraDateColumns => 'Extra date columns',
            self::ReceivableAccount => 'Accounts receivable',
            self::CustomerDownPaymentAccount => 'Customer down payments',
            self::SalesDiscountAccount => 'Sales discounts',
            self::PayableAccount => 'Accounts payable',
            self::VendorDownPaymentAccount => 'Vendor down payments',
            self::CostOfSalesAccount => 'Cost of goods sold',
            self::InventoryAccount => 'Inventory',
            self::GoodsInTransitAccount => 'Goods delivered, not yet invoiced',
            self::RoundingAccount => 'Rounding differences',
            self::GiroReceivableAccount => 'Giros receivable (cheques received, not yet cleared)',
            self::GiroPayableAccount => 'Giros payable (cheques issued, not yet cleared)',
            self::DecimalFormat => 'Number format',
            self::QuantityDecimals => 'Decimals on quantities',
            self::PriceDecimals => 'Decimals on prices',
            self::DateFormat => 'Date format',
            self::AgingRangeDays => 'Aging range (days)',
            self::AgingBasis => 'Age receivables from',
            self::AgingIntervalDays => 'Aging interval (days)',
            self::CommissionBasis => 'Commission is calculated from',
            self::SegregationOfDuties => 'Segregation of duties: whoever files a claim, return or stock count never verifies it',
            self::MarketingApprovalRequired => 'Every sales order waits for the approval of the marketing user in charge of the customer',
            self::AllowNegativeStock => 'Allow stock to go negative',
            self::SplitAcrossWarehouses => 'Split an order across warehouses when one cannot fill it',
            self::StoreVisits => 'Store visit check-ins by sales staff',
            self::CommissionScheme => 'Salesperson commission on paid invoices against targets',
        };
    }

    public function help(): ?string
    {
        return match ($this) {
            self::CogsSource => 'What the cost of a sold item is taken from when the invoice posts.',
            self::AccessRestriction => 'Applies to every access group that follows these preferences.',
            self::AgingRangeDays => 'Receivables older than this are reported as the last bucket.',
            self::AllowNegativeStock => 'When off, a delivery or adjustment that would take stock below zero is refused.',
            self::SegregationOfDuties => 'Turning this off is written to the activity log.',
            default => null,
        };
    }
}
