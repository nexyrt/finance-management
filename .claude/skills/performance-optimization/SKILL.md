---
name: performance-optimization
description: Optimize Livewire component performance by auditing queries, eliminating N+1, reducing query count, and moving computation to SQL. Use this skill when the user asks to optimize, improve performance, or fix slow pages -- including keywords like "optimasi", "lambat", "slow", "performa", "query optimization".
---

# Performance Optimization Protocol

Panduan langkah-demi-langkah untuk mengoptimasi performa Livewire component di project ini.

## Kapan Skill Ini Digunakan

- User meminta "optimasi halaman X" / "optimize page X"
- User melaporkan halaman lambat
- User meminta review performa query

## Prosedur Audit

### Step 1 -- Identifikasi File Target

Setiap page terdiri dari pasangan PHP + Blade:

```
app/Livewire/{Module}/{Component}.php
resources/views/livewire/{module}/{component}.blade.php
```

Baca **semua** PHP component di module tersebut. Fokus pada file PHP karena query ada di sana.

### Step 2 -- Scan Pola Bermasalah

Baca setiap file dan identifikasi masalah berikut:

#### 2a. Query Duplikasi

Cek apakah filter/WHERE clause yang sama ditulis di banyak tempat:

```php
// BAD: rows() dan totalX() punya WHERE clause identik
#[Computed]
public function rows() {
    return Model::where('type', 'debit')
        ->where('status', 'active')
        ->when($this->search, ...)  // 20 baris filter
        ->paginate();
}

#[Computed]
public function totalAmount() {
    return Model::where('type', 'debit')
        ->where('status', 'active')
        ->when($this->search, ...)  // 20 baris filter SAMA
        ->sum('amount');
}

// GOOD: Extract ke shared method
private function getFilteredQuery(): Builder {
    return Model::where('type', 'debit')
        ->where('status', 'active')
        ->when($this->search, ...);
}

#[Computed]
public function rows() {
    return $this->getFilteredQuery()->paginate();
}

#[Computed]
public function totalAmount() {
    return (int) $this->getFilteredQuery()->sum('amount');
}
```

#### 2b. `whereHas` -> JOIN

`whereHas` menghasilkan correlated subquery (`WHERE EXISTS (SELECT ...)`). Untuk filter sederhana, JOIN lebih efisien:

```php
// BAD: whereHas = correlated subquery
BankTransaction::where('transaction_type', 'debit')
    ->whereHas('category', fn($q) => $q->where('type', 'expense'))

// GOOD: JOIN = single scan
BankTransaction::query()
    ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
    ->where('transaction_categories.type', 'expense')
    ->select('bank_transactions.*')  // PENTING: hindari column ambiguity
```

**PENTING saat pakai JOIN:**
- Selalu `->select('main_table.*')` untuk hindari column ambiguity
- Prefix semua column di WHERE dengan table name: `bank_transactions.amount`, bukan `amount`
- OrderBy juga perlu prefix: `->orderBy('bank_transactions.transaction_date', ...)`

#### 2c. N+1 di Loop

```php
// BAD: Query per item
foreach ($this->selected as $id) {
    Model::find($id)?->delete();
}

// GOOD: Batch operation
Model::whereIn('id', $this->selected)->delete();
```

```php
// BAD: Load semua model untuk cek satu field
$items = Model::whereIn('id', $ids)->get();
foreach ($items as $item) {
    if ($item->attachment_path) { ... }
}

// GOOD: Hanya ambil field yang dibutuhkan
$paths = Model::whereIn('id', $ids)
    ->whereNotNull('attachment_path')
    ->pluck('attachment_path');
```

#### 2d. PHP Collection Processing -> SQL Aggregation

```php
// BAD: Load semua record, proses di PHP
$transactions = Transaction::with('category')->get();
$grouped = [];
foreach ($transactions as $t) {
    $key = $t->category->label;
    $grouped[$key] = ($grouped[$key] ?? 0) + $t->amount;
}

// GOOD: GROUP BY di SQL
DB::table('transactions')
    ->join('categories', ...)
    ->selectRaw('categories.label as category, SUM(transactions.amount) as total')
    ->groupBy('categories.label')
    ->get();
```

#### 2e. Multiple Queries per Period -> Batch Query

Jika ada method yang dipanggil per-bulan/per-period dalam loop:

```php
// BAD: 5 queries x 12 months = 60 queries
foreach ($months as $month) {
    $income = $this->calculateIncome($month['start'], $month['end']);   // 4 queries
    $expense = $this->calculateExpense($month['start'], $month['end']); // 1 query
}

// GOOD: 4 batch queries total, distribute di PHP
$allIncome = DB::table('transactions')
    ->whereBetween('date', [$globalStart, $globalEnd])
    ->select('date', 'amount')
    ->get();

// Lalu filter per period dari collection
foreach ($months as $month) {
    $income = $allIncome->whereBetween('date', [$month['start'], $month['end']])->sum('amount');
}
```

#### 2f. UNION: In-Memory Pagination -> DB-Level Pagination

```php
// BAD: Load SEMUA records ke memory, sort & slice di PHP
$query = $payments->union($transactions);
$results = $query->get();                              // SEMUA data
$results = $results->sortByDesc('date');               // PHP sort
$items = $results->slice($offset, $limit)->values();   // PHP slice

// GOOD: Wrap UNION dalam subquery, ORDER BY + LIMIT di DB
$unionQuery = DB::query()
    ->fromSub(function ($query) use ($payments, $transactions) {
        $query->fromSub($payments, 'p')
            ->unionAll(DB::query()->fromSub($transactions, 't'));
    }, 'combined');

$total = $unionQuery->count();
$items = (clone $unionQuery)
    ->orderBy($sortColumn, $sortDirection)
    ->offset($offset)
    ->limit($limit)
    ->get();
```

#### 2g. Model Accessor Query Avoidance

```php
// BAD: Accessor selalu query DB meskipun relation sudah eager loaded
public function getAmountPaidAttribute(): int {
    return $this->payments()->sum('amount');
}

// GOOD: Cek apakah relation sudah di-load
public function getAmountPaidAttribute(): int {
    if ($this->relationLoaded('payments')) {
        return $this->payments->sum('amount');
    }
    return $this->payments()->sum('amount');
}
```

#### 2h. Multiple Computed Properties -> Single Pass

```php
// BAD: 4 computed properties, masing-masing iterasi items
#[Computed] public function netRevenue() { return $this->invoice->items->where(...)->sum('amount'); }
#[Computed] public function totalCogs() { return $this->invoice->items->where(...)->sum('cogs_amount'); }
#[Computed] public function taxDeposits() { return $this->invoice->items->where(...)->sum('amount'); }
#[Computed] public function grossProfit() { return ...; }

// GOOD: Satu computed property, single pass
#[Computed]
public function invoiceMetrics(): array {
    $items = $this->invoice->items;
    $regular = $items->where('is_tax_deposit', false);
    $tax = $items->where('is_tax_deposit', true);
    return [
        'netRevenue' => $regular->sum('amount'),
        'totalCogs' => $regular->sum('cogs_amount'),
        'taxDeposits' => $tax->sum('amount'),
        'grossProfit' => $this->invoice->total_amount - $tax->sum('amount') - $regular->sum('cogs_amount'),
    ];
}

// Delegate individual properties
#[Computed] public function netRevenue(): int { return $this->invoiceMetrics['netRevenue']; }
```

#### 2i. Conditional SUM via CASE WHEN

Jika perlu beberapa SUM dari tabel yang sama dengan kondisi berbeda:

```php
// BAD: 3 query terpisah ke tabel yang sama
$income = Transaction::where('type', 'credit')->where('category', 'income')->sum('amount');
$expense = Transaction::where('type', 'debit')->where('category', 'expense')->sum('amount');
$transfer = Transaction::where('type', 'debit')->where('category', 'transfer')->sum('amount');

// GOOD: 1 query dengan CASE WHEN
Transaction::query()
    ->join('categories', ...)
    ->selectRaw("
        SUM(CASE WHEN type = 'credit' AND categories.type = 'income' THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = 'debit' AND categories.type = 'expense' THEN amount ELSE 0 END) as expense,
        SUM(CASE WHEN type = 'debit' AND categories.type = 'transfer' THEN amount ELSE 0 END) as transfer
    ")->first();
```

### Step 3 -- Buat Daftar Temuan

Setelah scan semua file, tampilkan temuan dalam format:

| File | Issue | Severity | Pattern |
|------|-------|----------|---------|
| Expenses.php | `totalExpense()` duplikasi `rows()` | HIGH | 2a |
| Expenses.php | `whereHas('category')` | MEDIUM | 2b |
| Income.php | `executeBulkDelete` N+1 find per item | MEDIUM | 2c |
| OverviewTab.php | `expenseByCategoryData` load all | HIGH | 2d |
| OverviewTab.php | 60 queries untuk yearly trend | HIGH | 2e |

### Step 4 -- Implementasi

Terapkan fix berdasarkan severity (HIGH dulu, lalu MEDIUM). Untuk setiap fix:

1. Baca file yang akan diubah
2. Terapkan pattern yang sesuai dari Step 2
3. Pastikan table prefix di semua column reference (hindari ambiguity)
4. Verifikasi bahwa output/behavior tidak berubah

### Step 5 -- Verifikasi

```bash
php artisan test
```

Jika test gagal karena perubahan, fix. Jika gagal karena masalah environment (SQLite driver, dll), catat dan lanjut.

## Aturan

- **JANGAN** ubah UI/blade kecuali untuk menambahkan skeleton placeholder
- **JANGAN** tambah fitur baru
- **JANGAN** refactor yang tidak berdampak performa (rename variabel, reformat, dll)
- **JANGAN** ubah business logic -- output harus tetap sama
- **Selalu** prefix column dengan table name saat pakai JOIN
- **Selalu** gunakan `->select('main_table.*')` saat JOIN untuk hindari ambiguity
- **Selalu** cek apakah `export()` method juga perlu update jika `getFilteredQuery()` diubah

## Lazy Loading Component + Skeleton UI

Selain optimasi query, perceived performance bisa ditingkatkan dengan `#[Lazy]` attribute dari Livewire. Component tidak langsung dirender dengan data -- HTML placeholder (skeleton) ditampilkan terlebih dahulu, lalu data dimuat secara async setelah initial page load.

### Kapan Digunakan

- Full-page component atau child component yang melakukan query berat saat mount
- Halaman dengan stats cards + filter + table (pattern umum di project ini)
- Component yang memakan waktu render > 200ms

### Implementasi

**Step 1: Tambah `#[Lazy]` attribute dan `placeholder()` method di PHP component**

```php
use Livewire\Attributes\Lazy;
use Illuminate\Contracts\View\View;

#[Lazy]
class Expenses extends Component
{
    public function placeholder(): View
    {
        return view('livewire.placeholders.cashflow-skeleton');
    }

    // ... rest of component
}
```

**Step 2: Buat skeleton blade yang match dengan layout component**

Skeleton harus meniru struktur visual component (jumlah stats cards, jumlah kolom tabel, ada/tidaknya filter). Gunakan `animate-pulse` dengan warna gray/dark-700.

```blade
<div class="space-y-6 animate-pulse">
    {{-- Stats Cards Skeleton --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach (range(1, 3) as $i)
            <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-5">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-dark-700 rounded-xl flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-2/3"></div>
                        <div class="h-6 bg-gray-200 dark:bg-dark-700 rounded w-3/4"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl overflow-hidden">
        <div class="border-b border-gray-200 dark:border-dark-600 px-4 py-3 flex gap-4">
            @foreach (range(1, 6) as $i)
                <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1"></div>
            @endforeach
        </div>
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-4 border-b border-gray-100 dark:border-dark-700 flex gap-4 items-center">
                @foreach (range(1, 6) as $col)
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1"></div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
```

### Skeleton yang Sudah Tersedia di Project

| File | Struktur | Digunakan Oleh |
|------|----------|----------------|
| `placeholders/table-skeleton.blade.php` | 4 stats + filter bar + table 5 kolom | Clients, Invoices Listing, Loans, Receivables |
| `placeholders/listing-skeleton.blade.php` | Filter bar + table 6 kolom (tanpa stats) | Payments Listing |
| `placeholders/stats-skeleton.blade.php` | 4 stats cards saja | Analytics tabs |
| `placeholders/cashflow-skeleton.blade.php` | Header + 3 stats + 4 filter + table 6 kolom | CashFlow Expenses |

Jika layout component tidak cocok dengan skeleton yang ada, buat file baru di `resources/views/livewire/placeholders/`.

### Aturan Skeleton

- Root element skeleton HARUS sama dengan root element component (biasanya `<div>`)
- Gunakan `animate-pulse` di root element
- Warna skeleton: `bg-gray-200 dark:bg-dark-700` untuk placeholder bars
- Container: `bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl`
- Jumlah stats cards, kolom tabel, dan filter fields harus match dengan component asli
- Jangan tambahkan konten interaktif di skeleton -- murni visual placeholder

## preventLazyLoading

Jika belum ada di `AppServiceProvider`, tambahkan:

```php
use Illuminate\Database\Eloquent\Model;

// Di boot()
Model::preventLazyLoading(!app()->isProduction());
```

Ini membantu mendeteksi N+1 di development -- lazy load akan throw exception alih-alih diam-diam query.
