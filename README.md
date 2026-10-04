# August's ERP

An ERP for an Indonesian trading business: accounting, inventory, purchasing, sales, cash
and bank, fixed assets, tax and reports. English interface, Indonesian number and date
conventions, built on the visual system in `docs/design/DESIGN.md`. It is built in two stages:
first every function staff rely on today exists and is tested, then the owner modifies it
and gives it a new interface.

The functional spec came from a structured, read-only study of the system the business
runs today (the *reference system*): every module, screen, list, form field, line grid,
tab, report and preference, so that nothing staff depend on is lost before the interface
is redesigned. The application is a Laravel 13 + Filament 5 skeleton that the spec fills
in, phase by phase.

## What is built (October 2026)

Every module of the reference system is in place and tested: 86 of its 89 replicated
screens are real (`ScreenRouteTest` is the honest counter), the other three are
placeholders that say what is missing — emailing tax invoices, and the two Article 21
income-tax forms that wait for payroll. Vendor services of the reference system
(e-banking, virtual accounts, e-payment, marketplace links, the add-on store, financing,
AI analysis) are not reproduced.

| Module | Built |
|---|---|
| Settings | Preferences, numbering, access groups, users, print layouts, approval rules |
| Company | Branches, currencies, tax codes, payment terms, shipping, FOB, employees, salary components, contacts, recurring and memorized transactions, month-end process, calendar, activity log |
| General Ledger | Chart of accounts, one posting layer with append-only ledgers, journal vouchers, expense accruals, payroll entries, budgets, budget monitor and transfers, account history, journal activity log |
| Cash & Bank | Payments, receipts, bank transfers, bank statements, bank book, reconciliation (a cleared line locks its document), giros |
| Sales | Quotation → order (marketing approval, credit limit, aging freeze) → delivery → invoice → receipt, down payments, returns, invoice exchange, price adjustments, commissions, targets, check-ins |
| Purchasing | Requisition → order → receipt → invoice → payment, down payments, returns, claims, vendor prices, payment orders |
| Inventory | Stock ledger per warehouse with moving average on document dates and recosting, adjustments, transfers, stock opname, order fulfilment, stock and minimum-stock inquiries |
| Fixed Assets | Assets, categories, fiscal groups, monthly depreciation by method (scheduled), changes, disposals, transfers, assets by location |
| Tax | e-Tax invoice export (bulk-import XML and the legacy CSV), serial numbers pasted back |
| Reports | A catalogue of sixteen reports computed from the ledgers with Excel export, and the VAT return |

Documents print under a designable layout; customers, vendors and items import from a
spreadsheet. Three scheduled commands run the books: `erp:depreciate` (last day of the
month), `erp:recurring` (daily), and the queue workers. Open accounting questions are listed
in `docs/spec` (the delivery journal's goods-in-transit account, same-day costing order);
confirm them with the accountant before relying on those figures.

| Where | What |
|---|---|
| [docs/spec/README.md](docs/spec/README.md) | **The functional spec**: every module and screen, its list columns, filters, form fields (with the reference system's own field names), line grids, tabs, and the behaviours to replicate. Generated |
| [docs/ROADMAP.md](docs/ROADMAP.md) | The build order: fifteen phases from Preferensi to UAT, and what "done" means for each |
| [docs/referensi/menu.md](docs/referensi/menu.md) | The reference system's full menu tree with the route of every screen, as studied |
| [docs/referensi/modul/](docs/referensi/modul/) · [laporan.md](docs/referensi/laporan.md) · [preferensi.md](docs/referensi/preferensi.md) | The raw study, rendered: one page per module, the reports, the preference switches as the business set them |
| [docs/referensi/PARITY-webtransaction.md](docs/referensi/PARITY-webtransaction.md) | The feature matrix written for the previous plan (WebTransaction against the reference system), kept as the source of the notes in `docs/spec/_catatan.json` |
| [tools/referensi-scan/](tools/referensi-scan/README.md) | The study tool: how it is run, what it refuses to do, how the spec is regenerated |
| [docs/design/DESIGN.md](docs/design/DESIGN.md) | The visual system: tokens, layout, page types, components |
| [CLAUDE.md](CLAUDE.md) | Rules for anyone (or any agent) working in this repository: stack, language, design, invariants, the footprint rule |

## Running the application

```bash
composer install            # PHP ^8.3, PostgreSQL 16 running, databases augusterp and augusterp_test
cp .env.example .env && php artisan key:generate
php artisan migrate --seed  # seeds the administrator: admin@august.test / ADMIN_PASSWORD (or "password" locally)
php artisan test
npm install && npm run build
php artisan serve           # /admin
```

In a Claude Code cloud session, `.claude/hooks/session-start.sh` does the database, Redis,
Composer and tool setup on start.

## Regenerating the spec

```bash
cd tools/referensi-scan
npm ci && npm test
npm run scan -- --screenshots       # credentials and database from the environment, see its README
npm run render && npm run spec
./privacy-check.sh
```

Then read the diff of `docs/referensi/` and `docs/spec/` before committing: labels and
structure only, never a record.
