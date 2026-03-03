# CLAUDE.md

Panduan untuk Claude Code saat bekerja di repository ini.

## Project Overview

**Finance Management System** - Laravel 12 + Livewire-first. Konteks bisnis Indonesia (NPWP/PKP, Terbilang, Rupiah).

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

### Livewire-First (121 components)

| Pattern | Fungsi |
|---------|--------|
| `Index.php` | Coordinator/dashboard dengan statistik |
| `Listing.php` | Tabel dengan filter, search, pagination |
| `Create.php` | Form pembuatan dengan validasi |
| `Edit.php` | Form update |
| `Delete.php` | Confirmation dialog |
| `Show.php` | Detail view |

### Key Patterns

```php
// Computed Properties
#[Computed]
public function stats(): array { return [...]; }

// Event-Driven Refresh
#[On('invoice-created')]
public function refreshStats(): void { unset($this->stats); }

// Currency Storage (integer)
// DB: 150000 → Display: Rp 1.500
// Parse: preg_replace('/[^0-9]/', '', $input)
// Format: number_format($value, 0, ',', '.')
```

---

## UI/UX Design System

**CRITICAL: Semua page baru atau redesign HARUS mengikuti design system ini untuk konsistensi.**

**Core Philosophy:** Minimalist, Clean & Readable, Consistent, Functional-First.

---

### Standard Page Template

**Reference:** `resources/views/livewire/invoices/index.blade.php`

```blade
<div class="space-y-6">
    {{-- Header Section (WAJIB SAMA DI SEMUA PAGE) --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.page_description') }}
            </p>
        </div>

        {{-- Action Button (optional) --}}
        <x-button color="primary" size="sm">
            <x-slot:left>
                <x-icon name="plus" class="w-4 h-4" />
            </x-slot:left>
            {{ __('common.action') }}
        </x-button>
    </div>

    {{-- Page Content (FLEXIBLE - Sesuai Kebutuhan) --}}
</div>
```

**Typography rules:**
- **Index/Main Pages**: `text-4xl font-bold` **DENGAN gradient**
- **Testing/Simple Pages**: `text-2xl font-bold` **TANPA gradient**
- **Modal Headers**: `text-xl font-bold` tanpa gradient

---

### Stats Cards (Horizontal — STANDAR DEFAULT)

**Reference:** `resources/views/livewire/clients/index.blade.php`

Gunakan layout horizontal untuk **SEMUA** stats cards. Vertical layout **DEPRECATED**.

```blade
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <x-card class="hover:shadow-lg transition-shadow">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-sm text-dark-600 dark:text-dark-400">Total Clients</p>
                <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">152</p>
            </div>
        </div>
    </x-card>
</div>
```

**Aturan:**
- Card: `<x-card class="hover:shadow-lg transition-shadow">`
- Layout: `flex items-center gap-4`
- Icon container: `h-12 w-12 rounded-xl flex-shrink-0`
- Icon bg: `bg-{color}-50 dark:bg-{color}-900/20` (SOFT, bukan -100)
- Label: `text-sm text-dark-600 dark:text-dark-400`
- Value: `text-2xl font-bold text-dark-900 dark:text-dark-50`

**Color Palette untuk Icons:**
| Color | Background | Icon | Usage |
|-------|------------|------|-------|
| Blue | `bg-blue-50 dark:bg-blue-900/20` | `text-blue-600 dark:text-blue-400` | Total count, general info |
| Green | `bg-green-50 dark:bg-green-900/20` | `text-green-600 dark:text-green-400` | Active status, positive |
| Purple | `bg-purple-50 dark:bg-purple-900/20` | `text-purple-600 dark:text-purple-400` | Companies, categories |
| Red | `bg-red-50 dark:bg-red-900/20` | `text-red-600 dark:text-red-400` | Outstanding, negative |
| Emerald | `bg-emerald-50 dark:bg-emerald-900/20` | `text-emerald-600 dark:text-emerald-400` | Paid, completed |

**Stats card di dalam modal** (gunakan plain div, tanpa `<x-card>`):
```blade
<div class="flex items-center gap-4 p-4 border border-secondary-200 dark:border-dark-600 rounded-xl">
    <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
        <x-icon name="document-duplicate" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
    </div>
    <div>
        <div class="text-sm text-dark-600 dark:text-dark-400">Total Invoices</div>
        <div class="text-2xl font-bold text-dark-900 dark:text-dark-50">152</div>
    </div>
</div>
```

**Quick Decision Tree:**
```
Main page stats → <x-card> + horizontal layout
Di dalam modal  → plain div + border + horizontal layout (no <x-card>, no hover)
```

---

### Filter + Table Layout

**Reference:** `resources/views/livewire/transactions/listing.blade.php`

**CRITICAL RULES:**
- ❌ JANGAN ada judul "Filter" atau section header
- ❌ JANGAN ada border/background di section filter
- ❌ JANGAN ada "Active Filter Tags" section terpisah dengan border-top
- ✅ LANGSUNG filter grid + status row — clean dan minimal

```blade
<div class="space-y-6">
    {{-- Filter Section (NO TITLE, NO BORDER!) --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">

            {{-- Main Filters Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{n} gap-3">
                <x-select.styled wire:model.live="filter1" label="Label" ... />
                <x-date wire:model.live="date" label="Date" ... />
            </div>

            {{-- Search + Filter Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">

                    {{-- Search (Fixed Width) --}}
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                                 placeholder="Cari..."
                                 icon="magnifying-glass"
                                 class="h-8" />
                    </div>

                    {{-- Active Filters Badge + Result Count (SATU BARIS) --}}
                    <div class="flex items-center gap-3">
                        @if ($activeFilters > 0)
                            <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                        @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="hidden sm:inline">Menampilkan </span>{{ $rows->count() }}
                            <span class="hidden sm:inline">dari {{ $rows->total() }}</span> hasil
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$rows :$sort selectable wire:model="selected" paginate loading>
        {{-- Table columns --}}
    </x-table>

    {{-- Bulk Actions Bar (Optional) --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
         class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                {{-- Selection info + actions --}}
            </div>
        </div>
    </div>
</div>
```

**Spacing Hierarchy:**
- Root container: `space-y-6`
- Filter wrapper: `space-y-4`
- Filter rows: `gap-4`
- Filter grid: `gap-3`
- Search width: `w-full sm:w-64`

---

### Modal Form Layout

**Reference:** `resources/views/livewire/transactions/create.blade.php`

**CRITICAL: Semua form dalam modal WAJIB menggunakan styling header & footer ini!**

#### Modal Header (Icon + Title + Description)

```blade
<x-modal title="..." wire="modal" size="xl" center persistent>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="icon-name" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Modal Title</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">Short description</p>
            </div>
        </div>
    </x-slot:title>
    {{-- Form content --}}
</x-modal>
```

**Icon color by context:**
```
Default/Primary  → bg-primary-50 dark:bg-primary-900/20 / text-primary-600 dark:text-primary-400
Success/Create   → bg-green-50 dark:bg-green-900/20     / text-green-600 dark:text-green-400
Warning/Edit     → bg-yellow-50 dark:bg-yellow-900/20   / text-yellow-600 dark:text-yellow-400
Danger/Delete    → bg-red-50 dark:bg-red-900/20         / text-red-600 dark:text-red-400
Info             → bg-blue-50 dark:bg-blue-900/20       / text-blue-600 dark:text-blue-400
```

#### Modal Footer (Responsive Button Layout)

```blade
<x-slot:footer>
    <div class="flex flex-col sm:flex-row justify-end gap-3">
        <x-button wire:click="$set('modal', false)"
                  color="zinc"
                  class="w-full sm:w-auto order-2 sm:order-1">
            {{ __('common.cancel') }}
        </x-button>
        <x-button type="submit"
                  form="form-id"
                  color="primary"
                  icon="check"
                  loading="save"
                  class="w-full sm:w-auto order-1 sm:order-2">
            {{ __('common.save') }}
        </x-button>
    </div>
</x-slot:footer>
```

**Aturan footer:**
- Cancel: `color="zinc"` (solid, better dark mode contrast vs outline)
- Mobile: full width, submit button first (`order-1`)
- Desktop: auto width, cancel button first (`order-1`)

**Submit button color by action:** `primary` (create), `green` (approve), `blue` (edit/update), `red` (delete), `yellow` (warning)

#### Form Content (2-Column Grid)

```blade
<form id="form-id" wire:submit="save" class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
            </div>
            <x-input wire:model="field" label="Label *" />
        </div>

        <div class="space-y-4">
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
            </div>
            <x-input wire:model="field" label="Label" />
        </div>
    </div>
</form>
```

---

### Card & Border Radius

**Standard: `rounded-xl` untuk semua containers.**

- ✅ Cards, icon containers, modals, dropdowns, bulk action bars
- ❌ JANGAN: `rounded-lg` (terlalu kecil), `rounded-2xl`, `rounded-3xl`

---

### Color Scheme

**Text colors:**
- Primary: `text-dark-900 dark:text-dark-50`
- Secondary: `text-dark-600 dark:text-dark-400`
- Muted: `text-dark-500 dark:text-dark-400`

**Backgrounds:** `bg-white dark:bg-dark-800`
**Borders:** `border-secondary-200 dark:border-dark-600`

**Avoid:**
- ❌ Gradient backgrounds untuk cards/sections (gradient HANYA untuk page title)
- ❌ Multiple accent colors dalam satu section
- ❌ `shadow-2xl`, `shadow-lg` — gunakan `hover:shadow-lg` untuk stats cards

---

### Dark Mode Color System

| Variable | Hex | Usage |
|----------|-----|-------|
| `dark-700` | `#27272a` | Sidebar, active/selected states |
| `dark-800` | `#3f3f46` | Body bg, cards, modals, table rows |
| `dark-600` | (border) | Card borders, dividers |

Sidebar (`dark-700`) lebih gelap dari body (`dark-800`) untuk depth perception. Table zebra striping: `dark:bg-dark-800/50`.

---

### Typography

- **Headings (h1-h6)**: Plus Jakarta Sans (600, 700, 800)
- **Body Text**: Inter (400, 500, 600, 700)

```css
/* Auto-applied via app.css */
h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }
```

---

### Spacing Rules

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

## TallStackUI Soft Personalization

**WAJIB:** Selalu gunakan soft personalization — JANGAN publish views atau extend classes.

**Workflow:** Cek `vendor/tallstackui/tallstackui/src/View/Components/{Component}.php` → method `personalization()` → identifikasi block name (dot notation dari `Arr::dot()`).

```php
// app/Providers/AppServiceProvider.php
TallStackUi::personalize()
    ->modal()->block('wrapper.first', 'fixed inset-0 bg-black/30 transform transition-opacity')
    ->and()
    ->card()->block('wrapper.second')->replace([
        'shadow-md' => 'border border-zinc-200 dark:border-dark-600 shadow-sm hover:shadow-md transition-shadow duration-150',
        'rounded-lg' => 'rounded-xl',
    ]);
```

**Helper methods** (hanya jika `->block()` tanpa parameter kedua): `->replace()`, `->remove()`, `->append()`, `->prepend()`. Gunakan `->and()` untuk chain ke component lain.

**❌ Jangan:** `->block('wrapper')` (nama salah), `->block('name', 'classes')->replace()` (tidak kompatibel), complete replacement tanpa cek original classes.

**Debugging:** `php artisan config:clear && php artisan view:clear`, rebuild CSS `npm run dev`.

---

## Modules Overview

### Livewire Components

| Module | Route | Components |
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
| Auth | — | Login, Register, ForgotPassword, ResetPassword, VerifyEmail, ConfirmPassword |
| Utility | — | LanguageSwitcher, Notifications/Bell, Notifications/Drawer, FloatingFeedbackButton |

### Key Business Logic

**Invoice:** `INV/{seq}/KSN/{mm}.{yy}` — status: `draft → partially_paid → paid`
**Reimbursement:** `draft → pending → approved → paid` (or `rejected`)
**Fund Request:** `draft → pending → approved → disbursed` (or `rejected`); format `001/KSN/I/2026`
**Recurring:** Manual generation (no scheduled tasks); frequencies: monthly/quarterly/semi_annual/annual
**Bank Balance:** COMPUTED dynamically — `initial_balance + payments(credit) + tx(credit) - tx(debit)` — NOT stored
**Receivable Debtor:** Polymorphic — User OR Client

**Notification events:** `Bell → dispatch('open-notification-drawer') → Drawer` / `Drawer → dispatch('notification-read') → Bell`

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
| RecurringInvoice | `app/Models/RecurringInvoice.php` | publish() converts draft → actual Invoice |
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

**Backend:** Laravel 12, Livewire 3.6, Spatie Permission 6.21, DomPDF 3.1, Maatwebsite Excel 3.1, ngekoding/terbilang, PHP 8.2+

**Frontend:** TallStackUI 2.0, Tailwind CSS 4.1, DaisyUI 5.0, Alpine.js 3.14, Chart.js 4.5, ApexCharts 5.3, Tiptap 2.26, Vite 6.0

---

## Development Guidelines

### Translation & Localization Protocol

**CRITICAL: Audit translation → WAJIB baca blade file DAN PHP component pasangannya.**

```
lang/
├── id/  (UTAMA) — common.php (166 keys), pages.php (1343 keys), invoice.php, feedback.php
├── en/  (partial)
└── zh/  (sync dengan id/ — salin nilai sama, tim update terjemahan Mandarin terpisah)
```

**Skenario A (satu file):**
1. Baca blade target + PHP component pasangannya
2. Identifikasi hardcoded text di KEDUANYA (blade: UI text; PHP: `$headers`, toast/dialog messages, Excel headings)
3. Baca `lang/id/common.php` + `lang/id/pages.php`
4. Audit tabel: Teks | Lokasi | Status | Key | File
5. Tambah missing keys ke `lang/id/` + langsung ke `lang/zh/` (nilai sama)
6. Update blade + PHP component (pindahkan `$headers` ke `mount()` — `__()` tidak bisa di property initializer)
7. Verifikasi tidak ada hardcoded text tersisa

**Skenario B (seluruh folder):** List semua file dulu, kemudian Step A untuk tiap pasangan.

**Pola pasangan:** `resources/views/livewire/cash-flow/expenses.blade.php` ↔ `app/Livewire/CashFlow/Expenses.php`

**❌ JANGAN hardcode:** judul page, label, placeholder, header kolom, teks tombol, badge status, pesan empty/error/sukses, tooltip.
**✅ BOLEH tidak translate:** brand names, kode format (`INV/01/KSN/02.26`), nilai variabel dinamis (`{{ $invoice->number }}`).

**Key naming:** `common.php` untuk reusable; `pages.php` dengan prefix module (`fund_request_title`, `fund_request_status_draft`).

**`$headers` pattern:**
```php
// ❌ SALAH — tidak bisa pakai __() di property initializer
public array $headers = [['label' => __('pages.col_date')]];

// ✅ BENAR
public array $headers = [];
public function mount(): void {
    $this->headers = [['index' => 'date', 'label' => __('pages.col_date')]];
}
```

### Dynamic Translation (Data dari DB)

| Sumber | Metode | Contoh |
|--------|--------|--------|
| UI string hardcoded | `__('file.key')` | `__('common.save')` |
| Enum/status diketahui | `__('pages.status_' . $model->status)` | — |
| Data dari DB (user-generated) | `translate_text($text)` | `translate_text($row->name)` |
| Nama kategori transaksi | `translate_category($name)` | `translate_category($row->label)` |

`translate_text()` dan `translate_category()` ada di `app/helpers.php` + `app/Services/TranslationService.php`. Menggunakan `stichoza/google-translate-php`, cache 6 bulan, fallback ke teks asli jika gagal. Jika tidak muncul di local: `php artisan cache:clear`.

❌ JANGAN: `translate_text('Simpan')` — gunakan `__('common.save')` untuk UI strings statis.

### Currency Input

**SELALU gunakan** `x-currency-input` (bukan `x-input` biasa atau `x-wireui-currency`).

```blade
<x-currency-input wire:model="amount" label="Jumlah *" placeholder="0" />
```

Props: `wire:model`, `label`, `hint`, `placeholder`, `prefix` (default `'Rp'`). Stores raw integer. Reset: `$dispatch('currency-reset')`.

### Component Usage Protocol

**CRITICAL: Sebelum implementasi ANY external component — baca dokumentasi resminya dulu (WebSearch + WebFetch). Jangan implement berdasarkan asumsi atau memory.**

Berlaku untuk: TallStackUI components, Laravel packages, JavaScript libraries.

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Permission issues | `php artisan permission:cache-reset` |
| PDF not generating | Cek `storage/` writable, `php artisan storage:link`, company profile has assets |
| Livewire not refreshing | `unset($this->stats)`, verify `#[On('event-name')]` |
| Balance incorrect | Balance is computed dynamically, NOT stored — cek transaction types |
| Currency display | Parse: `preg_replace('/[^0-9]/', '', $input)`; Format: `'Rp ' . number_format($value, 0, ',', '.')` |
| TallStackUI personalization | `php artisan config:clear && view:clear`, rebuild CSS, verify block name di vendor file |

---

## Important Files Reference

| File | Purpose |
|------|---------|
| `app/Services/InvoicePrintService.php` | PDF generation |
| `app/Models/Invoice.php` | Status & profit calculations |
| `app/Models/Reimbursement.php` | Workflow state machine |
| `app/Models/RecurringTemplate.php` | Recurring date calculations |
| `app/Models/BankAccount.php` | Dynamic balance |
| `app/Providers/AppServiceProvider.php` | TallStackUI personalization |
| `app/helpers.php` | translate_text(), translate_category() |
| `database/seeders/MasterPermissionSeeder.php` | Permission structure |
| `routes/web.php` | All route definitions (35 routes) |
| `resources/views/pdf/kisantra-invoice.blade.php` | Main invoice template |
| `resources/css/app.css` | Color variables, fonts, @source tracking |
