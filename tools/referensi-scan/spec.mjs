#!/usr/bin/env node
/**
 * docs/referensi/scan.json + docs/spec/_catatan.json → docs/spec/<modul>.md
 *
 * The functional spec of August's ERP: one page per module of the reference system, one
 * section per screen, with what the scan read (list columns, filters, the
 * new-record form's fields and line grids, tabs, buttons) and what the notes
 * file adds by hand — the behaviours to replicate, the WebTransaction extras
 * to keep behind a switch, and the questions still open. The notes are keyed
 * "<Modul>/<Layar>" for a screen, "<Modul>" for a module, "_global" for the
 * whole product; each holds { perilaku: [], saklar: [], pertanyaan: [] }.
 *
 * Generated, not hand-written: the scan will be run again, and a hundred
 * screens copied twice would drift. Run after render.mjs.
 */
import fs from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { fieldsTable, esc, slug } from './render.mjs';

const here = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(here, '../..');
const scanFile = path.resolve(process.argv[2] ?? path.join(root, 'docs/referensi/scan.json'));
const notesFile = path.resolve(process.argv[3] ?? path.join(root, 'docs/spec/_catatan.json'));
const outDir = path.resolve(process.argv[4] ?? path.join(root, 'docs/spec'));

const scan = JSON.parse(await fs.readFile(scanFile, 'utf8'));
const notes = JSON.parse(await fs.readFile(notesFile, 'utf8').catch(() => '{}'));
await fs.mkdir(outDir, { recursive: true });

/** Study data as it may be printed: the vendor's route prefix and the vendor's own service names stay out of the generated pages. */
const neutral = (s) => String(s ?? '').replace(/^#referensi__/, '').replace(/^Referensi Store$/, 'Add-on store (vendor service)').replace(/^Referensi Capital$/, 'Financing program (vendor service)');

const used = new Set();
const noteFor = (key) => {
    if (notes[key]) used.add(key);
    return notes[key] ?? {};
};

function notesSection(n, level) {
    const out = [];
    if (n.perilaku?.length) out.push(`${level} Perilaku yang direplikasi`, '', ...n.perilaku.map((x) => `- ${x}`), '');
    if (n.saklar?.length) out.push(`${level} Saklar (perilaku WebTransaction yang dipertahankan, bisa dimatikan)`, '', ...n.saklar.map((x) => `- ${x}`), '');
    if (n.pertanyaan?.length) out.push(`${level} Pertanyaan terbuka`, '', ...n.pertanyaan.map((x) => `- ${x}`), '');
    return out;
}

function fieldRows(fields) {
    if (!fields?.length) return ['_(tidak ada isian terbaca)_'];
    const rows = fields.map((f) => {
        const label = (f.section ? `${f.section} › ` : '') + f.label;
        const options = f.options ? f.options.join(', ') : f.optionCount !== undefined ? `(${f.optionCount} data)` : '';
        return `| ${esc(label)} | ${f.name ? `\`${f.name}\`` : ''} | ${f.type} | ${f.required ? 'ya' : ''} | ${esc(options)} |`;
    });
    return ['| Isian | Nama di sistem referensi | Jenis | Wajib | Pilihan |', '|---|---|---|---|---|', ...rows];
}

function screenSpec(moduleLabel, item, entry) {
    const out = [`## ${neutral(item)}`, ''];
    if (entry.error) return [...out, `_Layar ini gagal dibaca oleh pemindai (${entry.error}); lengkapi dengan tangan atau pindai ulang._`, ''];
    out.push(`Rute di sistem referensi: \`${neutral(entry.hash) || '—'}\` · Jenis: ${entry.kind === 'report' ? 'laporan' : entry.kind === 'preferences' ? 'preferensi' : 'layar'}`, '');

    const view = entry.view ?? {};
    if (view.columns?.length || view.filters?.length || view.buttons?.length || view.catalog?.length) {
        out.push('### Daftar', '');
        if (view.columns?.length) out.push(`**Kolom:** ${view.columns.join(' · ')}`, '');
        if (view.filters?.length) out.push(`**Saringan:** ${view.filters.join(' · ')}`, '');
        if (view.buttons?.length) out.push(`**Tombol:** ${view.buttons.join(' · ')}`, '');
        if (view.catalog?.length) out.push(`**Isi daftar:** ${view.catalog.join(' · ')}`, '');
    }
    // The screen's own fields, unless they are the first tab's, read again below.
    if (!entry.form && view.fields?.length && Object.keys(view.tabsRead ?? {}).length === 0) {
        out.push('### Isian', '', ...(entry.kind === 'preferences' ? fieldsTable(view.fields, true) : fieldRows(view.fields)), '');
    }
    for (const [tab, tv] of Object.entries(view.tabsRead ?? {})) {
        if (!tv) continue;
        out.push(`### Tab: ${tab}`, '');
        if (tv.columns?.length) out.push(`**Kolom:** ${tv.columns.join(' · ')}`, '');
        if (tv.fields?.length) out.push(...(entry.kind === 'preferences' ? fieldsTable(tv.fields, true) : fieldRows(tv.fields)), '');
        for (const grid of tv.grids ?? []) out.push(`**Kolom rincian:** ${grid.join(' · ')}`, '');
        if (tv.catalog?.length) out.push(`**Isi daftar:** ${tv.catalog.join(' · ')}`, '');
    }

    if (entry.form) {
        out.push('### Formulir baru', '');
        if (entry.form.headings?.length) out.push(`**Judul:** ${entry.form.headings.join(' · ')}`, '');
        out.push(...fieldRows(entry.form.fields), '');
        for (const grid of entry.form.grids ?? []) out.push(`**Kolom rincian:** ${grid.join(' · ')}`, '');
        if (entry.form.buttons?.length) out.push(`**Tombol:** ${entry.form.buttons.join(' · ')}`, '');
        // Fields a tab adds beyond the header: the header repeats on every tab.
        const header = new Set((entry.form.fields ?? []).map((f) => `${f.label}|${f.name ?? ''}`));
        for (const [tab, tv] of Object.entries(entry.form.tabsRead ?? {})) {
            out.push(`#### Tab: ${tab}`, '');
            if (!tv) {
                out.push('_(tab tidak terbaca)_', '');
                continue;
            }
            const own = (tv.fields ?? []).filter((f) => !header.has(`${f.label}|${f.name ?? ''}`));
            out.push(...fieldRows(own), '');
            for (const grid of tv.grids ?? []) out.push(`**Kolom rincian:** ${grid.join(' · ')}`, '');
        }
    }

    out.push(...notesSection(noteFor(`${moduleLabel}/${neutral(item)}`), '###'));
    return out;
}

const index = ['# Spesifikasi fungsional August\'s ERP', '', `Dibangkitkan dari studi sistem referensi ${scan.scannedAt ?? '—'} (\`docs/referensi/scan.json\`) dan catatan \`docs/spec/_catatan.json\` oleh \`tools/referensi-scan/spec.mjs\`. Jangan sunting berkas modul dengan tangan; sunting catatannya, lalu bangkitkan ulang.`, ''];
index.push(...notesSection(noteFor('_global'), '##'));
index.push('## Modul', '', '| Modul | Kunci | Layar | Berkas |', '|---|---|---|---|');

for (const [moduleLabel, mod] of Object.entries(scan.modules ?? {})) {
    const items = Object.entries(mod.items ?? {});
    const file = `${slug(moduleLabel)}.md`;
    index.push(`| ${moduleLabel} | \`${mod.key ?? ''}\` | ${items.length} | [${file}](${file}) |`);

    const page = [`# ${moduleLabel}`, '', `Modul sistem referensi \`${mod.key ?? ''}\`, ${items.length} layar. Dibangkitkan; sunting \`_catatan.json\`, bukan berkas ini.`, ''];
    page.push(...notesSection(noteFor(moduleLabel), '##'));
    page.push('## Layar', '', ...items.map(([item, entry]) => `- [${neutral(item)}](#${slug(neutral(item))})${entry.error ? ' _(gagal dibaca)_' : ''}`), '');
    for (const [item, entry] of items) page.push(...screenSpec(moduleLabel, item, entry));
    await fs.writeFile(path.join(outDir, file), page.join('\n') + '\n');
}

await fs.writeFile(path.join(outDir, 'README.md'), index.join('\n') + '\n');

const unmatched = Object.keys(notes).filter((k) => !used.has(k) && !(k.startsWith('_') && k !== '_global'));
if (unmatched.length) console.warn(`[spec] ${unmatched.length} note key(s) match no scanned module or screen: ${unmatched.join(', ')}`);
console.log(`[spec] ${Object.keys(scan.modules ?? {}).length} modules written to ${outDir}`);
