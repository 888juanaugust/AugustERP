## Behaviours

- The chain is quotation → order → delivery (partial or several) → invoice (from one or several deliveries, or direct) → receipt. Fulfilment status (waiting, partial, processed, closed) is derived from quantities; "Pull" picks open upstream documents of the customer, "Process" opens the next document prefilled.
- Prices are typed freely by those with the right; otherwise they come from the customer's price category and the price adjustments in force on the document's date. Discounts per line and per document; other charges to any account; tax included or excluded per document.
- The order's approval, when the Sales Order Approval rule is on, follows the approval rules and the credit check: amount limit (open receivables plus open orders), age limit, and the company's freeze days.
- Deliveries move stock out at the moving average cost; the invoice moves goods delivered to cost of sales. Down payments carry their own tax and are deducted on the invoice. Returns refer to an invoice (or none), bring goods back and issue a credit automatically. Invoice exchange records the handing of invoices to the customer for payment scheduling.
- Customers carry billing, shipping and tax addresses, contacts, a category, a price and a discount category, a default salesperson, payment term and discount, tax identity for the tax invoice, credit limits (own or the parent's), and opening receivables.
- Check-ins, commissions and targets (the Sales extras module) are off by default.
