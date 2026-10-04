# AugustERP Design Reference

Master UI reference for every AugustERP build (Laravel + Filament v4/v5).
Give this file to whoever (or whichever agent) builds screens. Rules here win over Filament defaults.

Chosen config (from the style configurator, 2026-10-04):

```json
{ "font": "Geist", "nav": "floating", "surface": "layered", "tone": "warm",
  "density": "comfy", "table": "hover", "input": "outline", "radius": "14px", "accent": "#2f5bea" }
```

Character: modern and polished. Calm cool-grey canvas, white cards that float above it with soft layered shadows, one confident blue accent, Geist for everything, numbers in tabular figures.

---

## 1. Design tokens

All values are CSS custom properties. `theme.css` defines them; components only use tokens, never raw hex.

### Color (light, the default)

| Token | Value | Use |
|---|---|---|
| `--ae-canvas` | `#e3e7f2` | Page background behind cards (layered look) |
| `--ae-surface` | `#ffffff` | Cards, sidebar, table, modal, dropdown |
| `--ae-surface-sunken` | `#f6f6f3` | Table header row, read-only fields, code blocks |
| `--ae-ink` | `#1d2433` | Primary text, headings, numbers |
| `--ae-muted` | `#6b7280` | Labels, helper text, secondary column text |
| `--ae-line` | `#e9e4da` | Dividers, table row lines |
| `--ae-card-edge` | `#f2efe9` | 1px card border (very faint, warm) |
| `--ae-input-edge` | `#d1d0ce` | Input border at rest |
| `--ae-accent` | `#2f5bea` | Primary buttons, active nav, links, focus |
| `--ae-accent-soft` | `#e6ebfc` | Active nav background, active tab, row hover, selected chips |
| `--ae-focus-ring` | `#dae1fb` | 3px focus ring around inputs and buttons |

Status colors (badge background / text). These are separate from the accent; never use the accent to mean "success".

| Status | Background | Text | Used for |
|---|---|---|---|
| Success | `#e4f6ea` | `#166534` | Paid, Received, Posted, In stock |
| Warning | `#fff2d4` | `#8a5a00` | Unpaid, Partial, Low stock, Pending approval |
| Danger | `#fde7e7` | `#a11d1d` | Overdue, Rejected, Out of stock, Void |
| Neutral | `#eef0f4` | `#4b5563` | Draft, Cancelled, Archived |
| Info | `#e6ebfc` | `#2f5bea` | Sent, Processing, In transit |

Trend text: up `#15803d`, down `#b42318`.

### Color (dark mode, proposed, not yet reviewed)

| Token | Value |
|---|---|
| `--ae-canvas` | `#0f1219` |
| `--ae-surface` | `#171b25` |
| `--ae-surface-sunken` | `#1d2230` |
| `--ae-ink` | `#e7eaf2` |
| `--ae-muted` | `#9aa3b5` |
| `--ae-line` | `#262c3a` |
| `--ae-card-edge` | `#232937` |
| `--ae-input-edge` | `#343b4d` |
| `--ae-accent` | `#7d9bff` |
| `--ae-accent-soft` | `#1f2a4d` |
| `--ae-focus-ring` | `#2a3a70` |

### Typography

- Family: **Geist** (UI and headings), **Geist Mono** (codes like SKU, document numbers in print, API keys).
- Self-host both (npm `@fontsource-variable/geist` and `@fontsource-variable/geist-mono`). Do not load from Google Fonts: clients on intranet or in China will get a fallback font.
- Fallback stack: `"Geist Variable", Geist, ui-sans-serif, system-ui, "Segoe UI", sans-serif`.
- All numeric table cells, KPI values and totals: `font-variant-numeric: tabular-nums`.

| Role | Size / line-height | Weight | Notes |
|---|---|---|---|
| Page title | 22px / 28px | 600 | letter-spacing -0.01em |
| Section / card title | 15px / 22px | 600 | |
| Body / table cell | 13.5px / 20px | 400 | Base size of the panel |
| Label / helper | 12px / 16px | 500 | `--ae-muted` |
| Table header | 11px / 16px | 600 | UPPERCASE, letter-spacing 0.04em, `--ae-muted` |
| KPI value | 22px / 28px | 700 | tabular-nums, letter-spacing -0.01em |
| Badge | 11px / 16px | 600 | with 6px status dot before text |

### Radius

Base radius `--ae-radius: 14px`. Derived sizes keep shapes nested correctly:

| Token | Value | Use |
|---|---|---|
| `--ae-radius` | 14px | Cards, sidebar, modal, table container |
| `--ae-radius-md` | 10px | Buttons, inputs, selects, nav items |
| `--ae-radius-sm` | 8px | Tabs, chips, small icon buttons |
| `--ae-radius-full` | 999px | Badges, avatars, toggles |

### Spacing (comfortable density)

4px grid. Page padding 22px. Gap between cards 12px. Card inner padding 16px. Table cell padding 11px vertical, 14px horizontal. Form field gap 12px. Sidebar item padding 8px 9px.

### Elevation (layered surfaces)

| Token | Value | Use |
|---|---|---|
| `--ae-shadow-card` | `0 1px 1px rgb(20 30 60 / .04), 0 12px 28px -8px rgb(20 30 60 / .14)` | Cards, table container, floating sidebar |
| `--ae-shadow-pop` | `0 4px 8px rgb(20 30 60 / .06), 0 24px 48px -12px rgb(20 30 60 / .22)` | Dropdowns, modals, slide-overs |
| `--ae-shadow-btn` | `inset 0 1px 0 rgb(255 255 255 / .2), 0 2px 6px rgb(47 91 234 / .35)` | Primary button only |

Every card = white surface + 1px `--ae-card-edge` border + `--ae-shadow-card`. Nothing else gets a shadow.

---

## 2. Layout and placement

### App shell

```
+--------------------------------------------------------------------------+
| canvas (#e3e7f2)                                                          |
|  +-----------+  +------------------------------------------------------+ |
|  | FLOATING  |  | Topbar: breadcrumb ......... [search 220px] [avatar] | |
|  | SIDEBAR   |  +------------------------------------------------------+ |
|  | 200px     |  | Page header: Title + subtitle  ......  [secondary][+] | |
|  | 12px from |  +------------------------------------------------------+ |
|  | edges,    |  | Content cards (12px gap)                             | |
|  | radius 14 |  |                                                      | |
|  | shadow    |  |                                                      | |
|  +-----------+  +------------------------------------------------------+ |
+--------------------------------------------------------------------------+
```

- Sidebar floats: 12px margin top, left and bottom; white; radius 14; card shadow. Collapsible to icons on desktop.
- Sidebar order: brand (logo + "AugustERP" or client name), then nav groups. Active item = `--ae-accent-soft` fill + accent text + accent icon.
- Topbar sits on the canvas (no white bar). Breadcrumb left, global search and user menu right.
- Page header: title (and one-line context such as period or warehouse) on the left, actions on the right. Max one primary button per page, placed rightmost.
- Content max width: full width for list and report pages; 1100px for create/edit forms.

### Navigation groups (standard module map)

1. **Dashboard**
2. **Sales**: Customers, Quotations, Sales Orders, Deliveries, Invoices, Returns
3. **Purchasing**: Suppliers, Purchase Requests, Purchase Orders, Goods Receipts, Bills
4. **Inventory**: Products / SKU, Stock Card, Stock Movements, Transfers, Stock Opname, Warehouses
5. **Finance**: Receivables, Payables, Payments, Cash and Bank, Journal, Chart of Accounts
6. **Reports**: Sales, Purchasing, Inventory, Finance
7. **Settings**: Company, Users and Roles, Numbering, Taxes, Printers

### Page types

**A. List page** (Sales Orders, Products, Invoices…)

```
[Page header: title + context ............ Export | + New]
[KPI row: 4 cards, equal width]                      <- optional, only for key lists
[Table card]
  [Tabs: All 48 | Draft 5 | Unpaid 14 | Overdue 3]   <- status filters as tabs with counts
  [Toolbar: search | filters | column toggle]
  [Table rows ........................................]
  [Pagination]
```

Optional right panel (230px card) for a quick-create form or a selected-row preview.

**B. Document page** (one Sales Order, PO, Invoice)

```
[Header: SO-2610-0412  [Status badge]  ...... Print | More | Primary action]
[2 columns: 2/3 | 1/3]
  Left:  Line items table card  ->  Notes card
  Right: Customer card -> Summary card (subtotal, discount, tax, total, paid, balance)
         -> Activity / history card
```

The primary action follows the document status: Draft → "Confirm", Confirmed → "Create delivery", Delivered → "Create invoice", Invoiced → "Record payment".

**C. Form page** (create/edit)

Sections as cards, max width 1100px, 2-column field grid on desktop and 1 column on phone. Sticky footer bar with Cancel (secondary) and Save (primary).

**D. Dashboard**

Row 1: 4 KPI cards. Row 2: revenue chart (2/3) and top customers or low stock (1/3). Row 3: recent orders table and overdue invoices table side by side.

**E. Report page**

Filter card on top (period, warehouse, customer…; "Apply" button), then summary KPIs, then table with totals row pinned at the bottom. Export to Excel/PDF in the page header.

**F. Print view** (invoice, delivery note, PO)

White A4, no canvas, no shadows. Company header left, document title and number right. Geist Mono for document numbers. Status badge hidden in print.

---

## 3. Components

### Buttons
- Primary: accent fill, white text, `--ae-shadow-btn`, radius 10, padding 8px 14px, weight 600.
- Secondary: white fill, 1px `--ae-line` border, ink text.
- Danger: `#a11d1d` fill (only in confirm modals).
- Ghost/icon: no fill, muted icon, `--ae-accent-soft` on hover.

### Inputs (outlined)
- White fill, 1px `--ae-input-edge` border, radius 10, padding 8px 10px.
- Focus: accent border + 3px `--ae-focus-ring`.
- Label above field (12px, 500, muted). Helper/error text below; error text `#a11d1d`.
- Money inputs: right-aligned, "Rp" prefix, tabular-nums.

### Tables (clean + hover)
- Container is a card (white, radius 14, card shadow). Header row on `--ae-surface-sunken`.
- No zebra stripes and no row lines by default. Row hover and selected row = `--ae-accent-soft`.
- First column: document number or name, weight 500. Optional second line in muted 12px (city, SKU).
- Numbers right-aligned, Indonesian format: `18.450.000` (thousands `.`, decimals `,`). Currency in header: "Total (Rp)".
- Status column uses badges. Row actions appear on hover at the right end.
- Dates: `17 Okt 2026` in tables, `17/10/2026` in inputs.

### KPI card
Label (12px muted) → value (22px bold, tabular) → trend line (12px, green or red, with period: "+12,3% vs Sep").

### Tabs
Pill tabs: active = `--ae-accent-soft` fill + accent text; inactive = muted text. Show counts in a lighter weight.

### Badges
Pill, 3px 9px padding, 6px colored dot before label, status colors from section 1.

### Modals and slide-overs
Surface white, radius 14, `--ae-shadow-pop`, backdrop `rgb(15 18 25 / .35)` with 2px blur. Destructive confirm: title states the action ("Void invoice INV-0412?"), body states the effect, buttons "Cancel" and "Void invoice".

---

## 4. Copy and formatting rules

- Interface language default: Indonesian or English per client; keep one language per panel.
- Buttons are verbs that say what happens: "Save order", "Record payment", "Post journal". Never "Submit" or "OK".
- Empty states name the record and the first action: "No purchase orders yet. Create the first one."
- Document numbers: `SO-YYMM-####`, `PO-YYMM-####`, `INV-YYMM-####`, `DO-YYMM-####`, `GR-YYMM-####`.

---

## 5. Filament mapping

| Design rule | Filament setting |
|---|---|
| Accent `#2f5bea` | `->colors(['primary' => Color::hex('#2f5bea')])` |
| Gray scale | `'gray' => Color::Slate` |
| Geist font | self-hosted, set in `theme.css` (see file) |
| Floating sidebar | `->sidebarCollapsibleOnDesktop()` + CSS in `theme.css` |
| Status colors | `->colors(['success' => …, 'warning' => …, 'danger' => …, 'info' => …])` and `Badge::color()` per status enum |
| Full-width lists | `->maxContentWidth(Width::Full)` |
| Layered canvas, cards, tables, inputs | `theme.css` |

Files in this pack:

- `DESIGN.md`: this reference.
- `theme.css`: Filament custom theme implementing the tokens.
- `AdminPanelProvider.snippet.php`: panel settings.
- `reference.html`: open in a browser to see the chosen style live.

Note: Filament CSS class names (`.fi-sidebar`, `.fi-ta-ctn`…) can change between versions. After install, check each selector in browser devtools and adjust `theme.css` if one does not match.
