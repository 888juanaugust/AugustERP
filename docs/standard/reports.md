# Reports

Module group `reports`. 5 screens in the standard menu.

## Behaviours

- Every report in the catalogue takes its filters (period, branch, and the report's own), is computed from the journal and the stock ledger when it opens, and exports to Excel. Nothing is stored.
- The VAT return summarises output and input tax per tax period, headed by the company's VAT identity (registered name, tax ID, VAT registration number and date, business type, KLU). The two Article 21 income-tax forms wait for the payroll module's completion.
- The balance sheet shows the income of fiscal years before the current one as retained earnings and the current fiscal year's as "Net income this year"; the fiscal year starts in the month Preferences name. The income statement, statement of changes in equity and cash flow open on the fiscal year to date; the other reports on the current month.
- Receivable and payable aging use the buckets Preferences set (an interval up to a range, then everything older: current, 1–30, 31–60, 61–90 and over 90 days to start) and age from the invoice date or the due date as Preferences say, which a report may change.

## Screens

- [Report Catalogue](#report-catalogue)
- [VAT Return](#vat-return)
- [AI Analysis](#ai-analysis) (not reproduced)
- [Income Tax Art. 21 Return](#income-tax-art-21-return)
- [Withholding Slips](#withholding-slips)

## Report Catalogue

Menu key `report__report` · module `reports`

A read-only screen with its own layout.

## VAT Return

Menu key `report__spt-masa` · module `tax` · switched by Preferences → Features → Tax

### Filters and inputs

| Field | Column | Type | Required |
|---|---|---|---|
| From | `from` | date |  |
| Until | `until` | date |  |
| Show | `kind` | select |  |
| Document kind | `document_code` | select |  |
| Search | `search` | text |  |

### List

**Columns:** Tax · Tax invoice No. · Transaction No. · Date · Document kind · Description · Tax base (DPP) · VAT · Customer / Vendor

## AI Analysis

Menu key `report-insight-analysis` · module `reports`

Not reproduced: a vendor service of the original product.

## Income Tax Art. 21 Return

Menu key `report__formulir-1721-induk` · module `payroll` · switched by Preferences → Features → Payroll entries and salary components

Planned for a later release. The Article 21 income tax return (form 1721) needs payroll, which is not built yet; the payroll phase brings it.

## Withholding Slips

Menu key `report__formulir-1721-bukti-potong` · module `payroll` · switched by Preferences → Features → Payroll entries and salary components

Planned for a later release. Withholding slips (1721-A1/A2) follow the payroll phase.

## Reports in the catalogue

Every figure is computed from the journal and the stock ledger when the report opens; nothing is stored. Each report exports to Excel.

| Report | Group | Needs | Filters | What it shows |
|---|---|---|---|---|
| Open Sales Orders | Sales | always on | From · Until · Branch | Order lines not yet fully delivered or invoiced, with what is left and its value. |
| Receivable Aging | Sales | always on | From · Until · Branch · Age from | Open receivables per customer by age at the period's end, in the buckets Preferences set (current, 1–30, 31–60, 61–90 and over 90 days to start). |
| Sales by Customer | Sales | always on | From · Until · Branch | Invoiced sales per customer in the period: invoices, quantity, amount, VAT and total. |
| Sales by Item | Sales | always on | From · Until · Branch | Invoiced sales per item in the period: invoices, quantity, amount, VAT and total. |
| Sales by Salesperson | Sales | always on | From · Until · Branch | Invoiced sales per salesperson in the period: invoices, quantity, amount, VAT and total. |
| Open Purchase Orders | Purchasing | always on | From · Until · Branch | Order lines not yet fully received or invoiced, with what is left and its value. |
| Payable Aging | Purchasing | always on | From · Until · Branch · Age from | Open payables per vendor by age at the period's end, in the buckets Preferences set (current, 1–30, 31–60, 61–90 and over 90 days to start). |
| Purchases by Item | Purchasing | always on | From · Until · Branch | Invoiced purchases per item in the period: invoices, quantity, amount, VAT and total. |
| Purchases by Vendor | Purchasing | always on | From · Until · Branch | Invoiced purchases per vendor in the period: invoices, quantity, amount, VAT and total. |
| Inventory Value by Warehouse | Inventory | always on | Warehouse · Item category | Quantity on hand, average cost and value of every item per warehouse, from the stock ledger's cost cache. |
| Stock Card | Inventory | always on | From · Until · Item · Warehouse | One item's movements in and out with the running quantity and value, per warehouse or across all. |
| Cash & Bank Mutations | Cash & Bank | always on | From · Until · Branch | Opening balance, money in, money out and closing balance of every cash and bank account over the period. |
| Depreciation Schedule | Fixed Assets | `fixed-assets` | From · Until · Asset category | Every asset with its cost, the period's depreciation, accumulated depreciation and book value at the period's end. |
| Balance Sheet | Financial | always on | From · Until · Branch | Assets, liabilities and equity as at the end of the period, current and non-current, with the income to date. |
| Cash Flow Statement | Financial | always on | From · Until · Branch | Cash in and out by operating, investing and financing activities, from the counter-accounts of every cash posting. |
| General Ledger | Financial | always on | From · Until · Branch · Account | Every posting of every account in the period, with running balances. |
| Income Statement | Financial | always on | From · Until · Branch | Revenue, cost of sales, expenses and the net income of the period, per branch when asked. |
| Statement of Changes in Equity | Financial | always on | From · Until · Branch | Equity at the start, capital movements, the period's income, equity at the end. |
| Trial Balance | Financial | always on | From · Until · Branch | Every account with its opening balance, the period's debits and credits, and the closing balance. |

