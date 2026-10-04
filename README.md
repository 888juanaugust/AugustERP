# August ERP

A standard, modular ERP template for trading companies: accounting, inventory, purchasing,
sales, cash and bank, fixed assets, tax and reports. Laravel 13 and Filament 5 on
PostgreSQL. English interface, translatable; Indonesian number, date and tax conventions by
default. One installation per client: create a repository from this template, run the
installer, switch off what the client does not need, and keep merging template updates.

## Modules

| Module group | Screens | Switch |
|---|---|---|
| Settings | Preferences, access groups, users, numbering, print layouts, approval rules | always on (approval rules switchable) |
| Company | Branches, currencies, tax codes, payment terms, shipping, FOB, employees, salary components, recurring and memorized transactions, month-end process, contacts, calendar, activity log | always on (branches and currencies follow their preferences) |
| General Ledger | Chart of accounts, journal vouchers, expense accruals, payroll entries, budgets, account history, journal activity log | always on (budgets and payroll switchable) |
| Cash & Bank | Payments, receipts, bank transfers, bank statements, bank book, reconciliation, giros | always on |
| Sales | Quotation → order → delivery → invoice → receipt, down payments, returns, invoice exchange, customers, price categories and adjustments; check-ins, commissions and targets | always on (sales extras off by default) |
| Purchasing | Requisition → order → receipt → invoice → payment, down payments, returns, claims, vendor prices, payment orders, vendor transfers | always on |
| Inventory | Stock per warehouse at moving average, adjustments, transfers, stock opname, order fulfilment, stock inquiries, items, units, categories, brands | always on |
| Fixed Assets | Assets, categories, fiscal groups, monthly depreciation, changes, disposals, transfers, assets by location | on by default |
| Tax | Tax invoice export (bulk-import XML and the legacy CSV), serial numbers pasted back, VAT return | on by default |
| Reports | A catalogue of sixteen reports computed from the ledgers, with Excel export | always on |

The functional standard, screen by screen, is in [docs/standard](docs/standard/README.md);
it is generated from the code (`php artisan erp:standard`), with hand-written notes on
behaviours and rules. The rules every installation keeps are in [CLAUDE.md](CLAUDE.md);
what the next releases add is in [docs/ROADMAP.md](docs/ROADMAP.md); the visual system in
[docs/design/DESIGN.md](docs/design/DESIGN.md).

## Starting a new client

1. On GitHub, **Use this template** to create the client's repository, then clone it.
2. `composer setup` — installs dependencies, writes `.env`, runs `erp:install` with the
   demo company and builds the assets. Or step by step:

   ```bash
   composer install
   cp .env.example .env && php artisan key:generate      # set DB_*, APP_NAME, APP_URL
   php artisan erp:install                                # prompts for the company, modules, administrator
   npm install && npm run build
   php artisan serve                                      # http://localhost:8000/admin
   ```

   Non-interactive, for a server:

   ```bash
   php artisan erp:install --no-interaction --company="Example Co" --currency=IDR \
     --admin-email=owner@example.test --admin-password='…' --disable=payroll,sales-extras
   ```

3. Put the client's own code in `app/Client` and its settings in `config/client.php`
   (extra modules, starting features, panel colours). The template never touches either.
4. Keep the template as a remote and merge its updates:

   ```bash
   git remote add template https://github.com/888juanaugust/augusterp.git
   git config merge.ours.driver true          # once: keeps app/Client, config/client.php, lang/*.json, .env.example
   git fetch template && git merge template/main
   ```

Requirements: PHP 8.3 or 8.4 with `pdo_pgsql`, `intl`, `bcmath`; PostgreSQL 16; Redis;
Node 22. The schedule needs `php artisan schedule:run` every minute (depreciation on the
month's last day, recurring transactions daily) and a queue worker.

## Running the tests

```bash
php artisan test            # against the erp_test database (phpunit.xml)
vendor/bin/pint --test
php artisan erp:standard --check
npm run smoke -- /admin     # screenshots of running pages into storage/app/smoke (php artisan serve first)
```

In a Claude Code cloud session, `.claude/hooks/session-start.sh` brings up PostgreSQL and
Redis, installs dependencies and installs the demo database.

## Translating

The English text of the UI is the translation key. For a new locale:

```bash
node tools/i18n/extract-strings.mjs id       # writes lang/id.json with every key, to fill in
cp -r lang/en/fields.php lang/id/ ; cp lang/en/status.php lang/id/   # lang/id/menu.php already exists
```

Set `APP_LOCALE=id`. `node tools/i18n/wrap-literals.mjs` wraps any new literal a developer
left unwrapped, and `TranslationGuardTest` fails the build on one.

## History

The template grew out of a complete, screen-by-screen rebuild for one Indonesian trading
company and was then made company-neutral and modular. The study material that rebuild
worked from is kept on the `archive/study` branch, not on `main`.
