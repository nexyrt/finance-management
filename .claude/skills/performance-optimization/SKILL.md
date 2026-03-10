---
name: performance-optimization
description: Use when asked to optimize, improve performance, or fix slow pages; or when auditing translation/localization (hardcoded strings, missing lang keys, $headers pattern); or when implementing charts in Livewire components. Keywords: optimasi, lambat, slow, performa, query optimization, translation, translasi, hardcode, lang, localization, chart, grafik, chartjs, alpine chart, dashboard.
---

# Performance Optimization & Chart Protocol

Panduan lengkap untuk optimasi performa Livewire, implementasi chart yang benar, dan audit kualitas kode di project ini.

## Kapan Skill Ini Digunakan

- User meminta "optimasi halaman X" / "optimize page X"
- User melaporkan halaman lambat atau ada N+1 query
- User ingin menambahkan chart/grafik di Livewire component
- User melaporkan chart tidak muncul atau error
- User meminta review performa query
- User meminta audit translasi / hardcoded string

---

## BAGIAN A — Laravel Boost: Database Investigation Protocol

**WAJIB dilakukan sebelum menulis atau mengubah query apapun.** Laravel Boost menyediakan tools yang jauh lebih akurat daripada asumsi dari kode saja.

### A1. Gunakan `database-schema` Sebelum Query

Sebelum menulis JOIN, filter, atau index, selalu cek struktur tabel terlebih dahulu:

```
Tool: mcp__laravel-boost__database-schema
```

Ini mencegah error seperti `Unknown column 'client_id'` karena nama kolom berbeda dari asumsi (contoh: `billed_to_id` bukan `client_id`).

### A2. Gunakan `database-query` untuk Debug Data Aktual

Untuk verifikasi apakah query menghasilkan data yang benar sebelum mengubah kode PHP:

```
Tool: mcp__laravel-boost__database-query
Query: SELECT COUNT(*), transaction_type FROM bank_transactions GROUP BY transaction_type
```

### A3. Gunakan `tinker` untuk Test Eloquent Query

Untuk test apakah eager loading atau computed property berjalan dengan benar:

```
Tool: mcp__laravel-boost__tinker
Code: BankAccount::with(['payments','transactions'])->first()->balance
```

### A4. Gunakan `last-error` Saat Ada Error PHP

Ketika ada error Laravel/PHP, selalu cek ini terlebih dahulu — jauh lebih informatif dari browser log:

```
Tool: mcp__laravel-boost__last-error
```

### A5. Auto-Check Logs Setelah Setiap Implementasi

**Ini WAJIB dilakukan setelah selesai membuat/mengubah kode.** Jangan tunggu user melaporkan error — aktif cek sendiri.

```
# Cek browser errors (JS, Alpine, Livewire frontend)
Tool: mcp__laravel-boost__browser-logs (entries: 15)

# Cek Laravel backend errors
Tool: mcp__laravel-boost__last-error

# Cek application log entries
Tool: mcp__laravel-boost__read-log-entries (entries: 10)
```

Jalankan ketiganya secara paralel. Jika ada error baru yang timestamp-nya setelah implementasi dimulai, perbaiki sebelum menyampaikan hasil ke user.

---

## BAGIAN B — Query Optimization

### Step 1 — Identifikasi File Target

Setiap page terdiri dari pasangan PHP + Blade:

```
app/Livewire/{Module}/{Component}.php
resources/views/livewire/{module}/{component}.blade.php
```

Baca **semua** PHP component di module tersebut. Fokus pada file PHP karena query ada di sana.

### Step 2 — Scan Pola Bermasalah

#### 2a. Query Duplikasi

```php
// BAD: rows() dan totalX() punya WHERE clause identik
#[Computed]
public function rows() {
    return Model::where('type', 'debit')->when($this->search, ...)->paginate();
}
#[Computed]
public function totalAmount() {
    return Model::where('type', 'debit')->when($this->search, ...)->sum('amount'); // DUPLIKAT
}

// GOOD: Extract ke shared method
private function getFilteredQuery(): Builder {
    return Model::where('type', 'debit')->when($this->search, ...);
}
#[Computed] public function rows() { return $this->getFilteredQuery()->paginate(); }
#[Computed] public function totalAmount() { return (int) $this->getFilteredQuery()->sum('amount'); }
```

#### 2b. `whereHas` → JOIN

`whereHas` menghasilkan correlated subquery. Untuk filter sederhana, JOIN lebih efisien:

```php
// BAD
BankTransaction::whereHas('category', fn($q) => $q->where('type', 'expense'))

// GOOD
BankTransaction::query()
    ->join('transaction_categories', 'bank_transactions.category_id', '=', 'transaction_categories.id')
    ->where('transaction_categories.type', 'expense')
    ->select('bank_transactions.*') // PENTING: hindari column ambiguity
```

**Selalu prefix column dengan table name saat JOIN** — gunakan `database-schema` untuk konfirmasi nama kolom yang benar.

#### 2c. N+1 di Loop

```php
// BAD: Query per item
foreach ($this->selected as $id) { Model::find($id)?->delete(); }

// GOOD: Batch operation
Model::whereIn('id', $this->selected)->delete();
```

#### 2d. PHP Collection Processing → SQL Aggregation

```php
// BAD: Load semua record ke PHP
$transactions = Transaction::with('category')->get();
$grouped = collect($transactions)->groupBy('category.label')->map->sum('amount');

// GOOD: GROUP BY di SQL
DB::table('transactions')->join('categories', ...)
    ->selectRaw('categories.label as category, SUM(amount) as total')
    ->groupBy('categories.label')->get();
```

#### 2e. Multiple Queries per Period → Batch + CASE WHEN

```php
// BAD: Query per bulan × tipe = banyak query
foreach ($months as $month) {
    $income = Transaction::whereBetween('date', ...)->where('type', 'credit')->sum('amount');
    $expense = Transaction::whereBetween('date', ...)->where('type', 'debit')->sum('amount');
}

// GOOD: 1 query GROUP BY + CASE WHEN
BankTransaction::select(
    DB::raw('YEAR(transaction_date) as yr'),
    DB::raw('MONTH(transaction_date) as mo'),
    DB::raw("SUM(CASE WHEN transaction_type = 'credit' THEN amount ELSE 0 END) as income"),
    DB::raw("SUM(CASE WHEN transaction_type = 'debit' THEN amount ELSE 0 END) as expenses"),
)->whereBetween('transaction_date', [$start, $end])
    ->groupBy('yr', 'mo')->orderBy('yr')->orderBy('mo')->get();
```

#### 2f. N+1 pada Accessor yang Query DB

```php
// BAD: Accessor selalu query DB meskipun relation sudah eager loaded
public function getBalanceAttribute(): int {
    return $this->payments()->sum('amount'); // N+1 jika tidak eager loaded
}

// GOOD: Cek apakah relation sudah di-load
public function getBalanceAttribute(): int {
    if ($this->relationLoaded('payments')) {
        return $this->payments->sum('amount');
    }
    return $this->payments()->sum('amount');
}
```

**Cara deteksi:** Cek apakah computed property memanggil accessor pada collection. Jika iya, pastikan `->with(['relation'])` ada di query.

#### 2g. Multiple Computed Properties → Single Pass

```php
// BAD: 4 computed properties, masing-masing iterasi items sendiri
#[Computed] public function netRevenue() { return $this->invoice->items->where(...)->sum('amount'); }
#[Computed] public function totalCogs() { return $this->invoice->items->where(...)->sum('cogs_amount'); }

// GOOD: Satu computed property, single pass
#[Computed]
public function invoiceMetrics(): array {
    $items = $this->invoice->items;
    $regular = $items->where('is_tax_deposit', false);
    return [
        'netRevenue' => $regular->sum('amount'),
        'totalCogs' => $regular->sum('cogs_amount'),
    ];
}
#[Computed] public function netRevenue(): int { return $this->invoiceMetrics['netRevenue']; }
```

### Step 3 — Buat Daftar Temuan

| File | Issue | Severity | Pattern |
|------|-------|----------|---------|
| Expenses.php | `totalExpense()` duplikasi `rows()` | HIGH | 2a |
| OverviewTab.php | 60 queries untuk yearly trend | HIGH | 2e |

### Step 4 — Implementasi

Terapkan fix berdasarkan severity (HIGH dulu, lalu MEDIUM). Untuk setiap fix:
1. Gunakan `database-schema` untuk konfirmasi nama kolom
2. Gunakan `database-query` atau `tinker` untuk verifikasi hasil query sebelum apply
3. Terapkan pattern yang sesuai
4. Jalankan `php artisan test` setelah selesai

### Step 5 — Verifikasi Wajib (Bagian A5)

Setelah implementasi, jalankan auto-check logs seperti di Bagian A5.

---

## BAGIAN C — Chart di Livewire: Alpine.data() Pattern

**Pattern resmi yang digunakan di project ini** (berdasarkan `accounts/index.blade.php`).

### C1. Pattern yang BENAR: Alpine.data() Registry

Gunakan `Alpine.data()` di dalam `@script` block — **bukan** `window.*` di `app.js`, **bukan** `x-init` dengan fungsi inline, **bukan** `@push('scripts')`.

#### HTML Template

```blade
{{-- Setiap chart: wire:ignore + x-data dengan chartType dan data awal --}}
<div class="h-[260px]" wire:ignore
     x-data="dashboardCharts('cashFlow', @js($this->cashFlowChart))">
    <canvas x-ref="canvas"></canvas>
</div>
```

Poin penting:
- `wire:ignore` — wajib, mencegah Livewire merusak DOM chart
- `x-data="namaComponent('chartType', @js($data))"` — nama komponen Alpine + tipe chart + data awal dari PHP
- `x-ref="canvas"` — bukan `id=`, bukan `querySelector('canvas')`

#### @script Block Template

```blade
@script
<script>
(function () {
    function registerCharts() {
        if (typeof Alpine === 'undefined') return;

        Alpine.data('dashboardCharts', (chartType, initialData) => ({
            chart: null,
            data: initialData,

            // Helper methods
            isDark() { return document.documentElement.classList.contains('dark'); },
            textColor() { return this.isDark() ? '#a1a1aa' : '#71717a'; },
            gridColor() { return this.isDark() ? '#3f3f46' : '#f4f4f5'; },
            tooltipTheme() {
                return {
                    backgroundColor: this.isDark() ? '#27272a' : '#ffffff',
                    titleColor: this.isDark() ? '#fafafa' : '#09090b',
                    bodyColor: this.isDark() ? '#d4d4d8' : '#52525b',
                    borderColor: this.isDark() ? '#52525b' : '#e4e4e7',
                    borderWidth: 1, padding: 10, cornerRadius: 8,
                };
            },
            formatRp(v) {
                if (v >= 1e9) return 'Rp ' + (v / 1e9).toFixed(1) + 'M';
                if (v >= 1e6) return 'Rp ' + (v / 1e6).toFixed(0) + 'jt';
                if (v >= 1e3) return 'Rp ' + (v / 1e3).toFixed(0) + 'rb';
                return 'Rp ' + v;
            },
            formatFull(v) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(v); },

            // Router: chartType menentukan render method mana yang dipanggil
            render() {
                if (typeof Chart === 'undefined') return;
                if (chartType === 'cashFlow') this.renderLineChart();
                if (chartType === 'barChart') this.renderBarChart();
                if (chartType === 'donut') this.renderDonutChart();
            },

            init() {
                const self = this;
                // Render setelah DOM siap
                this.$nextTick(() => self.render());

                // Update data saat Livewire dispatch event (misal: period berubah)
                Livewire.on('dashboard-charts-updated', (payload) => {
                    const d = payload[0];
                    if (chartType === 'cashFlow' && d.cashFlow) {
                        self.data = d.cashFlow;
                        self.render();
                    }
                    // tambah chartType lain sesuai kebutuhan
                });

                // Re-render saat dark mode toggle
                this._themeObserver = new MutationObserver(() => {
                    if (self.chart) setTimeout(() => self.render(), 50);
                });
                this._themeObserver.observe(document.documentElement, {
                    attributes: true, attributeFilter: ['class'],
                });
            },

            destroyChart() {
                if (this.chart) { this.chart.destroy(); this.chart = null; }
            },

            // Render methods — satu per tipe chart
            renderLineChart() {
                this.destroyChart();
                if (!this.data || !this.data.length || !this.$refs.canvas) return;
                const self = this;
                this.chart = new Chart(this.$refs.canvas, {
                    type: 'line',
                    data: {
                        labels: this.data.map(d => d.label),
                        datasets: [{
                            label: @js(__('pages.income')),
                            data: this.data.map(d => d.income),
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.07)',
                            fill: true, tension: 0.4, borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: Object.assign({}, self.tooltipTheme(), {
                                callbacks: { label: ctx => ' ' + ctx.dataset.label + ': ' + self.formatFull(ctx.parsed.y) },
                            }),
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: self.textColor(), font: { size: 11 }, callback: v => self.formatRp(v) }, grid: { color: self.gridColor(), drawBorder: false } },
                            x: { ticks: { color: self.textColor(), font: { size: 11 } }, grid: { display: false } },
                        },
                    },
                });
            },

            destroy() {
                this.destroyChart();
                if (this._themeObserver) this._themeObserver.disconnect();
            },
        }));
    }

    registerCharts();

    // Re-register setelah SPA navigation (wire:navigate)
    document.addEventListener('livewire:navigated', () => {
        registerCharts();
    });
})();
</script>
@endscript
```

### C2. Update Data dari PHP (saat filter/period berubah)

Di PHP component, dispatch event setelah computed property direset:

```php
public function updatedChartPeriod(): void
{
    unset($this->cashFlowChart);
    unset($this->revenueVsExpensesChart);

    // Dispatch ke Alpine — payload harus match dengan key yang dicek di Livewire.on()
    $this->dispatch('dashboard-charts-updated',
        cashFlow: $this->cashFlowChart,
        revenueExpense: $this->revenueVsExpensesChart,
    );
}
```

### C3. Pola yang SALAH — Jangan Gunakan

```blade
{{-- ❌ SALAH: const di top-level @script = SyntaxError di Alpine's AsyncFunction --}}
@script
<script>
const isDark = () => ...;
window.initChart = (canvas, data) => { ... };
</script>
@endscript

{{-- ❌ SALAH: window.* di app.js = timing issue, tidak ada akses ke @js() --}}
{{-- (app.js tidak bisa pakai @js() untuk translate string) --}}

{{-- ❌ SALAH: x-init dengan fungsi inline yang panjang --}}
<div x-init="chart = new Chart(...banyak config...)" wire:ignore>

{{-- ❌ SALAH: querySelector('canvas') alih-alih x-ref="canvas" --}}
<div x-init="chart = initChart($el.querySelector('canvas'), data)">
```

### C4. Checklist Sebelum Implementasi Chart

1. `wire:ignore` ada di div wrapper chart ✓
2. `x-ref="canvas"` ada di `<canvas>` element ✓
3. `Alpine.data()` diregistrasi di `@script`, bukan di `app.js` ✓
4. `init()` method pakai `this.$nextTick()` sebelum render ✓
5. `destroyChart()` dipanggil sebelum membuat chart baru ✓
6. Dark mode observer terpasang di `init()` ✓
7. `destroy()` method membersihkan observer ✓
8. `document.addEventListener('livewire:navigated', ...)` untuk SPA navigation ✓

---

## BAGIAN D — Lazy Loading + Skeleton UI

### Kapan Digunakan

- Component yang melakukan query berat saat mount
- Halaman dengan stats cards + filter + table
- Component dengan render time > 200ms

### Implementasi

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class Expenses extends Component
{
    public function placeholder(): View
    {
        return view('livewire.placeholders.cashflow-skeleton');
    }
}
```

### Skeleton yang Tersedia

| File | Struktur | Digunakan Oleh |
|------|----------|----------------|
| `placeholders/table-skeleton.blade.php` | 4 stats + filter + table 5 kolom | Clients, Invoices |
| `placeholders/stats-skeleton.blade.php` | 4 stats cards saja | Analytics tabs |
| `placeholders/cashflow-skeleton.blade.php` | Header + 3 stats + 4 filter + table | CashFlow Expenses |

Buat file baru di `resources/views/livewire/placeholders/` jika tidak ada yang cocok.

---

## BAGIAN E — Translation & Localization Protocol

### Kapan Diaudit

Setiap kali membuat atau memodifikasi file PHP/Blade.

### Prosedur Audit

1. Baca blade target + PHP component pasangannya
2. Identifikasi hardcoded text: UI labels, placeholders, `$headers`, pesan toast/dialog
3. Baca `lang/id/common.php` + `lang/id/pages.php`
4. Audit tabel: `Teks | Lokasi | Status | Key | File`
5. Tambah missing keys ke `lang/id/` + `lang/zh/` (nilai sama)
6. Update blade + PHP component

### `$headers` — Pola Wajib

```php
// ❌ SALAH — error saat boot
public array $headers = [['label' => __('pages.col_date')]];

// ✅ BENAR
public array $headers = [];
public function mount(): void
{
    $this->headers = [['index' => 'date', 'label' => __('pages.col_date')]];
}
```

### Dynamic Translation

| Sumber | Metode |
|--------|--------|
| UI string hardcoded | `__('file.key')` |
| Data dari DB (user-generated) | `translate_text($text)` |
| Nama kategori transaksi | `translate_category($name)` |

---

## BAGIAN F — preventLazyLoading

Pastikan ada di `AppServiceProvider::boot()`:

```php
Model::preventLazyLoading(!app()->isProduction());
```

Ini membantu deteksi N+1 — lazy load akan throw exception di development.

---

## Aturan Global

- **JANGAN** ubah UI/blade kecuali untuk chart atau skeleton placeholder
- **JANGAN** tambah fitur baru atau refactor di luar scope
- **JANGAN** ubah business logic — output harus tetap sama
- **SELALU** gunakan `database-schema` sebelum menulis JOIN atau migration
- **SELALU** jalankan auto-check logs (Bagian A5) setelah selesai implementasi
- **SELALU** prefix column dengan table name saat pakai JOIN
- **SELALU** cek `export()` method jika `getFilteredQuery()` diubah
