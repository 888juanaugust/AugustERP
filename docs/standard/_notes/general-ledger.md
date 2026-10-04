## Behaviours

- The chart of accounts is a tree of accounts, each of one of sixteen types (cash and bank, accounts receivable, inventory, other current asset, fixed asset, accumulated depreciation, other asset, accounts payable, other current liability, long-term liability, equity, revenue, cost of goods sold, expense, other income, other expense) that decide where it appears on the statements; an account may carry an opening balance per start date and be deactivated.
- The default accounts the posting layer uses (receivable, payable, down payments, inventory, cost of sales, goods delivered not yet invoiced, rounding, giros) are preferences, never constants.
- Journal vouchers are manual multi-line entries that must balance; expense accruals book expenses to any account with tax and branch per line; payroll entries book salaries by employee and component when the payroll module is on.
- Budgets hold an amount per account per month; the monitor compares them with the books; transfers move budget between accounts and months.
- Account history is the ledger of one account with a running balance; the journal activity log is the trail of changes to journals.
