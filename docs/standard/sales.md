# Sales

Module group `sales`. 16 screens in the standard menu.

## Behaviours

- The chain is quotation → order → delivery (partial or several) → invoice (from one or several deliveries, or direct) → receipt. Fulfilment status (waiting, partial, processed, closed) is derived from quantities; "Pull" picks open upstream documents of the customer, "Process" opens the next document prefilled.
- Prices are typed freely by those with the right; otherwise they come from the customer's price category and the price adjustments in force on the document's date. Discounts per line and per document; other charges to any account; tax included or excluded per document.
- The order's approval, when the Sales Order Approval rule is on, follows the approval rules and the credit check: amount limit (open receivables plus open orders), age limit, and the company's freeze days.
- Deliveries move stock out at the moving average cost; the invoice moves goods delivered to cost of sales. Down payments carry their own tax and are deducted on the invoice. Returns refer to an invoice (or none), bring goods back and issue a credit automatically. Invoice exchange records the handing of invoices to the customer for payment scheduling.
- Customers carry billing, shipping and tax addresses, contacts, a category, a price and a discount category, a default salesperson, payment term and discount, tax identity for the tax invoice, credit limits (own or the parent's), and opening receivables.
- Check-ins, commissions and targets (the Sales extras module) are off by default.

## Screens

- [Sales Quotations](#sales-quotations)
- [Sales Orders](#sales-orders)
- [Delivery Orders](#delivery-orders)
- [Sales Down Payments](#sales-down-payments)
- [Sales Invoices](#sales-invoices)
- [Sales Receipts](#sales-receipts)
- [Sales Returns](#sales-returns)
- [Invoice Exchanges](#invoice-exchanges)
- [Customer Categories](#customer-categories)
- [Price Categories](#price-categories)
- [Customers](#customers)
- [Price & Discount Adjustments](#price-discount-adjustments)
- [Salesman Commissions](#salesman-commissions)
- [Sales Targets](#sales-targets)
- [e-Commerce Links](#e-commerce-links)
- [Check-ins](#check-ins)

## Sales Quotations

Menu key `customer__sales-quotation` · module `sales`

### List

**Columns:** Number · Date · Customer · Notes · Status · Total · Approval

**Filters:** Trans date · Ordered by · Printed

**Actions:** Approve · Reject · Edit · Create order · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Ordered by | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Number format | `series_id` | select |  |
| Number | `number` | text |  |

#### Tab: Line items

**Line grid "Lines":** Item · Quantity · Unit · Unit price · Disc % · Amount · Tax · Salesperson · Processed · Memo

| Field | Column | Type | Required |
|---|---|---|---|
| Totals | `totals` | computed |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Payment term | `payment_term_id` | select |  |
| Branch | `branch_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |
| Taxable | `taxable` | toggle |  |
| Prices include tax | `inclusive_tax` | toggle |  |
| Discount on the total (%) | `discount_percent` | number |  |

#### Tab: Other charges

**Line grid "Charges":** Charge · Amount · Description

## Sales Orders

Menu key `customer__sales-order` · module `sales`

### List

**Columns:** Number · Date · Customer · Notes · Approval · Status · Total

**Filters:** Trans date · Ordered by · Approval · Printed

**Actions:** Edit · Approve · Reject · Deliver · Invoice · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Ordered by | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Order No. format | `series_id` | select |  |
| Order No. | `number` | text |  |

#### Tab: Line items

**Line grid "Lines":** Item · Quantity · Unit · Unit price · Disc % · Amount · Tax · Warehouse · Salesperson · Processed · Memo

| Field | Column | Type | Required |
|---|---|---|---|
| Totals | `totals` | computed |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Payment term | `payment_term_id` | select |  |
| PO number | `po_number` | text |  |
| Branch | `branch_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |
| Taxable | `taxable` | toggle |  |
| Prices include tax | `inclusive_tax` | toggle |  |
| Discount on the total (%) | `discount_percent` | number |  |
| Ship date | `ship_date` | date |  |
| Shipping method | `shipment_id` | select |  |
| FOB | `fob_id` | select |  |

#### Tab: Other charges

**Line grid "Charges":** Charge · Amount · Description

**Actions:** Pull from quotations

## Delivery Orders

Menu key `customer__delivery-order` · module `sales`

### List

**Columns:** Number · Date · Customer · Shipping method · Notes · Status · Approval

**Filters:** Trans date · Ship to · Shipping method

**Actions:** Approve · Reject · Edit · Invoice · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Ship to | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Delivery No. format | `series_id` | select |  |
| Delivery No. | `number` | text |  |
| Shipping method | `shipment_id` | select |  |

#### Tab: Line items

**Line grid "Lines":** Item · Quantity · Unit · Warehouse · Salesperson · Processed · Memo

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Branch | `branch_id` | select |  |
| PO number | `po_number` | text |  |
| FOB | `fob_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |

**Actions:** Pull from orders

## Sales Down Payments

Menu key `customer__sales-downpayment` · module `sales`

### List

**Columns:** Number · Date · Customer · Notes · Status · Age (days) · Total

**Filters:** Trans date · Customer

**Actions:** Edit · Receive payment

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Customer | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Invoice No. format | `series_id` | select |  |
| Invoice No. | `number` | text |  |

#### Tab: Down payment

| Field | Column | Type | Required |
|---|---|---|---|
| Down payment | `amount` | number | yes |
| PO number | `po_number` | text |  |
| Tax | `tax_code_id` | select |  |
| Taxable | `taxable` | toggle |  |
| Prices include tax | `inclusive_tax` | toggle |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Branch | `branch_id` | select |  |
| Payment term | `payment_term_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |

#### Tab: Payment info

| Field | Column | Type | Required |
|---|---|---|---|
| Paid | `paid` | computed |  |
| Deducted on invoices | `used` | computed |  |

## Sales Invoices

Menu key `customer__sales-invoice` · module `sales`

### List

**Columns:** Number · Date · Customer · Notes · Status · Age (days) · Total · NSFP · Printed · Approval

**Filters:** Trans date · Customer · Printed

**Actions:** Approve · Reject · Edit · Receive payment · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Customer | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Invoice No. format | `series_id` | select |  |
| Invoice No. | `number` | text |  |

#### Tab: Line items

**Line grid "Lines":** Item · Quantity · Unit · Unit price · Disc % · Amount · Tax · Warehouse · Salesperson · Memo

| Field | Column | Type | Required |
|---|---|---|---|
| Totals | `totals` | computed |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Payment term | `payment_term_id` | select |  |
| PO number | `po_number` | text |  |
| Due date | `due_date` | date |  |
| Tax invoice serial (NSFP) | `nsfp` | text |  |
| Branch | `branch_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |
| Taxable | `taxable` | toggle |  |
| Prices include tax | `inclusive_tax` | toggle |  |
| Discount on the total (%) | `discount_percent` | number |  |
| Ship date | `ship_date` | date |  |
| Shipping method | `shipment_id` | select |  |
| FOB | `fob_id` | select |  |

#### Tab: Other charges

**Line grid "Charges":** Charge · Amount · Description

#### Tab: Down payments

**Line grid "Down payments":** Down payment · Amount deducted

#### Tab: Payment info

| Field | Column | Type | Required |
|---|---|---|---|
| Paid | `paid` | computed |  |

**Actions:** Pull from deliveries · Pull from orders

## Sales Receipts

Menu key `customer__sales-receipt` · module `sales`

### List

**Columns:** Number · Date · Cheque No. · Cheque date · Customer · Bank · Notes · Credit used · Giro · Amount received

**Filters:** Trans date · Method · Bank · Received from

**Actions:** Edit · Giro cleared · Giro bounced · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Received from | `customer_id` | select | yes |
| Bank | `bank_account_id` | select | yes |
| Payment method | `payment_method` | select | yes |
| Payment date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Voucher No. format | `series_id` | select |  |
| Voucher No. | `number` | text |  |
| Amount received | `amount_preview` | computed |  |
| Use credit notes | `use_credit` | toggle |  |
| Cheque / giro No. | `cheque_no` | text |  |
| Cheque date | `cheque_date` | date |  |

#### Tab: Invoices

**Line grid "Lines":** Invoice · Open balance · Pay · Discount · Discount account

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Branch | `branch_id` | select |  |
| Notes | `description` | textarea |  |

**Actions:** Pull every open document

## Sales Returns

Menu key `customer__sales-return` · module `sales`

### List

**Columns:** Number · Date · Customer · Return from · Notes · Credit used · Total · Approval

**Filters:** Trans date · Customer · Return from · Printed

**Actions:** Approve · Reject · Edit · Print

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Customer | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Return No. format | `series_id` | select |  |
| Return No. | `number` | text |  |
| Return from | `return_type` | select | yes |
| Document | `source_key` | select |  |

#### Tab: Line items

**Line grid "Lines":** Item · Quantity · Unit · Unit price · Disc % · Amount · Tax · Warehouse · Salesperson · Memo

| Field | Column | Type | Required |
|---|---|---|---|
| Totals | `totals` | computed |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Branch | `branch_id` | select |  |
| Address | `to_address` | textarea |  |
| Notes | `description` | textarea |  |
| Taxable | `taxable` | toggle |  |
| Prices include tax | `inclusive_tax` | toggle |  |
| Discount on the total (%) | `discount_percent` | number |  |

#### Tab: Other charges

**Line grid "Charges":** Charge · Amount · Description

**Actions:** Pull the lines of the document

## Invoice Exchanges

Menu key `customer__exchange-invoice` · module `sales`

### List

**Columns:** Customer · Date · Exchange date · Number · Status · Invoice total

**Filters:** Trans date · Collect date · Customer

**Actions:** Edit

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Customer | `customer_id` | select | yes |
| Date | `trans_date` | date | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Number format | `series_id` | select |  |
| Number | `number` | text |  |
| Exchange date | `collect_date` | date | yes |
| Due date | `due_date` | date | yes |

#### Tab: Invoices

**Line grid "Lines":** Invoice · Invoice date · Due

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Notes | `description` | textarea |  |

## Customer Categories

Menu key `customer__customer-category` · module `sales`

### List

**Columns:** Category name · Default

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Category name | `name` | text | yes |
| Sub-category of | `parent_id` | select |  |
| Default category | `is_default` | toggle |  |

## Price Categories

Menu key `customer__price-category` · module `sales`

### List

**Columns:** Notes · Category name · Default

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Category name | `name` | text | yes |
| Notes | `notes` | textarea |  |
| Default level | `is_default` | toggle |  |

## Customers

Menu key `customer__customer` · module `sales`

### List

**Columns:** Name · Primary contact · Customer ID · Category · Price category · Discount category · Tax address · Branch · Address · Payment term · Credit limit

**Filters:** Active · Category · Branch

**Actions:** Edit · Delete

### Form

**Section: General**

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Customer ID format | `series_id` | select |  |
| Customer ID | `number` | text |  |
| Category | `category_id` | select |  |
| Work phone | `work_phone` | text |  |
| Mobile | `mobile_phone` | text |  |
| WhatsApp | `whatsapp` | text |  |
| Email | `email` | text |  |
| Fax | `fax` | text |  |
| Website | `website` | text |  |
| Used in branch | `branch_id` | select |  |
| Active | `is_active` | toggle |  |

#### Tab: Billing address

**Fieldset: Billing address**

| Field | Column | Type | Required |
|---|---|---|---|
| Street | `bill_street` | textarea |  |
| City | `bill_city` | text |  |
| Postcode | `bill_zip_code` | text |  |
| Province | `bill_province` | text |  |
| Country | `bill_country` | text |  |

#### Tab: Contacts

**Line grid "Contacts":** Full name · Position · Email · Mobile

#### Tab: Shipping

| Field | Column | Type | Required |
|---|---|---|---|
| Same as the billing address | `ship_same_as_bill` | toggle |  |

**Fieldset: Shipping address**

| Field | Column | Type | Required |
|---|---|---|---|
| Street | `ship_street` | textarea |  |
| City | `ship_city` | text |  |
| Postcode | `ship_zip_code` | text |  |
| Province | `ship_province` | text |  |
| Country | `ship_country` | text |  |

**Line grid "Other delivery addresses":** Address

#### Tab: Sales

| Field | Column | Type | Required |
|---|---|---|---|
| Price category | `price_category_id` | select |  |
| Discount category | `discount_category_id` | select |  |
| Default salesperson | `salesman_id` | select |  |
| Payment term | `payment_term_id` | select |  |
| Default discount (%) | `default_sales_disc` | number |  |
| Default invoice description | `default_invoice_desc` | text |  |

**Fieldset: Accounts**

| Field | Column | Type | Required |
|---|---|---|---|
| Receivable | `receivable_account_id` | select |  |
| Down payments | `down_payment_account_id` | select |  |
| Sales | `sales_account_id` | select |  |
| Item discounts | `item_discount_account_id` | select |  |
| Cost of goods sold | `cogs_account_id` | select |  |
| Sales returns | `sales_return_account_id` | select |  |
| Sales discounts | `sales_discount_account_id` | select |  |

#### Tab: Tax

| Field | Column | Type | Required |
|---|---|---|---|
| Invoice totals include tax by default | `default_inc_tax` | toggle |  |
| Tax ID type | `wp_type` | select |  |
| Tax ID number | `wp_number` | text |  |
| Taxpayer name | `wp_name` | text |  |
| Business location ID (NITKU) | `nitku` | text |  |
| Country code | `country_tax_code` | text |  |
| Transaction type | `document_code` | select |  |
| Tax address is the billing address | `tax_same_as_bill` | toggle |  |

**Fieldset: Tax address**

| Field | Column | Type | Required |
|---|---|---|---|
| Street | `tax_street` | textarea |  |
| City | `tax_city` | text |  |
| Postcode | `tax_zip_code` | text |  |
| Province | `tax_province` | text |  |
| Country | `tax_country` | text |  |

#### Tab: Opening balance

**Line grid "Opening balances":** Date · Amount · Payment term · Number · Description

#### Tab: Other

**Fieldset: Credit limit**

| Field | Column | Type | Required |
|---|---|---|---|
| Credit limit mode | `credit_limit_mode` | radio |  |
| Parent customer | `parent_customer_id` | select |  |
| Block when an invoice is older than | `credit_limit_age_enabled` | toggle |  |
| days | `credit_limit_age_days` | number |  |
| Block when receivables and open orders exceed | `credit_limit_amount_enabled` | toggle |  |
| amount | `credit_limit_amount` | number |  |

| Field | Column | Type | Required |
|---|---|---|---|
| Default warehouse | `default_warehouse_id` | select |  |
| Notes | `notes` | textarea |  |

## Price & Discount Adjustments

Menu key `inventory__sellingprice-adjustment` · module `sales`

### List

**Columns:** Number · Effective from · Price category · Notes · Ends on · Adjustment type · Active

**Filters:** Trans date · Active · Price category · Adjustment type

**Actions:** Edit

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Price category | `price_category_id` | select | yes |
| Adjustment type | `sales_adjustment_type` | select | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Number format | `series_id` | select |  |
| Number | `number` | text |  |
| Effective from | `trans_date` | date | yes |
| Ends on | `end_date` | date |  |
| Active | `is_active` | toggle |  |

#### Tab: Line items

**Line grid "Lines":** Item · Unit · New value

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Notes | `description` | textarea |  |

## Salesman Commissions

Menu key `company__salesman-commission` · module `sales-extras` · switched by Preferences → Features → Sales extras: check-ins, commissions, targets

### List

**Columns:** Notes · Rule name · In force · Gain · Active

**Filters:** Active

**Actions:** Edit · Delete

### Form

#### Tab: Commission

| Field | Column | Type | Required |
|---|---|---|---|
| Rule name | `name` | text | yes |
| In force | `active_period` | radio |  |
| From | `from_date` | date |  |
| Until | `to_date` | date |  |
| Salespeople | `salesman_scope` | radio |  |
| Chosen salespeople | `salesmen` | checkbox list |  |
| Applies to levels | `levels` | checkbox list |  |

**Fieldset: Requirement**

| Field | Column | Type | Required |
|---|---|---|---|
| Requirement | `requirement` | radio |  |
| From | `requirement_from` | number |  |
| To | `requirement_to` | number |  |
| Per quantity | `requirement_qty` | number |  |

**Fieldset: Gain**

| Field | Column | Type | Required |
|---|---|---|---|
| Commission is | `gain_type` | select | yes |
| — | `gain_value` | number | yes |
| % of | `gain_basis` | select |  |

#### Tab: Other

| Field | Column | Type | Required |
|---|---|---|---|
| Notes | `notes` | textarea |  |
| Active | `is_active` | toggle |  |

## Sales Targets

Menu key `budget-target__sales-target` · module `sales-extras` · switched by Preferences → Features → Sales extras: check-ins, commissions, targets

### List

**Columns:** From · Until · Year · Target name · Branch · Target type

**Filters:** Target type

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Target name | `name` | text | yes |
| Target type | `target_type` | select | yes |
| Branch sales | `branch_id` | select |  |
| From | `from_date` | date |  |
| Until | `to_date` | date | yes |

#### Tab: Targets

**Line grid "Lines":** For · Quantity · Value

#### Tab: Notes

| Field | Column | Type | Required |
|---|---|---|---|
| Notes | `notes` | textarea |  |
| Analyst | `analyst_name` | text |  |

## e-Commerce Links

Menu key `customer__ecommerce-setting` · module `sales`

Planned for a later release. Marketplace links were an integration service of the original product and are not replicated; a sales order can be entered for any customer.

## Check-ins

Menu key `customer__sales-check-in` · module `sales-extras` · switched by Preferences → Features → Sales extras: check-ins, commissions, targets

### List

**Columns:** Date · Number · Customer (at check-in) · Salesperson · Transaction · Location

**Filters:** Trans date · Salesperson

**Actions:** Edit · Take an order

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Checked in at | `checked_in_at` | date and time | yes |
| Enter the number by hand | `manual_number` | toggle |  |
| Number format | `series_id` | select |  |
| Number | `number` | text |  |
| Customer | `customer_id` | select |  |
| Customer name at check-in | `customer_name` | text | yes |
| Salesperson | `salesman_id` | select | yes |
| Order taken | `sales_order_id` | select |  |
| Latitude | `latitude` | number |  |
| Longitude | `longitude` | number |  |
| Notes | `notes` | textarea |  |

