# Invoice Template Implementation Guide

## 📋 Overview

Implementasi fitur **multiple invoice templates** untuk mendukung berbagai format invoice sesuai kebutuhan perusahaan yang berbeda. Sistem ini memungkinkan:

1. ✅ **Multiple Templates** - Pilih template invoice per invoice atau set default per company
2. ✅ **Unit Field** - Tambah kolom satuan (M³, Kg, Pcs, dll) di invoice items
3. ✅ **PPH 22 Support** - Kalkulasi PPH 22 (withholding tax) on-the-fly
4. ✅ **Dynamic Calculations** - PPN dan PPH dihitung saat generate PDF, tidak disimpan di database

---

## 🔄 Database Changes

### Migration File
📁 `database/migrations/2026_01_30_000001_add_template_fields_to_invoices.php`

**Yang Ditambahkan:**

### 1. Table: `invoice_items`
```php
$table->string('unit')->nullable()->after('quantity');
// Contoh: M³, Kg, Pcs, Ton, Liter, dll
```

### 2. Table: `invoices`
```php
$table->string('template')->default('kisantra-invoice')->after('status');
// Options: 'kisantra-invoice', 'semesta-invoice', 'agsa-invoice', 'invoice'
```

### 3. Table: `company_profiles`
```php
// Default template untuk company
$table->string('default_invoice_template')->default('kisantra-invoice');

// PPH 22 settings (untuk kalkulasi di PDF)
$table->decimal('pph22_rate', 5, 2)->default(0); // Contoh: 1.5 untuk 1.5%
$table->boolean('apply_pph22')->default(false);  // Enable/disable PPH 22
```

---

## 📊 Perbedaan Template: Kisantra vs Semesta

### **Kisantra Invoice** (Template saat ini)
```
ITEMS:
- Deskripsi Pekerjaan
- Qty
- Biaya Satuan
- Jumlah

CALCULATIONS:
- Subtotal Layanan
- DPP (Dasar Pengenaan Pajak)
- PP 55 (0.5%)
- Grand Total
```

### **Semesta Invoice** (Template baru)
```
ITEMS:
- NO
- DESCRIPTION
- CARGO QTY
- M³ (Unit)
- UNIT PRICE
- AMOUNT (termasuk PPN %)

CALCULATIONS:
- SUBTOTAL (items + PPN)
- PPH 22 (1.5%) - DEDUCTION
- DOWN PAYMENT 30% - DEDUCTION
- TOTAL
```

**Key Differences:**
1. ✅ **Unit Column** - Semesta menampilkan unit terpisah (M³, Kg, dll)
2. ✅ **PPN Display** - Semesta show PPN di kolom Amount per item
3. ✅ **PPH 22** - Semesta menggunakan PPH 22 (withholding tax), Kisantra pakai PP 55
4. ✅ **Down Payment** - Semesta menampilkan DP sebagai deduction dari subtotal

---

## 🛠️ Model Updates

### Invoice Model
📁 `app/Models/Invoice.php`

**Fillable Fields Added:**
```php
protected $fillable = [
    // ... existing fields
    'template', // NEW: Template selection
];
```

**TIDAK menambahkan** `pph_rate` dan `pph_amount` karena dihitung on-the-fly.

### InvoiceItem Model
📁 `app/Models/InvoiceItem.php`

**Fillable Fields Added:**
```php
protected $fillable = [
    // ... existing fields
    'unit', // NEW: M³, Kg, Pcs, dll
];
```

---

## 📄 Service Updates

### InvoicePrintService
📁 `app/Services/InvoicePrintService.php`

**New Calculations:**

```php
// PPN (dari company settings)
$ppnAmount = $company?->is_pkp ? ($itemsTotal * $company->ppn_rate / 100) : 0;

// PPH 22 (dari company settings)
$pph22Amount = 0;
if ($company?->apply_pph22 && $company->pph22_rate > 0) {
    $pph22Amount = $itemsTotal * $company->pph22_rate / 100;
}

// Grand Total
$grandTotal = $subtotal - $pph22Amount - $discountAmount - ($dpAmount ?? 0);
```

**New Data Passed to View:**
```php
'ppn_amount' => $ppnAmount,      // PPN yang dihitung
'pph22_amount' => $pph22Amount,  // PPH 22 yang dihitung
'dp_percentage' => $dpPercentage, // Persentase DP (auto-calculated)
```

**Template Selection:**
```php
// Priority: invoice template → company default → fallback 'kisantra-invoice'
$template = $invoice->template ?? $company?->default_invoice_template ?? 'kisantra-invoice';

return Pdf::loadView('pdf.' . $template, $data)
    ->setPaper('A4', 'portrait')
    ->setOptions([...]);
```

---

## 🎨 Template Files

### 1. Kisantra Invoice (Existing)
📁 `resources/views/pdf/kisantra-invoice.blade.php`
- Template untuk PT. KINARA SADAYATRA NUSANTARA
- Menggunakan PP 55 (0.5%)
- Letterhead image di header
- Colored branding (#42b2cc)

### 2. Semesta Invoice (NEW)
📁 `resources/views/pdf/semesta-invoice.blade.php`
- Template untuk PT. SEMESTA PERTAMBANGAN INDONESIA
- Menggunakan PPH 22 (configurable %)
- Simple header dengan logo
- Unit column untuk satuan (M³, Kg, dll)
- Down payment deduction support

### 3. AGSA Invoice (Existing)
📁 `resources/views/pdf/agsa-invoice.blade.php`
- Alternative template

### 4. Generic Invoice (Existing)
📁 `resources/views/pdf/invoice.blade.php`
- Basic template

---

## 🚀 Implementation Steps

### Step 1: Run Migration
```bash
php artisan migrate
```

**Output yang diharapkan:**
```
Migrating: 2026_01_30_000001_add_template_fields_to_invoices
Migrated:  2026_01_30_000001_add_template_fields_to_invoices (XX.XXms)
```

### Step 2: Update Company Profile (Optional)
Update company profile untuk mengaktifkan PPH 22 dan set default template:

```php
// Via Tinker atau di Settings UI
$company = CompanyProfile::first();
$company->default_invoice_template = 'semesta-invoice';
$company->apply_pph22 = true;
$company->pph22_rate = 1.5; // 1.5%
$company->save();
```

### Step 3: Update Invoice Creation Form
Tambahkan field di form create/edit invoice:

**Livewire Component:** `app/Livewire/Invoices/Create.php`

```php
// Add property
public string $template = 'kisantra-invoice';
public string $unit = 'M³'; // Default unit untuk items

// Add to validation rules
protected function rules(): array
{
    return [
        // ... existing rules
        'template' => 'required|string|in:kisantra-invoice,semesta-invoice,agsa-invoice,invoice',
        'items.*.unit' => 'nullable|string|max:20',
    ];
}
```

**View:** `resources/views/livewire/invoices/create.blade.php`

```blade
{{-- Template Selection --}}
<x-select
    label="Invoice Template"
    wire:model="template"
    :options="[
        ['value' => 'kisantra-invoice', 'label' => 'Kisantra (Default)'],
        ['value' => 'semesta-invoice', 'label' => 'Semesta (Mining)'],
        ['value' => 'agsa-invoice', 'label' => 'AGSA'],
        ['value' => 'invoice', 'label' => 'Generic'],
    ]"
/>

{{-- Unit Field per Item --}}
<x-input
    label="Unit (M³, Kg, Pcs)"
    wire:model="items.{{ $index }}.unit"
    placeholder="M³"
/>
```

### Step 4: Test Invoice Generation

```bash
# Via Tinker
php artisan tinker

# Test dengan invoice existing
$invoice = Invoice::first();
$service = new \App\Services\InvoicePrintService();

# Generate dengan template semesta
$invoice->template = 'semesta-invoice';
$invoice->save();

# Download PDF
$pdf = $service->downloadSingleInvoice($invoice);
```

---

## 🧪 Testing Scenarios

### Test 1: Invoice dengan PPH 22
```php
// Setup company
$company = CompanyProfile::first();
$company->apply_pph22 = true;
$company->pph22_rate = 1.5;
$company->is_pkp = true;
$company->ppn_rate = 11;
$company->save();

// Create invoice
$invoice = Invoice::create([...]);
$invoice->template = 'semesta-invoice';
$invoice->items()->create([
    'service_name' => 'Penjualan Batu Split Uk 1x2',
    'quantity' => 2666,
    'unit' => 'M³',
    'unit_price' => 295000,
    'amount' => 786470000,
]);

// Expected calculations:
// Items Total: Rp 786.470.000
// PPN 11%: Rp 86.511.700
// Subtotal: Rp 872.981.700
// PPH 22 (1.5%): Rp 11.797.050
// TOTAL: Rp 861.184.650
```

### Test 2: Invoice dengan Down Payment
```php
$dpAmount = 245587500; // 30% DP

$service = new InvoicePrintService();
$pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount);

// Expected output in PDF:
// SUBTOTAL: Rp 872.981.700
// PPH 22 (1.5%): Rp 11.797.050
// DOWN PAYMENT 30%: Rp 245.587.500
// TOTAL: Rp 615.597.150
```

### Test 3: Multiple Units
```php
$invoice->items()->createMany([
    [
        'service_name' => 'Batu Split',
        'quantity' => 2666,
        'unit' => 'M³',
        'unit_price' => 295000,
        'amount' => 786470000,
    ],
    [
        'service_name' => 'Pasir',
        'quantity' => 500,
        'unit' => 'Ton',
        'unit_price' => 150000,
        'amount' => 75000000,
    ],
    [
        'service_name' => 'Semen',
        'quantity' => 200,
        'unit' => 'Sak',
        'unit_price' => 65000,
        'amount' => 13000000,
    ],
]);
```

---

## 📚 Usage Examples

### Example 1: Create Invoice with Semesta Template
```php
use App\Models\Invoice;
use App\Models\InvoiceItem;

$invoice = Invoice::create([
    'invoice_number' => '002.1/INV/SPI-MLM/I/2026',
    'billed_to_id' => $client->id,
    'subtotal' => 786470000,
    'total_amount' => 786470000,
    'template' => 'semesta-invoice', // 👈 Set template
    'issue_date' => now(),
    'due_date' => now()->addDays(30),
    'status' => 'draft',
]);

InvoiceItem::create([
    'invoice_id' => $invoice->id,
    'client_id' => $client->id,
    'service_name' => 'Pelunasan Penjualan Batu Split Uk 1x2',
    'quantity' => 2666,
    'unit' => 'M³', // 👈 Set unit
    'unit_price' => 295000,
    'amount' => 786470000,
]);
```

### Example 2: Generate PDF with Down Payment
```php
use App\Services\InvoicePrintService;

$service = new InvoicePrintService();

// Generate invoice dengan DP 30%
$dpAmount = 245587500; // Rp 245.587.500
$pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount);

return $pdf->download('invoice-dp.pdf');
```

### Example 3: Switch Template for Existing Invoice
```php
$invoice = Invoice::find(1);
$invoice->template = 'semesta-invoice';
$invoice->save();

// Regenerate PDF dengan template baru
$service = new InvoicePrintService();
$pdf = $service->downloadSingleInvoice($invoice);
```

---

## 🎯 Key Features

### 1. **Flexible Tax Calculation**
- ✅ PPN dihitung berdasarkan `company->is_pkp` dan `company->ppn_rate`
- ✅ PPH 22 dihitung berdasarkan `company->apply_pph22` dan `company->pph22_rate`
- ✅ Tidak ada field tax di database → lebih fleksibel saat tax rate berubah

### 2. **Template Selection Priority**
```
invoice->template
  ↓ (if null)
company->default_invoice_template
  ↓ (if null)
'kisantra-invoice' (fallback)
```

### 3. **Unit Support**
- ✅ Field `unit` di `invoice_items` table
- ✅ Nullable → backward compatible dengan invoice lama
- ✅ Ditampilkan di template semesta sebagai kolom terpisah

### 4. **Down Payment Display**
- ✅ Pass `$dpAmount` ke service untuk menampilkan DP
- ✅ Auto-calculate percentage: `($dpAmount / $subtotal) * 100`
- ✅ Tampil sebagai deduction di summary section

---

## ⚠️ Important Notes

### 1. **Tax Rates**
- PPH dan PPN **TIDAK** disimpan di database
- Dihitung on-the-fly saat generate PDF
- Menggunakan settings dari `company_profiles` table
- Jika tax rate berubah, invoice lama akan otomatis pakai rate baru saat regenerate PDF

### 2. **Backward Compatibility**
- Field `unit` nullable → invoice lama tetap berfungsi
- Field `template` default 'kisantra-invoice' → invoice lama pakai template default
- Existing invoices tidak perlu di-update

### 3. **Template Customization**
Untuk membuat template baru:
1. Copy `resources/views/pdf/semesta-invoice.blade.php`
2. Rename menjadi `resources/views/pdf/your-template.blade.php`
3. Customize layout sesuai kebutuhan
4. Gunakan variable yang sama: `$invoice`, `$items`, `$company`, `$ppn_amount`, `$pph22_amount`

### 4. **Company Settings**
Update company settings untuk mengaktifkan fitur:
```php
// Via Settings UI atau Tinker
$company = CompanyProfile::first();

// Untuk template Semesta
$company->default_invoice_template = 'semesta-invoice';
$company->apply_pph22 = true;
$company->pph22_rate = 1.5; // 1.5%
$company->is_pkp = true;
$company->ppn_rate = 11; // 11%
$company->save();
```

---

## 🔍 Troubleshooting

### Issue 1: Template not found
**Error:** `View [pdf.my-template] not found`

**Solution:**
```bash
# Clear view cache
php artisan view:clear

# Verify file exists
ls resources/views/pdf/
```

### Issue 2: Unit tidak muncul di PDF
**Cause:** Field `unit` masih null di database

**Solution:**
```php
// Update existing items
InvoiceItem::whereNull('unit')->update(['unit' => 'M³']);
```

### Issue 3: PPH 22 tidak dihitung
**Cause:** Company settings belum diaktifkan

**Solution:**
```php
$company = CompanyProfile::first();
$company->apply_pph22 = true;
$company->pph22_rate = 1.5;
$company->save();
```

### Issue 4: PDF layout broken
**Cause:** DomPDF rendering issue

**Solution:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Check DomPDF config
cat config/dompdf.php
```

---

## 📝 Next Steps (Optional)

### 1. **UI untuk Template Selection**
Tambahkan dropdown di form create/edit invoice untuk pilih template

### 2. **Company Settings UI**
Buat interface untuk manage:
- Default invoice template
- PPH 22 settings (enable/disable, rate)
- Multiple templates per company

### 3. **Preview Feature**
Tambahkan button "Preview" untuk lihat PDF sebelum save

### 4. **Template Management**
Admin interface untuk:
- Upload custom templates
- Enable/disable templates
- Set template per client

### 5. **Invoice Numbering per Template**
Customize invoice number format berdasarkan template yang dipilih

---

## 📌 Summary

**Yang Ditambahkan:**
1. ✅ Migration untuk `unit`, `template`, `default_invoice_template`, `pph22_rate`, `apply_pph22`
2. ✅ Model updates (Invoice, InvoiceItem)
3. ✅ Service updates (InvoicePrintService dengan PPH 22 calculation)
4. ✅ Template baru: `semesta-invoice.blade.php`

**Yang TIDAK Ditambahkan:**
1. ❌ Field `pph_rate` dan `pph_amount` di invoices (dihitung on-the-fly)
2. ❌ Stored tax calculations (lebih fleksibel)

**Ready to Use:**
- ✅ Run migration
- ✅ Update company settings
- ✅ Test generate PDF dengan template baru
- ✅ Customize template sesuai kebutuhan

---

**Author:** Claude Code
**Date:** 2026-01-30
**Version:** 1.0.0
