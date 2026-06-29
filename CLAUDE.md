# CLAUDE.md

Panduan untuk Claude Code saat bekerja di repository ini.

## Project Overview

**Finance Management System** â€” Laravel 12 + **Inertia.js + React 18 + shadcn/ui**. Konteks bisnis Indonesia (NPWP/PKP, Terbilang, Rupiah).

Stack frontend **sepenuhnya React/Inertia** â€” migrasi dari Livewire + TallStackUI sudah selesai. Tidak ada lagi komponen Livewire atau view Blade untuk halaman aplikasi (Blade hanya dipakai untuk template PDF). Jangan menulis kode Livewire/TallStackUI/Alpine baru.

**Fitur:** Invoice & Pembayaran, Recurring Invoice, Reimbursement, Bank Account & Transaksi, Cash Flow, Loans & Receivables, Multi-role Permission, PDF Generation, Notifications, Feedback System, Multi-language (id/en/zh), Excel Export.

## Common Commands

```bash
composer dev                           # Start all servers (artisan + queue + vite)
php artisan migrate:fresh --seed       # Fresh migration + seeders
php artisan test                       # Run tests
./vendor/bin/pint                      # Format code
php artisan pail                       # View logs real-time
npm run build                          # Build for production
```

---

## Architecture

**Inertia + React 18** â€” controller Laravel mengembalikan `Inertia::render()` dengan props; halaman React menerimanya.

| Path | Isi |
|------|-----|
| `resources/js/pages/` | Halaman React (per module) |
| `resources/js/components/ui/` | Komponen UI primitif (shadcn/ui + custom) |
| `resources/js/components/shared/` | Komponen shared (PageHeader, DataTable, dll.) |
| `resources/js/types/` | TypeScript types |
| `resources/js/routes/` | Wayfinder generated type-safe routes |
| `app/Http/Controllers/` | Inertia controllers |

### Currency Storage (integer)

Mata uang disimpan sebagai **integer** di DB (rupiah penuh, tanpa desimal).

```
DB: 150000 â†’ Display: Rp 150.000
```

Di React, gunakan komponen `CurrencyInput` (lihat React Component Catalog) yang menangani parse/format otomatis dan menyimpan raw integer.

---

## UI/UX Design System

**CRITICAL: Semua page baru atau redesign HARUS mengikuti design system ini untuk konsistensi.**

**Design System Reference:** `.claude/design-systems/archipelago.md`
Sebelum membuat atau memodifikasi UI apapun, **baca file ini terlebih dahulu**. File tersebut mendokumentasikan token warna (primary + dark scale), tipografi, spacing, radius, layout patterns, interactive states, dan shadcn/ui mapping yang berlaku â€” hasil ekstraksi langsung dari production site dengan DevTools + visual analysis.

**Core Philosophy:** Minimalist, Clean & Readable, Consistent, Functional-First.

---

### Visual Design Tokens

#### Dark Mode Color System

Palette zinc â€” didefinisikan di `app.css` sebagai `--color-dark-*`. **JANGAN hardcode hex** â€” selalu pakai Tailwind class `dark:bg-dark-{n}`, `dark:text-dark-{n}`, dll.

**Hierarki Background (semakin gelap = semakin tinggi angka):**

| Variable | Hex | Dipakai untuk |
|----------|-----|---------------|
| `dark-950` | `#111113` | Body & main content background |
| `dark-900` | `#18181b` | Sidebar & header background |
| `dark-800` | `#1e1e1e` | Input field background (`<input>`, `<select>`, checkbox, textarea, range) |
| `dark-700` | `#27272a` | Card, modal, slide panel, dropdown/floating panel, navbar |
| `dark-600` | `#52525b` | Border utama, hover background item, disabled background |
| `dark-500` | `#71717a` | Divider (`divide-*`, `border-t`) |
| `dark-400` | `#a1a1aa` | Icon, placeholder text, muted text |
| `dark-300` | `#d4d4d8` | Teks konten utama (body text) |
| `dark-200` | `#e4e4e7` | Teks heading / prominent text |
| `dark-50` | `#fafafa` | Teks terang (di atas bg gelap) |

**Konvensi className saat menyusun komponen baru:**

```
// Input / Select trigger
dark:bg-dark-800 dark:text-dark-300 dark:ring-dark-600 dark:placeholder-dark-400

// Dropdown panel / floating container
dark:bg-dark-700 dark:ring-white/10

// Dropdown item (normal)
dark:text-dark-300 dark:hover:bg-dark-600

// Border / separator
dark:border-dark-600

// Icon di dalam input
dark:text-dark-400

// Disabled input
dark:bg-dark-600 dark:text-dark-500

// Label teks
dark:text-dark-300
```

---

#### Typography

- **Headings (h1-h6)**: Plus Jakarta Sans (600, 700, 800)
- **Body Text**: Inter (400, 500, 600, 700)

```css
/* Auto-applied via app.css */
h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
```

---

#### Spacing Rules

| Level | Value |
|-------|-------|
| Root container | `space-y-6` |
| Filter section | `space-y-4` |
| Stats grid gap | `gap-4` |
| Filter grid gap | `gap-3` |
| Section title | `text-xl font-semibold` |
| Label/Info | `text-sm` |
| Small text | `text-xs` |

---

## React Component Catalog (Inertia+React Pages)

**CRITICAL: Semua halaman React WAJIB menggunakan komponen yang ada sebelum menulis raw HTML/JSX. Jangan pernah menulis `<select>`, `<input type="date">`, `<input>` biasa, atau pola UI lainnya secara manual jika sudah ada komponen yang tersedia.**

Semua komponen sudah dikustomisasi penuh sesuai Archipelago design system. Referensi token yang berlaku:
- `primary-*` â€” aksen utama biru
- `dark-50` hingga `dark-950` â€” dark mode zinc scale
- `secondary-200` / `dark-600` â€” border/divider
- `rounded-xl` â€” radius standar semua container

### Komponen UI Primitif (`@/components/ui/`)

| Komponen | Import | Menggantikan | Keterangan |
|----------|--------|-------------|------------|
| `Button` | `@/components/ui/button` | `<button>` manual | Variants: `primary`, `zinc`, `red`, `green`, `yellow`, `blue`, `outline`, `ghost`, `link`. Sizes: `sm`, `md`, `lg`, `xl`, `icon`. |
| `Badge` | `@/components/ui/badge` | Status tag manual | Variants: `default`, `blue`, `green`, `emerald`, `yellow`, `orange`, `red`, `purple`, `zinc`, `outline`. |
| `Input` | `@/components/ui/input` | `<input>` biasa | Props: `label`, `hint`, `error`, `icon`, `iconRight`. |
| `Textarea` | `@/components/ui/textarea` | `<textarea>` biasa | Props: `label`, `hint`, `error`. |
| `Checkbox` | `@/components/ui/checkbox` | `<input type="checkbox">` | `primary-600` checked state. |
| `Switch` | `@/components/ui/switch` | Toggle HTML manual | `primary-600` on, `dark-600` off. |
| `SegmentedControl` | `@/components/ui/segmented-control` | **Tombol pilihan segmented/radio manual** | Pilihan eksklusif berbentuk grup tombol (mis. tipe/prioritas/status). Props: `options` (`{value,label,icon?,activeClassName?}`), `value`, `onChange`, `columns`, `layout` (`stack`\|`inline`), `label`, `error`. Generic atas tipe value. |
| `Label` | `@/components/ui/label` | `<label>` manual | `dark-900 dark:text-dark-300`. |
| `Combobox` | `@/components/ui/combobox` | **`<select>` â€” WAJIB** | Searchable dropdown via `cmdk`. Props: `options`, `value`, `onChange`, `placeholder`, `searchPlaceholder`, `emptyText`. |
| `DatePicker` | `@/components/ui/date-picker` | **`<input type="date">` â€” WAJIB** | Indonesian locale, `primary-600` selected, react-day-picker v10. Single: `value`, `onChange`. Range: tambah `mode="range"`, `value={from,to}`, `placeholderTo`. |
| `Dialog` | `@/components/ui/dialog` | Modal HTML manual | Sizes: `sm`, `md`, `lg`, `xl`, `2xl`, `3xl`, `4xl`, `full`. Exports: `Dialog`, `DialogContent`, `DialogHeader`, `DialogTitle`, `DialogDescription`, `DialogFooter`. |
| `Card` | `@/components/ui/card` | Div wrapper manual | `dark-700`, `rounded-xl`, `shadow-sm`. Exports: `Card`, `CardContent`, `CardHeader`, `CardTitle`, `CardDescription`, `CardFooter`. |
| `Tabs` | `@/components/ui/tabs` | Tab custom HTML | Pill/segment style matching CLAUDE.md spec. Props: `items`, `value`, `onChange`. Export `TabsPanel` untuk content. |
| `Popover` | `@/components/ui/popover` | Tooltip/flyout manual | `dark-700` bg, `rounded-xl`. Exports: `Popover`, `PopoverTrigger`, `PopoverContent`, `PopoverAnchor`. |
| `DropdownMenu` | `@/components/ui/dropdown-menu` | Dropdown manual | `dark-700` panel, `dark-600` hover. Exports: `DropdownMenu`, `DropdownMenuTrigger`, `DropdownMenuContent`, `DropdownMenuItem`, `DropdownMenuSeparator`, dll. |
| `Tooltip` | `@/components/ui/tooltip` | `title=` attribute | Exports: `Tooltip`, `TooltipContent`, `TooltipProvider`, `TooltipTrigger`. |
| `Avatar` | `@/components/ui/avatar` | Initials div manual | `primary-100/900` fallback bg. Exports: `Avatar`, `AvatarImage`, `AvatarFallback`. |
| `Skeleton` | `@/components/ui/skeleton` | Loading state manual | `dark-700` animate-pulse. |
| `ScrollArea` | `@/components/ui/scroll-area` | `overflow-y-auto` manual | `dark-600` scrollbar thumb. Exports: `ScrollArea`, `ScrollBar`. |
| `Separator` | `@/components/ui/separator` | `<hr>` / border manual | `secondary-200 dark:dark-600`. Horizontal dan vertical. |

### Komponen Shared (`@/components/shared/`)

| Komponen | Import | Keterangan |
|----------|--------|------------|
| `PageHeader` | `@/components/shared/page-header` | Header halaman dengan gradient title. Props: `title`, `description`, `action`. WAJIB di semua halaman utama. |
| `StatsCard` | `@/components/shared/stats-card` | Stats card horizontal. Props: `title`, `value`, `icon`, `color` (`blue`\|`green`\|`purple`\|`red`\|`emerald`\|`yellow`\|`orange`\|`zinc`), `inModal` (boolean). |
| `FormSection` | `@/components/shared/form-section` | Section header dalam form. Props: `title`, `description`. Gunakan sebagai pengganti div header manual dalam form. |
| `DataTable` | `@/components/shared/data-table` | Tabel data dengan sorting. Props: `columns`, `data`, `loading`, `emptyState`. Berbasis `@tanstack/react-table`. |
| `Pagination` | `@/components/shared/pagination` | Pagination navigasi. Props: `currentPage`, `lastPage`, `perPage`, `total`, `onPageChange`. |
| `EmptyState` | `@/components/shared/empty-state` | State kosong. Props: `icon`, `title`, `description`, `action`. |
| `ConfirmDialog` | `@/components/shared/confirm-dialog` | Dialog konfirmasi. Props: `open`, `onOpenChange`, `onConfirm`, `title`, `description`, `variant` (`danger`\|`warning`). |
| `CurrencyInput` | `@/components/shared/currency-input` | **Input Rupiah â€” WAJIB untuk semua input mata uang.** Props: `value`, `onChange`, `label`, `hint`, `error`. Format integer, display `Rp 1.500.000`. |
| `FileUpload` | `@/components/shared/file-upload` | **Upload file/gambar — WAJIB, bukan `<input type="file">`.** Berbasis react-dropzone. Props: `value` (File|null), `onChange`, `label`, `hint`, `error`, `accept` (array ekstensi). Single file. |

### Quick Decision: "Mana yang harus saya pakai?"

```
Input teks/angka biasa         â†’ Input
Input Rupiah/currency          â†’ CurrencyInput  â† JANGAN input biasa
Pilihan dari daftar (select)   â†’ Combobox       â† JANGAN <select> HTML
Pilihan tanggal (single)       â†’ DatePicker     â† JANGAN <input type="date">
Pilihan rentang tanggal        â†’ DatePicker mode="range"
Toggle on/off                  â†’ Switch
Pilihan eksklusif (radio/segment) â†’ SegmentedControl  â† JANGAN tombol custom
Yes/No checkbox                â†’ Checkbox
Label standalone               â†’ Label
Teks area panjang              â†’ Textarea
Upload file/lampiran           â†’ FileUpload      â† JANGAN <input type="file">
Preview/pilih gambar           â†’ FileUpload
Tombol aksi                    â†’ Button (pilih variant sesuai context)
Status/kategori label          â†’ Badge (pilih variant warna sesuai status)
Header halaman                 â†’ PageHeader
Stats di halaman utama         â†’ StatsCard
Stats di dalam modal           â†’ StatsCard dengan inModal={true}
Section header form            â†’ FormSection
Tabel data                     â†’ DataTable
Konfirmasi hapus/aksi kritis   â†’ ConfirmDialog
State kosong (no data)         â†’ EmptyState
Navigasi halaman               â†’ Pagination
Tab navigasi                   â†’ Tabs + TabsPanel
Kartu container                â†’ Card + CardContent
Modal/dialog                   â†’ Dialog + DialogContent dll.
Dropdown menu                  â†’ DropdownMenu dll.
Tooltip                        â†’ TooltipProvider + Tooltip + TooltipTrigger + TooltipContent
Popover/flyout                 â†’ Popover + PopoverTrigger + PopoverContent
Avatar user                    â†’ Avatar + AvatarFallback
Scroll area custom             â†’ ScrollArea
Pemisah visual                 â†’ Separator
Loading skeleton               â†’ Skeleton
```

---

## Modules Overview

### Modules & Routes

| Module | Route | Sub-views / Actions |
|--------|-------|------------|
| Dashboard | `/dashboard` | `Dashboard.php` |
| Clients | `/clients` | Index, Create, Edit, Delete, Show, Relationship |
| Services | `/services` | Index, Create, Edit, Delete |
| Invoices | `/invoices` | Index, Listing, Create, Edit, Delete, Show |
| Payments | (embedded) | Listing, Create, Edit, Delete, AttachmentViewer |
| Recurring | `/recurring-invoices` | Index, TemplatesTab, MonthlyTab, AnalyticsTab + CRUD |
| Bank Accounts | `/bank-accounts` | Index, Create, Edit, Delete, QuickActionsOverview |
| Cash Flow | `/cash-flow` | Index, OverviewTab, IncomeTab, ExpensesTab, TransfersTab |
| Transactions | `/bank-accounts` | Listing, Create, CreateIncome, CreateExpense, Delete, Categorize, Transfer |
| Tx Categories | `/transaction-categories` | Index, Create, Update, Delete |
| Reimbursements | `/reimbursements` | Index, AllRequests, MyRequests, Create, Update, Delete, Show, Review, Payment |
| Fund Requests | `/fund-requests` | Index, AllRequests, MyRequests, Create, Edit, Delete, Show, Review, Disburse |
| Loans | `/loans` | Index, Create, Update, Delete, PayLoan |
| Receivables | `/receivables` | Index, Create, Update, Delete, Submit, Approve, PayReceivable |
| Feedbacks | `/feedbacks` | Index, AllFeedbacks, MyFeedbacks, Create, Update, Delete, Show, Respond |
| Settings | `/settings/*` | Profile, Password, CompanyProfileSettings, DeleteUserForm |
| Users | `/admin/users` | Index, Create, Edit, Delete |
| Permissions/Roles | `/permissions` | Index, Delete + Roles: Create, Update, Delete |
| Auth | â€” | Login, Register, ForgotPassword, ResetPassword, VerifyEmail, ConfirmPassword |
| Utility | â€” | LanguageSwitcher, Notifications/Bell, Notifications/Drawer, FloatingFeedbackButton |

### Key Business Logic

**Invoice:** `INV/{seq}/KSN/{mm}.{yy}` â€” status: `draft â†’ partially_paid â†’ paid`
**Reimbursement:** `draft â†’ pending â†’ approved â†’ paid` (or `rejected`)
**Fund Request:** `draft â†’ pending â†’ approved â†’ disbursed` (or `rejected`); format `001/KSN/I/2026`
**Recurring:** Manual generation (no scheduled tasks); frequencies: monthly/quarterly/semi_annual/annual
**Bank Balance:** COMPUTED dynamically â€” `initial_balance + payments(credit) + tx(credit) - tx(debit)` â€” NOT stored
**Receivable Debtor:** Polymorphic â€” User OR Client

**Notification events:** `Bell â†’ dispatch('open-notification-drawer') â†’ Drawer` / `Drawer â†’ dispatch('notification-read') â†’ Bell`

---

## Models Reference

### Key Models

| Model | File | Notes |
|-------|------|-------|
| Invoice | `app/Models/Invoice.php` | Accessors: amount_paid, amount_remaining, total_cogs, gross_profit; `updateStatus()` |
| InvoiceItem | `app/Models/InvoiceItem.php` | Fields: quantity, unit, unit_price, cogs_amount, is_tax_deposit |
| Payment | `app/Models/Payment.php` | belongsTo Invoice + BankAccount |
| BankAccount | `app/Models/BankAccount.php` | balance = computed accessor, NOT stored |
| BankTransaction | `app/Models/BankTransaction.php` | transaction_type: `credit` \| `debit` |
| TransactionCategory | `app/Models/TransactionCategory.php` | Hierarchical (parent_code); accessor: `full_path` |
| Reimbursement | `app/Models/Reimbursement.php` | submit/approve/reject/recordPayment + canEdit/canDelete/canSubmit/canReview/canPay |
| FundRequest | `app/Models/FundRequest.php` | submit/approve/reject/disburse + check methods |
| RecurringTemplate | `app/Models/RecurringTemplate.php` | calculateNextGenerationDate(), isDueForGeneration() |
| RecurringInvoice | `app/Models/RecurringInvoice.php` | publish() converts draft â†’ actual Invoice |
| Client | `app/Models/Client.php` | type: `individual` \| `company`; self-referential owners/companies |
| CompanyProfile | `app/Models/CompanyProfile.php` | Singleton via `current()`; base64 logo/signature/stamp for PDF |
| Receivable | `app/Models/Receivable.php` | Polymorphic debtor (User OR Client) |
| AppNotification | `app/Models/AppNotification.php` | `notify()` factory method; `cleanupOld($days)` |

---

## Permission System

**Roles:** `admin` (full access), `finance manager` (no user mgmt), `staff` (view/create own)

**50 permissions across modules:** view/create/edit/delete + module-specific (approve, pay, publish, disburse, manage, respond)

```php
Route::middleware('can:view invoices')->name('invoices.index')
```

Reset cache: `php artisan permission:cache-reset`

---

## PDF Generation

**Service:** `app/Services/InvoicePrintService.php`

**Templates:** `kisantra-invoice` (default), `semesta-invoice`, `agsa-invoice`, `invoice`

```php
GET /invoice/{invoice}/download?dp_amount=X&pelunasan_amount=Y&template=kisantra-invoice
$pdf = (new InvoicePrintService())->generateSingleInvoicePdf($invoice, $dp, $pelunasan, $template);
```

---

## Tech Stack

**Backend:** Laravel 12, Spatie Permission 6.21, DomPDF 3.1, Maatwebsite Excel 3.1, ngekoding/terbilang, PHP 8.4

**Frontend (Inertia + React):** React 18, TypeScript, Inertia.js 3, shadcn/ui, Tailwind CSS 4.1, Wayfinder (type-safe routes), react-hook-form + zod, @tanstack/react-table, react-day-picker v10, cmdk, Sonner, i18next + react-i18next, react-apexcharts, react-chartjs-2, react-dropzone, Vite 6.0

> Blade dipertahankan **hanya** untuk template PDF (`resources/views/pdf/`). Tidak ada Livewire/TallStackUI/Alpine.

---

## Development Guidelines

### Translation & Localization Protocol

Aplikasi mendukung **id / en / zh**. Locale aktif dibagikan ke frontend lewat prop Inertia `locale` (`HandleInertiaRequests::share()`).

**Status frontend i18n:** `i18next` + `react-i18next` sudah terpasang di `package.json`, **tetapi belum diinisialisasi** di `resources/js` (belum ada provider/init, belum ada loader yang menarik `lang/*.php` ke bundle JS). Saat ini halaman React belum punya konvensi translation yang mapan. **Sebelum menambah teks UI baru yang perlu i18n, konfirmasi dulu pendekatan yang diinginkan** (mis. setup `react-i18next` dengan resource JSON, atau mengekspor `lang/*.php` ke JSON saat build). Jangan mengarang pola tanpa konfirmasi.

**Server-side `lang/` masih aktif** dan dipakai untuk: pesan validasi, template PDF (Blade), dan heading Excel export.

```
lang/
├── id/  (UTAMA) — common.php, pages.php, invoice.php, feedback.php, validation.php
├── en/  (partial)
└── zh/  (sync dengan id/ — salin nilai sama, terjemahan Mandarin diupdate terpisah)
```

**Aturan saat menyentuh `lang/`:** key baru ditambah ke `lang/id/` lalu langsung ke `lang/zh/` (nilai sama dulu). `common.php` untuk string reusable; `pages.php` dengan prefix module (`fund_request_title`, `fund_request_status_draft`).

### Currency

Mata uang disimpan sebagai **integer rupiah penuh** (lihat [Currency Storage](#currency-storage-integer)).

Di React: **SELALU gunakan komponen `CurrencyInput`** (`@/components/shared/currency-input`) untuk semua input mata uang — bukan `Input` biasa. Komponen ini menyimpan raw integer dan menampilkan `Rp 1.500.000`. Untuk display read-only, format dengan `Intl.NumberFormat('id-ID')` atau helper format rupiah yang ada.

### Component Usage Protocol

**CRITICAL: Sebelum implementasi ANY external component — baca dokumentasi resminya dulu (WebSearch + WebFetch atau MCP shadcn). Jangan implement berdasarkan asumsi atau memory.**

Berlaku untuk: shadcn/ui components, library React (react-day-picker, cmdk, react-hook-form, dll.), dan Laravel packages.


## Troubleshooting

| Issue | Solution |
|-------|----------|
| Permission issues | `php artisan permission:cache-reset` |
| PDF not generating | Cek `storage/` writable, `php artisan storage:link`, company profile has assets |
| Inertia props not updating | Pastikan controller mengirim ulang prop; gunakan `router.reload({ only: [...] })` untuk partial reload |
| Balance incorrect | Balance is computed dynamically, NOT stored â€” cek transaction types |
| Currency display | Simpan integer; tampilkan dengan `CurrencyInput` atau `Intl.NumberFormat('id-ID')` |
| Vite/React changes not visible | Run `npm run build` atau `composer run dev` |

---

## Important Files Reference

| File | Purpose |
|------|---------|
| `.claude/design-systems/archipelago.md` | Design system tokens, typography, spacing, color palette |
| `app/Services/InvoicePrintService.php` | PDF generation |
| `app/Models/Invoice.php` | Status & profit calculations |
| `app/Models/Reimbursement.php` | Workflow state machine |
| `app/Models/RecurringTemplate.php` | Recurring date calculations |
| `app/Models/BankAccount.php` | Dynamic balance |
| `app/Http/Middleware/HandleInertiaRequests.php` | Shared Inertia props (auth, locale, flash, notifications) |
| `app/helpers.php` | translate_text(), translate_category() |
| `database/seeders/MasterPermissionSeeder.php` | Permission structure |
| `routes/web.php` | All route definitions |
| `resources/views/pdf/kisantra-invoice.blade.php` | Main invoice template |
| `resources/css/app.css` | Color variables, fonts, @source tracking |
| `resources/js/components/ui/` | React UI primitives (shadcn/ui + custom) |
| `resources/js/components/shared/` | React shared components (PageHeader, DataTable, dll.) |
| `resources/js/pages/` | Inertia React pages (per module) |

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.8
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domainâ€”don't wait until you're stuck.

- `livewire-development` â€” Develops reactive Livewire 3 components. Activates when creating, updating, or modifying Livewire components; working with wire:model, wire:click, wire:loading, or any wire: directives; adding real-time updates, loading states, or reactivity; debugging component behavior; writing Livewire tests; or when the user mentions Livewire, component, counter, or reactive UI.
- `volt-development` â€” Develops single-file Livewire components with Volt. Activates when creating Volt components, converting Livewire to Volt, working with @volt directive, functional or class-based Volt APIs; or when the user mentions Volt, single-file components, functional Livewire, or inline component logic in Blade files.
- `tailwindcss-development` â€” Styles applications using Tailwind CSS v4 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces using only PHP â€” no JavaScript required.
- Instead of writing frontend code in JavaScript frameworks, you use Alpine.js to build the UI when client-side interactions are required.
- State lives on the server; the UI reflects it. Validate and authorize in actions (they're like HTTP requests).
- IMPORTANT: Activate `livewire-development` every time you're working with Livewire-related tasks.

=== volt/core rules ===

# Livewire Volt

- Single-file Livewire components: PHP logic and Blade templates in one file.
- Always check existing Volt components to determine functional vs class-based style.
- IMPORTANT: Always use `search-docs` tool for version-specific Volt documentation and updated code examples.
- IMPORTANT: Activate `volt-development` every time you're working with a Volt or single-file component-related task.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.

</laravel-boost-guidelines>
