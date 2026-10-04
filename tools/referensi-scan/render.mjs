#!/usr/bin/env node
/**
 * docs/referensi/scan.json → readable pages under docs/referensi/:
 *   modul/<modul>.md   every screen in a module: list columns, the "new" form
 *                      (fields, line grid, tabs)
 *   laporan.md         every report and its parameters
 *   preferensi.md      every preference tab, with the switches as set
 *   menu.md            the menu tree, one line per entry, with its hash route
 *
 * Rendering only — the scan already sanitised everything it wrote.
 * The table helpers are exported for spec.mjs, which writes the functional
 * spec from the same file.
 */
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
const docs = path.resolve(here, '../../docs/referensi');

export const slug = (s) =>
    String(s).toLowerCase().normalize('NFKD').replace(/[^\w\s-]/g, '').trim().replace(/[\s_]+/g, '-') || 'tanpa-nama';

export function esc(s) {
    return String(s).replace(/\|/g, '\\|');
}

/** A field list as a Markdown table; preference screens show the state instead of required/options. */
export function fieldsTable(fields, withState = false) {
    if (!fields || fields.length === 0) return ['_(tidak ada isian)_'];
    const head = withState ? '| Isian | Jenis | Nilai |' : '| Isian | Jenis | Wajib | Pilihan |';
    const rule = withState ? '|---|---|---|' : '|---|---|---|---|';
    const rows = fields.map((f) => {
        const label = (f.section ? `${f.section} › ` : '') + f.label;
        if (withState) {
            const state = typeof f.checked === 'boolean' ? (f.checked ? '✔ aktif' : '✘ mati') : (f.value ?? '');
            return `| ${esc(label)} | ${f.type} | ${state} |`;
        }
        const options = f.options ? f.options.join(', ') : f.optionCount !== undefined ? `(${f.optionCount} data)` : '';
        return `| ${esc(label)} | ${f.type} | ${f.required ? 'ya' : ''} | ${esc(options)} |`;
    });
    return [head, rule, ...rows];
}

/** One screen (list view and, when read, its empty new-record form) as Markdown lines. */
export function screenSection(item, entry, level = '##') {
    const page = [`${level} ${item}`, ''];
    if (entry.hash) page.push(`Rute: \`${entry.hash}\``, '');
    const view = entry.view ?? {};
    if (view.headings?.length) page.push(`**Judul layar:** ${view.headings.join(' · ')}`, '');
    if (view.columns?.length) page.push(`**Kolom daftar:** ${view.columns.join(' · ')}`, '');
    if (view.filters?.length) page.push(`**Saringan:** ${view.filters.join(' · ')}`, '');
    if (view.buttons?.length) page.push(`**Tombol:** ${view.buttons.join(' · ')}`, '');
    if (view.catalog?.length) page.push(`**Daftar:** ${view.catalog.join(' · ')}`, '');
    if (view.fields?.length) page.push(`${level}# Isian layar`, '', ...fieldsTable(view.fields), '');
    for (const [tab, tv] of Object.entries(view.tabsRead ?? {})) {
        page.push(`${level}# Tab daftar: ${tab}`, '');
        if (tv) {
            if (tv.columns?.length) page.push(`**Kolom:** ${tv.columns.join(' · ')}`, '');
            if (tv.fields?.length) page.push(...fieldsTable(tv.fields), '');
            if (tv.catalog?.length) page.push(`**Daftar:** ${tv.catalog.join(' · ')}`, '');
        }
        page.push('');
    }
    if (entry.form) {
        page.push(`${level}# Formulir baru`, '');
        if (entry.form.headings?.length) page.push(`**Judul:** ${entry.form.headings.join(' · ')}`, '');
        page.push(...fieldsTable(entry.form.fields), '');
        for (const grid of entry.form.grids ?? []) page.push(`**Kolom rincian:** ${grid.join(' · ')}`, '');
        for (const [tab, tv] of Object.entries(entry.form.tabsRead ?? {})) {
            page.push(`${level}## Tab: ${tab}`, '');
            if (tv) {
                page.push(...fieldsTable(tv.fields));
                for (const grid of tv.grids ?? []) page.push('', `**Kolom rincian:** ${grid.join(' · ')}`);
            }
            page.push('');
        }
        if (entry.form.buttons?.length) page.push(`**Tombol formulir:** ${entry.form.buttons.join(' · ')}`, '');
    }
    return page;
}

export async function render(scanFile, outDir) {
    const scan = JSON.parse(await fs.readFile(scanFile, 'utf8'));
    await fs.mkdir(path.join(outDir, 'modul'), { recursive: true });

    const menu = ['# Menu REFERENSI', '', `Dipindai ${scan.scannedAt ?? '—'}. Setiap entri menu adalah rute hash di aplikasi REFERENSI Online.`, ''];
    const reports = ['# Laporan REFERENSI', ''];
    const prefs = ['# Preferensi REFERENSI', '', 'Saklar dan angka seperti tersetel di database yang dipindai.', ''];

    for (const [moduleLabel, mod] of Object.entries(scan.modules ?? {})) {
        menu.push(`- **${moduleLabel}**${mod.key ? ` (\`${mod.key}\`)` : ''}`);
        const page = [`# ${moduleLabel}`, ''];

        for (const [item, entry] of Object.entries(mod.items ?? {})) {
            menu.push(`  - ${item}${entry.hash ? ` — \`${entry.hash}\`` : ''}${entry.error ? ' _(gagal dibaca)_' : ''}`);
            if (entry.error) {
                page.push(`## ${item}`, '', `_Gagal dibaca: ${entry.error}_`, '');
                continue;
            }

            if (entry.kind === 'report') {
                reports.push(...screenSection(item, entry));
                continue;
            }
            if (entry.kind === 'preferences') {
                prefs.push(`## ${item}`, '');
                for (const [tab, view] of Object.entries(entry.view.tabsRead ?? {})) {
                    prefs.push(`### ${tab}`, '', ...fieldsTable(view?.fields ?? [], true), '');
                }
                if (Object.keys(entry.view.tabsRead ?? {}).length === 0) prefs.push(...fieldsTable(entry.view.fields, true), '');
                continue;
            }

            page.push(...screenSection(item, entry));
        }
        await fs.writeFile(path.join(outDir, 'modul', `${slug(moduleLabel)}.md`), page.join('\n') + '\n');
    }

    if (scan.blockedRequests?.length) {
        menu.push('', '## Permintaan yang ditolak pemindai', '', 'Bukti bahwa tidak ada yang tersimpan ke REFERENSI selama pemindaian:', '');
        for (const b of scan.blockedRequests) menu.push(`- \`${b}\``);
    }

    await fs.writeFile(path.join(outDir, 'menu.md'), menu.join('\n') + '\n');
    await fs.writeFile(path.join(outDir, 'laporan.md'), reports.join('\n') + '\n');
    await fs.writeFile(path.join(outDir, 'preferensi.md'), prefs.join('\n') + '\n');
    return Object.keys(scan.modules ?? {}).length;
}

if (process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
    const scanFile = process.argv[2] ? path.resolve(process.argv[2]) : path.join(docs, 'scan.json');
    const outDir = process.argv[3] ? path.resolve(process.argv[3]) : docs;
    const n = await render(scanFile, outDir);
    console.log(`[referensi-scan] rendered ${n} modules into ${outDir}`);
}
