# accurate-scan

A read-only scan of the business's ACCURATE Online database. It writes down what
ACCURATE *is* — every menu entry and its route, every list's columns and filters,
every new-record form's fields (with ACCURATE's own field names), line grids, tabs,
buttons, report and preference switch — so that `docs/spec/` can say, screen by
screen, what August's ERP must do to match it.

**Not part of the application.** Laravel never loads it and the Laravel CI does not
run it; `.github/workflows/scanner.yml` runs its own tests.

## What it will and will not do

It runs against the live database, so it is built to be unable to change it:

| Layer | Rule |
|---|---|
| Network (`src/guard.mjs`) | Every request from every tab is classified before it leaves. Only plain reads go: GETs, asset files, and POSTs whose action says *list / detail / load / search…* or belongs to the login and database-opening exchange. Anything whose action says *save, delete, approve, process, close, void, import, print, export…* is refused, whatever its verb. WebSockets are not connected. Service workers are blocked. Third parties may serve files but receive nothing |
| Exact allowances (`selectors.json`) | `allowPost`: exact paths of reads whose name says nothing (`init.do`, `dashboard.do`, the database-upgrade progress poll, the news feed). The loader of each screen the menu names (`/accurate/<area>/<screen>.do` and `init-<screen>.do`) is allowed once the menu has been read: its name is the screen's, and screens are called *transfer* or *send* without meaning it; a save is `<screen>/save.do`, still refused. `stubPost`: two calls that are refused but answered locally with an empty success (the browser's clock offset, the client search index), because the app retries each failure every few seconds and raises a modal each time |
| Clicks | Only module buttons, submenu links (by their exact `href`), tabs, "Daftar", "+ / Tambah / Baru", Batal/Tutup, and the window tab's own close. Never Simpan, Hapus, Proses, Setujui, Tutup Buku, Impor, Cetak, Keluar. The one "OK" it presses is on ACCURATE's "request failed" modal, matched by its title |
| Disk (`src/sanitize.mjs`) | Labels and structure only. Anything that looks like a record — a name with PT/CV/Toko, an amount, a date, an email, a phone or document number — is dropped. A dropdown of customers or items is written as its *count*. Switch states and short numbers are kept on Preferensi screens only, because how the business configured ACCURATE is exactly what the replica needs |
| Challenges | A visible captcha or a verification code stops the run (exit code 3). In `--headful` mode on your own machine it waits for **you** to answer it; it never answers one itself. The invisible reCAPTCHA on the login form asks nobody anything and is not a challenge |

Every refused request is logged (method, host, path — no ids, no query string)
and listed at the bottom of `docs/accurate/menu.md` as evidence.

`test/smoke.test.mjs` proves the rules in a real browser against a fake
ACCURATE (`test/fixtures/`) built like the real one — icon sidebar, submenu
links to hash routes, screen loaders named after the screen — that autosaves a
draft when a form opens, holds a socket, and shows customer names and amounts:
nothing reaches the fake server but reads, and none of the records reach the
output.

## Credentials

From the environment only — never on the command line, never in chat:

| Variable | |
|---|---|
| `ACCURATE_EMAIL` | The login |
| `ACCURATE_PASSWORD` | Its password |
| `ACCURATE_DATABASE` | Part of the database name: the account holds several *Data Usaha*, and the business's own is `JAVAINDO` (CV JAVAINDO 35) |

In a Claude Code cloud session the first two are set in the environment's settings
(environment menu in the session title bar → Edit), and the environment's network
access must allow `accurate.id` and `*.accurate.id`. The session's TLS-intercepting
proxy must also be trusted by the browser's NSS store; `.claude/hooks/session-start.sh`
imports its CA. The session cookies are kept in `.state/session.json` (never
committed) and reused, so recon, the scan and its resumptions log in once between
them: every fresh login is another pass through ACCURATE's captcha scoring.

## Running it

```bash
cd tools/accurate-scan
npm ci                  # playwright only; the browser comes from /opt/pw-browsers in a cloud session
npm test                # guard + sanitiser unit tests, and the offline smoke test

ACCURATE_DATABASE=JAVAINDO npm run recon     # log in, open the database, record the menu tree and the markup
```

Read `.state/recon.json` (never committed): the `menu` stage is every module with
its entries and hash routes, what the full scan will walk; `.state/requests.json`
shows whether any read ACCURATE needs was refused. Tune `selectors.json` if ACCURATE
changed: `app` (the sidebar, loading mask, list toggle, window close), `menu`
(module buttons, entry links), `allowPost` (**exact paths**, never a save).

```bash
ACCURATE_DATABASE=JAVAINDO npm run scan -- --screenshots   # every entry → docs/accurate/scan.json (resumable)
npm run render          # → docs/accurate/{menu,laporan,preferensi}.md, modul/*.md
npm run spec            # + docs/spec/_catatan.json → docs/spec/*.md
```

Options: `--only=sales` (one module, by key or label), `--max-items=50`,
`--delay=800` (ms between actions), `--headful`, `--screenshots` (one PNG per
screen, form and tab under `.state/screenshots/`, never committed),
`--state=<dir>`, `--out=<file>`.

A rerun resumes: entries already in `scan.json` are skipped. An entry that failed
is stored with its error and also skipped, so to retry it, drop it first:

```bash
jq 'del(.modules[].items[] | select(.error))' ../../docs/accurate/scan.json > /tmp/s.json && mv /tmp/s.json ../../docs/accurate/scan.json
```

**Before committing**, read the diff of `docs/accurate/` and `docs/spec/` and run
the privacy greps. The sanitiser is conservative, but you are the last check that
no customer, supplier, employee or amount went in:

```bash
./privacy-check.sh          # business names, amounts, long numbers, dates, emails, the login — must print "clean"
```

CI runs the same script on every push that touches the scanner.

### On your own computer instead

When the cloud session cannot reach ACCURATE, or ACCURATE asks for a verification
code:

```bash
cd tools/accurate-scan
npm ci && npx playwright install chromium
export ACCURATE_EMAIL=... ACCURATE_PASSWORD=... ACCURATE_DATABASE=JAVAINDO   # in the shell, not in a file you commit
node scan.mjs --mode=full --headful --screenshots
node render.mjs && node spec.mjs
```

Then commit `docs/accurate/` and `docs/spec/`, after the greps above.

## How ACCURATE Online is built (what the selectors encode)

Learned in recon, 2026-10-03, and the reason the crawler looks the way it does:

- `account.accurate.id` holds the login (`#account`, `#password`, invisible reCAPTCHA)
  and the list of databases (`.card-db`). Opening one is a SAML single-sign-on form
  POST to the app host (`morpheus.accurate.id` for this database), which first
  upgrades the database ("Menunggu..", polling `db-update-progress.do`), then loads
  a single-page app (`init.do`, `dashboard.do`).
- The sidebar is ten icon buttons (`a.main-menu-button`, key in the icon's class
  `icn-menu-<key>`): setting, company, general-ledger, cash-bank, sales, purchase,
  inventory, asset, smartlink-tax, report. Each opens a submenu whose entries are
  links to hash routes `#accurate__<area>__<screen>`; 96 of them in this edition.
- Every entry opens in its own window under a tab bar (`ul.module-switcher`, close
  with `a.aol-main-tab-close`). A transaction entry opens as its empty new form
  ("Data Baru": header fields, detail tabs on the left, a SlickGrid of lines); the
  `Daftar` button (`button.module-list-button`) shows the list of records with
  its filters and `Tambah Kriteria`. Master-data entries open as the list.
- A refused request raises the modal "Terjadi Permasalahan pada Pemrosesan"; a
  loading mask (`.busy-load-container`) swallows clicks while a screen loads.
