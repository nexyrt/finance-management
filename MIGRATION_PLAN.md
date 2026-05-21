# Migration Plan: Livewire → Inertia.js + React

> **Dokumen ini adalah panduan kerja untuk Claude Code.**
> Dibuat: 2026-05-11 | Branch aktif: `feature/inertia-react-migration`
> Update dokumen ini setiap kali fase selesai.
> **Terakhir diupdate: 2026-05-21 — Fase 14 selesai: Testing (76 tests, SQLite compat fixes, new controller tests).**

---

## Konteks

Migrasi penuh dari **Laravel 12 + Livewire 3 + TallStackUI v2** ke **Laravel 12 + Inertia.js + React 18 + shadcn/ui**.

**Aturan deployment:** Migrasi harus **100% selesai** sebelum merge ke `main` dan deploy ke server.

### Stack Sebelum (main branch)
- Laravel 12, Livewire 3.6, TallStackUI v2, Alpine.js 3
- 124 Livewire components, semua validasi inline (0 Form Request)
- Blade views dengan custom dark mode color system (`dark-*` variables)

### Stack Setelah (migration branch)
- Laravel 12, Inertia.js, React 18, TypeScript, shadcn/ui
- Wayfinder untuk type-safe route helpers (generated TypeScript at build time)
- react-hook-form + zod untuk form validation
- TanStack React Table untuk data tables
- Sonner untuk toast notifications
- i18next + react-i18next untuk multi-language (id/en/zh)
- react-apexcharts + react-chartjs-2 untuk charts
- react-dropzone untuk file uploads
- cmdk untuk searchable select
- Tiptap React menggantikan Quill

### Branch Strategy
```
main                          → Production (frozen, tidak disentuh sampai migrasi selesai)
feature/inertia-react-migration → Semua pekerjaan migrasi
```

**Untuk membaca file dari main saat di migration branch:**
```bash
git show main:path/to/file.php
```

---

## Status Keseluruhan

| Fase | Nama | Status |
|------|------|--------|
| 0 | Foundation Setup | ✅ Selesai (2026-05-11) |
| 1 | Design System (shadcn/ui) | ✅ Selesai (2026-05-11) |
| 2 | AppLayout.tsx | ✅ Selesai (2026-05-11) |
| 3 | Auth Pages | ✅ Selesai (2026-05-12) |
| 4 | Master Data | ✅ Selesai (2026-05-12) |
| 5 | Invoice & Payment | ✅ Selesai (2026-05-13) |
| 6 | Recurring Invoices | ✅ Selesai (2026-05-13) |
| 7 | Banking (Accounts + Cash Flow + Transactions) | ✅ Selesai (2026-05-20) |
| 8 | Operations (Reimbursements + Fund Requests) | ✅ Selesai (2026-05-20) |
| 9 | Finance (Loans + Receivables) | ✅ Selesai (2026-05-20) |
| 10 | Admin (Users + Permissions + Settings) | ✅ Selesai (2026-05-21) |
| 11 | Utility Components & Dashboard | ✅ Selesai (2026-05-21) |
| 12 | Backend Refactoring (Controllers + Form Requests) | ✅ Selesai (2026-05-21) |
| 13 | PDF Integration | ✅ Selesai (2026-05-21) |
| 14 | Testing | ✅ Selesai (2026-05-21) |
| 15 | Cleanup & Deployment Prep | ⬜ Belum Dimulai |

**Legend:** ⬜ Belum Dimulai | 🔄 Sedang Dikerjakan | ✅ Selesai

---

## Fase 0 — Foundation Setup ✅ SELESAI

**Tujuan:** Install semua dependency, konfigurasi Vite dual entry, setup middleware Inertia.

**Commits:**
- `0b3555e` — feat(phase-0): setup Inertia.js + React foundation
- `e6a94df` — refactor(phase-0): replace Ziggy with Wayfinder for type-safe routing

**Catatan:** `@tiptap/react` ditunda ke Fase 11 (conflict Tiptap v2 vs v3). shadcn/ui CLI init dilakukan manual di Fase 1 (hindari overwrite app.css).

### Checklist

#### Server-side (Composer)
- [x] `composer require inertiajs/inertia-laravel` → v3.1.0
- [x] Publish dan register `HandleInertiaRequests` middleware di `bootstrap/app.php`
- [x] Setup shared props di middleware: `auth.user`, `auth.permissions`, `auth.roles`, `locale`, `flash`
- [x] Install Wayfinder: `composer require laravel/wayfinder` (menggantikan Ziggy)
- [x] Buat root Blade template `resources/views/app.blade.php` (Inertia entry point)

#### Client-side (NPM) ✅
- [x] React 18 + react-dom + @types/react + @types/react-dom
- [x] TypeScript + @vitejs/plugin-react v4.3 (kompatibel Vite 6)
- [x] @inertiajs/react + Wayfinder (menggantikan Ziggy — type-safe, zero runtime overhead)
- [x] laravel/wayfinder v0.1.18 (composer) + @laravel/vite-plugin-wayfinder (npm)
- [x] i18next + react-i18next
- [x] sonner (toast)
- [x] react-hook-form + @hookform/resolvers + zod
- [x] @tanstack/react-table
- [x] react-dropzone + cmdk + react-apexcharts
- [x] class-variance-authority + clsx + tailwind-merge + lucide-react
- [x] Semua Radix UI primitives (dialog, dropdown, select, tabs, tooltip, popover, checkbox, switch, avatar, separator, label, slot, scroll-area, collapsible, accordion)
- [ ] `@tiptap/react` — ditunda ke Fase 11 (conflict v2 vs v3)
- [x] `shadcn/ui init` — dilakukan manual (hindari overwrite app.css), `components.json` dibuat manual

#### Vite Dual Entry ✅
```js
// vite.config.js — kedua entry aktif, npm run build sukses
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/inertia.tsx'],
    refresh: true,
})
```

#### TypeScript Config ✅
- [x] `tsconfig.json` dengan `"jsx": "react-jsx"`, path alias `@/*` → `resources/js/*`
- [x] `resources/js/inertia.tsx` entry point dengan `createInertiaApp`

#### Struktur Folder React ✅
```
resources/js/
├── app.js              ← Livewire entry (JANGAN DIUBAH sampai Fase 15)
├── inertia.tsx         ← Inertia/React entry ✅
├── components/
│   ├── ui/             ← shadcn/ui base components (diisi Fase 1)
│   ├── layout/         ← AppLayout, Sidebar, dll (diisi Fase 2)
│   └── shared/         ← DataTable, CurrencyInput, dll (diisi Fase 1)
├── pages/
│   ├── welcome.tsx     ← placeholder page ✅
│   └── auth/           ← (diisi Fase 3)
├── hooks/
├── lib/
│   └── utils.ts        ← cn(), formatCurrency(), parseCurrency() ✅
└── types/
    └── index.d.ts      ← SharedProps, User, Auth, Flash types ✅
```

---

## Fase 1 — Design System (shadcn/ui) ✅ SELESAI

**Tujuan:** Setup semua komponen UI yang akan digunakan di seluruh project.

**Commit:** feat(phase-1): add React design system components (shadcn/ui + custom shared)

### Pemetaan TallStackUI → React/shadcn

| TallStackUI | React Equivalent | Notes |
|-------------|-----------------|-------|
| `x-button` | `<Button>` (shadcn) | variants: primary, zinc, red, green, yellow |
| `x-input` | `<Input>` (shadcn) | + label, hint, error state |
| `x-select.styled` | `cmdk` + popover | Searchable dropdown |
| `x-modal` | `<Dialog>` (shadcn) | size variants: sm, md, lg, xl, 2xl |
| `x-table` | TanStack React Table | sortable, paginated, selectable |
| `x-badge` | `<Badge>` (shadcn) | color variants |
| `x-card` | `<Card>` (shadcn) | hover shadow variant |
| `x-icon` | `lucide-react` | Heroicons equivalent |
| `x-date` | `<DatePicker>` custom | react-day-picker + popover |
| `x-currency-input` | `<CurrencyInput>` custom | stores raw integer, display Rp |
| `x-textarea` | `<Textarea>` (shadcn) | |
| `x-checkbox` | `<Checkbox>` (shadcn) | |
| `x-toggle` | `<Switch>` (shadcn) | |
| `x-avatar` | `<Avatar>` (shadcn) | |
| `x-tooltip` | `<Tooltip>` (shadcn) | |
| `x-tab` | Custom Alpine tabs | Pill/segment style |
| `x-alert` | Sonner `toast.*` | |
| `x-errors` | zod + react-hook-form | inline per field |
| `x-loading` | Inertia `useForm` loading | |
| TallStackUI Interactions | Sonner | toast, confirm dialog |
| `x-currency-input` | Custom component | `<CurrencyInput>` |
| Quill editor | Tiptap React | |
| `x-repeater` | Custom `<Repeater>` | dinamis add/remove rows |
| `x-upload` | react-dropzone | |
| ApexCharts | react-apexcharts | |
| Chart.js | react-chartjs-2 | |

### Komponen UI Base (resources/js/components/ui/) ✅
- [x] `button.tsx` — cva variants: primary, zinc, red, green, yellow, blue, outline, ghost, link
- [x] `badge.tsx` — cva variants: default, secondary, blue, green, emerald, yellow, orange, red, purple, zinc, outline
- [x] `card.tsx` — Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter
- [x] `input.tsx` — label, hint, error, icon left/right
- [x] `textarea.tsx` — label, hint, error
- [x] `dialog.tsx` — size variants sm→full, DialogHeader/Footer/Title/Description
- [x] `tabs.tsx` — custom pill/segment style (per design system, NOT Radix Tabs)
- [x] `combobox.tsx` — cmdk + popover, searchable, clearable
- [x] `date-picker.tsx` — react-day-picker + date-fns, id locale, clearable, min/max date
- [x] `label.tsx`, `separator.tsx`, `skeleton.tsx`
- [x] `checkbox.tsx`, `switch.tsx`, `avatar.tsx`
- [x] `tooltip.tsx`, `popover.tsx`, `scroll-area.tsx`, `dropdown-menu.tsx`

### Komponen Shared (resources/js/components/shared/) ✅
- [x] `currency-input.tsx` — stores integer, display `Rp X.XXX`
- [x] `data-table.tsx` — TanStack Table v8 wrapper, sortable, server-side pagination
- [x] `confirm-dialog.tsx` — danger/warning variants dengan icon
- [x] `page-header.tsx` — gradient title + description + action slot (sesuai design system)
- [x] `stats-card.tsx` — horizontal layout dengan icon + inModal variant
- [x] `form-section.tsx` — section header dengan border-bottom
- [x] `empty-state.tsx` — icon + title + description + action
- [x] `pagination.tsx` — page number buttons dengan ellipsis, first/last/prev/next
- [x] `file-upload.tsx` — drag-drop + click + clipboard paste (Ctrl+V), existing file chip, size/type validation (ditambahkan Fase 5)

**Catatan:** `Repeater` dan `StatusBadge` dibangun inline saat dibutuhkan per-module (Fase 4+).

**Catatan dialog animation:** `tailwindcss-animate` tidak ter-install. Animasi Dialog menggunakan `@keyframes` + `@utility` custom di `app.css` dengan `data-[state=open/closed]` attributes dari Radix UI. Keyframes menggunakan opacity-only untuk menghindari konflik dengan Tailwind centering transform.

---

## Fase 2 — AppLayout.tsx ✅ SELESAI

**Tujuan:** Konversi layout utama 642-line Blade ke React.

**File referensi:** `resources/views/components/layouts/app.blade.php`

**Commit:** feat(phase-2): add AppLayout with sidebar, header, dark mode

### Checklist
- [x] `resources/js/layouts/app-layout.tsx` — wrapper dengan dark mode + sidebar state management
- [x] `resources/js/layouts/sidebar.tsx` — sidebar collapsible + nav sections + permission-based visibility + user dropdown
- [x] `resources/js/layouts/header.tsx` — breadcrumbs + dark mode toggle + language switcher + notification bell (stub)
- [x] Dark mode init script di `resources/views/app.blade.php` (localStorage `theme` key, no FOUC)
- [x] `POST /language` route untuk language switching (session + user preference)
- [x] Route active state detection via `usePage().url`
- [x] `welcome.tsx` diupdate sebagai contoh penggunaan `AppLayout`

**Catatan:**
- Notification bell: stub hanya icon, real implementation di Fase 11
- Floating feedback button: stub, Fase 11
- Dark mode key: `theme` (bukan `tallstackui.theme` dari versi lama)
- Sidebar localStorage key: `sidebar.collapsed` (sama dengan versi lama)

---

## Fase 3 — Auth Pages ✅ SELESAI

**Livewire source:** `app/Livewire/Auth/`
**Blade source:** `resources/views/livewire/auth/`

| Halaman | Status |
|---------|--------|
| Login | ✅ |
| Register | N/A (tidak digunakan di production) |
| ForgotPassword | ✅ |
| ResetPassword | ✅ |
| VerifyEmail | ✅ |
| ConfirmPassword | ✅ |

**Controllers dibuat:**
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php` — login + logout
- `app/Http/Controllers/Auth/PasswordResetLinkController.php` — forgot password
- `app/Http/Controllers/Auth/NewPasswordController.php` — reset password
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php` — verify email
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php` — resend verification
- `app/Http/Controllers/Auth/ConfirmablePasswordController.php` — confirm password

**Halaman React dibuat:**
- `resources/js/pages/auth/login.tsx` — email + password + remember + show/hide toggle
- `resources/js/pages/auth/forgot-password.tsx` — email → send reset link
- `resources/js/pages/auth/reset-password.tsx` — token + email + password + confirm
- `resources/js/pages/auth/verify-email.tsx` — resend button + logout button
- `resources/js/pages/auth/confirm-password.tsx` — password confirm + security shield icon

**Layout:**
- `resources/js/layouts/auth-layout.tsx` — split-screen: dark navy hero (left) + white form (right)
- `.layout` property pattern (sama dengan dashboard.tsx: `Login.layout = (page) => <AuthLayout>{page}</AuthLayout>`)

**routes/auth.php** sepenuhnya dikonversi dari Livewire ke Inertia controller routes.

---

## Fase 4 — Master Data ✅ SELESAI

### Clients ✅
**Controller:** `app/Http/Controllers/ClientController.php`
**Page:** `resources/js/pages/clients/index.tsx`
- Stats: total, aktif, individu, perusahaan
- Table: nama, tipe, kontak, status, invoice count, finansial (total + outstanding)
- Create/Edit modal (2-column: info dasar + data pajak & kontak)
- Delete confirm (cascade delete: invoice items + invoices)
- Filter: search (nama/email/NPWP) + tipe + status

### Services ✅
**Controller:** `app/Http/Controllers/ServiceController.php`
**Page:** `resources/js/pages/services/index.tsx`
- Stats: total, avg price, highest price
- Table: nama, kategori (badge warna), harga, created_at
- Create/Edit modal (nama + kategori + harga)
- Delete confirm
- Filter: search + kategori (Perizinan, Administrasi Perpajakan, Digital Marketing, Sistem Digital)

### Transaction Categories ✅
**Controller:** `app/Http/Controllers/TransactionCategoryController.php`
**Page:** `resources/js/pages/transaction-categories/index.tsx`
- Stats: total, parent, sub, aktif digunakan
- Table: tipe (badge), nama, parent, usage count
- Create/Edit modal (tipe toggle + nama + parent dropdown—filtered by type)
- Delete disabled jika ada transaksi atau sub-kategori
- Guard di controller: cek children + transactions sebelum delete

---

## Fase 5 — Invoice & Payment ✅ SELESAI

**Route:** `/invoices`
**Livewire source:** `app/Livewire/Invoices/`, `app/Livewire/Payments/`

**Commit:** feat(phase-5): add Invoice & Payment CRUD with InvoiceDrawer, PaymentFormModal, FileUpload

### Invoice
| Component | Status |
|-----------|--------|
| Index (stats + pipeline + tabs + filter + table) | ✅ 2026-05-13 |
| Create (form + repeater items) | ✅ 2026-05-12 |
| Edit | ✅ 2026-05-12 |
| Delete (ConfirmDialog inline di index) | ✅ 2026-05-13 |
| Show (InvoiceDrawer slide-over di index) | ✅ 2026-05-13 |

**Catatan implementasi:**
- Stats card: accent bar top + tooltip pattern (design system baru)
- Status pipeline bar: proportional segments, clickable to filter
- Tabs: `variant="underline"` dengan badge count per status
- Filter: Combobox (klien) + DatePicker month/range + Input search — semua URL-based (`period_mode` param)
- Baris tabel clickable → buka InvoiceDrawer
- Controller: `InvoiceController@index` — mendukung period_mode, date_from, date_to

### Layout Redesign: Invoice Create & Edit (2026-05-14)

Layout `invoices/create.tsx` dan `invoices/edit.tsx` diubah dari flat single-column ke **2-column sticky grid**:

- **Kiri (4/5)** — dua card: "Detail Invoice" (nomor, klien, tanggal) + "Item Invoice" (compact repeater table)
- **Kanan (1/5)** — sticky summary panel: subtotal, titipan pajak, diskon accordion, total `2xl`, laba kotor & Est. PPh 0.5% pills, tombol submit + batal
- Grid: `xl:grid-cols-5` — kiri `xl:col-span-4`, kanan `xl:col-span-1 xl:sticky xl:top-6`
- Diskon: accordion collapsible dengan `ChevronDown` rotate-180
- `CurrencyCell` dan `ServiceLookup` di-export dari `create.tsx` untuk digunakan ulang di template pages
- Focus ring dihilangkan dari search input ServiceLookup (`focus:ring-0`)
- `onError` callback menampilkan pesan error aktual dari server (bukan pesan generik)

### Payment
| Component | Status |
|-----------|--------|
| Listing (embedded di InvoiceDrawer) | ✅ 2026-05-13 |
| Create (PaymentFormModal di InvoiceDrawer) | ✅ 2026-05-13 |
| Edit (PaymentFormModal di InvoiceDrawer) | ✅ 2026-05-13 |
| Delete (ConfirmDialog di InvoiceDrawer) | ✅ 2026-05-13 |
| AttachmentViewer (via attachment_url link) | ✅ 2026-05-13 |

**Business logic penting:**
- Invoice number format: `INV/{seq}/KSN/{mm}.{yy}`
- Status flow: `draft → partially_paid → paid`
- Currency: simpan integer (150000 = Rp 1.500)
- PDF download via `GET /invoice/{invoice}/download?template=kisantra-invoice`

---

## Fase 6 — Recurring Invoices ✅ SELESAI

**Route:** `/recurring-invoices`
**Livewire source:** `app/Livewire/RecurringInvoices/`

**Commits:**
- `feat(phase-6): add Inertia Recurring Invoices page (index, templates, monthly, analytics)`
- `feat(phase-6): migrate template create/edit from modal to dedicated Inertia pages`

| Component | Status |
|-----------|--------|
| Index (tabs) | ✅ 2026-05-13 |
| TemplatesTab | ✅ 2026-05-14 — navigasi ke halaman dedicated, `TemplateFormModal` dihapus |
| MonthlyTab | ✅ 2026-05-13 — MonthlyFormModal, GenerateModal, PublishModal, BulkPublishModal |
| AnalyticsTab | ✅ 2026-05-13 — ApexCharts bar chart + template stats + status breakdown |
| Create Template | ✅ 2026-05-14 — halaman `create-template.tsx` (dedicated full-page, bukan modal) |
| Edit Template | ✅ 2026-05-14 — halaman `edit-template.tsx` (dedicated full-page, bukan modal) |
| Delete/Archive Template | ✅ 2026-05-13 — ConfirmDialog + auto-archive jika ada published invoices |
| Generate Invoice (from template) | ✅ 2026-05-13 — GenerateModal + bulkPublishMonthly |

**Notes:**
- Controller: `RecurringInvoiceController.php` (14 endpoints — CRUD templates + monthly + analytics + 2 Inertia page methods)
- `createTemplate()` dan `editTemplate()` di controller: render halaman Inertia (bukan JSON)
- `storeTemplate()` dan `updateTemplate()`: detect `X-Inertia` header → redirect (Inertia) atau JSON response (API)
- URL-based filter navigation: `?tab=`, `?month=`, `?year=` via `router.get()`
- Bulk actions: select-all, bulk-destroy, bulk-publish
- Template form: sama komponen `TemplateForm` di-share antara create dan edit via export/import
- Template form layout: 4/5+1/5 sticky grid (identik dengan invoice create/edit)
- Template fields tambahan vs invoice: `template_name`, `frequency` (pill buttons), `start_date`, `end_date`
- Item `quantity` di template = integer (bukan float seperti di invoice biasa)
- Item `unit` field disertakan (autocomplete dari `datalist` 17 satuan umum)
- Reuse komponen: `CurrencyCell` + `ServiceLookup` diimport dari `@/pages/invoices/create`
- Analytics: ApexCharts bar chart, template performance table, status breakdown dengan progress bars

**Routes baru (GET, untuk Inertia pages):**
```php
Route::get('/templates/create', [RecurringInvoiceController::class, 'createTemplate'])
Route::get('/templates/{template}/edit', [RecurringInvoiceController::class, 'editTemplate'])
```

---

## Fase 7 — Banking

### Bank Accounts ✅ SELESAI (2026-05-20)
**Route:** `/bank-accounts`
**Livewire source:** `app/Livewire/Accounts/` + `app/Livewire/Transactions/`

| Component | Status |
|-----------|--------|
| Index (master-detail) | ✅ |
| Create + Edit (unified dialog) | ✅ |
| Delete (ConfirmDialog) | ✅ |
| QuickActionsOverview (charts + stats) | ✅ |
| TransactionList (with bulk delete) | ✅ |
| PaymentList | ✅ |
| CreateIncome + CreateExpense (unified) | ✅ |
| Transfer (debit+credit pair) | ✅ |
| Workflow Guide (3-tab info) | ✅ |

**Implementasi:**
- `BankAccountController` — index dengan accounts + chart data + stats
- `BankTransactionController` — JSON list endpoints (transactions + payments), CRUD, bulk delete, transfer
- 10 React/TSX file modular di `resources/js/pages/bank-accounts/`
- Charts: ApexCharts (bar 12-bulan + donut breakdown) dengan reactive dark mode
- Floating bulk-action bar untuk multi-select transaksi

**Penting:** Balance adalah COMPUTED (tidak stored). `initial_balance + payments(credit) + tx(credit) - tx(debit)`

### Cash Flow ✅ SELESAI (2026-05-20)
**Route:** `/cash-flow/income`, `/cash-flow/expenses`, `/cash-flow/transfers`
**Livewire source:** `app/Livewire/CashFlow/`

| Component | Status | Catatan |
|-----------|--------|---------|
| Index (redirect) | ✅ | `/cash-flow` → `/cash-flow/income` |
| OverviewTab (charts) | — | Orphan code di Livewire, tidak dipakai |
| IncomeTab | ✅ | `cash-flow/income.tsx` — UNION payments + bank_transactions |
| ExpensesTab | ✅ | `cash-flow/expenses.tsx` — debit bank_transactions |
| TransfersTab | ✅ | `cash-flow/transfers.tsx` — TRF pair visualization |

**Implementasi:**
- `CashFlowController` — 3 index methods + bulkDestroy
- Shared types & stats card component
- Server-side filter via `router.get()` + Inertia partial reload (`only: ['rows', ...]`)
- Single-select filter (clients/categories/bank_accounts) — backend support array, frontend kirim 1 item
- Floating bulk-action bar (sama pattern dengan bank-accounts)
- Export PDF via existing `CashFlowExportController` (window.open)
- Activate menu items (Pemasukan/Pengeluaran/Transfer & Penyesuaian)

### Transactions
**Livewire source:** `app/Livewire/Transactions/`

| Component | Status | Catatan |
|-----------|--------|---------|
| Listing | ✅ | Embedded di bank-accounts page (TransactionsTab) |
| Create | ✅ | Unified dialog (income/expense) via `transaction_type` |
| CreateIncome | ✅ | Bagian dari unified dialog |
| CreateExpense | ✅ | Bagian dari unified dialog |
| Delete | ✅ | ConfirmDialog + bulk delete |
| Categorize | ⬜ | Belum diperlukan (kategori dipilih saat create) |
| Transfer | ✅ | TransferDialog (TRF pair) |

---

## Fase 8 — Operations ✅ SELESAI (2026-05-20)

### Reimbursements ✅
**Route:** `/reimbursements`
**Controller:** `app/Http/Controllers/ReimbursementController.php`

| Component | Status |
|-----------|--------|
| Index (tabs: Semua / Saya, stats, filter, table) | ✅ |
| Create (Sheet drawer kanan) | ✅ |
| Edit (Sheet drawer kanan, pre-fill dari row) | ✅ |
| Delete (ConfirmDialog) | ✅ |
| Detail (Dialog) | ✅ |
| Review (approve/reject + category) | ✅ |
| Pay (BankTransaction debit) | ✅ |

**Status flow:** `draft → pending → approved → paid` (atau `rejected`)

### Fund Requests ✅
**Route:** `/fund-requests`
**Controller:** `app/Http/Controllers/FundRequestController.php`

| Component | Status |
|-----------|--------|
| Index (tabs: Semua / Saya, stats, filter, table) | ✅ |
| Create (Sheet drawer kanan, items repeater) | ✅ |
| Edit (Sheet drawer kanan, Inertia partial reload ?edit={id}) | ✅ |
| Delete (ConfirmDialog) | ✅ |
| Detail (Dialog) | ✅ |
| Review (approve/reject) | ✅ |
| Disburse (BankTransaction per item) | ✅ |

**Format nomor:** `001/KSN/I/2026`
**Status flow:** `draft → pending → approved → disbursed` (atau `rejected`)
**Sheet pattern:** Index controller pass `categories`, `nextNumber`, `editFundRequest` (lazy via `?edit={id}`)

---

## Fase 9 — Finance ✅ SELESAI (2026-05-20)

### Loans ✅
**Route:** `/loans`
**Controller:** `app/Http/Controllers/LoanController.php`

| Component | Status |
|-----------|--------|
| Index | ✅ |
| Create | ✅ |
| Update | ✅ |
| Delete | ✅ |
| PayLoan | ✅ |

### Receivables ✅
**Route:** `/receivables`
**Controller:** `app/Http/Controllers/ReceivableController.php`

| Component | Status |
|-----------|--------|
| Index | ✅ |
| Create | ✅ |
| Update | ✅ |
| Delete | ✅ |
| Submit | ✅ |
| Approve | ✅ |
| PayReceivable | ✅ |

**Penting:** Debtor adalah polymorphic — bisa User ATAU Client.

---

## Fase 10 — Admin ✅ SELESAI (2026-05-21)

### Users ✅
**Route:** `/admin/users`
**Controller:** `app/Http/Controllers/Admin/UserController.php`
**Page:** `resources/js/pages/users/index.tsx`

| Component | Status |
|-----------|--------|
| Index (stats + filter + table + bulk actions) | ✅ |
| Create (Dialog 2-column: Akun + Peran/Password) | ✅ |
| Edit (Dialog, password opsional) | ✅ |
| Delete (ConfirmDialog, blok hapus diri sendiri) | ✅ |
| BulkDelete (floating bar) | ✅ |

**Implementasi:**
- Stats: total, active, admins, finance_managers (StatsCard h-1 accent)
- Filter: peran (Combobox), status (Combobox), search (debounced 350ms)
- Tabel: avatar gradient + initials, role badge dengan ikon, status badge, kontak phone
- Form Dialog: 2-column grid (Akun + Peran/Password), show/hide password toggle
- Bulk action floating bar dengan auto-exclude user saat ini
- Permission gate: `manage users`

### Permissions & Roles ✅
**Route:** `/admin/permissions`
**Controllers:** `app/Http/Controllers/Admin/PermissionController.php`, `app/Http/Controllers/Admin/RoleController.php`
**Page:** `resources/js/pages/permissions/index.tsx`

| Component | Status |
|-----------|--------|
| Index (stats + master-detail role sidebar + permission panel) | ✅ |
| Toggle Permission (per role) | ✅ |
| Sync Module (grant/revoke per modul) | ✅ |
| Sync All (grant/revoke semua permission) | ✅ |
| Delete Permission | ✅ |
| Create Role (Dialog dengan icon picker 37 lucide icons) | ✅ |
| Update Role (Dialog) | ✅ |
| Delete Role (dengan auto-reassign user ke fallback role) | ✅ |

**Implementasi:**
- 3 stats cards: Total Peran, Total Permission, Modul
- Master-detail: sidebar kiri (3 cols) untuk role list, panel kanan (9 cols) untuk permissions
- Permission panel grouped by modul dengan sticky header, search realtime, per-module grant/revoke
- Icon picker 37 lucide icons (dengan backward-compat alias untuk heroicon names lama)
- Role tidak bisa hapus: 'admin', 'staff' (default), peran sendiri
- Permission cache di-reset (Spatie\Permission\PermissionRegistrar) setiap perubahan
- Permission gate: `view permissions` untuk view, `manage permissions` untuk write

**Roles:** `admin`, `finance manager`, `staff`

### Settings ✅
**Route:** `/settings/profile`, `/settings/password`, `/settings/company`
**Controllers:** `Settings/ProfileController`, `Settings/PasswordController`, `Settings/CompanyController`
**Pages:** `resources/js/pages/settings/{profile,password,company}.tsx`
**Layout:** `resources/js/layouts/settings-layout.tsx` (shared sidebar nav + card content)

| Component | Status |
|-----------|--------|
| Profile (nama + email + email verification + delete account) | ✅ |
| Password (current + new + confirm dengan show/hide toggle) | ✅ |
| Company (identitas + manager + PKP/NPWP + 4 visual assets) | ✅ |
| Delete Account (Dialog dengan konfirmasi password) | ✅ |

**Implementasi:**
- SettingsLayout: sidebar kiri 60w (Profile/Password/Company) + content card kanan
- Profile: form sederhana + warning email belum terverifikasi + section Hapus Akun (Dialog merah)
- Password: 3 field dengan toggle show/hide, validasi `current_password` rule Laravel
- Company: 4 FormSection (Identitas, Manager, PKP & Pajak, Aset Visual)
- Aset Visual grid 2×2: logo, kop surat, signature, stamp dengan preview Dialog & delete confirm
- File upload kustom (hidden input + label dengan icon Upload, ganti file label dinamis)
- Conditional PKP fields (NPWP + tarif PPN) muncul ketika checkbox PKP aktif
- Hint regenerasi favicon di bawah form ketika logo sudah ada

---

## Fase 11 — Utility Components & Dashboard ✅ SELESAI (2026-05-21)

| Component | Status | Catatan |
|-----------|--------|---------|
| Dashboard (stats + charts) | ✅ | Sudah lengkap sejak Fase awal (DashboardController + ApexCharts) |
| Notification Bell | ✅ | Popover dengan list recent + counter badge |
| Notification Drawer | ✅ | Sheet fullscreen kanan dengan load-more pagination |
| LanguageSwitcher (id/en/zh) | ✅ | Sudah ada di header.tsx sejak Fase 2 |
| FloatingFeedbackButton | ✅ | FAB primary di bottom-right dengan dialog kompak |

### Notifications ✅
**Routes:** `/notifications`, `/notifications/{id}/read`, `/notifications/mark-all-read`
**Controller:** `app/Http/Controllers/NotificationController.php`
**Components:**
- `resources/js/components/notifications/notification-bell.tsx` — Popover, badge counter, recent list (max 10), mark-all-read, link to drawer
- `resources/js/components/notifications/notification-drawer.tsx` — Sheet kanan, full list dengan load-more via fetch JSON

**Implementasi:**
- Shared props: `notifications.recent` (10 terbaru) + `notifications.unread_count` di-share lewat `HandleInertiaRequests`
- Icon map: lucide icons per `AppNotification.type` (feedback_submitted/responded/status_changed, invoice_*, payment_deleted)
- Color map: blue/green/yellow/red/gray dengan bg + text variants
- Time ago helper: format relatif Indonesia (baru saja / 5m / 3j / 2h)
- Bell counter: `min-w-4 h-4` red badge, max display "99+"
- Wired ke header.tsx menggantikan stub Bell button

### Feedbacks ✅
**Route:** `/feedbacks`
**Controller:** `app/Http/Controllers/FeedbackController.php`
**Page:** `resources/js/pages/feedbacks/index.tsx`

| Component | Status |
|-----------|--------|
| Index (tabs Semua / Saya untuk admin, stats, filter, card grid) | ✅ |
| Create (Dialog dengan type/priority/attachment) | ✅ |
| Update (Dialog dengan pre-fill, hanya saat status open) | ✅ |
| Delete (ConfirmDialog) | ✅ |
| Show (Dialog detail + admin response + status change menu) | ✅ |
| Respond (Dialog separate untuk admin/respond) | ✅ |
| Change Status (DropdownMenu inline di show dialog) | ✅ |

**Implementasi:**
- 4 stats cards: Total, Terbuka, Diproses, Selesai (StatsCard h-1 accent)
- Tabs (Semua/Saya) hanya tampil untuk admin yang punya `manage feedbacks`
- Card grid `md:2 xl:3` — accent bar atas per type (bug=red, feature=blue, feedback=zinc)
- Type icons: Bug, Lightbulb, MessageCircle
- Priority badges: zinc/blue/yellow/red
- Status badges: yellow (open), blue (in_progress, animate-spin), green (resolved), zinc (closed)
- Show dialog mencakup deskripsi full + URL halaman + lampiran link + admin response block (biru) + action buttons (status menu, edit, delete, respond)
- Inertia partial reload `?show={id}` untuk load detail (lazy load via showFeedback prop)
- File upload via FormData (`forceFormData: true`), accept: JPG/PNG/PDF max 5MB

### FloatingFeedbackButton ✅
**Component:** `resources/js/components/floating-feedback-button.tsx`
**Lokasi:** Wired di `app-layout.tsx` (z-40, bottom-6 right-6)

**Implementasi:**
- FAB primary dengan icon MessageSquare, label "Feedback" muncul saat hover (smooth expand)
- Auto-fill `page_url` dengan `window.location.pathname` saat dibuka
- Form: 3-type picker (Bug/Fitur/Saran) + Judul + Deskripsi + 4-priority picker + attachment
- Notifikasi admin otomatis dikirim ke users dengan role admin/finance manager via `AppNotification::notify()`

### LanguageSwitcher ✅
**Lokasi:** `resources/js/layouts/header.tsx` (sudah ada sejak Fase 2)
- Dropdown 3 opsi: id (🇮🇩), en (🇬🇧), zh (🇨🇳)
- POST ke `/language` route untuk update session + user preference

### Dashboard ✅
**Lokasi:** `resources/js/pages/dashboard.tsx` + `app/Http/Controllers/DashboardController.php` (sudah ada)
- Financial overview cards (Pemasukan/Profit/Outstanding/HPP/PP 0,5%/Saldo)
- Stats bulan ini (income, expenses, net)
- Charts: Cash flow bar (6 bulan) + Expenses by category donut (ApexCharts)
- 4-column quick lists: Bank Accounts, Reimbursements, Fund Requests, Invoices
- Recent Transactions grid 4-col

---

## Fase 12 — Backend Refactoring ✅ SELESAI (2026-05-21)

**Tujuan:** Ganti semua Livewire component logic ke controllers + Form Requests.

### Checklist
- [x] Buat 44 Form Request classes (StoreX, UpdateX, + action-specific: Review, Pay, Approve, Disburse, Respond, ChangeStatus, BulkDestroy, Transfer)
- [x] Semua controllers sudah menggunakan Form Requests (`$request->validated()` menggantikan inline validate)
- [x] Update `routes/web.php` — hapus TestingPage Livewire route (satu-satunya sisa)
- [x] Hapus Livewire dari `composer.json`: `livewire/livewire`, `livewire/volt`, `dasundev/livewire-quill-text-editor`
- [x] Hapus TallStackUI: `tallstackui/tallstackui` (+ `spatie/laravel-package-tools` dependency)
- [x] Hapus dari `package.json`: `alpinejs`, `flowbite`, `flowbite-typography`, `daisyui`, `quill`, `@toast-ui/chart`
- [x] Update `resources/css/app.css` — hapus TallStackUI import + @source entries + `[x-cloak]`
- [x] Update `app/Providers/AppServiceProvider.php` — hapus TallStackUI personalization block
- [x] Update `resources/js/app.js` — strip Quill/Livewire/TallStackUI upload dead code
- [x] Jalankan PHP Pint untuk format semua file baru
- [x] `npm run build` sukses — 3484 modules transformed, no errors

### Controller Mapping

| Module | Controller |
|--------|-----------|
| Clients | `ClientController` (index, create, store, edit, update, destroy, show) |
| Services | `ServiceController` |
| Invoices | `InvoiceController` |
| Payments | `PaymentController` |
| Recurring | `RecurringTemplateController`, `RecurringInvoiceController` |
| BankAccounts | `BankAccountController` |
| CashFlow | `CashFlowController` |
| Transactions | `BankTransactionController` |
| TxCategories | `TransactionCategoryController` |
| Reimbursements | `ReimbursementController` |
| FundRequests | `FundRequestController` |
| Loans | `LoanController` |
| Receivables | `ReceivableController` |
| Feedbacks | `FeedbackController` |
| Users | `Admin\UserController` |
| Permissions | `Admin\PermissionController` |
| Roles | `Admin\RoleController` |
| Settings | `Settings\ProfileController`, `Settings\CompanyController` |

---

## Fase 13 — PDF Integration ✅ SELESAI (2026-05-21)

**Tujuan:** Pastikan PDF generation tetap berfungsi dengan setup baru.

- [x] `InvoicePrintService` tetap digunakan (sudah server-side, tidak perlu diubah)
- [x] Route `GET /invoice/{invoice}/download` + `preview` tetap ada di `web.php`
- [x] Template Blade PDF (`resources/views/pdf/`) tidak diubah — DomPDF render server-side
- [x] Tombol PDF di InvoiceDrawer + dropdown tabel: preview inline di tab baru + auto-download via `window.location.href`
- [x] Fix: clear view cache setelah hapus Livewire (`php artisan view:clear`) agar `wayfinder:generate` tidak gagal

---

## Fase 14 — Testing ✅ Selesai (2026-05-21)

- [x] Enable SQLite pdo_sqlite + sqlite3 extensions di C:\PHP\php.ini
- [x] Fix migrasi SQLite-incompatible: drop index sebelum drop column (transaction_categories)
- [x] Fix MODIFY COLUMN MySQL-only di recurring_templates migration (skip on SQLite)
- [x] Fix SUBSTRING_INDEX MySQL-only di Invoice model `getMaxSequenceFromDb()` → PHP collection
- [x] Fix YEAR()/MONTH()/SUBSTRING_INDEX MySQL-only di InvoiceController `index()` → PHP collection
- [x] Fix YEAR()/MONTH() MySQL-only di BankAccountController → PHP collection
- [x] Fix `branch` Undefined array key di BankAccountController store/update
- [x] Rewrite Auth tests (tidak ada lagi Livewire::test()) → HTTP assertions
- [x] Rewrite Settings tests (PasswordUpdate, ProfileUpdate) → HTTP assertions
- [x] Fix ExampleTest → redirect ke /login untuk guest
- [x] Update RegistrationTest → registrasi publik disabled, 404
- [x] Feature tests baru: ClientControllerTest (11 tests)
- [x] Feature tests baru: InvoiceControllerTest (13 tests) — happy path, auth, rollback
- [x] Feature tests baru: BankAccountControllerTest (10 tests)
- [x] Feature tests baru: ReimbursementControllerTest (10 tests) — workflow approve
- [x] Jalankan PHP Pint pada semua file yang dimodifikasi
- [x] Semua 76 tests pass

---

## Fase 15 — Cleanup & Deployment Prep

### Cleanup
- [ ] Hapus semua file `app/Livewire/**/*.php`
- [ ] Hapus semua file `resources/views/livewire/**/*.blade.php`
- [ ] Hapus `resources/views/components/layouts/app.blade.php` (ganti dengan `app.blade.php` Inertia)
- [ ] Hapus `resources/js/app.js` (entry Livewire)
- [ ] Update `.env.example`
- [ ] Jalankan `composer dump-autoload`
- [ ] Jalankan `php artisan view:clear && config:clear && cache:clear`

### Deployment
- [ ] Build assets: `npm run build`
- [ ] Merge `feature/inertia-react-migration` → `main`

**Cara merge:**
```bash
git checkout main
git merge feature/inertia-react-migration
# Resolve konflik jika ada
git push origin main
```

- [ ] Deploy ke server
- [ ] Jalankan `php artisan migrate` jika ada migration baru
- [ ] Jalankan `php artisan permission:cache-reset`

---

## Catatan Penting

### Jangan Diubah Sampai Fase 15
- `resources/js/app.js` — Livewire entry
- `app/Livewire/` — semua Livewire PHP components
- `resources/views/livewire/` — semua Blade views
- `app/Providers/AppServiceProvider.php` — TallStackUI personalization (hapus hanya di Fase 12)

### File PDF Tidak Diubah Sama Sekali
- `resources/views/pdf/` — semua template PDF tetap Blade
- `app/Services/InvoicePrintService.php` — tetap digunakan

### Multi-language
- File `lang/id/`, `lang/en/`, `lang/zh/` tetap digunakan
- Shared via `HandleInertiaRequests` middleware sebagai prop `translations`
- Di React: akses via `usePage().props.translations` atau i18next

### Currency
- Simpan sebagai integer di DB: `150000` = Rp 1.500
- Parse input: `parseInt(value.replace(/[^0-9]/g, ''))`
- Format display: `'Rp ' + value.toLocaleString('id-ID')`

### Permission Check di React
```tsx
const { auth } = usePage<SharedProps>().props
const can = (permission: string) => auth.permissions.includes(permission)

// Usage:
{can('create invoices') && <Button>Buat Invoice</Button>}
```
