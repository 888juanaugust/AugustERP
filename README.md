# August's ERP

A replica of **REFERENSI Online**'s functions, built to be modified and re-skinned by its
owner afterwards. The spec was read from the owner's own REFERENSI database by a read-only
scanner; the application is a Laravel 13 + Filament 5 skeleton that the spec fills in,
phase by phase.

| Where | What |
|---|---|
| [docs/spec/README.md](docs/spec/README.md) | **The functional spec**: every REFERENSI module and screen, its list columns, filters, form fields (with REFERENSI's own field names), line grids, tabs, and the behaviours to replicate. Generated |
| [docs/ROADMAP.md](docs/ROADMAP.md) | The build order: 14 phases from Preferensi to importers, and what "done" means for each |
| [docs/referensi/menu.md](docs/referensi/menu.md) | REFERENSI's full menu tree with the hash route of every screen, as scanned |
| [docs/referensi/modul/](docs/referensi/modul/) · [laporan.md](docs/referensi/laporan.md) · [preferensi.md](docs/referensi/preferensi.md) | The raw scan, rendered: one page per module, the reports, the preference switches as the business set them |
| [docs/referensi/PARITY-webtransaction.md](docs/referensi/PARITY-webtransaction.md) | The feature matrix written for the previous plan (WebTransaction vs REFERENSI), kept as the source of the notes in `docs/spec/_catatan.json` |
| [tools/referensi-scan/](tools/referensi-scan/README.md) | The scanner: how it is run, what it refuses to do, how the spec is regenerated |
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
cd tools/referensi-scan
npm ci && npm test
REFERENSI_DATABASE=JAVAINDO npm run scan -- --screenshots    # credentials from the environment, see the README
npm run render && npm run spec
```

Then read the diff of `docs/referensi/` and `docs/spec/` before committing: labels and
structure only, never a record.
