# Company

Module group `company`. 14 screens in the standard menu.

## Behaviours

- The company's name, address, tax ID and phone are preferences printed on every document; the base currency's symbol prefixes every amount.
- Branches tag documents and journal lines inside one set of books; reports filter by branch. One default branch always exists; the Branches screen shows when Multiple branches is on.
- Tax codes carry a rate and the accounts for tax on sales and purchases; a code may compute its base as a fraction of the price (the 12 % VAT whose base is 11/12 of the price is the seeded default). Prices on a document are entered including or excluding tax, per document.
- Payment terms name a due period in days, an early-payment discount and its window; chosen per customer or vendor and per document.
- Shipping methods and FOB terms are masters chosen on orders and deliveries.
- Employees are the salespeople named on sales documents, customers and commissions; salary components feed payroll entries when the payroll module is on.
- Recurring transactions make their document (journal voucher, payment or receipt) on schedule; memorized transactions are templates filled in by hand. The calendar shows what falls due: receivables, payables, maturing giros, recurring runs, notes and month end.
- Month-end process closes months in order; a closed month refuses any addition, change or deletion dated in it, on the old and the new date of an edit; reopening takes a special right and is audited.
- The activity log is the append-only record of who did what, with the document's revisions before and after each change.

## Screens

- [Currencies](#currencies)
- [Branches](#branches)
- [Tax Codes](#tax-codes)
- [Payment Terms](#payment-terms)
- [Shipping Methods](#shipping-methods)
- [FOB Terms](#fob-terms)
- [Salary Components](#salary-components)
- [Employees](#employees)
- [Recurring Transactions](#recurring-transactions)
- [Month-end Process](#month-end-process)
- [Contacts](#contacts)
- [Memorized Transactions](#memorized-transactions)
- [Calendar](#calendar)
- [Activity Log](#activity-log)

## Currencies

Menu key `company__currency` · module `company` · switched by Preferences → Features → Multiple currencies

### List

**Columns:** Symbol · Code · Country / Name · Base · Active

**Filters:** Active

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Code | `code` | text | yes |
| Symbol | `symbol` | text | yes |
| Name | `name` | text | yes |
| Country | `country` | text |  |
| Base currency | `is_base` | toggle |  |
| Active | `is_active` | toggle |  |

## Branches

Menu key `company__branch` · module `company` · switched by Preferences → Features → Multiple branches

### List

**Columns:** Active · Name · Phone number · Users · Default

**Filters:** Active

**Actions:** Edit · Delete

### Form

#### Tab: General

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Phone number | `phone_number` | text |  |
| Address | `address` | textarea |  |
| Default branch | `is_default` | toggle |  |
| Active | `is_active` | toggle |  |

#### Tab: Tax info

| Field | Column | Type | Required |
|---|---|---|---|
| Business location ID (NITKU) | `nitku` | text |  |

#### Tab: Users

| Field | Column | Type | Required |
|---|---|---|---|
| Available to all users | `used_all_user` | toggle |  |
| Users | `users` | checkbox list |  |

## Tax Codes

Menu key `company__tax` · module `company`

### List

**Columns:** Description · Tax type · Rate · Burden · Default · Active

**Filters:** Tax type · Active

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Tax type | `tax_type` | select | yes |
| Description | `description` | text | yes |
| Rate (%) | `rate_percent` | number | yes |

**Fieldset: Tax base**

| Field | Column | Type | Required |
|---|---|---|---|
| Numerator | `dpp_numerator` | number | yes |
| Denominator | `dpp_denominator` | number | yes |

| Field | Column | Type | Required |
|---|---|---|---|
| Sales tax account | `sales_tax_account_id` | select | yes |
| Purchase tax account | `purchase_tax_account_id` | select | yes |
| Default tax code | `is_default` | toggle |  |
| Active | `is_active` | toggle |  |

## Payment Terms

Menu key `company__payment-term` · module `company`

### List

**Columns:** Name · Discount · Discount period (days) · Due (days) · Notes · Active · Default

**Filters:** Active

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |

**Fieldset: Early payment discount**

| Field | Column | Type | Required |
|---|---|---|---|
| If paid within (days) | `discount_days` | number | yes |
| Discount (%) | `discount_percent` | number | yes |

| Field | Column | Type | Required |
|---|---|---|---|
| Due in (days) | `due_days` | number | yes |
| Notes | `memo` | textarea |  |
| Default term | `is_default` | toggle |  |
| Active | `is_active` | toggle |  |

## Shipping Methods

Menu key `company__shipment` · module `company`

### List

**Columns:** Name · Contact person · Phone number · Address · Active

**Filters:** Active

**Actions:** Edit · Delete

### Form

#### Tab: General

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Contact person | `pic_name` | text |  |
| Phone number | `pic_phone_number` | text |  |

#### Tab: Other info

| Field | Column | Type | Required |
|---|---|---|---|
| Address | `address` | textarea |  |
| Active | `is_active` | toggle |  |

## FOB Terms

Menu key `company__freeonboard` · module `company`

### List

**Columns:** Name

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |

## Salary Components

Menu key `company__employee-fee` · module `payroll` · switched by Preferences → Features → Payroll entries and salary components

### List

**Columns:** Name · Component kind · Expense account · Active

**Filters:** Active · Component kind

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Component kind | `fee_type` | select | yes |
| Expense account | `expense_account_id` | select | yes |
| Active | `is_active` | toggle |  |

## Employees

Menu key `company__employee` · module `company`

### List

**Columns:** Name · Position · Email · Mobile · Employee ID · PTKP · Employment · Sales

**Filters:** Active · Salesperson · Employment status

**Actions:** Edit · Delete

### Form

**Section: Personal data**

| Field | Column | Type | Required |
|---|---|---|---|
| Salutation | `salutation` | select |  |
| Full name | `name` | text | yes |
| National ID (NIK) | `nik_no` | text |  |
| Email | `email` | text |  |
| Mobile | `mobile_phone` | text |  |
| Work phone | `work_phone` | text |  |
| Home phone | `home_phone` | text |  |
| WhatsApp | `whatsapp` | text |  |
| Website | `website` | text |  |
| Nationality | `nationality` | text |  |

**Section: Employment**

| Field | Column | Type | Required |
|---|---|---|---|
| Enter the number by hand | `manual_number` | toggle |  |
| Employee ID format | `series_id` | select |  |
| Employee ID | `number` | text |  |
| Position | `position` | text |  |
| Join date | `join_date` | date |  |
| Branch | `branch_id` | select |  |
| Salesperson: may be named on sales documents | `is_salesman` | toggle |  |
| Active | `is_active` | toggle |  |
| Notes | `notes` | textarea |  |

#### Tab: Address

**Fieldset: Home address**

| Field | Column | Type | Required |
|---|---|---|---|
| Street | `street` | textarea |  |
| City | `city` | text |  |
| Postcode | `zip_code` | text |  |
| Province | `province` | text |  |
| Country | `country` | text |  |

#### Tab: Income tax

| Field | Column | Type | Required |
|---|---|---|---|
| Withhold income tax (Art. 21) | `withhold_income_tax` | toggle |  |
| Tax ID (NPWP) | `npwp_no` | text |  |
| Employment status | `work_status` | select |  |
| Non-taxable income status (PTKP) | `tax_status` | select |  |
| Tax counted from month | `start_month_payment` | select |  |
| year | `start_year_payment` | select |  |
| Income earned before joining | `previous_income` | number |  |
| Tax withheld before joining | `previous_tax` | number |  |

#### Tab: Salary account

| Field | Column | Type | Required |
|---|---|---|---|
| Bank | `bank_id` | select |  |
| Account number | `bank_account` | text |  |
| Account holder | `bank_account_name` | text |  |

## Recurring Transactions

Menu key `company__recurring` · module `company`

### List

**Columns:** Category · Name · Document · Frequency · Next run · Last run · Runs · Status

**Filters:** Document · Category · Status

**Actions:** Edit · Run now · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Category | `category` | text |  |
| Document | `transaction_type` | select | yes |
| Frequency | `frequency` | select | yes |
| Next run | `next_run_on` | date | yes |
| Until | `end_on` | date |  |
| Status | `status` | select | yes |

**Section: Template**

| Field | Column | Type | Required |
|---|---|---|---|
| Description on the document | `template.description` | text |  |
| Cash / Bank | `template.bank_account_id` | select |  |
| Payee | `template.payee` | text |  |
| Payer | `template.payer` | text |  |

**Line grid "Lines":** Account · Debit · Credit · Amount · Memo

## Month-end Process

Menu key `company__period-end` · module `company`

### List

**Columns:** Month · Status · Closed on · By · Notes

**Filters:** Year · Month

**Actions:** Reopen

## Contacts

Menu key `company__contact` · module `company`

### List

**Columns:** Full name · Type · Company · Email · Mobile

**Filters:** Type

**Actions:** Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Full name | `name` | text | yes |
| Type | `contact_type` | select | yes |
| Company | `company` | text |  |
| Position | `position` | text |  |
| Email | `email` | text |  |
| Mobile | `mobile_phone` | text |  |
| Work phone | `work_phone` | text |  |
| Notes | `notes` | textarea |  |

## Memorized Transactions

Menu key `company__memorize-transaction` · module `company`

### List

**Columns:** Name · Document · Users

**Filters:** Document

**Actions:** Use · Edit · Delete

### Form

| Field | Column | Type | Required |
|---|---|---|---|
| Name | `name` | text | yes |
| Document | `transaction_type` | select |  |
| All users | `used_all_user` | toggle |  |
| Users | `users` | checkbox list |  |

## Calendar

Menu key `company__calendar` · module `company`

A read-only screen with its own layout.

## Activity Log

Menu key `company__audit` · module `company`

### List

**Columns:** Transaction date · Reference · Action · Transaction type · Timestamp · User · Email · IP address

**Filters:** Trans date · Created at · Transaction type · User · Action

**Actions:** View

