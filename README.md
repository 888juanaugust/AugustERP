# August's ERP

A replica of **ACCURATE Online**'s functions, built to be modified and re-skinned by its
owner afterwards. The spec was read from the owner's own ACCURATE database by a read-only
scanner; the application is a Laravel 13 + Filament 5 skeleton that the spec fills in,
phase by phase.

| Where | What |
|---|---|
| [docs/spec/README.md](docs/spec/README.md) | **The functional spec**: every ACCURATE module and screen, its list columns, filters, form fields (with ACCURATE's own field names), line grids, tabs, and the behaviours to replicate. Generated |
| [docs/ROADMAP.md](docs/ROADMAP.md) | The build order: 14 phases from Preferensi to importers, and what "done" means for each |
| [docs/accurate/menu.md](docs/accurate/menu.md) | ACCURATE's full menu tree with the hash route of every screen, as scanned |
| [docs/accurate/modul/](docs/accurate/modul/) · [laporan.md](docs/accurate/laporan.md) · [preferensi.md](docs/accurate/preferensi.md) | The raw scan, rendered: one page per module, the reports, the preference switches as the business set them |
| [docs/accurate/PARITY-webtransaction.md](docs/accurate/PARITY-webtransaction.md) | The feature matrix written for the previous plan (WebTransaction vs ACCURATE), kept as the source of the notes in `docs/spec/_catatan.json` |
| [tools/accurate-scan/](tools/accurate-scan/README.md) | The scanner: how it is run, what it refuses to do, how the spec is regenerated |
| [CLAUDE.md](CLAUDE.md) | Rules for anyone (or any agent) working in this repository: stack, language, invariants |

## Running the application

```bash
composer install            # PHP ^8.3, PostgreSQL 16 running, databases augusterp and augusterp_test
cp .env.example .env && php artisan key:generate
php artisan migrate
php artisan test
php artisan serve           # /admin is the (placeholder) Filament panel
```

In a Claude Code cloud session, `.claude/hooks/session-start.sh` does the database, Redis,
Composer and scanner setup on start.

## Regenerating the spec

```bash
cd tools/accurate-scan
npm ci && npm test
ACCURATE_DATABASE=JAVAINDO npm run scan -- --screenshots    # credentials from the environment, see the README
npm run render && npm run spec
```

Then read the diff of `docs/accurate/` and `docs/spec/` before committing: labels and
structure only, never a record.
