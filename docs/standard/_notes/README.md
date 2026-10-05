This is the functional standard of the template: what every module does, screen by screen, and the rules every installation keeps. A client installation starts from the whole standard and switches off the modules it does not need in Preferences → Features. Anything a client adds lives in its own layer (`app/Client`, `config/client.php`), never in the standard.

## Rules every installation keeps

- Money is stored as whole units of the base currency (BIGINT); tax base and tax are computed and stored per line; a document's totals are sums of its lines.
- Postings (journal, stock movements, payment allocations) are derived from documents by one posting layer and are never written by hand; the audit log and document revisions only grow.
- A recorded document may be edited or deleted, subject to access rights, the closed-period lock (on the old and the new date) and blockers: reconciled, settled, referenced by a later document.
- Branches are a tag inside one set of books: one ledger, one stock, one average cost.
- Every transaction screen follows one pattern: a header (customer or vendor, date, a number drawn from a numbering series or typed by hand), a tab of lines, a tab of other information, a tab of other charges; the list opens with filters on date, status and printed state.
- An invoice is paid only when the allocations of payment documents reach its total; a sales order is approved only under the approval rules when the Sales Order Approval rule is on.

## Business rules (Preferences → Business Rules)

| Rule | Default | What it does |
|---|---|---|
| Sales Order Approval | off | Sales orders wait for approval before delivery or invoicing; who approves comes from Transaction Approvers, else the "approve transactions" right |
| Segregation of Duties | on | Whoever enters a document never approves or verifies it; switching this off is audited |
| Allow Negative Stock | off | When off, a delivery or adjustment that would take stock below zero is refused |
| Credit notice days | 0 (off) | A customer with an invoice unpaid longer than this is flagged on sales documents and the invoice list |
| Credit freeze days | 0 (off) | No order is approved for a customer with an invoice unpaid longer than this, until it is settled |

## Open questions

Confirm with the client's accountant before relying on these figures:

- The delivery order's journal: goods delivered but not yet invoiced go to the "Goods delivered, not yet invoiced" account named in Preferences; the invoice that follows moves them to cost of sales.
- Same-day costing order: movements are costed by date, receipts before issues on the same day, then in entry order.
- Retained earnings on the balance sheet are computed from the income statement (prior fiscal years' income), not formed by a closing entry.
- Withholding tax codes are modelled as a tax type but take part in no posting.
- VAT on payment, receipt and accrual lines is booked to the tax code's VAT in or out account; these lines do not appear in the VAT return or the e-Tax export.
- Fiscal depreciation follows the fiscal group's method and rate with the rest in the last year of the useful life; the groups, rates and the first-year month count are for the accountant to confirm.
- The early-payment discount is proposed on the open balance including VAT; whether the VAT on it should be corrected (a credit note on the tax invoice) is for the accountant.
- Cost is a moving average per warehouse; FIFO is not offered.
