## Project

**August's ERP** replicates the functions of **REFERENSI Online**, the Indonesian cloud
ERP the business runs on today, so that the owner can first match it and then change it,
including a new user interface. Decided 2026-10-03; the previous plan (make WebTransaction,
the B2B spare-parts portal, match REFERENSI in place) is superseded, and WebTransaction is
not touched by this project.

The definition of done is `docs/spec/`: one page per REFERENSI module, one section per
screen, generated from a read-only scan of the owner's own REFERENSI database. A phase of
`docs/ROADMAP.md` is done when every screen in its spec pages exists with its fields and
every *Perilaku yang direplikasi* line has a test.

**The UI is a placeholder.** Filament's admin panel is scaffolding so the functions can be
built and tested; the owner redesigns the interface afterwards. Do not spend effort on
visual polish; spend it on the domain, the data model and the tests.

## Stack

- Laravel 13, Filament 5 (one panel, `/admin`), PHP ^8.3 locally, 8.4 in CI and on the VPS
- PostgreSQL 16 only (row locks, partial unique indexes, jsonb). Never SQLite, not even in tests
- Redis for queue and cache; supervisor-managed workers in production
- `tools/referensi-scan/`: the Playwright scanner that produced the spec. Node 22. Not part
  of the application; Laravel never loads it

## Language

Bahasa Indonesia is the UI language and the language of `docs/spec/`, because that is what
the business and REFERENSI speak. Code identifiers are English. Domain terms stay Indonesian
where staff say them that way: `faktur`, `surat jalan`, `gudang`, `cabang`, `giro`,
`pelanggan`, `pemasok`. REFERENSI's own field names (`transDate`, `typeAutoNumber`,
`paymentTerm`, recorded in the spec as *Nama di REFERENSI*) are the preferred English
identifiers for the same concepts, so a later import from or export to REFERENSI maps
one to one.

## Invariants

Kept from WebTransaction because they are right for any ERP, and reconciled with how
REFERENSI behaves. If a change appears to require breaking one, stop and ask.

1. **Postings are derived from documents, and only the posting layer writes them.** A
   document (faktur, penerimaan, pengiriman, penyesuaian, jurnal umum…) is what a person
   edits. Its postings (`journal_entries`/`journal_lines`, `stock_movements`,
   `payment_allocations`) are regenerated from it by one posting service, keyed by a stable
   `posting_key`. Never touch a ledger table from a controller, form, report, seeder or raw
   SQL. Cached columns (stock on hand, average cost, settled amounts) must always be
   reconstructible from the ledgers.
2. **Documents may be edited and deleted, as in REFERENSI**, subject to hak akses, the
   closed-period lock on both the old and the new date, and blockers (reconciled, settled,
   referenced by a later document). Every edit or delete is recorded in `audit_logs` and
   `document_revisions` (before and after), which are strictly append-only.
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

REFERENSI's model: permission groups hold rights per menu (lihat / tambah / ubah / hapus /
cetak) plus special rights; a user belongs to a group, carries per-user grants or
revocations, and is limited to the cabang and gudang assigned to them. WebTransaction's
two-key rules (filer ≠ verifier, counter ≠ approver, whoever is paid on a sale never
approves its credit) are kept behind the `PemisahanTugas` switch, default on; turning one
off is itself audited.

## The scanner and the spec

- `tools/referensi-scan/README.md` says how the scan is run and what it refuses to do. It is
  read-only by construction (network guard, click rules, sanitiser) and it never commits
  `.state/` (session, request log, screenshots of live records).
- `docs/referensi/` is what the scan wrote: `scan.json`, `menu.md`, `modul/*.md`,
  `laporan.md`, `preferensi.md`. Rendered by `render.mjs`.
- `docs/spec/` is generated by `spec.mjs` from `scan.json` and `docs/spec/_catatan.json`
  (the hand-kept notes: behaviours to replicate, switches, open questions). **Edit the
  notes, never the generated pages**, then regenerate: `npm run render && npm run spec`.
- Before committing anything under `docs/referensi/` or `docs/spec/`, run the privacy
  greps in the scanner README: no customer, supplier, employee name, amount or document
  number may enter the repository.

## Conventions

- Migrations are additive. Never edit a shipped migration.
- Every money-affecting action writes to the audit log.
- Queue jobs are idempotent; assume they run twice.
- Tests required for: posting (journal balance, stock, allocations), tax per line, HPP
  recalculation, period lock and blockers, hak akses. These are where bugs cost money.
- A new switch defaults to today's behaviour, so the suite stays green; tests of the
  REFERENSI behaviour turn the switch.
- CI: `.github/workflows/laravel.yml` (PHP 8.4, PostgreSQL 16) and
  `.github/workflows/scanner.yml` (`npm test` for the scanner). The cloud session hook
  `.claude/hooks/session-start.sh` brings up Postgres, Redis, Composer, the scanner's
  dependencies and the browser's trust in the session proxy.
