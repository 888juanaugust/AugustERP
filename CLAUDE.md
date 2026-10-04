## Project

**August's ERP** is the business's own ERP: accounting, inventory, purchasing, sales,
cash and bank, fixed assets, tax and reports for an Indonesian trading company (registered
PT/CV, wholesale automotive spare parts). It is built in two stages: first every function
staff rely on today exists and is tested, then the owner modifies it and gives it a new
interface. Decided 2026-10-03.

The definition of done is `docs/spec/`: one page per module, one section per screen,
generated from a structured study of the **reference system**, the ERP the business runs
today. A phase of `docs/ROADMAP.md` is done when every screen in its spec pages exists with
its fields and every *Perilaku yang direplikasi* line has a test.

**The UI follows DESIGN.md.** Filament's admin panel is skinned with the design system and
lays every screen out the way the reference system does. Spend the effort on the domain,
the data model and the tests; the look is the tokens' job.

**Footprint rule.** Outside `tools/referensi-scan/` (the study tool, which has to name the
system it logs into), the reference product is called *sistem referensi* and nothing else:
not in code, identifiers, docs, commit messages, the UI or the repository description.
August's ERP is its own product; the study is where its spec came from.

## Stack

- Laravel 13, Filament 5 (one panel, `/admin`, English), PHP ^8.3 locally, 8.4 in CI and on the VPS
- PostgreSQL 16 only (row locks, partial unique indexes, jsonb). Never SQLite, not even in tests
- Redis for queue and cache; supervisor-managed workers in production
- `tools/referensi-scan/`: the Playwright study tool that produced the spec. Node 22. Not
  part of the application; Laravel never loads it

## Language

**The product speaks English** (owner, 2026-10-04): navigation, screen names, field labels,
buttons, statuses, validation messages, emails and print views. `APP_LOCALE=en`. Buttons are
verbs that say what happens ("Save order", "Record payment"), never "Submit" or "OK". Numbers
and dates follow the Indonesian convention DESIGN.md specifies, through
`App\Domain\Shared\Format`, not through the locale: `Rp 18.450.000`, `17 Oct 2026` in
tables, `17/10/2026` in inputs.

The study documents (`docs/spec/`, `docs/referensi/`) stay in the language of the reference
system, as the faithful record of what was studied. Every screen has its English name in
`lang/en/menu.php` beside its studied name, and `App\Domain\Access\MenuKey::source()`
returns the studied name, so the spec stays traceable from any screen.

Code identifiers are English, preferring the reference system's own field names
(`transDate`, `typeAutoNumber`, `paymentTerm`, recorded in the spec as *Nama di sistem
referensi*) as snake_case columns (`trans_date`, `payment_term_id`), so that data can later
be moved in and out of it one to one. Indonesian domain words that have no English
equivalent in daily use (`giro`, `faktur pajak`, `NPWP`, `NITKU`, `PPh`) stay as they are.

## Design

`docs/design/DESIGN.md` is the visual system; `resources/css/filament/admin/theme.css`
implements its tokens and the panel provider its settings. Components use tokens, never raw
hex. The layout of every screen follows the reference system (its list columns and filters,
its form's header, tabs and line grids, recorded in `docs/spec/`); DESIGN.md decides how
that layout looks. Geist is self-hosted; no font or asset is loaded from a third party.

## Invariants

Kept from WebTransaction because they are right for any ERP, and reconciled with how the
reference system behaves. If a change appears to require breaking one, stop and ask.

1. **Postings are derived from documents, and only the posting layer writes them.** A
   document (faktur, penerimaan, pengiriman, penyesuaian, jurnal umum…) is what a person
   edits. Its postings (`journal_entries`/`journal_lines`, `stock_movements`,
   `payment_allocations`) are regenerated from it by one posting service, keyed by a stable
   `posting_key`. Never touch a ledger table from a controller, form, report, seeder or raw
   SQL. Cached columns (stock on hand, average cost, settled amounts) must always be
   reconstructible from the ledgers.
2. **Documents may be edited and deleted**, subject to hak akses, the closed-period lock on
   both the old and the new date, and blockers (reconciled, settled, referenced by a later
   document). Every edit or delete is recorded in `audit_logs` and `document_revisions`
   (before and after), which are strictly append-only.
3. **Money is BIGINT rupiah.** Never float, never DECIMAL for totals. Unit prices may use
   DECIMAL(18,4) where fractional rupiah is unavoidable, rounded to whole rupiah per line.
4. **Tax is per line.** DPP and PPN are computed and stored on every line, with the line's
   tax code; a document's totals are sums. The PPN 12% with DPP 11/12 (PMK 131/2024) rule is
   one tax code, not a constant. Confirm any change to tax logic with the accountant.
5. **Stock is per warehouse, costed by moving average, and every movement carries its
   document date.** A back-dated or edited document re-costs what came after it, never
   before the first open period.
6. **Units are modelled.** A SKU has a base unit and any number of other units with
   conversion ratios; document lines store the entered unit and quantity and the base
   quantity; the stock ledger is always in base units.
7. **`paid` is set only by settlement.** An invoice is paid when the allocations of
   payment documents reach its total; never by a flag flipped elsewhere.
8. **Cabang is a tag in one set of books**, on documents and journal lines. One ledger, one
   stock, one average cost, one supplier list.

## Access

Permission groups hold rights per menu (lihat / tambah / ubah / hapus / cetak) plus special
rights; a user belongs to a group, carries per-user grants or revocations, and is limited to
the cabang and gudang assigned to them. WebTransaction's two-key rules (filer ≠ verifier,
counter ≠ approver, whoever is paid on a sale never approves its credit) are kept behind the
`PemisahanTugas` switch, default on; turning one off is itself audited.

## The study and the spec

- `tools/referensi-scan/README.md` says how the study is run and what it refuses to do. It
  is read-only by construction (network guard, click rules, sanitiser) and it never commits
  `.state/` (session, request log, screenshots of live records).
- `docs/referensi/` is what the study wrote: `scan.json`, `menu.md`, `modul/*.md`,
  `laporan.md`, `preferensi.md`. Rendered by `render.mjs`.
- `docs/spec/` is generated by `spec.mjs` from `scan.json` and `docs/spec/_catatan.json`
  (the hand-kept notes: behaviours to replicate, switches, open questions). **Edit the
  notes, never the generated pages**, then regenerate: `npm run render && npm run spec`.
- Before committing anything under `docs/referensi/` or `docs/spec/`, run
  `tools/referensi-scan/privacy-check.sh`: no customer, supplier, employee name, amount or
  document number may enter the repository.

## Conventions

- Migrations are additive. Never edit a shipped migration.
- Every money-affecting action writes to the audit log.
- Queue jobs are idempotent; assume they run twice.
- Tests required for: posting (journal balance, stock, allocations), tax per line, HPP
  recalculation, period lock and blockers, hak akses. These are where bugs cost money.
- A new switch defaults to today's behaviour, so the suite stays green; tests of the
  reference behaviour turn the switch.
- CI: `.github/workflows/laravel.yml` (PHP 8.4, PostgreSQL 16) and
  `.github/workflows/scanner.yml` (`npm test` for the study tool and the privacy check).
  The cloud session hook `.claude/hooks/session-start.sh` brings up Postgres, Redis,
  Composer, the tool's dependencies and the browser's trust in the session proxy.
