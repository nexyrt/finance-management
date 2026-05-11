# Migration Plan: Livewire → Inertia.js + React

> **Dokumen ini adalah panduan kerja untuk Claude Code.**
> Dibuat: 2026-05-11 | Branch aktif: `feature/inertia-react-migration`
> Update dokumen ini setiap kali fase selesai.
> **Terakhir diupdate: 2026-05-11 — Fase 2 selesai, mulai Fase 3**

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

**Catatan:** `Repeater` dan `StatusBadge` dibangun inline saat dibutuhkan per-module (Fase 4+).

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
