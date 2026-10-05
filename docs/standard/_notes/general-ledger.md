## Behaviours

- The chart of accounts is a tree of accounts, each of one of sixteen types (cash and bank, accounts receivable, inventory, other current asset, fixed asset, accumulated depreciation, other asset, accounts payable, other current liability, long-term liability, equity, revenue, cost of goods sold, expense, other income, other expense) that decide where it appears on the statements; an account may carry an opening balance per start date and be deactivated.
- The default accounts the posting layer uses (receivable, payable, down payments, inventory, cost of sales, goods delivered not yet invoiced, rounding, giros) are preferences, never constants.
- Journal vouchers are manual multi-line entries that must balance; expense accruals book expenses to any account with tax and branch per line; payroll entries book salaries by employee and component when the payroll module is on.
- An expense accrual or a payroll entry is paid by a payment (Cash & Bank) whose line settles it: "Pay" on the list opens one, or "Pull open accruals and payroll" on the payment. The line debits the document's own payable account, never more than is open; the document's paid amount and status follow the allocations, and a paid one is locked until its payments are undone.
- Budgets hold an amount per account per month; the monitor compares them with the books; transfers move budget between accounts and months.
- Account history is the ledger of one account with a running balance; the journal activity log is the trail of changes to journals.
