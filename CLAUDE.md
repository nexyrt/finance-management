# CLAUDE.md

File ini memberikan panduan kepada Claude Code (claude.ai/code) saat bekerja dengan kode di repository ini.

## Project Overview

**Finance Management System** - Sistem manajemen keuangan berbasis Laravel 12 dengan arsitektur Livewire-first. Dibangun untuk konteks bisnis Indonesia dengan dukungan NPWP/PKP, Terbilang (konversi angka ke kata), dan format Rupiah.

**Fitur Utama:**
- Manajemen Invoice & Pembayaran
- Recurring Invoice (tagihan berulang)
- Reimbursement Karyawan
- Bank Account & Transaksi
- Cash Flow Tracking
- Pinjaman (Loans) & Piutang (Receivables)
- Multi-role Permission System
- PDF Generation dengan multiple template
- Notification System (in-app, real-time bell + slide drawer)
- Feedback System (bug report, feature request, dll)
- Multi-language Support (Bahasa Indonesia & English)
- Excel Export (Invoice & Payment)

## Common Commands

### Development
```bash
# Start semua development servers (artisan + queue + vite)
composer dev

# Atau jalankan individual:
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Database
```bash
php artisan migrate                    # Run migrations
php artisan db:seed                    # Seed users, company profile, permissions
php artisan migrate:fresh --seed       # Fresh migration dengan seeders
```

### Testing & Code Quality
```bash
php artisan test                       # Run tests
./vendor/bin/pint                      # Format code dengan Laravel Pint
php artisan pail                       # View logs real-time
```

### Assets & Dependencies
```bash
npm run build                          # Build untuk production
composer install && npm install        # Install dependencies
```

---

## High-Level Architecture

### Livewire-First Approach

Aplikasi menggunakan **121 Livewire components** yang diorganisir berdasarkan domain bisnis dengan minimal traditional controllers.

**Component Patterns:**
| Pattern | Fungsi | Contoh |
|---------|--------|--------|
| `Index.php` | Coordinator/dashboard dengan statistik | `Invoices/Index.php` |
| `Listing.php` | Tabel dengan filter, search, pagination | `Invoices/Listing.php` |
| `Create.php` | Form pembuatan dengan validasi | `Invoices/Create.php` |
| `Edit.php` | Form update | `Invoices/Edit.php` |
| `Delete.php` | Confirmation dialog | `Invoices/Delete.php` |
| `Show.php` | Detail view | `Invoices/Show.php` |

### Key Patterns

**1. Computed Properties untuk Performance**
```php
#[Computed]
public function stats(): array
{
    // Kalkulasi berat di-cache sampai component refresh
    return [...];
}
```

**2. Event-Driven Refreshing**
```php
#[On('invoice-created')]
public function refreshStats(): void
{
    unset($this->stats); // Force recompute
}
```

**3. Currency Storage (Integer)**
```php
// Semua nilai uang disimpan sebagai integer (smallest unit)
// Database: 150000 → Display: Rp 1.500
// Parsing: preg_replace('/[^0-9]/', '', $input)
// Format: number_format($value, 0, ',', '.')
```

---

## UI/UX Design System

**CRITICAL: Semua page baru atau redesign HARUS mengikuti design system ini untuk konsistensi.**

### Design Principles

**Core Philosophy:**
- **Minimalist** - No fancy decorations, focus on functionality
- **Clean & Readable** - Code dan UI harus mudah dibaca
- **Consistent** - Spacing, typography, dan pattern yang sama di semua page
- **Functional-First** - Prioritas pada komponen dan data, bukan estetika berlebihan

### Layout Guidelines

**CRITICAL: Template ini WAJIB diikuti untuk konsistensi visual yang kuat dan professional!**

**Quick Reference:**
1. **Page Template** - Root `space-y-6` + Header gradient (Invoice pattern)
2. **Stats Cards** - 3 info (statis) atau 4 info (dengan trend) + `rounded-xl`
3. **Filter + Table** - Filter `space-y-4` → Table → Bulk actions (Transactions pattern)
4. **Rounded Standard** - SELALU `rounded-xl` untuk cards & containers

---

### Standard Page Template

**Reference:** `resources/views/livewire/invoices/index.blade.php`

**FIXED (Tidak Boleh Berubah):**
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
    {{-- Stats cards, filters, tables, forms, dll --}}
</div>
```

**Kenapa Template Ini Penting:**
- ✅ **Brand Identity** - User langsung recognize aplikasi
- ✅ **Professional** - Konsisten di semua halaman
- ✅ **Predictable** - User tidak perlu "belajar ulang" tiap pindah page
- ✅ **Clean** - Tidak over-designed, fokus pada konten

---

### Stats Card Templates

**PENTING: Horizontal layout sekarang menjadi STANDAR DEFAULT untuk stats cards.**

### **Horizontal Layout - Icon + Info (STANDAR DEFAULT)**
**Reference:** `resources/views/livewire/clients/index.blade.php`

Layout horizontal mengurangi empty space dan lebih compact. **Gunakan layout ini untuk SEMUA stats cards kecuali ada kebutuhan khusus.**

```blade
{{-- Grid responsive untuk stats cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    {{-- Stats Card dengan icon di kiri, info di kanan --}}
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

**Aturan Horizontal Layout (DEFAULT):**
- ✅ **Card component**: `<x-card class="hover:shadow-lg transition-shadow">`
- ✅ **Main layout**: `flex items-center gap-4`
- ✅ **Icon container**: `h-12 w-12 rounded-xl flex-shrink-0` (mencegah icon compress)
- ✅ **Icon background**: `bg-{color}-50 dark:bg-{color}-900/20` (SOFT colors, bukan -100)
- ✅ **Icon color**: `text-{color}-600 dark:text-{color}-400`
- ✅ **Label**: `text-sm text-dark-600 dark:text-dark-400`
- ✅ **Value**: `text-2xl font-bold text-dark-900 dark:text-dark-50`
- ✅ **Hover effect**: `hover:shadow-lg transition-shadow` (pada card)
- ✅ **Grid gap**: `gap-4` (konsisten, tidak perlu sm:gap-6)

**Color Palette untuk Icons:**
| Color | Background | Icon Color | Usage |
|-------|------------|------------|-------|
| Blue | `bg-blue-50 dark:bg-blue-900/20` | `text-blue-600 dark:text-blue-400` | Total count, general info |
| Green | `bg-green-50 dark:bg-green-900/20` | `text-green-600 dark:text-green-400` | Active status, positive metrics |
| Purple | `bg-purple-50 dark:bg-purple-900/20` | `text-purple-600 dark:text-purple-400` | Companies, categories |
| Red | `bg-red-50 dark:bg-red-900/20` | `text-red-600 dark:text-red-400` | Outstanding, negative metrics |
| Emerald | `bg-emerald-50 dark:bg-emerald-900/20` | `text-emerald-600 dark:text-emerald-400` | Paid, completed |

**Kapan menggunakan horizontal layout:**
- ✅ **SEMUA main page stats** (Clients, Invoices, Services, dll)
- ✅ Inside modals (width terbatas)
- ✅ Sidebar panels
- ✅ Compact areas dengan banyak stats cards
- ✅ **DEFAULT CHOICE** - selalu gunakan kecuali ada alasan kuat

---

### **Vertical Layout - DEPRECATED (Jangan Gunakan)**

Vertical layout sudah **TIDAK DISARANKAN** karena menghasilkan banyak empty space. Gunakan horizontal layout untuk semua stats cards baru.

**Hanya gunakan vertical jika:**
- Legacy code yang belum direfactor
- Dashboard dengan chart/graph yang memerlukan vertical alignment khusus

---

### **Stats Card di Modal (Tanpa <x-card>)**
**Reference:** `resources/views/livewire/clients/show.blade.php` (Financial Tab, Client Header)

Untuk stats card **inside modal** yang tidak memerlukan card wrapper (sudah dalam modal container):

```blade
{{-- Stats Card tanpa x-card component --}}
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

**Perbedaan dengan card wrapper:**
- ❌ JANGAN gunakan `<x-card>` component
- ✅ Gunakan plain `div` dengan `border border-secondary-200 dark:border-dark-600`
- ✅ Tambahkan `p-4` untuk padding
- ❌ JANGAN tambahkan `hover:shadow-lg` (tidak perlu di modal)

---

### **Summary: Quick Decision Tree**

```
Apakah stats card di main page (index)?
├─ YES → Gunakan horizontal layout dengan <x-card>
└─ NO → Apakah di dalam modal?
    ├─ YES → Gunakan horizontal layout TANPA <x-card> (plain div + border)
    └─ NO → Gunakan horizontal layout dengan <x-card>
```

---

### Filter + Table Layout

**Reference:** `resources/views/livewire/transactions/listing.blade.php`

**CRITICAL RULES:**
- ❌ **JANGAN ada judul "Filter"** atau section header
- ❌ **JANGAN ada border/background** di section filter
- ❌ **JANGAN ada "Active Filter Tags"** section dengan border-top
- ✅ **LANGSUNG filter grid + status row** - clean dan minimal
- ✅ Badge aktif filter + result count **cukup di satu baris**

**WAJIB gunakan template ini untuk semua page dengan filter + table:**

```blade
<div class="space-y-6">
    {{-- Filter Section (NO TITLE, NO BORDER!) --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">

            {{-- Main Filters Grid (Responsive) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{n} gap-3">
                {{-- Filter fields --}}
                <x-select.styled wire:model.live="filter1" label="Label" ... />
                <x-select.styled wire:model.live="filter2" label="Label" ... />
                <x-date wire:model.live="date" label="Date" ... />
                {{-- etc --}}
            </div>

            {{-- Search Bar + Filter Status Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                {{-- Left: Search + Status Info --}}
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">

                    {{-- Search Field (Fixed Width) --}}
                    <div class="w-full sm:w-64">
                        <x-input wire:model.live.debounce.300ms="search"
                                 placeholder="Cari..."
                                 icon="magnifying-glass"
                                 class="h-8" />
                    </div>

                    {{-- Active Filters Badge + Result Count --}}
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

                {{-- Right: Additional Actions (Optional) --}}
            </div>

        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$rows :$sort selectable wire:model="selected" paginate loading>
        {{-- Table columns... --}}
    </x-table>

    {{-- Bulk Actions Bar (Optional - if selectable) --}}
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

**Spacing Hierarchy (WAJIB):**
- Root container: `space-y-6`
- Filter wrapper: `space-y-4`
- Filter rows: `gap-4`
- Filter grid: `gap-3`
- Search width: `w-full sm:w-64`

**Kenapa Layout Ini:**
- ✅ **Clean & Minimal** - No title, no border, no redundant elements
- ✅ **Clean Separation** - Filter dan table terpisah jelas
- ✅ **Responsive** - Grid adaptif mobile/desktop
- ✅ **Informative** - User tahu filter aktif & jumlah hasil dalam satu baris
- ✅ **Consistent Spacing** - Visual rhythm yang jelas
- ✅ **Search Prominent** - Fixed width, mudah ditemukan
- ✅ **No Over-Styling** - Langsung di page, tidak pakai card wrapper

**Common Mistakes:**
- ❌ Menambahkan `<h2>Filter</h2>` atau section title
- ❌ Wrapping filter dalam card atau bordered container
- ❌ Membuat "Active Filter Tags" section terpisah dengan border-top
- ❌ Memisahkan badge filter aktif dan result count ke section berbeda

---

### Spacing Rules

**1. Page Level:**
- Root container: `space-y-6` (STANDARD untuk semua page)
- Section spacing: Gunakan parent `space-y-6`

**2. Component Level:**
- Filter section: `space-y-4`
- Element spacing: `mt-2`, `mt-4` untuk spacing kecil
- Stats grid gaps: `gap-4 sm:gap-6`
- Filter grid gaps: `gap-3`

**3. Typography:**
- Page Title: `text-4xl font-bold` dengan gradient (dari template)
- Page Description: `text-lg text-gray-600 dark:text-zinc-400`
- Section Title: `text-xl font-semibold`
- Label/Info: `text-sm`
- Small text: `text-xs`

---

### Modal Form Layout

**Reference:** `resources/views/livewire/transactions/create.blade.php`

**CRITICAL: Semua form dalam modal WAJIB menggunakan styling header & footer ini!**

### Modal Header Structure

**Pattern (Icon + Title + Description):**

```blade
<x-modal title="..." wire="modal" size="xl" center persistent>
    <x-slot:title>
        <div class="flex items-center gap-4 my-3">
            {{-- Icon Container --}}
            <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                <x-icon name="icon-name" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>

            {{-- Title & Description --}}
            <div>
                <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Modal Title</h3>
                <p class="text-sm text-dark-600 dark:text-dark-400">Short description of the action</p>
            </div>
        </div>
    </x-slot:title>

    {{-- Form content... --}}
</x-modal>
```

**Styling Rules:**
- ✅ Icon container: `h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl`
- ✅ Icon: `w-6 h-6 text-primary-600 dark:text-primary-400`
- ✅ Title: `text-xl font-bold text-dark-900 dark:text-dark-50`
- ✅ Description: `text-sm text-dark-600 dark:text-dark-400`
- ✅ Outer wrapper: `flex items-center gap-4 my-3`

**Icon Color Variations (by context):**
```blade
{{-- Default/Primary Action --}}
bg-primary-50 dark:bg-primary-900/20
text-primary-600 dark:text-primary-400

{{-- Success/Create Action --}}
bg-green-50 dark:bg-green-900/20
text-green-600 dark:text-green-400

{{-- Warning/Edit Action --}}
bg-yellow-50 dark:bg-yellow-900/20
text-yellow-600 dark:text-yellow-400

{{-- Danger/Delete Action --}}
bg-red-50 dark:bg-red-900/20
text-red-600 dark:text-red-400

{{-- Info Action --}}
bg-blue-50 dark:bg-blue-900/20
text-blue-600 dark:text-blue-400
```

---

### Modal Footer Structure

**Pattern (Responsive Button Layout):**

```blade
<x-slot:footer>
    <div class="flex flex-col sm:flex-row justify-end gap-3">
        {{-- Cancel Button (Zinc, Solid) --}}
        <x-button wire:click="$set('modal', false)"
                  color="zinc"
                  class="w-full sm:w-auto order-2 sm:order-1">
            Batal
        </x-button>

        {{-- Submit Button (Primary/Colored, with Icon) --}}
        <x-button type="submit"
                  form="form-id"
                  color="primary"
                  icon="check"
                  loading="save"
                  class="w-full sm:w-auto order-1 sm:order-2">
            Simpan
        </x-button>
    </div>
</x-slot:footer>
```

**Styling Rules:**
- ✅ Wrapper: `flex flex-col sm:flex-row justify-end gap-3`
- ✅ Cancel button: `color="zinc"` (solid, better contrast in dark mode)
- ✅ Submit button: `color="primary" icon="check" loading="save" class="w-full sm:w-auto order-1 sm:order-2"`
- ✅ Mobile: Full width, submit button first (order-1)
- ✅ Desktop: Auto width, cancel button first (order-1)
- ✅ **Why zinc?** Better text contrast in dark mode vs outline buttons

**Submit Button Color by Action:**
```blade
{{-- Create/Default --}}
color="primary"

{{-- Confirm/Approve --}}
color="green"

{{-- Update/Edit --}}
color="blue"

{{-- Delete/Danger --}}
color="red"

{{-- Warning Action --}}
color="yellow"
```

---

### Form Content Structure

**Pattern (2-Column Grid with Section Headers):**

```blade
<form id="form-id" wire:submit="save" class="space-y-6">
    {{-- Optional: Selection/Choice Section --}}
    <div class="rounded-xl p-4">
        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">Section Title</h4>
        {{-- Radio buttons / Choices --}}
    </div>

    {{-- Main Form Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column --}}
        <div class="space-y-4">
            {{-- Section Header --}}
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
            </div>

            {{-- Form fields --}}
            <x-input wire:model="field" label="Label *" />
            {{-- etc --}}
        </div>

        {{-- Right Column --}}
        <div class="space-y-4">
            {{-- Section Header --}}
            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
            </div>

            {{-- Form fields --}}
            <x-input wire:model="field" label="Label" />
            {{-- etc --}}
        </div>
    </div>
</form>
```

**Styling Rules:**
- ✅ Form wrapper: `space-y-6`
- ✅ Column grid: `grid grid-cols-1 lg:grid-cols-2 gap-6`
- ✅ Column content: `space-y-4`
- ✅ Section header border: `border-b border-secondary-200 dark:border-dark-600 pb-4`
- ✅ Section title: `text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1`
- ✅ Section desc: `text-xs text-dark-500 dark:text-dark-400`

---

### Complete Modal Template

```blade
<div>
    <x-modal title="Modal Title" wire="modal" size="xl" center persistent>
        {{-- HEADER --}}
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Form Title</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Form description</p>
                </div>
            </div>
        </x-slot:title>

        {{-- FORM CONTENT --}}
        <form id="my-form" wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
                    </div>
                    {{-- Fields --}}
                </div>

                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Section Title</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Section description</p>
                    </div>
                    {{-- Fields --}}
                </div>
            </div>
        </form>

        {{-- FOOTER --}}
        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="my-form" color="primary" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Simpan
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
```

---

### Why This Pattern?

- ✅ **Visual Hierarchy** - Icon + title + desc memberikan context jelas
- ✅ **Professional** - Consistent dengan design system modern (Stripe, Linear, Vercel)
- ✅ **Responsive** - Button order & width adapt mobile/desktop
- ✅ **Organized** - Section headers memisahkan form groups dengan jelas
- ✅ **Accessible** - Loading state, outline cancel, colored action button
- ✅ **Clean** - No over-styling, focus pada content

---

### Card & Border Radius

**Standard rounded level:** `rounded-xl`

Digunakan di:
- ✅ Cards (`<x-card>`)
- ✅ Icon containers
- ✅ Modals
- ✅ Dropdowns
- ✅ Bulk action bars
- ✅ Semua container utama

**Jangan gunakan:**
- ❌ `rounded-lg` (terlalu kecil)
- ❌ `rounded-2xl` (terlalu besar)
- ❌ `rounded-3xl` (over-designed)

**Konsistensi visual = Professional!**

---

### Component Usage

**DO's:**
```blade
{{-- Clean, minimal attribute usage --}}
<x-currency-input
    wire:model.live="amount"
    label="Amount"
/>

{{-- Direct output display --}}
<p class="mt-2 text-sm">Value: {{ $amount }}</p>

{{-- Descriptive comments --}}
{{-- Test 1: Tanpa Prefix --}}
```

**DON'Ts:**
```blade
{{-- ❌ Jangan gunakan warna/dekorasi berlebihan --}}
<div class="border-2 border-green-500 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-2xl p-8">

{{-- ❌ Jangan nested containers berlebihan --}}
<div class="wrapper">
    <div class="inner">
        <div class="content">
            <x-input />
        </div>
    </div>
</div>

{{-- ❌ Jangan typography yang berlebihan --}}
<h1 class="text-5xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r...">
```

**IMPORTANT - Page Header Exception:**
- **Index/Main Pages**: Menggunakan `text-4xl font-bold` **DENGAN gradient** sesuai Layout Template (line 135)
- **Testing/Simple Pages**: Menggunakan `text-2xl font-bold` **TANPA gradient**
- **Modal Headers**: Menggunakan `text-xl font-bold` tanpa gradient

### Color Scheme

**Default Colors Only:**
- Text: `text-dark-900 dark:text-dark-50` untuk primary text
- Secondary text: `text-dark-600 dark:text-dark-400`
- Muted text: `text-dark-500 dark:text-dark-400`
- Backgrounds: Clean white/dark mode (`bg-white dark:bg-dark-800`)
- Borders: `border-secondary-200 dark:border-dark-600`
- Accents: Only when necessary (`text-blue-600 dark:text-blue-400`)

**Avoid:**
- ❌ Custom gradient backgrounds for cards/sections (gradient HANYA untuk page title)
- ❌ Multiple accent colors dalam satu section
- ❌ Heavy shadows (`shadow-2xl`, `shadow-lg`) - gunakan `hover:shadow-lg` untuk stats cards
- ❌ Bright/neon colors

### Code Style

**Blade Templates:**
```blade
{{-- Descriptive comments for sections --}}
{{-- Test 1: Description of what this tests --}}
<div>
    <x-component
        wire:model.live="property"
        label="Label"
        attribute="value"
    />
    <p class="mt-2 text-sm">Output: {{ $property }}</p>
</div>
```

**Livewire Components:**
```php
class PageName extends Component
{
    // Clear, descriptive property comments
    public $property = 0;

    public function render()
    {
        return view('livewire.page-name');
    }
}
```

### Reference Examples

**Good Example (Index/Main Page):**
```blade
<div class="space-y-6">
    {{-- Header with gradient (SESUAI LAYOUT TEMPLATE) --}}
    <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
        Page Title
    </h1>

    {{-- Stats Cards --}}
    <x-card class="hover:shadow-lg transition-shadow">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <x-icon name="icon" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-sm text-dark-600 dark:text-dark-400">Label</p>
                <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">Value</p>
            </div>
        </div>
    </x-card>
</div>
```

**Good Example (Testing/Simple Page):**
```blade
<div class="space-y-8">
    <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50">Page Title</h1>

    {{-- Section 1 --}}
    <div>
        <x-component wire:model.live="data" label="Label" />
        <p class="mt-2 text-sm">Value: {{ $data }}</p>
    </div>
</div>
```

**When to Deviate:**
- Complex dashboards requiring grid layouts
- Data tables requiring specific styling
- Modal/dialog components
- Alert/notification components with semantic colors

**Always Ask First** jika tidak yakin apakah design memerlukan deviasi dari system ini.

### Typography

**Fonts:**
- **Headings (h1-h6)**: Plus Jakarta Sans (weights: 600, 700, 800)
  - Modern dan profesional
  - Digunakan untuk semua heading dan judul
- **Body Text**: Inter (weights: 400, 500, 600, 700)
  - Sangat readable, digunakan di aplikasi finance seperti Stripe, Mercury
  - Digunakan untuk semua body text, paragraphs, labels

**Implementation:**
```css
/* app.css */
--font-sans: 'Inter', ...;
--font-heading: 'Plus Jakarta Sans', ...;

/* Auto-applied */
h1, h2, h3, h4, h5, h6, .heading, .font-heading {
    font-family: var(--font-heading);
}
```

### Dark Mode Color System

**Color Variables** (`resources/css/app.css`):

| Variable | Hex | Usage |
|----------|-----|-------|
| `--color-dark-50` | `#fafafa` | Light mode backgrounds, lightest gray |
| `--color-dark-100` | `#f4f4f5` | Very light backgrounds |
| `--color-dark-200` | `#e4e4e7` | Light borders, dividers |
| `--color-dark-300` | `#d4d4d8` | Medium-light text/borders |
| `--color-dark-400` | `#a1a1aa` | Muted text, icons |
| `--color-dark-500` | `#71717a` | Secondary text |
| `--color-dark-700` | `#27272a` | **Sidebar background** (dark mode) |
| `--color-dark-800` | `#3f3f46` | **Body/Card background** (dark mode) |
| `--color-dark-950` | `#09090b` | Deepest black (rarely used) |

**Key Dark Mode Classes & Usage:**

| Class | Hex | Primary Usage | Components |
|-------|-----|---------------|------------|
| `dark:bg-dark-700` | `#27272a` | **Sidebar, Info boxes, Stats boxes, Selected/Active states** | Sidebar menu, Dashboard stats, Pagination active button, Info sections |
| `dark:bg-dark-800` | `#3f3f46` | **Primary cards, Body background, Modals, Panels, Table rows** | Cards, Modals, Dropdowns, Notification bell, Language switcher, Table striped rows (`dark:bg-dark-800/50`) |
| `dark:border-dark-600` | (Missing - needs definition) | **Card borders, Dividers, Separators** | All card borders, Section dividers, Table borders |
| `dark:border-dark-800` | `#3f3f46` | **Subtle borders, Inner dividers** | Sidebar border, Internal separators |

**Layout Structure:**
```blade
<!-- app.blade.php -->
<body class="dark:bg-dark-800">  <!-- Body: #3f3f46 -->
    <sidebar class="dark:bg-dark-700">  <!-- Sidebar: #27272a (darker) -->
    </sidebar>
    <main>
        <card class="dark:bg-dark-800 dark:border-dark-600">
            <!-- Cards use same color as body for flat design -->
        </card>
    </main>
</body>
```

**Design Principles:**
- Sidebar (`dark-700`) is **darker** than body (`dark-800`) for depth perception
- Cards use `dark-800` background with `dark-600` borders for subtle elevation
- Hover states and active items use `dark-700` for visual feedback
- Table alternating rows use `dark-800/50` (50% opacity) for zebra striping

**Missing Variables (TODO):**
- `--color-dark-600`: Should be defined for border consistency (suggested: `#52525b`)
- `--color-dark-900`: Currently missing, used in some views (suggested: `#1a1a1a`)

### Primary Color Palette

**Professional Blue Theme:**
- Primary: `#2563eb` (blue-600)
- Used for: Buttons, links, active states, focus rings
- Full spectrum: 50-950 defined in `app.css`

**Secondary & Dark:**
- Secondary: Slate/Gray for neutral elements
- Dark: Zinc-based for dark mode backgrounds (see table above)

---

## TallStackUI Soft Personalization

**CRITICAL: SELALU gunakan soft personalization untuk customize TallStackUI components!**

### Workflow: Cara Menemukan Blocks Component

**WAJIB DIIKUTI: Sebelum customize component, HARUS cari tahu blocks-nya terlebih dahulu!**

#### Step 1: Cari File Component di Vendor

```bash
# Contoh: mencari Card component
vendor/tallstackui/tallstackui/src/View/Components/Card.php
```

#### Step 2: Baca Method `personalization()`

Method ini mengembalikan array semua blocks dan classes default:

```php
public function personalization(): array
{
    return Arr::dot([
        'wrapper' => [
            'first' => 'flex justify-center gap-4 min-w-full',
            'second' => 'dark:bg-dark-700 flex w-full flex-col rounded-lg bg-white shadow-md',
        ],
        'header' => [
            'wrapper' => [
                'base' => 'flex items-center justify-between p-4',
                'border' => 'dark:border-b-dark-600 border-b border-gray-100',
            ],
            'text' => [
                'size' => 'text-md font-medium',
                'color' => 'text-secondary-700 dark:text-dark-300',
            ],
        ],
        'body' => 'text-secondary-700 dark:text-dark-300 grow rounded-b-xl px-4 py-5',
        // ... dst
    ]);
}
```

**Key Learning:**
- `Arr::dot()` memflatkan nested array menjadi dot notation
- `wrapper.first` → 'flex justify-center...'
- `wrapper.second` → 'dark:bg-dark-700 flex w-full flex-col **rounded-lg**...'
- `header.text.color` → 'text-secondary-700...'

**Jadi jika ingin ganti `rounded-lg` di Card, targetnya adalah block `wrapper.second`!**

#### Step 3: Tentukan Target Block

Dari hasil Step 2, identifikasi:
1. **Block mana** yang berisi class yang ingin diubah
2. **Class apa** yang mau di-replace/append/remove/prepend

Contoh:
- Mau ganti `rounded-lg` → `rounded-xl`: Target block `wrapper.second`
- Mau ganti `shadow-md` → custom shadow: Target block `wrapper.second`
- Keduanya di block yang sama? Bisa digabung dalam satu `replace()`!

---

### Syntax Soft Personalization

**File:** `app/Providers/AppServiceProvider.php`

#### Format Dasar

```php
use TallStackUi\Facades\TallStackUi;

public function boot(): void
{
    TallStackUi::personalize()
        ->component_name()
        ->block('block.name')      // TANPA parameter kedua!
        ->replace('old', 'new');   // Baru bisa pakai helper methods
}
```

**CRITICAL:**
- `->block('name')` **TANPA parameter kedua** → Bisa pakai helper methods
- `->block('name', 'classes')` **DENGAN parameter kedua** → Complete replacement, TIDAK bisa pakai helper methods

#### Helper Methods (hanya jika block tanpa parameter kedua)

```php
// 1. Replace - Ganti class tertentu
->block('wrapper.second')
->replace('rounded-lg', 'rounded-xl')

// 2. Replace Multiple - Ganti banyak class sekaligus
->block('wrapper.second')
->replace([
    'rounded-lg' => 'rounded-xl',
    'shadow-md' => 'shadow-lg',
])

// 3. Remove - Hapus class
->block('wrapper.second')
->remove('shadow-md')

// 4. Append - Tambah class di akhir
->block('wrapper.second')
->append('border border-gray-200')

// 5. Prepend - Tambah class di awal
->block('wrapper.second')
->prepend('relative')
```

#### Fluent Chaining dengan `->and()`

Untuk personalize multiple components sekaligus:

```php
TallStackUi::personalize()
    ->modal()
    ->block('wrapper.first', 'fixed inset-0 bg-black/30')
    ->and()  // Pindah ke component lain
    ->card()
    ->block('wrapper.second')
    ->replace([
        'shadow-md' => 'border border-zinc-200 shadow-sm',
        'rounded-lg' => 'rounded-xl',
    ])
    ->and()  // Pindah lagi
    ->button()
    ->block('base')
    ->append('transition-all duration-200');
```

---

### Current Implementation

**File:** `app/Providers/AppServiceProvider.php`

```php
// TallStackUI Component Personalization - Professional Blue Theme
TallStackUi::personalize()
    ->modal()
    ->block('wrapper.first', 'fixed inset-0 bg-black/30 transform transition-opacity')
    ->and()
    ->card()
    ->block('wrapper.second')
    ->replace([
        'shadow-md' => 'border border-zinc-200 dark:border-dark-600 shadow-sm hover:shadow-md transition-shadow duration-150',
        'rounded-lg' => 'rounded-xl',
    ]);
```

**Explanation:**
1. **Modal personalization** - Complete replacement untuk `wrapper.first` (backdrop overlay)
2. **Card personalization** - Replace 2 classes di `wrapper.second`:
   - `shadow-md` → custom border + shadow
   - `rounded-lg` → `rounded-xl`

---

### CSS Source Tracking

**WAJIB:** Tambahkan di `resources/css/app.css` agar TailwindCSS track classes dari PHP files:

```css
@source '../../app/Providers/*.php';
```

Sudah ada di project ini (line 8 di `app.css`).

---

### Component Usage After Personalization

```blade
{{-- Card otomatis rounded-xl + custom shadow --}}
<x-card>
    Content here
</x-card>

{{-- Tambah class lain (merge, tidak overwrite) --}}
<x-card class="hover:shadow-lg transition-shadow">
    Content here
</x-card>
```

---

### Common Mistakes to Avoid

**❌ JANGAN:**
```php
// 1. SALAH - Menebak nama block
->card()
->block('wrapper')  // Block 'wrapper' tidak ada! Yang ada 'wrapper.first' dan 'wrapper.second'
->replace('rounded-lg', 'rounded-xl')

// 2. SALAH - Pakai parameter kedua lalu replace
->card()
->block('wrapper.second', 'some classes')  // Dengan parameter kedua...
->replace('rounded-lg', 'rounded-xl')      // ...tidak bisa pakai replace()!

// 3. SALAH - Complete replacement tanpa cek original classes
->card()
->block('wrapper.second', 'rounded-xl')  // Menghilangkan SEMUA classes lain!
```

**✅ LAKUKAN:**
```php
// 1. Cek vendor file dulu, pastikan block name
// vendor/tallstackui/tallstackui/src/View/Components/Card.php

// 2. Gunakan replace untuk partial modification
->card()
->block('wrapper.second')  // Tanpa parameter kedua
->replace('rounded-lg', 'rounded-xl')  // Hanya ganti satu class

// 3. Atau array untuk multiple replacements
->card()
->block('wrapper.second')
->replace([
    'rounded-lg' => 'rounded-xl',
    'shadow-md' => 'shadow-lg',
])
```

---

### Benefits

- ✅ **Centralized** - Satu tempat untuk modifikasi global
- ✅ **No File Creation** - Tidak perlu extend class atau publish views
- ✅ **TailwindCSS Compatible** - Class tracked via @source
- ✅ **Easy to Maintain** - Ganti di AppServiceProvider, apply ke semua
- ✅ **Framework Standard** - Mengikuti TallStackUI documentation
- ✅ **Type Safe** - IDE autocomplete untuk component names
- ✅ **Partial Modification** - Hanya ubah class yang perlu, sisanya tetap

---

### Debugging Tips

**Jika personalization tidak apply:**

1. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan view:clear
   ```

2. **Cek TailwindCSS tracking:**
   - Pastikan `@source '../../app/Providers/*.php';` ada di `app.css`
   - Rebuild CSS: `npm run dev` atau `npm run build`

3. **Verify block name:**
   - Buka `vendor/tallstackui/tallstackui/src/View/Components/{Component}.php`
   - Lihat method `personalization()` untuk nama block yang benar

4. **Check syntax:**
   - Pastikan `->block()` tanpa parameter kedua jika mau pakai `->replace()`
   - Pastikan nama component dan block sesuai dengan vendor file

**JANGAN:**
```blade
{{-- ❌ JANGAN tambahkan rounded manual --}}
<x-card class="rounded-xl">  {{-- Redundant! --}}

{{-- ❌ JANGAN override dengan rounded lain --}}
<x-card class="rounded-lg">  {{-- Inconsistent! --}}
```

**Benefit:**
- ✅ **Zero effort** - Tidak perlu mendefinisikan `rounded-xl` setiap kali
- ✅ **Konsisten** - Semua card pasti `rounded-xl`
- ✅ **Single source of truth** - Ganti di 1 tempat, apply ke semua
- ✅ **Future-proof** - Ganti design system? Edit 1 file aja

---

## Page Structure (Livewire Components)

### Dashboard
**Route:** `/dashboard`
**Component:** `app/Livewire/Dashboard.php`

Dashboard utama dengan metrics overview keuangan perusahaan.

---

### Clients Module
**Route:** `/clients`
**Components:** `app/Livewire/Clients/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Halaman utama dengan statistik client |
| `Create.php` | Form tambah client (individual/company) |
| `Edit.php` | Form edit client |
| `Delete.php` | Modal konfirmasi hapus |
| `Show.php` | Detail client dengan invoice history |
| `Relationship.php` | Manage relasi owner ↔ company |

**Fitur:**
- Tipe client: `individual` atau `company`
- Relasi kepemilikan (owner dapat memiliki banyak company)
- Data pajak: NPWP, KPP, EFIN
- Cascade delete: Client → Invoices → Items → Relationships

---

### Services Module
**Route:** `/services`
**Components:** `app/Livewire/Services/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List semua services dengan search |
| `Create.php` | Form tambah service |
| `Edit.php` | Form edit service |
| `Delete.php` | Modal konfirmasi hapus |

**Fitur:**
- Definisi layanan yang dapat ditagih
- Harga default per service
- Digunakan sebagai template saat membuat invoice item

---

### Invoices Module
**Route:** `/invoices`, `/invoices/create`, `/invoices/{invoice}/edit`
**Components:** `app/Livewire/Invoices/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Dashboard invoice dengan statistik (total, paid, outstanding) |
| `Listing.php` | Tabel invoice dengan filter (status, client, tanggal) |
| `Create.php` | Form pembuatan invoice multi-item |
| `Edit.php` | Form edit invoice (hanya draft) |
| `Delete.php` | Modal konfirmasi hapus |
| `Show.php` | Detail invoice dengan payment history |

**Invoice Number Format:** `INV/{sequence}/KSN/{mm}.{yy}`
- Contoh: `INV/01/KSN/02.26` (Invoice pertama Februari 2026)
- Sequence reset setiap bulan

**Status Flow:**
```
draft → partially_paid → paid
```

**Fitur Create/Edit:**
- Multi-item dengan quantity, unit, unit_price
- COGS tracking per item
- Tax deposit item (excluded from profit)
- Discount (percentage atau fixed amount)
- Auto-calculate subtotal, discount, total
- Upload faktur attachment

**Profit Calculations:**
- `total_cogs` - Sum of all item COGS
- `gross_profit` - total_amount - total_cogs
- `outstanding_profit` - Profit dari invoice belum lunas
- `paid_profit` - Profit dari invoice sudah lunas

---

### Payments Module
**Route:** (embedded dalam Invoice)
**Components:** `app/Livewire/Payments/`

| Component | Fungsi |
|-----------|--------|
| `Listing.php` | Tabel payment dengan filter |
| `Create.php` | Form catat pembayaran |
| `Edit.php` | Form edit payment |
| `Delete.php` | Modal konfirmasi hapus |
| `AttachmentViewer.php` | Modal view bukti transfer |

**Fitur:**
- Link ke invoice dan bank account
- Upload bukti transfer (image/PDF)
- Auto-update invoice status setelah payment
- Payment method tracking

---

### Recurring Invoices Module
**Route:** `/recurring-invoices`, `/recurring-invoices/template/create`, `/recurring-invoices/template/{template}/edit`, `/recurring-invoices/monthly/{invoice}/edit`
**Components:** `app/Livewire/RecurringInvoices/`

**Tab-based Navigation:**

| Tab Component | Fungsi |
|---------------|--------|
| `Index.php` | Coordinator dengan tab navigation |
| `TemplatesTab.php` | List semua template recurring |
| `MonthlyTab.php` | List invoice bulanan (draft/published) |
| `AnalyticsTab.php` | Statistik recurring invoices |

**Template Management:**

| Component | Fungsi |
|-----------|--------|
| `CreateTemplate.php` | Form buat template recurring |
| `EditTemplate.php` | Form edit template |
| `DeleteTemplate.php` | Modal hapus template |
| `ViewTemplate.php` | Detail template |

**Monthly Invoice Management:**

| Component | Fungsi |
|-----------|--------|
| `Monthly/CreateInvoice.php` | Generate invoice dari template |
| `Monthly/EditInvoice.php` | Edit draft invoice |
| `Monthly/DeleteInvoice.php` | Hapus draft invoice |
| `Monthly/ViewInvoice.php` | View invoice detail |

**Workflow:**
1. **Create Template** - Pilih client, frequency, items
2. **Generate Invoices** - Manual trigger via MonthlyTab
3. **Review Drafts** - Review generated drafts
4. **Publish** - Convert draft → actual Invoice

**Frequencies:** `monthly`, `quarterly`, `semi_annual`, `annual`

**Note:** Tidak ada scheduled task, generation dilakukan manual via UI button.

---

### Bank Accounts Module
**Route:** `/bank-accounts`
**Components:** `app/Livewire/Accounts/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List bank accounts dengan balance |
| `Create.php` | Form tambah bank account |
| `Edit.php` | Form edit bank account |
| `Delete.php` | Modal konfirmasi hapus |
| `QuickActionsOverview.php` | Quick action buttons |

**Balance Calculation:**
```php
balance = initial_balance + payments(credit) + transactions(credit) - transactions(debit)
```
- Balance **TIDAK disimpan** di database
- Dihitung dinamis dari relasi Payment dan BankTransaction

---

### Cash Flow Module
**Route:** `/cash-flow`
**Components:** `app/Livewire/CashFlow/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Coordinator dengan tab navigation |
| `OverviewTab.php` | Summary cash flow (income vs expense) |
| `IncomeTab.php` | List pemasukan dengan filter |
| `ExpensesTab.php` | List pengeluaran dengan filter |
| `TransfersTab.php` | Transfer & penyesuaian antar rekening |
| `AttachmentViewer.php` | Modal view bukti transaksi |

---

### Transactions Module
**Route:** `/bank-accounts` (embedded)
**Components:** `app/Livewire/Transactions/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Coordinator |
| `Listing.php` | Tabel transaksi dengan filter |
| `Create.php` | Form catat transaksi (credit/debit) |
| `CreateIncome.php` | Form catat pemasukan (cash flow) |
| `CreateExpense.php` | Form catat pengeluaran (cash flow) |
| `Delete.php` | Modal hapus transaksi |
| `Categorize.php` | Assign kategori ke transaksi |
| `Transfer.php` | Transfer antar bank account |
| `InlineCategoryCreate.php` | Buat kategori baru inline saat input transaksi |

**Transaction Types:** `credit` (masuk) atau `debit` (keluar)

---

### Transaction Categories Module
**Route:** `/transaction-categories`
**Components:** `app/Livewire/TransactionsCategories/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List kategori (hierarchical) |
| `Create.php` | Form tambah kategori |
| `Update.php` | Form edit kategori |
| `Delete.php` | Modal hapus kategori |

**Hierarchical Structure:**
- Parent categories (tanpa parent_code)
- Child categories (dengan parent_code)
- Display format: "Parent → Child"

---

### Reimbursements Module
**Route:** `/reimbursements`
**Components:** `app/Livewire/Reimbursements/`

**View Components (Role-based):**

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Coordinator dengan role-based view |
| `AllRequests.php` | List semua request (admin/manager) |
| `MyRequests.php` | List request sendiri (staff) |

**CRUD Components:**

| Component | Fungsi |
|-----------|--------|
| `Create.php` | Form ajukan reimbursement |
| `Update.php` | Form edit reimbursement (draft only) |
| `Delete.php` | Modal hapus reimbursement |
| `Show.php` | Detail reimbursement |

**Workflow Components:**

| Component | Fungsi |
|-----------|--------|
| `Review.php` | Form approve/reject (manager) |
| `Payment.php` | Form catat pembayaran (finance) |

**Status Flow:**
```
draft → pending → approved → paid
              ↘ rejected
```

**Payment Status:** `unpaid` → `partial` → `paid`

**Key Methods:**
```php
$reimbursement->submit();       // draft → pending
$reimbursement->approve();      // pending → approved
$reimbursement->reject();       // pending → rejected
$reimbursement->recordPayment(); // Record payment
```

**Permission Checks:**
- `canEdit()` - Hanya draft
- `canDelete()` - Hanya draft
- `canSubmit()` - Draft dengan amount > 0
- `canReview()` - Pending status
- `canPay()` - Approved dan belum full paid

---

### Fund Requests Module
**Route:** `/fund-requests`
**Components:** `app/Livewire/FundRequests/`

**View Components (Role-based):**

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Coordinator dengan role-based tab view |
| `AllRequests.php` | List semua request (admin/finance manager) |
| `MyRequests.php` | List request sendiri (staff) |

**CRUD Components:**

| Component | Fungsi |
|-----------|--------|
| `Create.php` | Form buat fund request dengan items |
| `Edit.php` | Form edit fund request (draft/rejected only) |
| `Delete.php` | Modal konfirmasi hapus |
| `Show.php` | Detail fund request |

**Workflow Components:**

| Component | Fungsi |
|-----------|--------|
| `Review.php` | Form approve/reject (finance manager) |
| `Disburse.php` | Form catat pencairan dana (finance) |

**Status Flow:**
```
draft → pending → approved → disbursed
              ↘ rejected ↗ (bisa edit & resubmit)
```

**Priority Levels:** `urgent`, `high`, `medium`, `low`

**Request Number Format:** `{sequence}/{abbreviation}/{roman_month}/{year}`
- Contoh: `001/KSN/I/2026` (Request pertama Januari 2026)
- Sequence reset setiap bulan, zero-padded 3 digit
- Abbreviation dari `CompanyProfile`

**Key Methods:**
```php
$fundRequest->submit();                          // draft → pending
$fundRequest->approve($reviewerId, $notes);      // pending → approved
$fundRequest->reject($reviewerId, $notes);       // pending → rejected
$fundRequest->disburse($bankTransactionId, $date, $disbursedBy, $notes); // approved → disbursed
$fundRequest->calculateTotalAmount();            // Recalculate dari items
```

**Permission Checks:**
- `canEdit()` - Draft atau rejected
- `canDelete()` - Admin selalu bisa, atau draft/rejected
- `canSubmit()` - Draft dengan minimal 1 item dan total > 0
- `canReview()` - Status pending
- `canDisburse()` - Status approved

**Export PDF:**
```
GET /fund-requests/export/pdf?month=&status=&priority=&user_id=&search=&show_requestor=
```

---

### Loans Module
**Route:** `/loans`
**Components:** `app/Livewire/Loans/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List pinjaman dengan statistik |
| `Create.php` | Form catat pinjaman baru |
| `Update.php` | Form edit pinjaman |
| `Delete.php` | Modal hapus pinjaman |
| `PayLoan.php` | Form catat pembayaran pinjaman |

**Tracking:**
- Principal amount & interest
- Interest type & rate
- Term months
- Payment history via LoanPayment

---

### Receivables Module
**Route:** `/receivables`
**Components:** `app/Livewire/Receivables/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List piutang dengan filter |
| `Create.php` | Form catat piutang baru |
| `Update.php` | Form edit piutang |
| `Delete.php` | Modal hapus piutang |
| `Submit.php` | Submit untuk approval |
| `Approve.php` | Form approve/reject |
| `PayReceivable.php` | Form catat pembayaran piutang |

**Polymorphic Debtor:**
```php
// Debtor bisa User (karyawan) atau Client
$receivable->debtor_type = 'App\Models\User'  // atau 'App\Models\Client'
$receivable->debtor_id = $userId              // atau $clientId
```

---

### Feedbacks Module
**Route:** `/feedbacks`
**Components:** `app/Livewire/Feedbacks/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | Coordinator dengan tab view |
| `AllFeedbacks.php` | List semua feedback (admin) |
| `MyFeedbacks.php` | List feedback sendiri |
| `Create.php` | Form submit feedback |
| `Update.php` | Form edit feedback |
| `Delete.php` | Modal hapus feedback |
| `Show.php` | Detail feedback |
| `Respond.php` | Form respond feedback (admin) |

**Standalone Component:**
- `FloatingFeedbackButton.php` - Floating button untuk submit feedback

**Types:** bug, feature, improvement, question
**Priorities:** low, medium, high, critical
**Statuses:** open, in_progress, resolved, closed

---

### Settings Module
**Route:** `/settings/profile`, `/settings/password`, `/settings/company`
**Components:** `app/Livewire/Settings/`

| Component | Fungsi |
|-----------|--------|
| `Profile.php` | Edit profile user |
| `Password.php` | Ganti password |
| `CompanyProfileSettings.php` | Edit company profile |
| `DeleteUserForm.php` | Delete account |

**Company Profile:**
- Nama, alamat, email, phone
- Logo, signature, stamp (untuk PDF)
- PKP status & PPN rate
- Bank accounts (JSON)
- Finance manager info

---

### Users Module (Admin Only)
**Route:** `/admin/users`
**Components:** `app/Livewire/Users/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List users dengan role filter |
| `Create.php` | Form tambah user |
| `Edit.php` | Form edit user & role |
| `Delete.php` | Modal hapus user |

---

### Permissions Module
**Route:** `/permissions`
**Components:** `app/Livewire/Permissions/`

| Component | Fungsi |
|-----------|--------|
| `Index.php` | List permissions by category |
| `Delete.php` | Modal hapus permission |

---

### Roles Module
**Components:** `app/Livewire/Roles/`

| Component | Fungsi |
|-----------|--------|
| `Create.php` | Form buat role baru |
| `Update.php` | Form edit role permissions |
| `Delete.php` | Modal hapus role |

---

### Authentication Module
**Components:** `app/Livewire/Auth/`

| Component | Fungsi |
|-----------|--------|
| `Login.php` | Login form |
| `Register.php` | Register form |
| `ForgotPassword.php` | Request reset password |
| `ResetPassword.php` | Reset password form |
| `VerifyEmail.php` | Email verification |
| `ConfirmPassword.php` | Confirm password |

---

### Utility Components

| Component | Fungsi |
|-----------|--------|
| `Dashboard.php` | Main dashboard |
| `LanguageSwitcher.php` | Toggle bahasa (id/en) |
| `Notifications/Bell.php` | Notification bell + dropdown (header) |
| `Notifications/Drawer.php` | Slide panel semua notifikasi (body level, dipanggil via event `open-notification-drawer`) |
| `Actions/Logout.php` | Logout action |
| `Admin/RoleManagement.php` | Role management page |
| `TestingPage.php` | Testing/development page |
| `Traits/Alert.php` | Reusable alert trait |

**Notification Event Flow:**
```
Bell.php → dispatch('open-notification-drawer') → Drawer.php (x-slide)
Drawer.php → dispatch('notification-read') → Bell.php (refresh badge count)
```

---

## Models & Relationships

### Core Models

#### Invoice
**File:** `app/Models/Invoice.php`

```php
// Fields
invoice_number, billed_to_id, subtotal, discount_amount, discount_type,
discount_value, discount_reason, total_amount, issue_date, due_date,
status, faktur

// Relationships
belongsTo(Client) as client
hasMany(InvoiceItem) as items
hasMany(Payment) as payments

// Accessors
amount_paid, amount_remaining, total_cogs, gross_profit,
outstanding_profit, paid_profit

// Methods
updateStatus() - Auto-update status based on payments
```

#### InvoiceItem
**File:** `app/Models/InvoiceItem.php`

```php
// Fields
invoice_id, client_id, service_name, quantity, unit, unit_price,
amount, cogs_amount, is_tax_deposit

// Relationships
belongsTo(Invoice)
belongsTo(Client)

// Accessors
net_revenue, net_profit, profit_amount
```

#### Payment
**File:** `app/Models/Payment.php`

```php
// Fields
invoice_id, bank_account_id, amount, payment_date, payment_method,
reference_number, attachment_path, attachment_name

// Relationships
belongsTo(Invoice)
belongsTo(BankAccount)

// Methods
hasAttachment(), isImageAttachment(), isPdfAttachment()
```

---

### Recurring Models

#### RecurringTemplate
**File:** `app/Models/RecurringTemplate.php`

```php
// Fields
client_id, template_name, start_date, end_date, next_generation_date,
frequency, status, invoice_template (JSON)

// Relationships
belongsTo(Client)
hasMany(RecurringInvoice, 'template_id')

// Methods
calculateNextGenerationDate()
isDueForGeneration()
```

#### RecurringInvoice
**File:** `app/Models/RecurringInvoice.php`

```php
// Fields
template_id, client_id, scheduled_date, invoice_data (JSON),
status, published_invoice_id

// Relationships
belongsTo(RecurringTemplate, 'template_id')
belongsTo(Client)
belongsTo(Invoice, 'published_invoice_id')

// Methods
publish() - Convert draft to actual Invoice
generateInvoiceNumber()

// Scopes
forMonth(), forYear()
```

---

### Reimbursement Models

#### Reimbursement
**File:** `app/Models/Reimbursement.php`

```php
// Fields
user_id, reviewed_by, title, description, category_input, amount,
amount_paid, expense_date, category_id, attachment_path, attachment_name,
status, payment_status, reviewed_at, review_notes

// Relationships
belongsTo(User) as user
belongsTo(User, 'reviewed_by') as reviewer
belongsTo(TransactionCategory) as category
hasMany(ReimbursementPayment) as payments

// Status Methods
submit(), approve($reviewerId, $notes), reject($reviewerId, $notes)
recordPayment($amount, $bankTransactionId, $payerId, $paymentDate, $notes)

// Check Methods
isDraft(), isPending(), isApproved(), isRejected()
canEdit(), canDelete(), canSubmit(), canReview(), canPay()
isFullyPaid(), hasPartialPayment(), isUnpaid()

// Scopes
forUser(), byStatus(), pending(), approved(), paid(), rejected()
byCategory(), dateBetween(), month(), year()
```

#### ReimbursementPayment
**File:** `app/Models/ReimbursementPayment.php`

```php
// Fields
reimbursement_id, bank_transaction_id, paid_by, amount, payment_date, notes

// Relationships
belongsTo(Reimbursement)
belongsTo(BankTransaction)
belongsTo(User, 'paid_by') as payer
```

---

### Bank & Transaction Models

#### BankAccount
**File:** `app/Models/BankAccount.php`

```php
// Fields
account_name, account_number, bank_name, branch, initial_balance

// Relationships
hasMany(BankTransaction)
hasMany(Payment)
hasMany(LoanPayment)

// Accessors (COMPUTED, not stored)
balance = initial_balance + payments + credits - debits
```

#### BankTransaction
**File:** `app/Models/BankTransaction.php`

```php
// Fields
bank_account_id, category_id, amount, transaction_date, transaction_type,
description, reference_number, attachment_path, attachment_name

// Relationships
belongsTo(BankAccount)
belongsTo(TransactionCategory) as category

// transaction_type: 'credit' | 'debit'
```

#### TransactionCategory
**File:** `app/Models/TransactionCategory.php`

```php
// Fields (Hierarchical)
type, code, label, parent_code

// Relationships
belongsTo(TransactionCategory, 'parent_code', 'code') as parent
hasMany(TransactionCategory, 'parent_code', 'code') as children
hasMany(BankTransaction, 'category_id')

// Scopes
parents(), ofType($type)

// Methods
isParent()

// Accessors
full_path - "Parent → Child"
```

---

### Fund Request Models

#### FundRequest
**File:** `app/Models/FundRequest.php`

```php
// Fields
request_number, user_id, title, purpose, total_amount, priority,
needed_by_date, attachment_path, attachment_name,
status, reviewed_by, reviewed_at, review_notes,
disbursed_by, disbursed_at, disbursement_date, bank_transaction_id, disbursement_notes

// Relationships
belongsTo(User) as user
belongsTo(User, 'reviewed_by') as reviewer
belongsTo(User, 'disbursed_by') as disburser
belongsTo(BankTransaction)
hasMany(FundRequestItem) as items

// Status Methods
submit()                                        // draft → pending
approve($reviewerId, $notes)                    // pending → approved
reject($reviewerId, $notes)                     // pending → rejected
disburse($bankTransactionId, $date, $disbursedBy, $notes) // approved → disbursed
calculateTotalAmount()                          // Recalculate dari sum items

// Check Methods
isDraft(), isPending(), isApproved(), isRejected(), isDisbursed()
canEdit()       // Draft atau rejected
canDelete($user) // Admin selalu bisa, atau draft/rejected
canSubmit()     // Draft dengan minimal 1 item dan total > 0
canReview()     // Pending status
canDisburse()   // Approved status

// Scopes
forUser(), byStatus(), pending(), approved(), disbursed()
byPriority(), urgent(), neededBy($date)

// Auto-generated Request Number
// Format: {sequence}/{abbreviation}/{roman_month}/{year}
// Contoh: 001/KSN/I/2026
```

**priority:** `urgent` | `high` | `medium` | `low`
**status:** `draft` | `pending` | `approved` | `rejected` | `disbursed`

#### FundRequestItem
**File:** `app/Models/FundRequestItem.php`

```php
// Fields
fund_request_id, description, category_id, amount, notes, quantity, unit_price

// Relationships
belongsTo(FundRequest)
belongsTo(TransactionCategory) as category

// Auto-update parent total saat created/updated/deleted
```

---

### Loan & Receivable Models

#### Loan
**File:** `app/Models/Loan.php`

```php
// Fields
loan_number, lender_name, principal_amount, interest_amount,
interest_type, interest_rate, term_months, start_date, maturity_date,
status, purpose, contract_attachment

// Relationships
hasMany(LoanPayment)
```

#### Receivable (Polymorphic)
**File:** `app/Models/Receivable.php`

```php
// Fields
receivable_number, type, debtor_type, debtor_id, principal_amount,
installment_amount, interest_rate, installment_months, loan_date,
due_date, status, purpose, notes, disbursement_account, approved_by,
approved_at, review_notes, rejection_reason, contract_attachment_path

// Polymorphic Relationship
morphTo() as debtor - Can be User OR Client

// Relationships
hasMany(ReceivablePayment) as payments
belongsTo(User, 'approved_by') as approver
```

---

### Other Models

#### Client
**File:** `app/Models/Client.php`

```php
// Fields
name, type, email, NPWP, KPP, EFIN, logo, status,
account_representative, ar_phone_number, person_in_charge, address

// Relationships
belongsToMany(Client) as ownedCompanies (self-referential)
belongsToMany(Client) as owners (self-referential)
hasMany(Invoice, 'billed_to_id') as invoices
hasMany(InvoiceItem) as invoiceItems
morphMany(Receivable, 'debtor') as receivables

// type: 'individual' | 'company'
```

#### CompanyProfile (Singleton)
**File:** `app/Models/CompanyProfile.php`

```php
// Fields
name, address, email, phone, logo_path, signature_path, stamp_path,
is_pkp, npwp, ppn_rate, bank_accounts (JSON),
finance_manager_name, finance_manager_position

// Methods
current() - Get singleton instance
getLogoBase64Attribute() - For PDF embedding
getSignatureBase64Attribute()
getStampBase64Attribute()
```

#### User
**File:** `app/Models/User.php`

```php
// Fields
name, email, password, phone_number, status, locale

// Relationships
morphMany(Receivable, 'debtor') as receivables

// Spatie Traits
HasRoles - admin, finance manager, staff
```

#### Feedback
**File:** `app/Models/Feedback.php`

```php
// Fields
user_id, responded_by, title, description, type, priority, status,
page_url, attachment_path, admin_response, responded_at

// Methods
respond($responderId, $response, $newStatus)
changeStatus($status)
```

#### AppNotification
**File:** `app/Models/AppNotification.php`

```php
// Fields
user_id, type, title, message, data (JSON), read_at

// Methods
notify($userId, $type, $title, $message, $data) - Factory method
markAsRead()
cleanupOld($days)
```

---

## Permission System (Spatie\Permission)

### Three Roles

| Role | Access Level |
|------|--------------|
| `admin` | Full system access |
| `finance manager` | All operations except user management |
| `staff` | Limited (view/create own records) |

### Permission Categories (50 permissions)

```
Clients:         view, create, edit, delete                    (4)
Services:        view, create, edit, delete                    (4)
Invoices:        view, create, edit, delete                    (4)
Payments:        view, create, edit, delete                    (4)
Bank Accounts:   view, create, edit, delete                    (4)
Cash Flow:       view, manage                                  (2)
Transactions:    view, create, edit, delete                    (4)
Categories:      view, manage                                  (2)
Recurring:       view, create, edit, delete, publish           (5)
Reimbursements:  view, create, edit, delete, approve, pay      (6)
Fund Requests:   view, create, edit, delete, approve, disburse (6)
Loans:           view, create, edit, delete, pay               (5)
Receivables:     view, create, edit, delete, approve, pay      (6)
Feedbacks:       view, create, edit, delete, respond, manage   (6)
Permissions:     view, manage                                  (2)
Users:           manage (admin only)                           (1)
```

### Route Protection
```php
Route::middleware('can:view invoices')->name('invoices.index')
Route::middleware('can:create invoices')->name('invoices.create')
```

---

## PDF Generation

**Service:** `app/Services/InvoicePrintService.php`

### Available Templates

| Template | Usage |
|----------|-------|
| `kisantra-invoice` | Default - PT. Kinara Sadayatra Nusantara |
| `semesta-invoice` | Alternative dengan PPN 11% + PPH 22 1.5% |
| `agsa-invoice` | Another branded template |
| `invoice` | Generic template |

### Routes
```php
GET /invoice/{invoice}/download?dp_amount=X&pelunasan_amount=Y&template=kisantra-invoice
GET /invoice/{invoice}/preview?...
```

### Features
- Down Payment (DP) & Settlement (Pelunasan) breakdown
- Tax deposit item exclusion from profit
- Terbilang (angka ke kata dalam Bahasa Indonesia)
- Base64-encoded logo/signature/stamp
- PPN calculation based on PKP status
- Multiple template switching

### Usage
```php
$service = new InvoicePrintService();
$pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount, $template);
return $pdf->stream(); // atau ->download()
```

---

## Directory Structure

```
app/
├── Console/Commands/
├── Exports/                          # Maatwebsite Excel exports
├── Http/
│   ├── Controllers/                  # Minimal usage
│   │   └── Api/
│   ├── Middleware/
│   └── Requests/
├── Livewire/                         # 121 components
│   ├── Accounts/                     # (5) Bank account management
│   ├── Actions/                      # (1) Logout
│   ├── Admin/                        # (1) RoleManagement
│   ├── Auth/                         # (6) Authentication flows
│   ├── CashFlow/                     # (6) Cash flow tracking
│   ├── Clients/                      # (6) Client CRUD + relationships
│   ├── Dashboard.php
│   ├── Feedbacks/                    # (8) Feedback system
│   ├── FloatingFeedbackButton.php
│   ├── FundRequests/                 # (9) Fund request workflow
│   ├── Invoices/                     # (6) Invoice management
│   ├── LanguageSwitcher.php
│   ├── Loans/                        # (5) Loan tracking
│   ├── Notifications/                # (2) Bell + Drawer
│   ├── Payments/                     # (5) Payment processing
│   ├── Permissions/                  # (2) Permission management
│   ├── Receivables/                  # (7) Receivables management
│   ├── RecurringInvoices/            # (12) Recurring invoice system
│   │   ├── Index.php
│   │   ├── TemplatesTab.php
│   │   ├── MonthlyTab.php
│   │   ├── AnalyticsTab.php
│   │   ├── CreateTemplate.php
│   │   ├── EditTemplate.php
│   │   ├── DeleteTemplate.php
│   │   ├── ViewTemplate.php
│   │   └── Monthly/                  # (4) Monthly invoice components
│   │       ├── CreateInvoice.php
│   │       ├── EditInvoice.php
│   │       ├── DeleteInvoice.php
│   │       └── ViewInvoice.php
│   ├── Reimbursements/               # (9) Reimbursement workflow
│   ├── Roles/                        # (3) Role management
│   ├── Services/                     # (4) Service definitions
│   ├── Settings/                     # (4) User & company settings
│   ├── TestingPage.php
│   ├── Traits/                       # (1) Alert trait
│   ├── Transactions/                 # (9) Bank transactions
│   ├── TransactionsCategories/       # (4) Category management
│   └── Users/                        # (4) User management
├── Models/                           # 23 Eloquent models
├── Services/                         # Business logic services
│   ├── InvoicePrintService.php
│   ├── InvoiceExportService.php
│   └── PaymentExportService.php
└── Providers/

database/
├── migrations/                       # 37 migrations
└── seeders/
    ├── DatabaseSeeder.php            # Orchestrator
    ├── MasterPermissionSeeder.php    # 50 permissions, 3 roles
    ├── UserSeeder.php
    ├── CompanyProfileSeeder.php
    └── ... (commented seeders for testing)

resources/views/
├── livewire/                         # Component views
├── pdf/                              # Invoice PDF templates
│   ├── invoice.blade.php
│   ├── kisantra-invoice.blade.php
│   ├── semesta-invoice.blade.php
│   └── agsa-invoice.blade.php
└── exports/

routes/
├── web.php                           # Main routes (35 routes)
└── auth.php

config/
├── permission.php
├── dompdf.php
├── excel.php
├── tallstackui.php
└── wireui.php
```

---

## Tech Stack

### Backend
| Package | Version | Purpose |
|---------|---------|---------|
| Laravel | 12 | Framework |
| Livewire | 3.6 | UI Components |
| Livewire Flux | 2.2 | Additional Livewire features |
| Livewire Volt | 1.7 | Single-file components |
| Spatie Permission | 6.21 | Roles & Permissions |
| Barryvdh DomPDF | 3.1 | PDF Generation |
| Maatwebsite Excel | 3.1 | Excel Export |
| ngekoding Terbilang | 1.0 | Number to Indonesian words |
| PHP | 8.2+ | Runtime |

### Frontend
| Package | Version | Purpose |
|---------|---------|---------|
| TallStackUI | 2.0 | Livewire UI components |
| WireUI | 2.4 | Additional UI components |
| Tailwind CSS | 4.1 | Styling |
| DaisyUI | 5.0 | UI Components |
| Alpine.js | 3.14 | JavaScript framework |
| Chart.js | 4.5 | Charts |
| ApexCharts | 5.3 | Advanced charts |
| Tiptap | 2.26 | Rich text editor |
| dayjs | 1.11 | Date handling |
| Vite | 6.0 | Build tool |

---

## Database Seeding

### Active Seeders (always run)
```bash
php artisan db:seed
```
1. `UserSeeder` - Test users with roles
2. `CompanyProfileSeeder` - Company info for PDF
3. `MasterPermissionSeeder` - 50 permissions & 3 roles

### Commented Seeders (manual testing)
Uncomment in `database/seeders/DatabaseSeeder.php`:
- ServiceSeeder
- BankAccountSeeder
- ClientSeeder
- ClientRelationshipSeeder
- InvoiceSeeder
- BankTransactionSeeder
- RecurringTemplateSeeder
- RecurringInvoiceSeeder
- TransactionCategorySeeder

---

## Troubleshooting

### Permission Issues
```bash
php artisan permission:cache-reset
```

### PDF Not Generating
1. Check `storage/` directory writable
2. Verify symlink: `php artisan storage:link`
3. Check company profile has logo/signature/stamp paths
4. Verify DomPDF config in `config/dompdf.php`

### Livewire Component Not Refreshing
1. Unset computed properties: `unset($this->stats)`
2. Verify event listeners: `#[On('event-name')]`
3. Check browser console for errors

### Balance Calculations Incorrect
1. Balance is computed dynamically, NOT stored
2. Verify transaction types (credit/debit)
3. Ensure relationships loaded

### Currency Display Issues
```php
// Parsing input (remove formatting)
$value = preg_replace('/[^0-9]/', '', $input);

// Display formatting
$display = 'Rp ' . number_format($value, 0, ',', '.');
```

---

## Development Guidelines

### Translation & Localization Protocol

**CRITICAL: Setiap kali diminta untuk translate atau audit translation (baik satu file maupun seluruh folder), WAJIB ikuti langkah-langkah ini secara berurutan tanpa perlu diperintah ulang.**

#### Struktur Translation

```
lang/
├── en/        # English (partial coverage)
├── id/        # Bahasa Indonesia (UTAMA, coverage lengkap)
│   ├── common.php    # Navigation, actions, labels, status (166 keys)
│   ├── pages.php     # UI per-module (1,343 keys - TERBESAR)
│   ├── invoice.php   # Invoice PDF & template (206 keys)
│   └── feedback.php  # Feedback module (158 keys)
└── zh/        # Mandarin (harus selalu disync dengan id/)
```

**Default Locale:** `id` (Bahasa Indonesia)
**Helper:** `__('file.key')` — contoh: `__('common.save')`, `__('pages.invoice_title')`

---

#### Skenario A: Audit Satu File Blade

**Step 1 — Baca file blade target**

**Step 1b — WAJIB: Baca juga PHP Livewire component pasangannya**

Setiap file blade `resources/views/livewire/{module}/listing.blade.php` memiliki pasangan PHP `app/Livewire/{Module}/Listing.php`. File PHP ini WAJIB dibaca karena mengandung:
- **`$headers` array** — label kolom tabel yang tampil di UI
- **Toast messages** — `->warning('...')`, `->success('...')`
- **Dialog messages** — `->question('...')`
- **Excel export headings** — `headings(): array { return ['Tanggal', ...] }`
- **Dropdown options** — string yang tampil di filter/select
- **Error messages** — string hardcoded di method logic

```
# Pola penamaan pasangan:
resources/views/livewire/cash-flow/expenses.blade.php
→ app/Livewire/CashFlow/Expenses.php

resources/views/livewire/invoices/listing.blade.php
→ app/Livewire/Invoices/Listing.php
```

**⚠️ KENAPA INI SERING TERLEWAT:**
Header tabel di TallStackUI `x-table` didefinisikan sebagai PHP property:
```php
// DI PHP FILE — bukan di blade!
public array $headers = [
    ['index' => 'transaction_date', 'label' => 'Tanggal'],  // ← HARDCODED
];
```
Tanpa membaca PHP file, string ini tidak akan terdeteksi saat audit blade.

**CATATAN PENTING untuk `$headers`:**
PHP class property initializer tidak mendukung function call, jadi `__()` tidak bisa langsung di property. Harus dipindahkan ke `mount()`:
```php
// ❌ SALAH — tidak bisa pakai __() di property initializer
public array $headers = [
    ['index' => 'date', 'label' => __('pages.col_date')],  // Error!
];

// ✅ BENAR — pindahkan ke mount()
public array $headers = [];

public function mount(): void
{
    $this->headers = [
        ['index' => 'date', 'label' => __('pages.col_date')],
    ];
}
```

**Step 2 — Identifikasi semua teks hardcoded**

Cek di **dua tempat**: blade file DAN PHP component file.

**Di Blade:**
- Judul, label, placeholder, tooltip, button, empty state
- Badge/status values, teks tabel, pesan konfirmasi
- `__()` yang sudah ada → verifikasi key-nya benar

**Di PHP Component:**
- `$headers` array labels
- `->warning('judul', 'pesan')` — toast warning
- `->success('judul', 'pesan')` — toast success
- `->question('judul', 'pesan')` — dialog konfirmasi
- `headings(): array { return ['...', '...'] }` — Excel headings
- String hardcoded lain di method logic

**Step 3 — Baca file translation**
```
Read lang/id/common.php
Read lang/id/pages.php
```

**Step 4 — Buat daftar audit**
Tampilkan hasil audit dalam format tabel (pisahkan blade vs PHP):
| Teks | Lokasi | Status | Key yang Digunakan/Diusulkan | File |
|------|--------|--------|------------------------------|------|
| "Simpan" | blade | ✅ Ada | `common.save` | common.php |
| "Tanggal" | PHP `$headers` | ❌ Missing | `col_date` | pages.php |
| "Berhasil dihapus" | PHP toast | ❌ Missing | `deleted_success` | pages.php |

**Step 5 — Tambahkan missing keys**
- Umum/reusable → `lang/id/common.php`
- Spesifik module → `lang/id/pages.php`
- Konvensi: `snake_case`, dikelompokkan dengan comment section
- **Langsung tambahkan juga ke `lang/zh/` dengan nilai Mandarin yang sesuai** (bukan copy Indonesian)

**Step 6 — Update blade file DAN PHP component file**
- Blade: ganti semua hardcoded text dengan `__('file.key')`
- PHP `$headers`: pindahkan ke `mount()` jika belum, gunakan `__()` untuk setiap label
- PHP toast/dialog: ganti string hardcoded dengan `__('file.key')`
- PHP Excel headings: extract ke variabel luar anonymous class, inject via constructor

**Step 7 — Verifikasi**
- Baca ulang blade — tidak ada teks hardcoded yang tersisa
- Baca ulang PHP component — tidak ada string hardcoded di $headers, toast, dialog, Excel
- Semua key ada di file translation (tidak ada typo/salah file)

---

#### Skenario B: Audit Seluruh Folder

**Langkah tambahan sebelum Step 1:**

**Step 0 — List SEMUA file di folder (blade DAN PHP)**
```bash
# Blade files (UI)
ls resources/views/livewire/{module}/

# PHP Livewire component files (logic)
ls app/Livewire/{Module}/
```

**Kemudian untuk setiap pasangan (blade + PHP component), jalankan Step 1-7 secara berurutan.**

**Aturan pasangan file:**
- `resources/views/livewire/cash-flow/expenses.blade.php` ↔ `app/Livewire/CashFlow/Expenses.php`
- `resources/views/livewire/invoices/listing.blade.php` ↔ `app/Livewire/Invoices/Listing.php`
- Selalu baca keduanya, jangan hanya blade-nya saja.

**Setelah semua file selesai:**
- Baca ulang `lang/id/pages.php` dan `lang/zh/pages.php` untuk pastikan semua key baru sudah tersync
- Laporkan ringkasan: berapa file diproses (blade + PHP), berapa key baru ditambahkan

---

#### Aturan Wajib (Berlaku di Semua Skenario)

**❌ TIDAK BOLEH ada teks hardcoded untuk:**
- Judul page, deskripsi, subtitle
- Label form, placeholder input
- Header kolom tabel
- Teks tombol / action
- Badge status (contoh: "Draft", "Pending", "Approved")
- Tipe data yang divisualisasikan (contoh: "Kredit", "Debit", "Urgent")
- Pesan empty state, error, sukses
- Tooltip, label konfirmasi modal
- Teks apapun yang terlihat user

**✅ BOLEH tidak ditranslate:**
- Nama brand/produk: `TallStackUI`, `Laravel`
- Kode/format: `INV/01/KSN/02.26`, `001/KSN/I/2026`
- Nilai variabel dinamis: `{{ $invoice->number }}`
- Nama file/path

---

#### Konvensi Penamaan Keys

```php
// common.php — hal yang dipakai di banyak tempat/module
'save'                  // Tombol aksi umum
'cancel'                // Tombol aksi umum
'status'                // Label field
'created_at'            // Label kolom tabel

// pages.php — spesifik per module, gunakan prefix module
'fund_request_title'         // Judul page
'fund_request_description'   // Deskripsi/subtitle page
'fund_request_empty'         // Empty state
'fund_request_status_draft'  // Nilai status spesifik module
```

#### Sync Bahasa zh/ dengan id/

Setiap key baru yang ditambahkan ke `lang/id/` HARUS langsung ditambahkan juga ke `lang/zh/` dengan nilai yang **sama persis** (bukan translate ke Mandarin, cukup copy value-nya). Tim dapat mengupdate terjemahan Mandarin secara terpisah.

```php
// lang/id/pages.php
'fund_request_title' => 'Pengajuan Dana',

// lang/zh/pages.php (tambahkan nilai sama)
'fund_request_title' => 'Pengajuan Dana',
```

---

#### Dynamic Translation (Data Dinamis dari Database)

**Kapan menggunakan Dynamic Translation vs Static `__()`:**

| Sumber Teks | Metode | Contoh |
|-------------|--------|--------|
| UI string (hardcoded) | `__('file.key')` | `__('common.save')` |
| Enum/status diketahui | `__('pages.status_' . $model->status)` | `__('pages.status_draft')` |
| Data dari DB (user-generated) | `translate_text($model->name)` | `translate_text($category->label)` |
| Nama kategori transaksi | `translate_category($name)` | `translate_category($row->label)` |

**Implementasi yang sudah ada di project:**

File: `app/Services/TranslationService.php`
File: `app/helpers.php`

```php
// Translate teks dinamis ke locale user saat ini
translate_text(string $text, string $sourceLang = 'id'): string

// Translate khusus untuk nama kategori transaksi
translate_category(string $categoryName): string
```

**Cara kerja:**
- Menggunakan Google Translate free endpoint (`translate.googleapis.com/translate_a/single`)
- Hasil di-cache selama **6 bulan** dengan key `translation.{src}.{target}.{md5(text)}`
- Fallback ke teks asli jika API gagal (tidak throw exception)
- Tidak translate jika locale saat ini sama dengan `$sourceLang`

**Usage di Blade template:**

```blade
{{-- ✅ Data nama dari DB (user-generated, tidak bisa diprediksi) --}}
{{ translate_text($row->name) }}
{{ translate_text($row->type) }}
{{ translate_category($row->category_label) }}

{{-- ✅ Enum/status yang diketahui nilainya → gunakan __() saja --}}
{{ __('pages.status_' . $row->status) }}
{{ __('pages.priority_' . $row->priority) }}

{{-- ❌ JANGAN gunakan translate_text untuk static UI strings --}}
{{ translate_text('Simpan') }}  {{-- Salah! Gunakan __('common.save') --}}
```

**Contoh dari modul Services (`resources/views/livewire/services/`):**

```blade
{{-- Table column: nama service (data dari DB) --}}
{{ translate_text($row->name) }}

{{-- Badge tipe service (data dari DB, nilai tidak tetap) --}}
{{ translate_text($row->type) }}

{{-- Dropdown option yang hardcoded tapi perlu translate --}}
'options' => [
    ['label' => translate_text('Perizinan'), 'value' => 'Perizinan'],
]
```

**Audit Checklist untuk Dynamic Translation:**

Saat audit translation, identifikasi juga data yang:
1. Berasal dari kolom DB yang diisi user (name, description, label, type, dll)
2. Ditampilkan sebagai teks biasa di tabel/modal (bukan status enum)
3. Kategori atau label yang user-defined

→ Data tersebut harus menggunakan `translate_text()`, **bukan** `__()`.

---

### Component Usage Protocol

**CRITICAL: Always Study Documentation First**

Before implementing ANY external component or library (TallStackUI, WireUI, Laravel packages, JavaScript libraries, etc.), you MUST follow this protocol:

1. **Search for official documentation** using WebSearch tool
2. **Read the complete documentation** using WebFetch tool
3. **Understand the correct usage** including:
   - Available attributes/parameters
   - Livewire integration patterns
   - Common pitfalls and warnings
   - Best practices for the specific component
4. **Only then implement** the component with correct syntax

**Never implement based on assumptions or memory alone.**

**Examples of components requiring documentation review:**
- TallStackUI components (`x-currency`, `x-select`, `x-input`, `x-color`, `x-date`, etc.)
- WireUI components (`x-button`, `x-modal`, `x-notification`, etc.)
- Third-party Livewire components
- New Laravel packages (Spatie packages, intervention/image, etc.)
- JavaScript libraries integration (Chart.js, ApexCharts, Alpine plugins)

**Mandatory Workflow:**
```
User Request → WebSearch official docs → WebFetch full documentation →
Understand attributes & usage → Verify examples → Implement correctly
```

**This protocol applies to:**
- ✅ Initial component implementation
- ✅ Modifications to existing component code
- ✅ Debugging component-related issues
- ✅ Adding new features using components
- ✅ Upgrading/changing component versions

**Why This Matters:**
- Prevents incorrect usage that causes bugs
- Ensures all features are utilized properly
- Reduces trial-and-error implementation
- Maintains code quality standards
- Saves debugging time later

---

## Important Files Reference

| File | Purpose |
|------|---------|
| `app/Services/InvoicePrintService.php` | PDF generation logic |
| `app/Models/Invoice.php` | Invoice status & profit calculations |
| `app/Models/Reimbursement.php` | Complex workflow state machine |
| `app/Models/RecurringTemplate.php` | Recurring logic & date calculations |
| `app/Models/BankAccount.php` | Dynamic balance calculation |
| `database/seeders/MasterPermissionSeeder.php` | Permission structure |
| `routes/web.php` | All route definitions |
| `resources/views/pdf/kisantra-invoice.blade.php` | Main invoice template |
