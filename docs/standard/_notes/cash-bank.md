## Behaviours

- Any number of cash and bank accounts (accounts of type cash and bank), each with its own book and reconciliation.
- Payments and receipts are multi-line documents to or from any account, with tax and branch per line; a giro (cheque) recorded on a payment or receipt sits in the giro account until it clears or bounces, each a dated event.
- Bank transfers move money between cash and bank accounts, with a fee.
- Bank statements are imported from CSV or Excel (the column headers are recognised in English and Indonesian); reconciliation matches statement lines to the book per account and period, and a reconciled line blocks changes to its document.
- The bank book is the mutation list of one account with a running balance.
