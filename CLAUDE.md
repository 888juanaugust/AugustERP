## Project

**August ERP** is a base template: a standard, modular ERP for trading companies, cloned
once per client and installed as that client's own system. Accounting, inventory,
purchasing, sales, cash and bank, fixed assets, tax and reports, each a module a client
switches on or off. Nothing company-specific is baked in: no client name, no client
master data, no client rule. A new client is bootstrapped with `php artisan erp:install`.

The definition of what the template does is `docs/standard/`: one page per module group,
one section per screen, generated from the code by `php artisan erp:standard`, with
hand-written notes on behaviours and rules in `docs/standard/_notes/`. A change to a
screen is a change to the standard; regenerate the pages in the same commit
(`StandardDocsTest` fails otherwise).

**One install per client.** A client's repository is created from this one ("Use this
template" on GitHub), keeps `template` as a second remote, and merges template updates.
Everything the client changes lives in `app/Client`, `config/client.php`, `lang/*.json`
and `.env`; `.gitattributes` keeps those on the client's side when a template update is
merged. Never edit a standard module for one client; override it from the client layer.

**Footprint rule.** The commercial product this standard was once studied from is never
named: not in code, identifiers, docs, commit messages, the UI or the repository
description. `FootprintTest` greps the whole repository for it.

## Stack

- Laravel 13, Filament 5 (one panel, `/admin`), PHP ^8.3 locally, 8.4 in CI
- PostgreSQL 16 only (row locks, partial unique indexes, jsonb). Never SQLite, not even in tests
- Redis for queue and cache; supervisor-managed workers in production
- Node 22 for the Vite build, the UI smoke script (`npm run smoke`) and the i18n tools

## Modules

`config/modules.php` lists the standard modules, each a class under `app/Modules/`
implementing `App\Modules\Module`: its key, the Features preference that switches it (or
null for always on), the `MenuKey`s it owns, its morph-map aliases, what it wires in
`boot()` (ledger writers, blockers, fulfilment chains), its default seeders, commands and
schedule. `ModuleRegistry` answers which are on; `ErpResource` and `ErpPage` refuse and
hide the screens of a module that is off; the morph map stays complete so ledgers and logs
holding an off module's rows still read.

Sidebar groups are the `Modul` enum (ten groups); a module may own screens across groups.
Optional modules: fixed assets, tax, approval rules, budgets (on by default); payroll and
sales extras (check-ins, commissions, targets; off by default). Branches and currencies
screens follow the Multiple branches / Multiple currencies preferences.

**Adding a module:** the class, its entry in `config/modules.php` (or the client's
`config/client.php`), its `MenuKey` cases with `modul()` and `sort()`, its English names in
`lang/en/menu.php`, its resources and pages (extending `ErpResource` / `ErpPage`), its
seeders under `database/seeders/Defaults` returned by `defaultSeeders()`, and the notes in
`docs/standard/_notes/`. `ScreenRouteTest` and `ModuleToggleTest` cover every screen's
ownership, reachability and switch.

## Language

**The product speaks English and is translatable.** Every string the UI shows passes
through `__()`; the English text is the key, so no `lang/en.json` is needed. A locale is
added with `node tools/i18n/extract-strings.mjs <locale>` (writes `lang/<locale>.json` to
fill in) plus copies of `lang/en/{menu,fields,status}.php`; `lang/id/menu.php` already
holds the Indonesian screen names. `node tools/i18n/wrap-literals.mjs` wraps new literals;
`TranslationGuardTest` fails the build on one it would have wrapped. Buttons are verbs
("Save order", "Record payment"), never "Submit".

Numbers and dates follow the Indonesian convention through `App\Domain\Shared\Format`
(`Rp 18.450.000`, `17 Oct 2026` in tables, `17/10/2026` in inputs), with the base
currency's symbol from `Format::symbol()`. Locale number and date formats are on the
roadmap. Code identifiers are English; Indonesian domain words without an English
equivalent in daily use (`giro`, `faktur pajak`, `NPWP`, `NITKU`) stay as they are.

## Design

`docs/design/DESIGN.md` is the visual system; `resources/css/filament/admin/theme.css`
implements its tokens and `AdminPanelProvider` its settings, with the colours overridable
per client in `config/client.php`. Components use tokens, never raw hex. Geist is
self-hosted; no font or asset is loaded from a third party. Dark mode is off.

## Invariants

If a change appears to require breaking one, stop and ask.

1. **Postings are derived from documents, and only the posting layer writes them.** A
   document (invoice, receipt, delivery, adjustment, journal voucher…) is what a person
   edits. Its postings (`journal_entries`/`journal_lines`, `stock_movements`,
   `payment_allocations`) are regenerated from it by one posting service, keyed by a stable
   `posting_key`. Never touch a ledger table from a controller, form, report, seeder or raw
   SQL. Cached columns (stock on hand, average cost, settled amounts) must always be
   reconstructible from the ledgers.
2. **Documents may be edited and deleted**, subject to access rights, the closed-period lock
   on both the old and the new date, and blockers (reconciled, settled, referenced by a
   later document). Every edit or delete is recorded in `audit_logs` and
   `document_revisions` (before and after), which are strictly append-only.
3. **Money is BIGINT in the base currency.** Never float, never DECIMAL for totals. Unit
   prices may use DECIMAL(18,4) where fractions are unavoidable, rounded per line.
4. **Tax is per line.** Tax base and tax are computed and stored on every line, with the
   line's tax code; a document's totals are sums. The 12 % VAT with an 11/12 base is one
   tax code, not a constant. Confirm any change to tax logic with the client's accountant.
5. **Stock is per warehouse, costed by moving average, and every movement carries its
   document date.** A back-dated or edited document re-costs what came after it, never
   before the first open period.
6. **Units are modelled.** An item has a base unit and any number of other units with
   conversion ratios; document lines store the entered unit and quantity and the base
   quantity; the stock ledger is always in base units.
7. **`paid` is set only by settlement.** An invoice is paid when the allocations of payment
   documents reach its total; never by a flag flipped elsewhere.
8. **A branch is a tag in one set of books**, on documents and journal lines. One ledger,
   one stock, one average cost, one vendor list.

## Access and business rules

Access groups hold the five rights per screen plus special rights; a user belongs to
groups, carries per-user grants or revocations, and is limited to assigned branches and
warehouses. The seeded groups are Administrator, Accounting, Finance, Sales, Purchasing and
Warehouse (`Database\Seeders\Defaults\AccessGroupSeeder`); a client reshapes them on the
Access Groups screen.

Business rules are preferences on the Business Rules tab, read through
`App\Domain\Pengaturan\BusinessRule` and `CreditCheck`, never hard-coded: Sales Order
Approval (off; who approves comes from Transaction Approvers, else the approve right),
Segregation of Duties (on; whoever enters never approves or verifies, switching it off is
audited), Allow Negative Stock (off), credit notice and freeze days (0 = off). A new rule
defaults to today's behaviour, so the suite stays green.

## Seeds

- `database/seeders/System/`: what the code relies on (administrator, branch, currency,
  chart of accounts, numbering series, the core masters). Always seeded.
- `database/seeders/Defaults/`: what a company usually wants and edits (banks, tax codes,
  payment terms, FOB, units, print layouts, access groups), plus each module's own defaults
  through `defaultSeeders()`. Seeded for the modules that are on.
- `database/seeders/Demo/`: a neutral demo company (Acme Trading, Contoso Supplies, six
  items with opening stock). Only on request. Never anything a real client would keep.

`DatabaseSeeder` = System + Defaults + enabled modules' defaults. Tests build their sample
customer, vendor, item and salesperson from `Tests\Support\Fixtures`, or `seedDemo()`.

## Starting a client

`php artisan erp:install` migrates, seeds the system tables, records the company (name,
address, tax ID, fiscal year), sets the base currency (IDR, USD, SGD, MYR, EUR), switches
the optional modules on or off (`--enable`, `--disable`, else `config('client.features')`),
creates the first administrator, seeds the defaults of the modules that are on and, when
asked, the demo company. It prompts for what the options leave out, refuses a second run
without `--force`, and `--fresh` drops every table first (never in production). Indonesian
defaults (IDR, 12 % VAT with 11/12 base, Indonesian banks and fiscal asset groups) are the
template's defaults; `--currency` and the Tax switch cover other markets.

## Conventions

- Migrations are additive. Never edit a shipped migration.
- Every money-affecting action writes to the audit log.
- Queue jobs are idempotent; assume they run twice.
- Tests required for: posting (journal balance, stock, allocations), tax per line, cost
  recalculation, period lock and blockers, access rights, module switches. These are where
  bugs cost money.
- Before committing: `php artisan test`, `vendor/bin/pint --test`, and `php artisan
  erp:standard` when a screen changed. CI is `.github/workflows/laravel.yml` (PHP 8.4,
  PostgreSQL 16, Pint, `composer audit`). The cloud session hook
  `.claude/hooks/session-start.sh` brings up Postgres and Redis, installs dependencies and
  runs `erp:install --demo` on a first session.
- Commit messages say what changed for the product, never which tool or model wrote them.
