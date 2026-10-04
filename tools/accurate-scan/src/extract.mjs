/**
 * Functions that run inside the ACCURATE page (via page.evaluate), so each is
 * self-contained: no imports, no closures over module scope.
 *
 * They read the DOM generically — labels, headers, buttons — scoped to the
 * active screen when the caller names its container (ACCURATE keeps every
 * opened screen in its own window, `.module-container`, under one tab bar).
 *
 * What they return is raw and stays in memory: sanitize.mjs decides what of it
 * may be written down.
 */

/**
 * The structure of whatever is in front of the user: a dialog if one is open,
 * else the last visible element matching `rootSel`, else the page.
 * `catalog` also lists the texts of links and list items (for a report
 * catalogue, where the entries are the product's own names).
 */
export function extractStructure({ rootSel = null, catalog = false } = {}) {
    const norm = (s) => String(s ?? '').replace(/\s+/g, ' ').trim();
    const visible = (el) => {
        if (!el || !(el instanceof Element)) return false;
        const r = el.getBoundingClientRect();
        const cs = getComputedStyle(el);
        return r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && cs.display !== 'none';
    };
    const textOf = (el) => norm(el?.getAttribute?.('aria-label') || el?.innerText || el?.textContent || el?.value || el?.title);

    const dialogs = [...document.querySelectorAll('[role=dialog], [aria-modal=true], .modal.show, .modal.in, .x-window, .ui-dialog')].filter(visible);
    const screens = rootSel ? [...document.querySelectorAll(rootSel)].filter(visible) : [];
    const root = dialogs.length > 0 ? dialogs[dialogs.length - 1] : screens.length > 0 ? screens[screens.length - 1] : document.body;
    const all = (sel) => [...root.querySelectorAll(sel)].filter(visible);

    // The label of a control: its <label for>, aria, or the nearest label
    // element before it in the same form row — ACCURATE lays rows out as
    // <div.row><div.spanN><label/></div><div.spanM><div.input-control><input/></div></div>,
    // so the label is two to four ancestors up, and the last one preceding the
    // control is its own (not the next column's).
    const labelFor = (el) => {
        if (el.labels && el.labels.length > 0 && norm(el.labels[0].innerText)) return el.labels[0];
        const aria = el.getAttribute('aria-label');
        if (aria) return norm(aria);
        const by = el.getAttribute('aria-labelledby');
        if (by) {
            const ref = document.getElementById(by.split(' ')[0]);
            if (ref) return ref;
        }
        const wrapping = el.closest('label');
        if (wrapping && norm(wrapping.innerText)) return wrapping;
        let p = el.parentElement;
        for (let i = 0; i < 5 && p && p !== root.parentElement; i++) {
            const labels = [...p.querySelectorAll('label, .control-label, th')].filter((l) => !l.contains(el) && norm(l.innerText) && visible(l));
            if (labels.length > 0) {
                const before = labels.filter((l) => l.compareDocumentPosition(el) & Node.DOCUMENT_POSITION_FOLLOWING);
                return before.length > 0 ? before[before.length - 1] : labels[0];
            }
            p = p.parentElement;
        }
        let prev = el.previousElementSibling;
        while (prev && !norm(prev.innerText)) prev = prev.previousElementSibling;
        return prev ? norm(prev.innerText) : '';
    };
    const labelText = (l) => (l instanceof Element ? norm(l.innerText) : norm(l));
    const labelRequired = (l) => (l instanceof Element ? l.classList.contains('required') || /\*\s*$/.test(norm(l.innerText)) : /\*\s*$/.test(norm(l)));

    const sectionFor = (el) => {
        const fs = el.closest('fieldset');
        if (fs) {
            const legend = fs.querySelector('legend');
            if (legend) return norm(legend.innerText);
        }
        const panel = el.closest('.panel, .card, .x-panel, section');
        const head = panel?.querySelector('.panel-heading, .card-header, .x-panel-header, h3, h4');
        return head ? norm(head.innerText) : '';
    };

    const controls = all('input, select, textarea, [contenteditable=true], [role=combobox], [role=checkbox], [role=switch], [role=radio], label.toggle-switch, .toggle-switch')
        .filter((el) => !(el.tagName === 'INPUT' && ['hidden', 'submit', 'button', 'image', 'reset'].includes(el.type)))
        // A switch is its visible wrapper; its hidden checkbox is read through it.
        .filter((el) => !(el.tagName === 'INPUT' && el.type === 'checkbox' && el.closest('.toggle-switch')))
        // Controls inside a data grid are cells, not form fields.
        .filter((el) => !el.closest('[role=row], tbody tr, .slick-row'));

    const fields = controls.map((el) => {
        const tag = el.tagName.toLowerCase();
        const role = el.getAttribute('role');
        const isSwitch = el.classList.contains('toggle-switch');
        const inner = isSwitch ? el.querySelector('input[type=checkbox]') : null;
        let type = tag === 'input' ? el.type || 'text' : tag;
        if (role === 'combobox' || el.getAttribute('aria-autocomplete')) type = 'combobox';
        if (role === 'checkbox' || role === 'switch' || isSwitch) type = 'checkbox';
        if (el.closest('.lookupbox, .lookup')) type = 'lookup';
        if (el.closest('.datepicker, .date-picker, .calendar-input') || (el.name && /date|tanggal/i.test(el.name))) type = 'date';
        if (el.closest('.input-control.number')) type = 'number';
        const lab = labelFor(el);
        const label = labelText(lab);
        const field = {
            label,
            name: norm(el.name || (inner && inner.name) || ''),
            placeholder: norm(el.getAttribute('placeholder')),
            type,
            required: el.required === true || el.getAttribute('aria-required') === 'true' || labelRequired(lab),
            section: sectionFor(el),
        };
        if (tag === 'select') field.options = [...el.options].map((o) => norm(o.text));
        if (type === 'checkbox' || type === 'radio') {
            const box = inner ?? el;
            field.checked = box.checked ?? box.getAttribute('aria-checked') === 'true';
        }
        if (type === 'number' || type === 'text') field.value = el.value ?? '';
        return field;
    });

    const headerCells = (grid) =>
        [...grid.querySelectorAll('th, [role=columnheader], .x-column-header-text, .slick-header-column')]
            .filter(visible)
            .map((h) => norm(h.querySelector('.slick-column-name')?.innerText ?? h.innerText));

    const gridEls = all('table, [role=grid], [role=treegrid], .x-grid, .ag-root, [class*=slickgrid]');
    const grids = gridEls.map(headerCells).filter((g) => g.length > 0);
    // The list of records, when the screen shows one, is the grid in its list pane.
    const listGrid = gridEls.find((g) => g.closest('.module-list'));
    const columns = listGrid ? headerCells(listGrid) : grids[0] ?? [];

    return {
        title: document.title,
        headings: all('h1, h2, h3, h4, [role=heading], .page-title, .x-title-text, .module-title, [data-bind*="getTitle"]').map(textOf),
        tabs: all('[role=tab], .nav-tabs > li > a, .x-tab-inner, .tab-control li a, .tabs > li > a, [data-role=tabcontrol] li a, .frame-left-tab li a, .tab-link').map(textOf),
        buttons: all('button, [role=button], a.btn, a.button, input[type=button], input[type=submit]').map(textOf),
        columns,
        filters: all('[class*=filter] label, [class*=filter] [placeholder], [class*=kriteria] label').map((el) => norm(el.innerText || el.getAttribute('placeholder'))),
        fields,
        grids,
        catalog: catalog ? all('a, li, [role=treeitem], [role=listitem], .tree-node, .list-item').map(textOf) : [],
        inDialog: dialogs.length > 0,
    };
}

/**
 * Every visible thing a person could click, with enough about it to write a
 * selector from: text, tag, role, classes. Recon writes this (sanitised) so the
 * menu selectors can be set from the real markup.
 */
export function listClickables() {
    const norm = (s) => String(s ?? '').replace(/\s+/g, ' ').trim();
    const visible = (el) => {
        const r = el.getBoundingClientRect();
        const cs = getComputedStyle(el);
        return r.width > 0 && r.height > 0 && cs.visibility !== 'hidden' && cs.display !== 'none';
    };
    const sel = 'a, button, [role=button], [role=menuitem], [role=tab], [role=treeitem], li, [onclick], [data-toggle], [tabindex]';
    return [...document.querySelectorAll(sel)]
        .filter(visible)
        .map((el) => {
            const r = el.getBoundingClientRect();
            return {
                text: norm(el.getAttribute('aria-label') || el.innerText || el.title).slice(0, 80),
                tag: el.tagName.toLowerCase(),
                role: el.getAttribute('role') || '',
                id: el.id || '',
                cls: (typeof el.className === 'string' ? el.className : '').split(/\s+/).slice(0, 4).join(' '),
                x: Math.round(r.x),
                y: Math.round(r.y),
            };
        })
        .filter((c) => c.text !== '');
}
