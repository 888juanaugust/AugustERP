## Behaviours

- The chain is requisition → order → goods receipt (from the order, partial) → invoice (from receipts or direct, moving stock when direct) → payment. Discounts and tax code per line; other charges to any account, including allocation into the goods' cost (landed cost).
- Down payments to a vendor are deducted on the invoice. Returns refer to a receipt or invoice and issue a debit automatically; claims are a vendor's credit without goods coming back.
- With "last purchase price is updated by purchase invoices" on (the default), an item's purchase price follows its latest purchase invoice dated from the cutoff date, net of discount and included tax, per base unit; deleting that invoice falls back to the one before.
- Vendor prices are the default purchase price per vendor and item. Payment orders instruct the bank to pay several vendor invoices; vendor transfers pay many vendors in one document.
- Vendors carry payment terms, tax status and identity, default tax, bank accounts, contacts and opening payables.
- With departments or projects on, orders, receipts, invoices, returns, vendor claims, down payments and payments carry a department and a project on the header and on every line and charge. A line's own wins; a line that names none, and the document's own legs (receivable or payable, tax, down payments), take the header's. A document made from another, or a line pulled from one, keeps its source's tags; the income statement filtered by a department shows its revenue and cost of sales.
