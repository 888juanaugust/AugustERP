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
