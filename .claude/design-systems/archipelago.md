# Archipelago

> Extracted from: live site (finance management application)
> Sources: DevTools computed styles + CSS custom properties + visual screenshot analysis
> Values marked † are inferred; all others are verified via DevTools or visual inspection.

## Essence

A disciplined, professional financial interface built for clarity under pressure — every element earns its place. The design speaks in clean whites and deep darks anchored by a confident primary blue, communicating institutional trust without coldness. Two modes, one system: light is crisp and airy like a freshly printed ledger; dark is deep and focused like a lit workstation at night. Layout thinking is contextual — lists use tables, summaries use card grids, details use full-width slide-overs.

---

## Color Palette

### Primary Brand (Blue)

| Token | Hex | Usage |
|-------|-----|-------|
| `primary-50` | `#eff6ff` | Active nav bg (light), light tint backgrounds |
| `primary-100` | `#dbeafe` | Hover states, subtle fills |
| `primary-200` | `#bfdbfe` | Icon bg containers |
| `primary-300` | `#93c5fd` | Accent borders, highlights |
| `primary-400` | `#60a5fa` | Icon color in muted contexts |
| `primary-500` | `#3b82f6` | Active nav bg (dark), chart fill (income) |
| `primary-600` | `#2563eb` | Primary CTA button, active nav text (light) |
| `primary-700` | `#1d4ed8` | Hover on primary button† |
| `primary-800` | `#1e40af` | Pressed/active state† |

### Backgrounds — Light Mode

| Token | Value | Usage |
|-------|-------|-------|
| `bg-page` | `oklch(0.985 0.002 247.839)` ≈ `#f9fafb` | Page/main content area |
| `bg-sidebar` | `rgb(255, 255, 255)` | Sidebar panel (white with right border) |
| `bg-header` | `rgb(255, 255, 255)` | Top header bar |
| `bg-card` | `rgb(255, 255, 255)` | Cards, content panels |
| `bg-modal` | `rgb(255, 255, 255)` | Modal + slide-over panels |
| `bg-input` | transparent over white wrapper | Form inputs (ring wrapper is white) |

### Backgrounds — Dark Mode

| Token | Hex | Variable | Usage |
|-------|-----|----------|-------|
| `dark-950` | `#09090a` | `--color-dark-950` | Body, main content area |
| `dark-900` | `#0f0f11` | `--color-dark-900` | Sidebar, secondary surfaces |
| `dark-800` | `#141414` | `--color-dark-800` | Input field backgrounds |
| `dark-700` | `#1a1a1d` | `--color-dark-700` | Cards, modals, slide-overs, dropdowns |
| `dark-600` | `#3f3f46` | `--color-dark-600` | Borders, hover bg items, disabled bg |
| `dark-500` | `#71717a` | `--color-dark-500` | Dividers (`border-t`) |
| `dark-400` | `#a1a1aa` | `--color-dark-400` | Icons, placeholder text, muted labels |
| `dark-300` | `#e4e4e7` | `--color-dark-300` | Primary body text |
| `dark-200` | `#f4f4f5` | `--color-dark-200` | Headings, prominent text |
| `dark-50`  | `#fafafa` | `--color-dark-50`  | Highest-contrast text on dark surfaces |

### Text

| Token | Light Value | Dark Value | Usage |
|-------|-------------|------------|-------|
| `text-primary` | gray-900 ≈ `#111827` | `#f4f4f5` (dark-200) | Body content, table data |
| `text-secondary` | `oklch(0.446 0.03 256.802)` ≈ gray-600 | `#a1a1aa` (dark-400) | Labels, captions, table headers |
| `text-muted` | gray-500† | `#71717a` (dark-500) | Placeholder, hint text |
| `text-heading` | gradient gray-900→blue-800→indigo-800 | gradient white→blue-200→indigo-200 | Page `<h1>` (gradient bg-clip) |
| `text-nav-section` | gray-400 uppercase† | dark-500 uppercase† | Sidebar section labels (DATA MASTER, KEUANGAN, etc.) |

### Semantic Colors (Status Badges)

| Role | bg | text | Observed in |
|------|----|------|-------------|
| Terkirim (Sent) | `oklch(0.488 0.243 264.376)` ≈ blue-600 | blue-50 | Invoice table |
| Jatuh Tempo (Overdue) | `oklch(0.505 0.213 27.518)` ≈ red-600 | red-50 | Invoice table |
| Lunas (Paid) | `oklch(0.527 0.154 150.069)` ≈ green-600 | green-50 | Invoice table |
| Aktif (Active) | green-600† | green-50† | Recurring invoice cards |
| Draft | zinc-600 | white | Invoice table† |
| Sebagian (Partial) | yellow-600† | yellow-50† | Invoice table† |

### Chart Colors (visual)

| Role | Color | Usage |
|------|-------|-------|
| Income / Pemasukan | `#22c55e`† (green-500) | Area chart line + fill |
| Expense / Pengeluaran | `#ef4444`† (red-500) | Area chart line + fill |
| Chart grid lines (dark) | white/10%† | Horizontal tick lines |

### Borders

| Context | Light | Dark |
|---------|-------|------|
| Card border | zinc-200 ≈ `#e4e4e7` | `white/8%` (`oklab ... / 0.08`) |
| Dropdown border | gray-100 | `white/8%` |
| Slide-over backdrop | `gray-400/75%` | `black/30%` |
| Header bg (dark) | — | semi-transparent dark + backdrop-blur |
| Sidebar right border | gray-200† | dark-600† |

---

## Typography

### Families

| Role | Family | Variable | Weights Used |
|------|--------|----------|-------------|
| Heading / Display | Plus Jakarta Sans | `--font-heading` | 600, 700, 800 |
| Body / UI | Inter | `--font-sans` | 400, 500, 600, 700 |
| Mono | system mono† | — | 400 |

### Scale

| Level | Size | Weight | Usage |
|-------|------|--------|-------|
| Page Title (h1) | 36px | 700 | Main page headings with gradient clip |
| Widget Title | 20px† | 700 | Chart titles, card section headers |
| Modal/Panel Title | 18px | 600 | Slide-over headers, modal subtitles |
| Body | 16px | 400 | Button labels, general prose |
| Label | 14px | 600 | Form field labels, table col headers |
| Small / Nav | 14px | 400–500 | Table data, nav item text |
| Nav Section | 11px† | 600 | Uppercase sidebar section labels |
| Badge / Micro | 12px | 700 | Status badges, frequency tags |

### Notable Treatments

- **Page h1**: gradient `bg-clip-text text-transparent from-gray-900 via-blue-800 to-indigo-800` (light) / `from-white via-blue-200 to-indigo-200` (dark). 36px, bold.
- **Sidebar section labels**: `DATA MASTER`, `KEUANGAN`, `ARUS KAS`, `OPERASIONAL`, `HUTANG & PIUTANG`, `ADMINISTRASI` — uppercase, small, gray, not interactive.
- **Currency values**: large numbers (e.g. `Rp 136,4jt`, `Rp 103.500.000`) use `font-bold text-2xl` in stat cards; `font-semibold` in tables.
- **Negative values** (e.g. Laba Kotor merah): `text-red-500` applied to the value itself, not the card.
- **Muted subtitles**: appear directly under h1, 16px regular, gray (`text-muted-foreground` equivalent).

---

## Spacing

### Base Unit

4px — all spacing is a multiple of 4px.

### Scale

| Token | Value | Common Usage |
|-------|-------|-------------|
| `space-1` | 4px | Tight icon-text gap |
| `space-2` | 8px | Badge padding (2px 8px), inline element gaps |
| `space-3` | 12px | Card internal padding for sub-elements |
| `space-4` | 16px | Form modal padding, field gaps |
| `space-6` | 24px | Section separation, stats card gap |
| `space-8` | 32px | Major layout separation |

### Layout

- Sidebar width: `224px` fixed, collapsible (with chevron toggle button)
- Header height: `~64px`†
- Page content padding: `~24px` horizontal and vertical†
- Stats grid: `grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4`
- Recurring cards grid: `grid-cols-3 gap-4`† (3-column on desktop)
- Table search bar width: `w-64` (256px)
- Form modal max-widths: `max-w-xl` (576px) for full forms with 2-col layout
- Slide-over panel (detail view): full-viewport-height, ~80% viewport width†

---

## Elevation

### Border Radii

| Token | Value | Variable | Applied To |
|-------|-------|----------|-----------|
| `radius-sm` | 4px | `--radius-sm` | Checkboxes, small tags† |
| `radius-md` | 6px | `--radius-md` | Buttons, inputs, badges, frequency tags |
| `radius-lg` | 8px | `--radius-lg` | Navigation active item highlight |
| `radius-xl` | 12px | `--radius-xl` | Cards, modals, slide-overs, dropdowns, icon containers |

**Rule:** `rounded-xl` (12px) for every container. `rounded-md` (6px) for every component. Binary system — no middle ground.

### Shadows

| Level | Applied To | Class |
|-------|-----------|-------|
| `shadow-sm` | Cards default | baseline lift |
| `shadow-md` | Cards on hover | `hover:shadow-md transition-shadow` |
| `shadow-xl` | Modals, slide-overs | strong elevation |
| none | Sidebar, header | separated by border only |

---

## Page Layout Patterns (Visual)

This is a critical section missing from DevTools-only analysis. The application uses **three distinct layout strategies** depending on content type:

### 1. Table Layout — Lists (Invoices)

```
┌─────────────────────────────────────────────────────┐
│ H1 Title + subtitle           [Action Button]        │
├──────────────┬──────────────┬───────────────────────┤
│  Stat Card   │  Stat Card   │  Stat Card  Stat Card  │
├──────────────┴──────────────┴───────────────────────┤
│ ████████▓▓▓▒▒▒░░░  ← Status pipeline bar            │
│ ● Draft (n)  ● Terkirim (n)  ● Sebagian (n)  ● Lunas│
├──────────────────────────────────────────────────────┤
│ [Semua] [Draft n] [Terkirim n] [Sebagian n] [Lunas n]│  ← underline tabs
├──────────────────────────────────────────────────────┤
│ ┌──────────────────── Card ───────────────────────┐  │
│ │ [Klien ▼] [Periode: Bulan|Rentang] [🔍 Cari...]│  │  ← filter toolbar (border-b)
│ ├─────────────────────────────────────────────────┤  │
│ │ Table (NO, Klien+Avatar, Tgl, Jatuh Tempo,      │  │
│ │        Jumlah+progress, Status, ⋯ Aksi)        │  │  ← clickable rows
│ ├─────────────────────────────────────────────────┤  │
│ │ [Pagination]                       n–m dari tot │  │  ← conditional footer (border-t)
│ └─────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────┘
```

- Stats: 4 cards, each with `h-1` colored top accent bar + uppercase tracked label + icon (top-right) + large value + sub-text line 3 + `Tooltip` on hover
- Status pipeline bar: proportional `h-2 rounded-full` horizontal segments per status count, clickable to filter
- Status tabs: `variant="underline"` with badge count per status
- Unified table card: filter toolbar inside `Card` header (`border-b`), table in body, pagination in footer (`border-t`, conditional on `last_page > 1`)
- Filter toolbar: `Combobox` for client, date mode toggle (pill), `DatePicker` (`mode="month"` or `mode="range"`), `Input` for search, reset button with `Badge` count
- Table rows: fully clickable (`onClick → openDrawer`), `cursor-pointer`, actions cell stops propagation
- Client column: `Avatar` + `AvatarFallback` (initials) + name + type
- Amount column: value + inline progress bar for `partially_paid` status
- Action column: `DropdownMenu` with `MoreHorizontal` trigger (Lihat Detail / Edit / Download PDF / Hapus)

### 2. Card Grid Layout — Templates (Recurring Invoices)

```
┌─────────────────────────────────────────────────────┐
│ H1 Title + subtitle    [14 aktif]  [Rp 253,9jt]    │
│ [Template | Bulanan | Analitik] tab bar              │
├───────────────┬───────────────┬─────────────────────┤
│  ┌──────────┐ │  ┌──────────┐ │  ┌──────────┐      │
│  │ Card     │ │  │ Card     │ │  │ Card     │      │
│  │ Client   │ │  │ Client   │ │  │ Client   │      │
│  │ Rp 2jt  │ │  │ Rp 2jt  │ │  │ Rp 2jt  │      │
│  │ [aktif] │ │  │ [aktif] │ │  │ [aktif] │      │
│  │ [Lihat] │ │  │ [Lihat] │ │  │ [Lihat] │      │
│  └──────────┘ │  └──────────┘ │  └──────────┘      │
└───────────────┴───────────────┴─────────────────────┘
```

- Each card: rounded-xl, border, white/dark-700 bg, client name, amount, date meta, action buttons
- Status badge top-right of card: `aktif` (green pill)
- Date info: colored pills/tags (orange for next due date, etc.)
- Tab bar: custom pill/segment style (not default tabs)

### 3. Master-Detail Split Layout — Bank Accounts

```
┌─────────────────────────────────────────────────────┐
│ H1 Title + subtitle         [Panduan] [Buat]        │
├──────────────────┬──────────────────────────────────┤
│ Account List     │ Detail Panel (selected account)  │
│ ─────────────── │ ────────────────────────────────  │
│ • Account A  Rp │ Account Name  [Pengeluaran][Pemasukan]│
│ • Account B  Rp │                                   │
│ • Account C  Rp │ Period: Mar 2026                  │
│ • Account D  Rp │ [Pemasukan] [Pengeluaran] [Arus]  │
│ • Account E  Rp │                                   │
│ ─────────────── │ [Line Chart] [Donut Chart]        │
│ + Tambah Baru   │                                   │
│                  │ [Transaction Table]               │
│ Summary Box:    │                                   │
│ Total Saldo     │                                   │
│ Pemasukan       │                                   │
│ Pengeluaran     │                                   │
│ Arus Bersih     │                                   │
└──────────────────┴──────────────────────────────────┘
```

- Left panel: scrollable list of account items (icon, name, bank name, balance)
- Active account: highlighted with blue accent†
- Right panel: full detail with period summary stats, two charts side-by-side, transaction table

### 4. Slide-Over / Drawer — Invoice Detail

The invoice detail is **not a centered modal** — it's a full-height right-side panel:

```
┌──────────────────────────────────────────────────┐
│ [Dimmed backdrop]  ┌────────────────────────────┐│
│                    │ Invoice No. [badge] [PDF]  ││
│                    │ Client name                ││
│                    │                            ││
│                    │ [Date] [Due] [Laba Kotor]  ││
│                    │                            ││
│                    │ LINE ITEMS TABLE           ││
│                    │                            ││
│                    │ ─────────────────────────  ││
│                    │ PAYMENT / TOTAL            ││
│                    │                            ││
│                    │ [Catat Pembayaran btn]     ││
│                    │                            ││
│                    │ SIDEBAR (meta + timeline)  ││
│                    └────────────────────────────┘│
└──────────────────────────────────────────────────┘
```

- Three stat boxes at top of slide-over: Tanggal Invoice / Jatuh Tempo (red bg†) / Laba Kotor
- Line items table is embedded inside the panel
- Right meta sidebar within the panel: Informasi Bisnis, Faktur (linked files), Timeline

### 5. Form Modals — Transaction Creation

```
┌──────────────────────────────────────────────┐
│ [Icon] Title                                 │
│        Short description                     │
├──────────────────────┬───────────────────────┤
│ Detail Transaksi     │ Keterangan            │
│ ─────────────────   │ ──────────────────    │
│ Rekening Sumber *   │ Deskripsi             │
│ [select ▼]          │ [textarea]            │
│                      │                       │
│ Kategori            │ Nomor Referensi       │
│ [select ▼]          │ [input]               │
│                      │                       │
│ Jumlah *            │ Bank Transaksi        │
│ [Rp input]          │ [input]               │
│                      │                       │
│ Tanggal *           │ [File upload zone]    │
│ [date picker]        │ Klik atau drag & drop │
├──────────────────────┴───────────────────────┤
│                         [Batal] [Simpan X]   │
└──────────────────────────────────────────────┘
```

- Modal header has icon + title + description (3-element cluster)
- Two-column body: left = required fields, right = optional/supporting info
- File upload zone: dashed border†, drag & drop area in right column
- Footer: `Batal` (zinc) left-of / `Simpan Pengeluaran` (red) or `Simpan Pemasukan` (green) — color signals intent

---

## Interactive States

### Buttons — Primary (Blue)

| State | Visual Treatment |
|-------|-----------------|
| Default | `bg: #2563eb`, text: white, radius: 6px, height: 42px, padding: 8px 16px |
| Hover | bg darkens to `#1d4ed8`†, transition 200ms |
| Focus | 3px outline ring in primary color |
| Disabled | opacity reduced†, cursor: not-allowed† |

### Buttons — Semantic Variants

| Variant | bg | Usage | Observed in |
|---------|-----|-------|-------------|
| Green | `oklch(0.723 0.219 149.579)` ≈ green-500 | Income/positive save | "Simpan Pemasukan" |
| Red† | red-600 | Expense/danger save | "Simpan Pengeluaran" |
| Zinc | `oklch(0.552 0.016 285.938)` ≈ zinc-500 | Cancel, secondary | "Batal" in all forms |
| Blue | primary-600 | Default CTA | "Buat Invoice", "Catat Pembayaran" |

All buttons: `border: 1px solid transparent`, radius 6px, height 42px, font-size 16px.

### Form Inputs

| State | Light | Dark |
|-------|-------|------|
| Default | wrapper: `bg-white`, `ring-1 gray-300` | wrapper: `bg-dark-800` (#141414), `ring dark-600` |
| Focus | `ring-2 primary-600` | `ring-2 primary-500` |
| Error† | `ring-2 red-500` | `ring-2 red-400` |

Input internals: transparent bg, radius 6px, padding `6px 12px`, font-size 14px, label above (14px/600).

### Navigation Links

| State | Light | Dark |
|-------|-------|------|
| Default | no bg, text gray-600† | no bg, text dark-400 (`#a1a1aa`) |
| Hover | bg gray-50†, text gray-900† | bg dark-600 (`#3f3f46`), text dark-200 |
| Active | **full row** bg primary-50 (`#eff6ff`), text primary-600, radius 8px | **full row** bg primary-500 (`#3b82f6`), text near-white |

Note: in light mode the active nav item has a tinted blue-50 background that fills the entire row width. In dark mode it becomes a solid blue-500 row — much more prominent.

### Tab Bar — Pill/Segment style

```
[inactive tab] [ACTIVE TAB] [inactive tab]
```

Container: `bg-zinc-100 dark:bg-dark-700 rounded-xl border p-1`
Active: `bg-white dark:bg-dark-800 shadow-sm border rounded-lg`
Inactive: text muted, hover bg-zinc-50/dark-600

Use for: switching between content modes (Templates / Bulanan / Analitik, etc.)

### Tab Bar — Underline style

```
[Semua] [Draft 4] [Terkirim 2] [Sebagian 1] [Lunas 12]
────────────────────────────────────────────────────────
```

Container: `flex items-center border-b border-secondary-200 dark:border-dark-600`
Active tab: `border-b-2 -mb-px border-primary-600 dark:border-primary-400 text-primary-700`
Inactive tab: `border-transparent text-dark-500`, hover `text-dark-800 hover:border-dark-200`
Badge (active): `bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300`
Badge (inactive): `bg-zinc-100 dark:bg-dark-700 text-dark-500`

Use for: status filtering on list pages (all / draft / sent / paid, etc.)

### Date Mode Toggle (Bulan / Rentang)

```
[Bulan] [Rentang]
```

A pill-style switcher (smaller scale than full tab bar) placed inline above a `DatePicker`:
- Container: `inline-flex p-0.5 bg-zinc-100 dark:bg-dark-700 rounded-lg border`
- Active: `bg-white dark:bg-dark-800 shadow-sm border rounded-md text-xs`
- Inactive: `text-dark-500 text-xs`, hover text-dark-700
- Drives `period_mode` URL param (`month` | `range`); switching navigates and preserves all other filters

---

## Motion

### Philosophy

Snappy and functional — transitions confirm state, never entertain. 100–300ms range, Material-style easing. No theatrical entrance animations.

### Timing Scale

| Name | Duration | Easing | Used For |
|------|----------|--------|---------|
| `fast` | 150ms | `ease-out` | Tab reveal (x-transition), shadow |
| `base` | 200ms | `cubic-bezier(0.4, 0, 0.2, 1)` | Button hover, nav hover, card shadow |
| `modal` | 300ms | `cubic-bezier(0.4, 0, 0.2, 1)` | Slide-over open/close, backdrop fade |

### Patterns

- **Slide-over/Modal enter**: `transform transition-all`, slides + fades. Backdrop `transition-opacity`.
- **Tab content**: `ease-out duration-150`, `opacity-0 translate-y-1` → `opacity-100 translate-y-0`.
- **Card hover**: `hover:shadow-md transition-shadow` — shadow lifts, no movement.
- **Livewire progress bar**: cyan `#2299dd` (not primary blue — intentionally distinct).

---

## Design Principles

1. **Layout follows content type** — lists get tables; collections of similar items get card grids; detail views get slide-over drawers. No one-size-fits-all layout.

2. **Dual-mode parity** — 10-stop `dark-*` scale (`#09090a` → `#fafafa`) with semantic stop assignments. Each surface layer has its own token: body / sidebar / card / input / border.

3. **Two radii, always** — `rounded-xl` (12px) for containers; `rounded-md` (6px) for components. Enforced without exception.

4. **Blue as a signal** — primary blue only for active states, CTAs, and focus. Passive UI is pure gray. Makes blue instantly meaningful.

5. **Color encodes intent in forms** — the submit button color is semantic: green = income action, red = expense action, blue = neutral create. Users understand the direction before reading.

---

## Implementation Notes

**CSS custom property naming (verified):**
- `--color-primary-{50-800}` — blue scale in hex
- `--color-dark-{50-950}` — gray scale in hex
- `--font-heading` — `"Plus Jakarta Sans", ui-sans-serif, ...`
- `--font-sans` — `"Inter", ui-sans-serif, ...`
- `--radius-{sm|md|lg|xl}` — 4px / 6px / 8px / 12px

**Stack:** Laravel + Livewire 3 + TallStackUI (production). Migration branch: Inertia.js + React 18 + shadcn/ui. Tokens unchanged.

**Dark mode:** `class` strategy on `<html>`. Toggled via header button (moon icon), persisted to `localStorage`.

**Tailwind v4:** Computed colors return OKLCH/OKLab. Custom props return hex. Use class names, not computed values, in new code.

**shadcn/ui token mapping (React migration):**

| Archipelago | shadcn/ui |
|------------|-----------|
| `bg-white dark:bg-dark-700` | `bg-card` |
| `bg-gray-50 dark:bg-dark-950` | `bg-background` |
| `text-dark-900 dark:text-dark-50` | `text-foreground` |
| `text-dark-600 dark:text-dark-400` | `text-muted-foreground` |
| `border-zinc-200 dark:border-white/8` | `border` |
| `primary-600` / `primary-500` (dark) | `primary` |
| `rounded-xl` | override shadcn default (`rounded-md`) to `rounded-xl` |

**Global cursor pointer (app.css):**
```css
button:not(:disabled),
a,
[role="button"]:not([aria-disabled="true"]),
label[for],
summary {
    cursor: pointer;
}
```
Applied globally — do not add `cursor-pointer` individually to every element.

**Clickable table rows:** Add `onClick` + `cursor-pointer` to `<tr>`. Add `onClick={(e) => e.stopPropagation()}` to the actions cell to prevent row click from firing when the dropdown is used.

**Icon library:** Heroicons in Blade → `lucide-react` or `@heroicons/react` in React.

**Gradient heading (React):**
```tsx
<h1 className="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
  Page Title
</h1>
```

**Stats card (React/shadcn) — current pattern:**

Three-row vertical layout with colored top accent bar and tooltip.
```tsx
<Tooltip>
  <TooltipTrigger asChild>
    <Card className="hover:shadow-md transition-all duration-200 overflow-hidden cursor-default">
      <div className="bg-blue-500 h-1" />   {/* colored accent bar — 1px top stripe */}
      <CardContent className="p-5">
        <div className="flex items-start justify-between mb-3">
          <p className="text-xs font-semibold uppercase tracking-wider text-dark-500 dark:text-dark-400 leading-none">
            Label
          </p>
          <span className="text-blue-500 dark:text-blue-400 shrink-0"><Icon className="w-5 h-5" /></span>
        </div>
        <p className="text-xl font-bold text-dark-900 dark:text-dark-50 leading-none">Value</p>
        <p className="text-xs text-dark-500 dark:text-dark-400 mt-2">Sub-text (concise context)</p>
      </CardContent>
    </Card>
  </TooltipTrigger>
  <TooltipContent side="bottom" className="max-w-56 text-center">
    Full explanation of what this metric shows
  </TooltipContent>
</Tooltip>
```

Wrap grid in `<TooltipProvider delayDuration={300}>`.

Accent bar colors per semantic role:
| Role | Accent | Icon color |
|------|--------|-----------|
| Revenue / total | `bg-blue-500` | `text-blue-500` |
| Profit / positive metric | `bg-emerald-500` (red if negative) | `text-emerald-500` |
| Payments received | `bg-green-500` | `text-green-500` |
| Count / quantity | `bg-purple-500` | `text-purple-500` |

**Form modal submit button — semantic color by action:**
```tsx
// Income action
<Button className="bg-green-500 hover:bg-green-600 text-white">Simpan Pemasukan</Button>
// Expense action  
<Button className="bg-red-600 hover:bg-red-700 text-white">Simpan Pengeluaran</Button>
// Neutral create
<Button>Buat Invoice</Button> {/* uses default primary */}
```
