#!/usr/bin/env node
/**
 * Read-only structural scan of ACCURATE Online.
 *
 *   node scan.mjs --mode=recon   log in, open the database, record the menu
 *                                tree (every module, every entry and its hash
 *                                route) and what the app's markup and requests
 *                                look like. Writes only to .state/
 *   node scan.mjs --mode=full    visit every menu entry, its list, its empty
 *                                "new" form and each tab; write
 *                                docs/referensi/scan.json (resumable)
 *
 * Credentials come from ACCURATE_EMAIL / ACCURATE_PASSWORD and are never
 * printed. ACCURATE_DATABASE picks the database when the account has several.
 *
 * --screenshots writes one PNG per screen, form and tab under
 * .state/screenshots/ (or --state=<dir>). They show live records and are
 * never committed; they are the visual reference for the UI that replaces
 * ACCURATE's.
 *
 * Safety, in the order it holds:
 *   1. src/guard.mjs refuses every request that is not plainly a read, for
 *      every page in the context, and WebSockets are not connected at all.
 *      A refused call the app would otherwise retry forever (selectors
 *      stubPost) is answered locally with an empty success; it never leaves.
 *   2. The crawler clicks only menu entries, tabs, "new" buttons and
 *      close/cancel buttons — mayClick() refuses Simpan, Hapus, Proses…
 *      The one "OK" it presses is on ACCURATE's "request failed" modal.
 *   3. sanitize.mjs keeps labels and drops anything that looks like a record.
 * A captcha or OTP stops the run; it is never worked around.
 */
import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { classifyRequest, describeRequest, mayClick, NEW_RECORD_BUTTON, CLOSE_BUTTON } from './src/guard.mjs';
import { extractStructure, listClickables } from './src/extract.mjs';
import { cleanText, sanitizePage } from './src/sanitize.mjs';

const here = path.dirname(fileURLToPath(import.meta.url));
const repoRoot = path.resolve(here, '../..');

const args = Object.fromEntries(
    process.argv.slice(2).map((a) => {
        const [k, v] = a.replace(/^--/, '').split('=');
        return [k, v ?? true];
    }),
);

const mode = args.mode ?? 'recon';
const delay = Number(args.delay ?? 800);
const maxItems = Number(args['max-items'] ?? 500);
const only = args.only ? new RegExp(String(args.only), 'i') : null;
const stateDir = path.resolve(args.state ?? process.env.ACCURATE_SCAN_STATE ?? path.join(here, '.state'));
// Screenshots show live records, so they live under the state dir, which is
// never committed; one PNG per screen, named after the menu path.
const shotDir = path.join(stateDir, 'screenshots');
const outFile = path.resolve(args.out ?? path.join(repoRoot, 'docs/referensi/scan.json'));
const selectors = JSON.parse(await fs.readFile(path.resolve(args.selectors ?? path.join(here, 'selectors.json')), 'utf8'));

const email = process.env.ACCURATE_EMAIL;
const password = process.env.ACCURATE_PASSWORD;
if (!email || !password) {
    console.error('Set ACCURATE_EMAIL and ACCURATE_PASSWORD in the environment (never on the command line).');
    process.exit(2);
}

await fs.mkdir(stateDir, { recursive: true });
if (args.screenshots) await fs.mkdir(shotDir, { recursive: true });

const requestLog = new Map(); // described request → {allow, reason, count}
const blocked = [];
const pause = (ms = delay) => new Promise((r) => setTimeout(r, ms));

function log(msg) {
    console.log(`[accurate-scan] ${msg}`);
}

const executablePath = process.env.ACCURATE_SCAN_CHROMIUM ?? (await exists('/opt/pw-browsers/chromium') ? '/opt/pw-browsers/chromium' : undefined);
const browser = await chromium.launch({ headless: !args.headful, executablePath });
// The session (cookies) from the last run is reused, so that recon, a scan
// and its resumptions log in once between them rather than once each: every
// fresh login is another pass through ACCURATE's captcha scoring.
const sessionFile = path.join(stateDir, 'session.json');
const context = await browser.newContext({
    serviceWorkers: 'block', // a service worker's fetches would bypass the route below
    locale: 'id-ID',
    viewport: { width: 1440, height: 900 },
    storageState: (await exists(sessionFile)) ? sessionFile : undefined,
});

// ACCURATE answers a request the guard refused with a modal, "Terjadi
// Permasalahan pada Pemrosesan … ERROR CODE:0", whose buttons are Salin and
// OK, and whose backdrop swallows every click until OK is pressed. The page
// presses that OK itself, and nothing else: the dialog is matched by its
// title, the button by its exact text, inside that dialog only.
await context.addInitScript(() => {
    window.__accurateScanDismissed = 0;
    setInterval(() => {
        for (const el of document.querySelectorAll('.window, [role=dialog], .dialog')) {
            if (!/Terjadi Permasalahan pada Pemrosesan/.test(el.innerText || '')) continue;
            const ok = [...el.querySelectorAll('button')].find((b) => /^\s*OK\s*$/i.test(b.innerText || ''));
            if (ok) {
                ok.click();
                window.__accurateScanDismissed++;
            }
        }
    }, 400);
});

// Offline smoke test only (test/smoke.test.mjs): serve *.accurate.id from
// files. Registered before the guard, so the guard — which Playwright runs
// first, as the later route — still decides; this only answers what the
// guard let through, and records it so the test can prove no save arrived.
const fixtureHits = [];
if (process.env.ACCURATE_SCAN_FIXTURES) {
    const dir = path.resolve(process.env.ACCURATE_SCAN_FIXTURES);
    await context.route('**/*', async (route) => {
        const req = route.request();
        const u = new URL(req.url());
        fixtureHits.push(`${req.method()} ${u.hostname}${u.pathname}`);
        const base = path.join(dir, u.hostname, u.pathname === '/' ? 'index.html' : u.pathname);
        for (const file of [base, `${base}.html`, `${base}.json`]) {
            if (await exists(file) && !(await fs.stat(file)).isDirectory()) {
                const type = file.endsWith('.html') ? 'text/html' : file.endsWith('.js') ? 'text/javascript' : 'application/json';
                return route.fulfill({ status: 200, contentType: type, body: await fs.readFile(file) });
            }
        }
        return route.fulfill({ status: 404, contentType: 'application/json', body: '{}' });
    });
}

const stubPost = selectors.stubPost ?? [];
// The loader of each screen the menu names: a menu entry #accurate__<area>__<screen>
// opens by POSTing /accurate/<area>/<screen>.do, which returns the screen's
// markup. Its name is the screen's, and screens are named transfer, send or
// edited without meaning it, so these exact paths are allowed once the menu
// has been read. A save is <screen>/save.do, a different path, still refused.
const screenLoaders = new Set();
await context.route('**/*', async (route) => {
    const req = route.request();
    const verdict = classifyRequest(
        { method: req.method(), url: req.url(), resourceType: req.resourceType() },
        { allowPost: [...(selectors.allowPost ?? []), ...screenLoaders] },
    );
    const key = describeRequest({ method: req.method(), url: req.url() });
    const stubbed = !verdict.allow && req.method() === 'POST' && stubPost.includes(new URL(req.url()).pathname);
    const seen = requestLog.get(key) ?? { allow: verdict.allow, reason: stubbed ? `${verdict.reason}, stubbed` : verdict.reason, count: 0 };
    seen.count++;
    requestLog.set(key, seen);
    if (verdict.allow) return route.fallback();
    if (seen.count === 1) {
        blocked.push(`${key} (${verdict.reason}${stubbed ? ', answered locally' : ''})`);
        log(`blocked ${key} — ${verdict.reason}${stubbed ? ' (answered locally)' : ''}`);
    }
    if (stubbed) {
        // An empty success, so the app stops retrying and raising its modal.
        return route.fulfill({ status: 200, contentType: 'application/json', body: '{"s":true,"d":null}' });
    }
    return route.abort('blockedbyclient');
});

// A socket can carry a save as easily as a POST can, and route() cannot see
// inside it. Not connecting it is the only read-only answer; recon will show
// whether ACCURATE needs one to display anything.
await context.routeWebSocket(/.*/, (ws) => {
    blocked.push(`WS ${describeRequest({ method: 'GET', url: ws.url() })}`);
    ws.close();
});

context.on('page', (p) => p.on('dialog', (d) => d.dismiss().catch(() => {})));

// Page loads and their status, so that a proxy or server error page is
// told apart from a page the crawler failed to read. Paths only.
context.on('response', (res) => {
    if (res.request().resourceType() !== 'document') return;
    const key = describeRequest({ method: res.request().method(), url: res.url() });
    log(`document ${res.status()} ${key}`);
});

let page = await context.newPage();
page.on('dialog', (d) => d.dismiss().catch(() => {}));

try {
    await login();
    page = await openDatabase();
    await waitForApp();
    await context.storageState({ path: sessionFile }).catch(() => {});

    if (mode === 'recon') {
        await recon();
    } else {
        await fullScan();
    }
} catch (e) {
    log(`stopped: ${e.message}`);
    process.exitCode = process.exitCode || 1;
} finally {
    if (process.env.ACCURATE_SCAN_FIXTURES) {
        await fs.writeFile(path.join(stateDir, 'fixture-hits.json'), JSON.stringify(fixtureHits, null, 2)).catch(() => {});
    }
    await writeRequestLog();
    await browser.close();
}

// ---------------------------------------------------------------------------

async function login() {
    log(`opening ${selectors.loginUrl}`);
    await page.goto(selectors.loginUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});

    const emailBox = await first(page, selectors.login.email);
    const passBox = await first(page, selectors.login.password);
    if (!emailBox && !passBox && (await first(page, selectors.database.open))) {
        log('session reused; already logged in');
        return;
    }
    if (!emailBox || !passBox) throw new Error('login form not found — run recon with --headful and tune selectors.login');

    await emailBox.fill(email);
    await passBox.fill(password);

    if (await challengeShowing()) {
        // A captcha on the form itself is answered together with it — by the
        // person at the window, who then presses Masuk.
        if (!args.headful) {
            process.exitCode = 3;
            throw new Error('login page asks for a captcha. Run on your own machine with --headful and answer it by hand.');
        }
        log('login page shows a captcha — answer it and press Masuk in the browser window (3 minutes)');
        await page.locator('input[type=password]').filter({ visible: true }).first().waitFor({ state: 'hidden', timeout: 180000 });
    } else {
        const submit = await first(page, selectors.login.submit);
        if (submit) await submit.click();
        else await passBox.press('Enter');
    }

    // The form posts, the captcha scores, and the redirect to the database
    // list follows a few seconds later: wait for the password box to go, or
    // for a challenge to appear, rather than look once.
    const passwordBox = page.locator('input[type=password]').filter({ visible: true }).first();
    const until = Date.now() + 30000;
    while (Date.now() < until && (await passwordBox.count()) > 0 && !(await challengeShowing())) {
        await pause(1000);
    }
    await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
    await pause(1500);
    await assertNoChallenge('after login');

    if ((await page.locator('input[type=password]').filter({ visible: true }).count()) > 0) {
        throw new Error('still on the login form — wrong credentials, or the login POST was blocked (see blocked list; add its exact path to allowPost)');
    }
    log('logged in');
    await context.storageState({ path: sessionFile }).catch(() => {});
}

async function openDatabase() {
    // The list of databases renders after the login redirect, a moment after
    // the network goes quiet; wait for it — or for the app itself, when the
    // account has one database and goes straight in — before deciding.
    await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
    const either = [...selectors.database.open, selectors.app?.ready].filter(Boolean).join(', ');
    await page.locator(either).first().waitFor({ state: 'visible', timeout: 15000 }).catch(() => {});
    if (selectors.app?.ready && (await page.locator(selectors.app.ready).filter({ visible: true }).count()) > 0) {
        log('the app is already open; no database list to pick from');
        return page;
    }

    const wanted = process.env.ACCURATE_DATABASE;
    let target = null;
    if (wanted) {
        target = page.getByText(wanted, { exact: false }).filter({ visible: true }).first();
        if ((await target.count()) === 0) throw new Error('ACCURATE_DATABASE not found on the database list');
    } else {
        const candidates = [];
        for (const s of selectors.database.open) {
            const loc = page.locator(s).filter({ visible: true });
            const n = await loc.count();
            if (n > 0) candidates.push({ loc, n });
        }
        if (candidates.length === 0) {
            log('no database list seen; assuming the app is already open');
            return page;
        }
        if (candidates[0].n > 1) {
            throw new Error(`${candidates[0].n} databases on this account: set ACCURATE_DATABASE to part of the one to scan`);
        }
        target = candidates[0].loc.first();
    }

    const popup = context.waitForEvent('page', { timeout: 15000 }).catch(() => null);
    await target.click();
    const opened = await popup;
    const appPage = opened ?? page;
    appPage.on('dialog', (d) => d.dismiss().catch(() => {}));
    await appPage.waitForLoadState('domcontentloaded').catch(() => {});
    log('database opened');
    return appPage;
}

/**
 * After the SSO handoff the app host shows "Menunggu.." while ACCURATE
 * upgrades the database it just opened, then loads a large single-page
 * app. Wait for the sidebar's module buttons to exist, not merely for the
 * network to go quiet.
 */
async function waitForApp() {
    const ready = selectors.app?.ready;
    const readyCount = selectors.app?.readyCount ?? 1;
    const until = Date.now() + 120000;
    while (Date.now() < until) {
        await page.waitForLoadState('networkidle', { timeout: 10000 }).catch(() => {});
        await dismissErrorDialog();
        if (ready) {
            if ((await page.locator(ready).filter({ visible: true }).count()) >= readyCount) {
                await pause(3000);
                return;
            }
        } else {
            const body = await page.locator('body').innerText().catch(() => '');
            const n = (await page.evaluate(listClickables).catch(() => [])).length;
            if (!/^\s*menunggu/i.test(body) && n >= 10) {
                await pause(2000);
                return;
            }
        }
        await pause(2000);
    }
    log('the app did not finish loading within two minutes; continuing with what is there');
}

/**
 * Close an informational dialog: a window whose only buttons are OK, Tutup,
 * Close, Salin, Tidak or Batal. One that offers Simpan, Hapus, Proses or Ya
 * is a decision and is left alone (the crawler then fails the entry rather
 * than answer it). The in-page sweeper handles the "request failed" modal;
 * this covers the others the app raises.
 */
async function dismissErrorDialog() {
    const windows = page.locator('.window, [role=dialog], .dialog').filter({ visible: true });
    const n = await windows.count();
    let closed = false;
    for (let i = n - 1; i >= 0; i--) {
        const w = windows.nth(i);
        const buttons = w.locator('button, a.button').filter({ visible: true });
        const labels = [];
        for (let j = 0; j < Math.min(await buttons.count(), 12); j++) labels.push(((await buttons.nth(j).innerText().catch(() => '')) || '').trim());
        if (labels.length === 0 || labels.some((l) => !/^(ok|tutup|close|salin|tidak|batal|×|x)$/i.test(l))) continue;
        const idx = labels.findIndex((l) => /^(ok|tutup|close|tidak|batal)$/i.test(l));
        if (idx < 0) continue;
        await buttons.nth(idx).click({ timeout: 2000 }).catch(() => {});
        closed = true;
        await pause(500);
    }
    return closed;
}

// --- the menu ---------------------------------------------------------------

/**
 * Every module button on the sidebar, with the entries its submenu holds.
 * Each entry is a link to a hash route (#accurate__<area>__<screen>), which
 * is how the crawler opens it later: by its exact href, never by text.
 */
async function listModules() {
    const buttons = page.locator(selectors.menu.moduleButtons).filter({ visible: true });
    const n = await buttons.count();
    if (n === 0) throw new Error(`no module buttons match selectors.menu.moduleButtons (${selectors.menu.moduleButtons})`);
    const keyRe = new RegExp(selectors.menu.moduleKey);
    const modules = [];

    for (let i = 0; i < n; i++) {
        const cls = (await buttons.nth(i).locator('i, svg, span').first().getAttribute('class').catch(() => null)) ?? '';
        const key = (cls.match(keyRe) ?? [])[1] ?? `menu-${i}`;
        await dismissErrorDialog();
        await buttons.nth(i).click({ timeout: 5000 });
        await pause(Math.max(delay, 1000));

        const { heading, entries } = await page.evaluate((sel) => {
            const visible = (el) => {
                const r = el.getBoundingClientRect();
                return r.width > 0 && r.height > 0;
            };
            const norm = (s) => String(s ?? '').replace(/\s+/g, ' ').trim();
            const h = [...document.querySelectorAll('h1, h2, h3, h4')].filter(visible).find((el) => {
                const r = el.getBoundingClientRect();
                return r.x < 400 && r.y < 240 && norm(el.innerText);
            });
            return {
                heading: h ? norm(h.innerText) : '',
                entries: [...document.querySelectorAll(sel)].filter(visible).map((a) => ({ hash: a.getAttribute('href'), label: norm(a.innerText) })),
            };
        }, selectors.menu.entryLinks);

        await page.keyboard.press('Escape').catch(() => {});
        await pause(500);

        const label = cleanText(heading) || selectors.menu.labels?.[key] || key;
        const seen = new Set();
        const list = [];
        for (const e of entries) {
            if (!e.hash || seen.has(e.hash)) continue;
            seen.add(e.hash);
            list.push({ hash: e.hash, label: cleanText(e.label) ?? e.hash.replace(/^#accurate__/, '') });
            for (const loader of loaderPaths(e.hash)) screenLoaders.add(loader);
        }
        modules.push({ index: i, key, label, entries: list });
    }
    return modules;
}

/**
 * "#accurate__customer__sales-invoice" → the two calls that open that screen,
 * "/accurate/customer/sales-invoice.do" (its markup) and
 * "/accurate/customer/init-sales-invoice.do" (its initial state); nothing for
 * a hash of another shape.
 */
function loaderPaths(hash) {
    const m = /^#accurate__([\w-]+)__([\w-]+)$/.exec(hash ?? '');
    return m ? [`/accurate/${m[1]}/${m[2]}.do`, `/accurate/${m[1]}/init-${m[2]}.do`] : [];
}

/** Open one menu entry: the module button, then the entry's link by href. */
async function openEntry(module, entry) {
    await dismissErrorDialog();
    // The app reloads itself after some failures; when the sidebar is gone,
    // wait for it to come back before clicking.
    if (selectors.app?.ready && (await page.locator(selectors.app.ready).filter({ visible: true }).count()) === 0) {
        await waitForApp();
    }
    await notBusy();
    const link = () => page.locator(`${selectors.menu.entryLinks}[href="${entry.hash}"]`).filter({ visible: true }).first();
    // A submenu left open closes again when its button is clicked: use what
    // is open when it is the right one, close it first when it is not.
    if ((await link().count()) === 0) {
        if ((await page.locator(selectors.menu.entryLinks).filter({ visible: true }).count()) > 0) {
            await page.keyboard.press('Escape').catch(() => {});
            await pause(500);
        }
        const buttons = page.locator(selectors.menu.moduleButtons).filter({ visible: true });
        await buttons.nth(module.index).click({ timeout: 10000 });
        await pause(Math.max(delay, 800));
    }
    if ((await link().count()) === 0) {
        await shot(`error--${slug(module.label)}--${slug(entry.label)}`);
        throw new Error(`entry ${entry.hash} not visible in the ${module.label} submenu`);
    }
    await link().click({ timeout: 5000 });
    await settle();
}

// --- recon -------------------------------------------------------------------

async function recon() {
    const stages = [];
    stages.push(await snapshot('app'));

    // The sidebar's markup, text or not: ACCURATE's is a column of icons
    // whose labels only show once their submenu opens. Record the elements,
    // so the selectors can be checked against them.
    const sidebarDom = await page.evaluate((maxX) => {
        const norm = (s) => String(s ?? '').replace(/\s+/g, ' ').trim();
        const out = [];
        for (const el of document.querySelectorAll('body *')) {
            const r = el.getBoundingClientRect();
            if (!(r.width > 0 && r.height > 0 && r.x < maxX && r.y > 40 && r.width < 200 && r.height < 120)) continue;
            const attrs = {};
            for (const a of el.attributes) if (/^(title|aria-label|data-[\w-]+|href|role)$/.test(a.name)) attrs[a.name] = a.value.slice(0, 60);
            out.push({ tag: el.tagName.toLowerCase(), id: el.id || '', cls: (typeof el.className === 'string' ? el.className : '').slice(0, 80), text: norm(el.innerText).slice(0, 40), x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width), h: Math.round(r.height), attrs });
            if (out.length >= 80) break;
        }
        return out;
    }, selectors.menu.sidebarMaxX);
    stages.push({ stage: 'sidebar markup', elements: sidebarDom.map((e) => ({ ...e, text: cleanText(e.text) ?? '' })) });

    // The menu tree: what the full scan will walk.
    const modules = await listModules();
    stages.push({ stage: 'menu', modules });
    log(`${modules.length} modules, ${modules.reduce((n, m) => n + m.entries.length, 0)} entries`);
    for (const m of modules) log(`  ${m.label} (${m.key}): ${m.entries.map((e) => e.label).join(' · ')}`);

    const file = path.join(stateDir, 'recon.json');
    await fs.writeFile(file, JSON.stringify({ when: new Date().toISOString(), stages, blocked }, null, 2));
    log(`recon written to ${file}`);
}

async function snapshot(stage) {
    if (args.screenshots) {
        await page.screenshot({ path: path.join(stateDir, `${stage.replace(/\W+/g, '-')}.png`), fullPage: true }).catch(() => {});
    }
    const clickables = (await page.evaluate(listClickables)).map(cleanClickable).filter(Boolean);
    return { stage, host: new URL(page.url()).hostname, title: cleanText(await page.title()), clickables };
}

function cleanClickable(c) {
    const text = cleanText(c.text);
    return text === null ? null : { ...c, text };
}

// --- the full scan -----------------------------------------------------------

async function fullScan() {
    const result = await loadExisting();
    const modules = (await listModules()).filter((m) => selectors.menu.modules.length === 0 || selectors.menu.modules.includes(m.key) || selectors.menu.modules.includes(m.label));
    result.menu = modules.map((m) => ({ key: m.key, label: m.label, entries: m.entries }));

    log(`${modules.length} modules: ${modules.map((m) => m.label).join(', ')}`);
    let visited = 0;

    for (const module of modules) {
        if (only && !only.test(module.label) && !only.test(module.key)) continue;
        log(`${module.label}: ${module.entries.length} entries`);
        const mod = (result.modules[module.label] ??= { key: module.key, items: {} });
        mod.key = module.key;

        for (const entry of module.entries) {
            if (visited++ >= maxItems) return save(result);
            if (mod.items[entry.label]) continue; // resumable: a rerun skips what is done

            try {
                await openEntry(module, entry);
                const shotBase = `${slug(module.label)}--${slug(entry.label)}`;
                const kind = module.key === 'report' || /laporan|report/i.test(module.label)
                    ? 'report'
                    : /preferensi|preference/i.test(entry.label + ' ' + entry.hash) ? 'preferences' : 'screen';
                const item = { hash: entry.hash, kind };

                const listToggle = selectors.app?.listToggle ? (await screen()).locator(selectors.app.listToggle).filter({ visible: true }).first() : null;
                if (kind === 'screen' && listToggle && (await listToggle.count()) > 0) {
                    // A transaction entry opens as its empty new form; the
                    // Daftar button shows the list of records.
                    await shot(`${shotBase}--baru`);
                    item.form = await read(false);
                    item.form.tabsRead = await readTabs(false, `${shotBase}--baru`);
                    await listToggle.click({ timeout: 3000 });
                    await settle();
                    await shot(`${shotBase}--daftar`);
                    item.view = await read(false);
                    item.view.tabsRead = {};
                } else {
                    await shot(`${shotBase}--daftar`);
                    item.view = await read(kind === 'preferences', kind === 'report');
                    item.view.tabsRead = await readTabs(kind === 'preferences', `${shotBase}--daftar`);
                    if (kind === 'screen' && (await openNewRecord())) {
                        await shot(`${shotBase}--baru`);
                        item.form = await read(false);
                        item.form.tabsRead = await readTabs(false, `${shotBase}--baru`);
                        await closeForm();
                    }
                }
                mod.items[entry.label] = item;
                log(`  ✓ ${entry.label}`);
            } catch (e) {
                mod.items[entry.label] = { hash: entry.hash, error: cleanText(e.message) ?? 'error' };
                log(`  ✗ ${entry.label}: ${e.message.split('\n')[0]}`);
                await shot(`error--${slug(module.label)}--${slug(entry.label)}--after`);
                await page.keyboard.press('Escape').catch(() => {});
                await dismissErrorDialog();
            }
            await closeScreen();
            await save(result);
        }
    }
    await save(result);
}

async function read(preferences, catalog = false) {
    return sanitizePage(await page.evaluate(extractStructure, { rootSel: selectors.app?.screenRoot ?? null, catalog }), { preferences });
}

/** The active screen's container, when the selectors name one and one is showing; else the page. */
async function screen() {
    const sel = selectors.app?.screenRoot;
    if (!sel) return page;
    const loc = page.locator(sel).filter({ visible: true });
    return (await loc.count()) > 0 ? loc.last() : page;
}

async function readTabs(preferences, shotBase = '') {
    const ignore = new Set((selectors.app?.ignoreTabs ?? []).map((t) => t.toLowerCase()));
    const tabs = (await page.evaluate(extractStructure, { rootSel: selectors.app?.screenRoot ?? null })).tabs
        .map(cleanText)
        .filter(Boolean)
        // The app's own window tabs (Dashboard, Berita) and a "Tutup" that
        // closes the window are not the screen's tabs.
        .filter((t) => !ignore.has(t.toLowerCase()) && !CLOSE_BUTTON.test(t));
    const out = {};
    for (const tab of tabs.slice(0, 15)) {
        if (!mayClick(tab)) continue;
        try {
            await clickTab(tab);
            await settle();
            if (shotBase) await shot(`${shotBase}--tab-${slug(tab)}`);
            out[tab] = await read(preferences);
        } catch {
            // A tab that will not click is recorded by name only.
            out[tab] = null;
        }
    }
    return out;
}

async function clickTab(tab) {
    const within = await screen();
    const byRole = within.getByRole('tab', { name: tab, exact: true }).filter({ visible: true }).first();
    if ((await byRole.count()) > 0) return byRole.click({ timeout: 3000 });
    // ACCURATE's detail tabs are icons whose name is a title or aria-label,
    // so match on whatever the extractor read, not on inner text only.
    const candidates = within
        .locator('[role=tab], .tab-control li a, .tabs > li > a, .nav-tabs > li > a, [data-role=tabcontrol] li a, .x-tab-inner, .frame-left-tab li a, .tab-link')
        .filter({ visible: true });
    const n = Math.min(await candidates.count(), 40);
    for (let i = 0; i < n; i++) {
        const c = candidates.nth(i);
        const name = ((await c.getAttribute('aria-label')) || (await c.innerText().catch(() => '')) || (await c.getAttribute('title')) || '').replace(/\s+/g, ' ').trim();
        if (name === tab) return c.click({ timeout: 3000 });
    }
    throw new Error(`tab "${tab}" not found`);
}

async function openNewRecord() {
    const buttons = (await screen()).locator('button, [role=button], a.button, a.btn, a').filter({ visible: true });
    const n = Math.min(await buttons.count(), 120);
    for (let i = 0; i < n; i++) {
        const b = buttons.nth(i);
        const text = ((await b.innerText().catch(() => '')) || (await b.getAttribute('title')) || (await b.getAttribute('aria-label')) || '').trim();
        if (NEW_RECORD_BUTTON.test(text) && mayClick(text)) {
            await b.click({ timeout: 3000 });
            await settle();
            return true;
        }
    }
    return false;
}

async function closeForm() {
    await page.keyboard.press('Escape').catch(() => {});
    await pause(400);
    const buttons = (await screen()).locator('button, [role=button], a.button, a.btn').filter({ visible: true });
    const n = Math.min(await buttons.count(), 120);
    for (let i = 0; i < n; i++) {
        const b = buttons.nth(i);
        const text = ((await b.innerText().catch(() => '')) || (await b.getAttribute('title')) || '').trim();
        if (CLOSE_BUTTON.test(text) && mayClick(text)) {
            await b.click({ timeout: 2000 }).catch(() => {});
            await pause(400);
            return;
        }
    }
}

/**
 * Every entry opens in its own window under the app's tab bar, and the app
 * keeps them all. Close the window just read, by its tab's own close control
 * (the last one is the active window's), so the count never grows.
 */
async function closeScreen() {
    const sel = selectors.app?.closeWindow ?? selectors.app?.closeScreen;
    if (!sel) return;
    const btn = page.locator(sel).filter({ visible: true }).last();
    if ((await btn.count()) === 0) return;
    const text = ((await btn.innerText().catch(() => '')) || (await btn.getAttribute('title')) || '').trim();
    if (text && !mayClick(text)) return;
    await btn.click({ timeout: 2000 }).catch(() => {});
    await pause(600);
    await dismissErrorDialog();
}

async function settle() {
    await page.waitForLoadState('networkidle', { timeout: 8000 }).catch(() => {});
    await notBusy();
    await pause();
    if (await dismissErrorDialog()) await pause();
}

/** ACCURATE covers the page with a loading mask that swallows clicks; wait until it is gone. */
async function notBusy() {
    const sel = selectors.app?.busy;
    if (!sel) return;
    await page.locator(sel).filter({ visible: true }).first().waitFor({ state: 'hidden', timeout: 30000 }).catch(() => {});
}

/** One PNG of the current screen under .state/screenshots/, only with --screenshots. */
async function shot(name) {
    if (!args.screenshots) return;
    await page.screenshot({ path: path.join(shotDir, `${name}.png`), fullPage: true }).catch(() => {});
}

/** A menu label as a file name: "Faktur Penjualan" → "faktur-penjualan". */
function slug(s) {
    return String(s).toLowerCase().normalize('NFKD').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-') || 'tanpa-nama';
}

async function challengeShowing() {
    const re = new RegExp(selectors.challenge, 'i');
    // ACCURATE's login form carries an *invisible* reCAPTCHA: a widget that
    // asks the person nothing unless Google decides to show a puzzle. Only a
    // captcha frame a person could see counts as a challenge — the puzzle
    // frame once it is displayed, never the hidden anchor. When one shows,
    // the run stops as before; nothing here answers it.
    const frames = await page.locator('iframe[src*="captcha"], iframe[src*="recaptcha"], iframe[src*="hcaptcha"]').all();
    let visibleCaptcha = false;
    for (const f of frames) {
        const src = (await f.getAttribute('src').catch(() => '')) ?? '';
        if (/size=invisible/.test(src)) continue;
        const box = await f.boundingBox().catch(() => null);
        if (box && box.width > 50 && box.height > 50) visibleCaptcha = true;
    }
    const body = await page.locator('body').innerText().catch(() => '');
    return visibleCaptcha || re.test(body);
}

async function assertNoChallenge(where) {
    if (!(await challengeShowing())) return;

    // A person at the keyboard may answer it; the scan never does.
    if (args.headful) {
        log(`${where}: a captcha or verification code is showing — complete it in the browser window (3 minutes)`);
        const until = Date.now() + 180000;
        while (Date.now() < until) {
            await pause(2000);
            if (!(await challengeShowing())) {
                await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
                return;
            }
        }
    }
    process.exitCode = 3;
    throw new Error(`${where}: a captcha or verification code is being asked for. The scan does not get past these — run it on your own machine with --headful and complete the step by hand.`);
}

async function first(p, list) {
    for (const s of list ?? []) {
        const loc = p.locator(s).filter({ visible: true }).first();
        if ((await loc.count()) > 0) return loc;
    }
    return null;
}

async function loadExisting() {
    try {
        const existing = JSON.parse(await fs.readFile(outFile, 'utf8'));
        if (existing && existing.modules) return existing;
    } catch {
        // first run
    }
    return { scannedAt: null, modules: {} };
}

async function save(result) {
    result.scannedAt = new Date().toISOString();
    result.blockedRequests = [...new Set(blocked)];
    await fs.mkdir(path.dirname(outFile), { recursive: true });
    await fs.writeFile(outFile, JSON.stringify(result, null, 2) + '\n');
}

async function writeRequestLog() {
    const rows = [...requestLog.entries()].map(([k, v]) => ({ request: k, ...v })).sort((a, b) => a.request.localeCompare(b.request));
    await fs.writeFile(path.join(stateDir, 'requests.json'), JSON.stringify(rows, null, 2)).catch(() => {});
    const refused = rows.filter((r) => !r.allow).length;
    log(`${rows.length} distinct requests, ${refused} refused — ${path.join(stateDir, 'requests.json')}`);
}

async function exists(p) {
    try {
        await fs.access(p);
        return true;
    } catch {
        return false;
    }
}
