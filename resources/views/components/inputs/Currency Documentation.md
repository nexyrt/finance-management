# Currency Input Component Documentation

## Overview
Komponen input untuk mata uang Rupiah (IDR) dengan format otomatis menggunakan titik sebagai pemisah ribuan. Kompatibel dengan Laravel Livewire dan TALL Stack.

## Basic Usage

### 1. Dengan Livewire (Recommended)
```html
<x-inputs.currency wire:model.live="amount" />
```

### 2. Tanpa Livewire (Display Only)
```html
<x-inputs.currency :value="500000" />
```

## Props Available

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | string | 'Nominal' | Label untuk input |
| `placeholder` | string | '50.000.000' | Placeholder text |
| `required` | boolean | false | Apakah field wajib diisi |
| `name` | string | 'amount' | Nama field untuk validation |
| `size` | string | null | Ukuran: `sm`, `xs`, atau `null` |
| `value` | numeric | null | Nilai default (jangan pakai dengan wire:model) |
| `hint` | string | null | Text bantuan di bawah input |
| `disabled` | boolean | false | Status disabled |

## Examples

### Penggunaan Dasar
```html
<!-- Minimal -->
<x-inputs.currency wire:model.live="amount" />

<!-- Dengan label custom -->
<x-inputs.currency 
    wire:model.live="price" 
    label="Harga Produk" 
/>
```

### Form Lengkap
```html
<form wire:submit="save">
    <x-inputs.currency 
        wire:model.live="payment_amount" 
        label="Jumlah Pembayaran"
        :required="true"
        name="payment_amount"
        hint="Minimal pembayaran Rp 100.000"
    />
    
    <x-inputs.currency 
        wire:model="admin_fee" 
        label="Biaya Admin"
        size="sm"
    />
    
    <button type="submit">Simpan</button>
</form>
```

### Different Sizes
```html
<!-- Default size -->
<x-inputs.currency wire:model="amount" />

<!-- Small -->
<x-inputs.currency wire:model="amount" size="sm" />

<!-- Extra small -->
<x-inputs.currency wire:model="amount" size="xs" />
```

### Display Only (Disabled)
```html
<x-inputs.currency 
    :value="$calculatedTotal"
    label="Total Kalkulasi"
    :disabled="true"
/>
```

## Livewire Component Setup

### Basic Component
```php
<?php

namespace App\Livewire;

use Livewire\Component;

class PaymentForm extends Component
{
    public $amount = 0;
    public $fee = 15000; // Default value
    public $total = 0;
    
    protected $rules = [
        'amount' => 'required|numeric|min:1000',
        'fee' => 'required|numeric',
    ];
    
    public function mount()
    {
        $this->calculateTotal();
    }
    
    public function updatedAmount()
    {
        $this->calculateTotal();
    }
    
    public function calculateTotal()
    {
        $this->total = ($this->amount ?: 0) + ($this->fee ?: 0);
    }
    
    public function save()
    {
        $this->validate();
        
        // Process data
        // $this->amount contains clean numeric value
    }
    
    public function render()
    {
        return view('livewire.payment-form');
    }
}
```

### Form Edit (dengan data existing)
```php
public function mount($invoice = null)
{
    if ($invoice) {
        $this->amount = $invoice->amount;
        $this->fee = $invoice->admin_fee;
    }
}
```

## Important Rules

### ✅ DO
- Gunakan `wire:model.live` untuk real-time updates
- Set default value di Livewire property, bukan di prop `:value`
- Gunakan prop `:value` hanya untuk display-only
- Gunakan `name` prop untuk validation

### ❌ DON'T
- Jangan gunakan `:value` bersamaan dengan `wire:model`
- Jangan set value langsung di template jika menggunakan Livewire

## Data Format

### Input Format
User dapat mengetik:
- `500000` → otomatis jadi `500.000`
- `1000000` → otomatis jadi `1.000.000`
- `Rp 5.000.000` → otomatis dibersihkan jadi `5.000.000`

### Output Format
- **Display**: `500.000` (dengan titik pemisah)
- **Livewire Property**: `500000` (numeric murni)
- **Database**: `500000` (siap disimpan)

## Validation Example

```php
protected $rules = [
    'amount' => 'required|numeric|min:1000|max:999999999',
];

protected $messages = [
    'amount.required' => 'Jumlah harus diisi.',
    'amount.numeric' => 'Jumlah harus berupa angka.',
    'amount.min' => 'Jumlah minimal Rp 1.000.',
    'amount.max' => 'Jumlah maksimal Rp 999.999.999.',
];
```

## Error Handling

Error otomatis ditampilkan jika nama field sama dengan validation:

```html
<x-inputs.currency 
    wire:model="amount"
    name="amount"
    label="Jumlah"
/>
<!-- Error akan muncul otomatis jika validation gagal -->
```

## Troubleshooting

### Value selalu jadi 0
**Penyebab**: Menggunakan `:value` bersamaan dengan `wire:model`
**Solusi**: Hapus prop `:value`, set default di Livewire property

### Input tidak ter-format
**Penyebab**: Alpine.js tidak loaded atau conflict
**Solusi**: Pastikan Alpine.js sudah di-include di layout

### Validation tidak jalan
**Penyebab**: Prop `name` tidak di-set
**Solusi**: Tambahkan `name="field_name"` yang sama dengan Livewire property

## Advanced Usage

### Conditional Required
```html
<x-inputs.currency 
    wire:model="amount"
    :required="$isPaymentRequired"
    label="Jumlah Pembayaran"
/>
```

### Dynamic Placeholder
```html
<x-inputs.currency 
    wire:model="amount"
    placeholder="{{ $maxAmount ? number_format($maxAmount, 0, ',', '.') : '50.000.000' }}"
/>
```

### Custom Styling
```html
<x-inputs.currency 
    wire:model="amount"
    class="border-red-500"
    label="Jumlah"
/>
```