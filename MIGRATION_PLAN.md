# Migration Plan: Livewire → Inertia.js + React

> **Dokumen ini adalah panduan kerja untuk Claude Code.**
> Dibuat: 2026-05-11 | Branch aktif: `feature/inertia-react-migration`
> Update dokumen ini setiap kali fase selesai.

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
- Ziggy untuk `route()` helper di React
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
| 0 | Foundation Setup | ⬜ Belum Dimulai |
| 1 | Design System (shadcn/ui) | ⬜ Belum Dimulai |
| 2 | AppLayout.tsx | ⬜ Belum Dimulai |
| 3 | Auth Pages | ⬜ Belum Dimulai |
| 4 | Master Data | ⬜ Belum Dimulai |
| 5 | Invoice & Payment | ⬜ Belum Dimulai |
| 6 | Recurring Invoices | ⬜ Belum Dimulai |
| 7 | Banking (Accounts + Cash Flow + Transactions) | ⬜ Belum Dimulai |
| 8 | Operations (Reimbursements + Fund Requests) | ⬜ Belum Dimulai |
| 9 | Finance (Loans + Receivables) | ⬜ Belum Dimulai |
| 10 | Admin (Users + Permissions + Settings) | ⬜ Belum Dimulai |
| 11 | Utility Components & Dashboard | ⬜ Belum Dimulai |
| 12 | Backend Refactoring (Controllers + Form Requests) | ⬜ Belum Dimulai |
| 13 | PDF Integration | ⬜ Belum Dimulai |
| 14 | Testing | ⬜ Belum Dimulai |
| 15 | Cleanup & Deployment Prep | ⬜ Belum Dimulai |

**Legend:** ⬜ Belum Dimulai | 🔄 Sedang Dikerjakan | ✅ Selesai

---

## Fase 0 — Foundation Setup

**Tujuan:** Install semua dependency, konfigurasi Vite dual entry, setup middleware Inertia.

### Checklist

#### Server-side (Composer)
- [ ] `composer require inertiajs/inertia-laravel`
- [ ] Publish dan register `HandleInertiaRequests` middleware di `bootstrap/app.php`
- [ ] Setup shared props di middleware: `auth.user`, `auth.permissions`, `locale`, `translations`, `flash`
- [ ] Install Ziggy: `composer require tightenco/ziggy`
- [ ] Buat root Blade template `resources/views/app.blade.php` (Inertia entry point)

#### Client-side (NPM)
```bash
npm install react react-dom
npm install -D @types/react @types/react-dom typescript @vitejs/plugin-react
npm install @inertiajs/react
npm install ziggy-js
npm install -D @types/ziggy-js
npm install i18next react-i18next
npm install sonner
npm install react-hook-form @hookform/resolvers zod
npm install @tanstack/react-table
npm install react-dropzone
npm install cmdk
npm install apexcharts react-apexcharts
npm install chart.js react-chartjs-2
npm install @tiptap/react @tiptap/pm @tiptap/starter-kit
npm install class-variance-authority clsx tailwind-merge
npm install lucide-react
```

#### shadcn/ui prerequisites
```bash
npm install @radix-ui/react-dialog @radix-ui/react-dropdown-menu
npm install @radix-ui/react-select @radix-ui/react-tabs
npm install @radix-ui/react-tooltip @radix-ui/react-popover
npm install @radix-ui/react-checkbox @radix-ui/react-switch
npm install @radix-ui/react-avatar @radix-ui/react-badge
npm install @radix-ui/react-separator @radix-ui/react-label
npm install @radix-ui/react-slot @radix-ui/react-scroll-area
npx shadcn@latest init
```

#### Vite Dual Entry
File `vite.config.ts` → tambah entry point `resources/js/inertia.tsx` di samping `app.js` yang sudah ada.
```ts
// Livewire entry tetap ada: resources/js/app.js
// Inertia entry baru: resources/js/inertia.tsx
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/inertia.tsx'],
    refresh: true,
})
```

#### TypeScript Config
- [ ] Buat `tsconfig.json` dengan `"jsx": "react-jsx"`, path aliases `@/*`
- [ ] Buat `resources/js/inertia.tsx` sebagai entry point React/Inertia

#### Struktur Folder React
```
resources/js/
├── app.js              ← Livewire entry (JANGAN DIUBAH sampai Fase 15)
├── inertia.tsx         ← Inertia/React entry baru
├── components/
│   ├── ui/             ← shadcn/ui base components
│   ├── layout/         ← AppLayout, Sidebar, Header, dll
│   └── shared/         ← DataTable, CurrencyInput, StatusBadge, dll
├── pages/              ← Inertia pages (satu file per route)
│   ├── auth/
│   ├── clients/
│   ├── invoices/
│   └── ...
├── hooks/              ← Custom React hooks
├── lib/                ← utils.ts, formatters.ts, ziggy.d.ts
└── types/              ← TypeScript type definitions
```

---

## Fase 1 — Design System (shadcn/ui)

**Tujuan:** Setup semua komponen UI yang akan digunakan di seluruh project.

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

### Komponen Shared Wajib Dibuat
- [ ] `CurrencyInput` — stores integer, display `Rp X.XXX`
- [ ] `DataTable` — wrapper TanStack Table dengan filter, sort, pagination, bulk select
- [ ] `StatusBadge` — badge dengan warna per status (draft/pending/approved/paid/rejected)
- [ ] `ConfirmDialog` — replacement untuk TallStackUI confirm interaction
- [ ] `PageHeader` — title + description + action button (sesuai design system)
- [ ] `StatsCard` — horizontal card dengan icon (sesuai design system)
- [ ] `FormSection` — 2-column grid dengan section header
- [ ] `EmptyState` — tampilan saat data kosong
- [ ] `Repeater` — add/remove rows dinamis (untuk invoice items dll)

---

## Fase 2 — AppLayout.tsx

**Tujuan:** Konversi layout utama 642-line Blade ke React.

**File referensi:** `resources/views/components/layouts/app.blade.php`

### Checklist
- [ ] Sidebar navigation dengan collapse support (Alpine.js → React state)
- [ ] Dark mode toggle (localStorage persist, `class` strategy di `<html>`)
- [ ] Breadcrumbs (dari Inertia shared props atau per-page prop)
- [ ] Header dengan user dropdown
- [ ] Notification bell → drawer (event-driven seperti sebelumnya)
- [ ] Language switcher (id/en/zh)
- [ ] Floating feedback button
- [ ] Route active state detection via `usePage().url`

---

## Fase 3 — Auth Pages

**Livewire source:** `app/Livewire/Auth/`
**Blade source:** `resources/views/livewire/auth/`

| Halaman | Status |
|---------|--------|
| Login | ⬜ |
| Register | ⬜ |
| ForgotPassword | ⬜ |
| ResetPassword | ⬜ |
| VerifyEmail | ⬜ |
| ConfirmPassword | ⬜ |

**Controller:** Laravel Breeze/Fortify sudah ada, tinggal ganti response dari Blade ke Inertia.

---

## Fase 4 — Master Data

### Clients
**Route:** `/clients`
**Livewire source:** `app/Livewire/Clients/`

| Component | Status |
|-----------|--------|
| Index (stats + tabs) | ⬜ |
| Listing (table + filter) | ⬜ |
| Create (modal form) | ⬜ |
| Edit (modal form) | ⬜ |
| Delete (confirm dialog) | ⬜ |
| Show (detail view) | ⬜ |
| Relationship tab | ⬜ |

### Services
**Route:** `/services`
**Livewire source:** `app/Livewire/Services/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Create | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |

### Transaction Categories
**Route:** `/transaction-categories`
**Livewire source:** `app/Livewire/TransactionCategories/`

| Component | Status |
|-----------|--------|
| Index (tree view) | ⬜ |
| Create | ⬜ |
| Update | ⬜ |
| Delete | ⬜ |

---

## Fase 5 — Invoice & Payment

**Route:** `/invoices`
**Livewire source:** `app/Livewire/Invoices/`, `app/Livewire/Payments/`

### Invoice
| Component | Status |
|-----------|--------|
| Index (stats + tabs) | ⬜ |
| Listing (table + filter) | ⬜ |
| Create (form + repeater items) | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |
| Show (detail + PDF download) | ⬜ |

### Payment
| Component | Status |
|-----------|--------|
| Listing | ⬜ |
| Create | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |
| AttachmentViewer | ⬜ |

**Business logic penting:**
- Invoice number format: `INV/{seq}/KSN/{mm}.{yy}`
- Status flow: `draft → partially_paid → paid`
- Currency: simpan integer (150000 = Rp 1.500)
- PDF download via `GET /invoice/{invoice}/download?template=kisantra-invoice`

---

## Fase 6 — Recurring Invoices

**Route:** `/recurring-invoices`
**Livewire source:** `app/Livewire/RecurringInvoices/`

| Component | Status |
|-----------|--------|
| Index (tabs) | ⬜ |
| TemplatesTab | ⬜ |
| MonthlyTab | ⬜ |
| AnalyticsTab | ⬜ |
| Create Template | ⬜ |
| Edit Template | ⬜ |
| Delete Template | ⬜ |
| Generate Invoice (from template) | ⬜ |

---

## Fase 7 — Banking

### Bank Accounts
**Route:** `/bank-accounts`
**Livewire source:** `app/Livewire/BankAccounts/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Create | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |
| QuickActionsOverview | ⬜ |

**Penting:** Balance adalah COMPUTED (tidak stored). `initial_balance + payments(credit) + tx(credit) - tx(debit)`

### Cash Flow
**Route:** `/cash-flow`
**Livewire source:** `app/Livewire/CashFlow/`

| Component | Status |
|-----------|--------|
| Index (tabs) | ⬜ |
| OverviewTab (charts) | ⬜ |
| IncomeTab | ⬜ |
| ExpensesTab | ⬜ |
| TransfersTab | ⬜ |

### Transactions
**Livewire source:** `app/Livewire/Transactions/`

| Component | Status |
|-----------|--------|
| Listing | ⬜ |
| Create | ⬜ |
| CreateIncome | ⬜ |
| CreateExpense | ⬜ |
| Delete | ⬜ |
| Categorize | ⬜ |
| Transfer | ⬜ |

---

## Fase 8 — Operations

### Reimbursements
**Route:** `/reimbursements`
**Livewire source:** `app/Livewire/Reimbursements/`

| Component | Status |
|-----------|--------|
| Index (tabs: AllRequests / MyRequests) | ⬜ |
| Create | ⬜ |
| Update | ⬜ |
| Delete | ⬜ |
| Show | ⬜ |
| Review (approve/reject) | ⬜ |
| Payment | ⬜ |

**Status flow:** `draft → pending → approved → paid` (atau `rejected`)

### Fund Requests
**Route:** `/fund-requests`
**Livewire source:** `app/Livewire/FundRequests/`

| Component | Status |
|-----------|--------|
| Index (tabs: AllRequests / MyRequests) | ⬜ |
| Create | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |
| Show | ⬜ |
| Review | ⬜ |
| Disburse | ⬜ |

**Format nomor:** `001/KSN/I/2026`
**Status flow:** `draft → pending → approved → disbursed` (atau `rejected`)

---

## Fase 9 — Finance

### Loans
**Route:** `/loans`
**Livewire source:** `app/Livewire/Loans/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Create | ⬜ |
| Update | ⬜ |
| Delete | ⬜ |
| PayLoan | ⬜ |

### Receivables
**Route:** `/receivables`
**Livewire source:** `app/Livewire/Receivables/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Create | ⬜ |
| Update | ⬜ |
| Delete | ⬜ |
| Submit | ⬜ |
| Approve | ⬜ |
| PayReceivable | ⬜ |

**Penting:** Debtor adalah polymorphic — bisa User ATAU Client.

---

## Fase 10 — Admin

### Users
**Route:** `/admin/users`
**Livewire source:** `app/Livewire/Users/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Create | ⬜ |
| Edit | ⬜ |
| Delete | ⬜ |

### Permissions & Roles
**Route:** `/permissions`
**Livewire source:** `app/Livewire/Permissions/`, `app/Livewire/Roles/`

| Component | Status |
|-----------|--------|
| Index | ⬜ |
| Delete Permission | ⬜ |
| Create Role | ⬜ |
| Update Role | ⬜ |
| Delete Role | ⬜ |

**Roles:** `admin`, `finance manager`, `staff`

### Settings
**Route:** `/settings/*`
**Livewire source:** `app/Livewire/Settings/`

| Component | Status |
|-----------|--------|
| Profile | ⬜ |
| Password | ⬜ |
| CompanyProfileSettings (logo, signature, stamp) | ⬜ |
| DeleteUserForm | ⬜ |

---

## Fase 11 — Utility Components & Dashboard

| Component | Status |
|-----------|--------|
| Dashboard (stats + charts) | ⬜ |
| Notification Bell | ⬜ |
| Notification Drawer | ⬜ |
| LanguageSwitcher (id/en/zh) | ⬜ |
| FloatingFeedbackButton | ⬜ |

### Feedbacks
**Route:** `/feedbacks`
**Livewire source:** `app/Livewire/Feedbacks/`

| Component | Status |
|-----------|--------|
| Index (tabs: AllFeedbacks / MyFeedbacks) | ⬜ |
| Create | ⬜ |
| Update | ⬜ |
| Delete | ⬜ |
| Show | ⬜ |
| Respond | ⬜ |

---

## Fase 12 — Backend Refactoring

**Tujuan:** Ganti semua Livewire component logic ke controllers + Form Requests.

### Checklist
- [ ] Buat ~80 Form Request classes (saat ini 0 Form Request — semua inline di Livewire)
- [ ] Buat controller untuk setiap module (gunakan `php artisan make:controller`)
- [ ] Update `routes/web.php` — ganti route Livewire ke Inertia controller routes
- [ ] Hapus Livewire dari `composer.json`: `livewire/livewire`, `livewire/volt`
- [ ] Hapus TallStackUI: `tallstackui/tallstackui`
- [ ] Hapus dari `package.json`: `alpinejs`, `flowbite`, `daisyui`, `quill`
- [ ] Update `resources/css/app.css` — hapus TallStackUI, DaisyUI, Alpine imports
- [ ] Update `app/Providers/AppServiceProvider.php` — hapus TallStackUI personalization

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

## Fase 13 — PDF Integration

**Tujuan:** Pastikan PDF generation tetap berfungsi dengan setup baru.

- [ ] `InvoicePrintService` tetap digunakan (sudah server-side, tidak perlu diubah)
- [ ] Route `GET /invoice/{invoice}/download` tetap ada (tidak perlu Livewire)
- [ ] Template Blade PDF (`resources/views/pdf/`) tidak diubah — DomPDF render server-side
- [ ] Test PDF download dari React page via direct link

---

## Fase 14 — Testing

- [ ] Feature tests untuk semua controllers (happy path + validation errors + unauthorized)
- [ ] Test permission gates (admin vs finance manager vs staff)
- [ ] Test PDF generation
- [ ] Test currency calculations
- [ ] Test invoice status flow
- [ ] Test reimbursement/fund request workflow

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
