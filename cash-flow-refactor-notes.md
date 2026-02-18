# Cash Flow Refactor Notes

## Tanggal Diskusi
18 Februari 2026

---

## Konteks

Sedang membangun sistem manajemen keuangan berbasis Laravel 12 + Livewire 3 + TallStackUI.

Modul **Arus Kas (Cash Flow)** saat ini menggunakan **tab-based navigation** dalam satu halaman:

```
/cash-flow  →  4 tab:
               - Overview
               - Income (Pemasukan)
               - Expenses (Pengeluaran)
               - Transfers
```

Setiap tab adalah Livewire component yang di-lazy load:
- `livewire:cash-flow.overview-tab`
- `livewire:cash-flow.income-tab`
- `livewire:cash-flow.expenses-tab`
- `livewire:cash-flow.transfers-tab`

---

## Masalah yang Diidentifikasi

**Terlalu berat dalam 1 halaman.** Karena 4 tab dalam 1 halaman berarti potensi 4 Livewire components, masing-masing dengan:
- Query database sendiri (listing, pagination, filter)
- State management sendiri
- Child components (create, delete, categorize, dll)

Meski lazy loading membantu, tetap terasa berat sebagai satu kesatuan halaman.

---

## Solusi yang Disepakati

**Pisah jadi halaman/route terpisah, tapi TANPA sub-menu di sidebar.**

Pola: Satu menu "Arus Kas" di sidebar → klik tab = navigasi ke route terpisah.

### Target Route Structure

```
/cash-flow              →  Overview (default)
/cash-flow/income       →  Income listing
/cash-flow/expenses     →  Expenses listing
/cash-flow/transfers    →  Transfers listing
```

### Keuntungan Pendekatan Ini
- Setiap halaman load hanya 1 Livewire component — jauh lebih ringan
- URL bisa di-bookmark / di-share (misalnya langsung buka `/cash-flow/expenses`)
- Browser back button berfungsi dengan benar
- Sidebar tidak bertambah penuh (tetap 1 menu "Arus Kas")
- Tab navigation di header halaman tetap ada untuk UX yang familiar
- Tetap terasa seperti satu modul

### Referensi Pattern
Mirip dengan Invoice module yang sudah ada:
- `/invoices` → index/listing
- `/invoices/create` → create page
- `/invoices/{id}/edit` → edit page

---

## Implementasi yang Perlu Dilakukan

### 1. Routes (`routes/web.php`)

```php
// Cash Flow - pisah per halaman
Route::get('/cash-flow', CashFlowOverview::class)->name('cash-flow.overview');
Route::get('/cash-flow/income', CashFlowIncome::class)->name('cash-flow.income');
Route::get('/cash-flow/expenses', CashFlowExpenses::class)->name('cash-flow.expenses');
Route::get('/cash-flow/transfers', CashFlowTransfers::class)->name('cash-flow.transfers');
```

### 2. Livewire Components — Jadikan Full-Page

Ubah dari tab coordinator → masing-masing jadi standalone full-page component:

| File Lama | File Baru (Full-Page) |
|-----------|----------------------|
| `app/Livewire/CashFlow/Index.php` (coordinator) | Bisa dihapus atau dijadikan redirect |
| `app/Livewire/CashFlow/OverviewTab.php` | `app/Livewire/CashFlow/Overview.php` |
| `app/Livewire/CashFlow/IncomeTab.php` | `app/Livewire/CashFlow/Income.php` |
| `app/Livewire/CashFlow/ExpensesTab.php` | `app/Livewire/CashFlow/Expenses.php` |
| `app/Livewire/CashFlow/TransfersTab.php` | `app/Livewire/CashFlow/Transfers.php` |

### 3. View Layout — Standard Page Template

Setiap halaman menggunakan standard page template dari design system:

```blade
{{-- Contoh: resources/views/livewire/cash-flow/expenses.blade.php --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 ...">
                Arus Kas
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">Monitor dan kelola transaksi</p>
        </div>
        <livewire:transactions.create-expense />
    </div>

    {{-- Tab Navigation (sebagai link, bukan Livewire tab) --}}
    <div class="flex gap-1 border-b border-secondary-200 dark:border-dark-600">
        <a href="{{ route('cash-flow.overview') }}"
           class="px-4 py-2 text-sm font-medium {{ request()->routeIs('cash-flow.overview') ? 'border-b-2 border-primary-600 text-primary-600' : 'text-dark-500' }}">
            Overview
        </a>
        <a href="{{ route('cash-flow.income') }}" ...>Pemasukan</a>
        <a href="{{ route('cash-flow.expenses') }}" ...>Pengeluaran</a>
        <a href="{{ route('cash-flow.transfers') }}" ...>Transfer</a>
    </div>

    {{-- Content: listing, filter, table --}}
    ...
</div>
```

### 4. Sidebar — Tidak Perlu Diubah

Menu "Arus Kas" di sidebar tetap 1 item, pointing ke `/cash-flow` (overview).

### 5. Attachment Viewer

`livewire:cash-flow.attachment-viewer` yang sekarang ada di `index.blade.php` perlu dipindah ke masing-masing halaman yang membutuhkannya (income, expenses).

---

## Status Saat Ini (Sebelum Refactor)

### Komponen yang Sudah Diperbarui
- ✅ `livewire:transactions.create-income` — dipakai di income-tab
- ✅ `livewire:transactions.create-expense` — dipakai di expenses-tab
- ✅ `QuickActionsOverview` (Bank Accounts) — tombol Tambah Transaksi & Transfer sudah diganti dengan `create-expense` dan `create-income`

### File yang Perlu Direfactor untuk Pendekatan Baru
- `app/Livewire/CashFlow/Index.php`
- `resources/views/livewire/cash-flow/index.blade.php`
- `app/Livewire/CashFlow/OverviewTab.php`
- `app/Livewire/CashFlow/IncomeTab.php` → sudah pakai `create-income`
- `app/Livewire/CashFlow/ExpensesTab.php` → sudah pakai `create-expense`
- `app/Livewire/CashFlow/TransfersTab.php`
- `routes/web.php` — tambah 4 route baru
- Sidebar navigation file (cek di `resources/views/` atau component navigation)

---

## Catatan Teknis

### Create Income & Expense Components
Kedua komponen baru (`CreateIncome`, `CreateExpense`) sudah menggunakan:
- `:request` API route untuk bank accounts dan categories (tidak pakai computed property)
- `x-file-upload` custom component (bukan TallStackUI `x-upload`)
- Alpine `keydown.enter` untuk submit (tidak pakai `<form>`)
- Modal stay open setelah save (reset form, tidak tutup modal)
- Tombol trigger ada **di dalam komponen sendiri**

### API Routes yang Sudah Ada
```php
GET /api/bank-accounts           → route('api.bank-accounts')
GET /api/transaction-categories  → route('api.transaction-categories') + ?type=credit/debit
```

---

## Next Steps di Sesi Berikutnya

1. Implementasi route baru untuk `/cash-flow/*`
2. Rename/buat Livewire components sebagai full-page (bukan tab)
3. Buat shared tab navigation component (atau partial)
4. Pindahkan attachment viewer ke masing-masing halaman
5. Update sidebar navigation link jika diperlukan
6. Test semua halaman berfungsi dengan benar
