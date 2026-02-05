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

Aplikasi menggunakan **113 Livewire components** yang diorganisir berdasarkan domain bisnis dengan minimal traditional controllers.

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

### Design Principles

**CRITICAL: Semua page baru atau redesign HARUS mengikuti design system ini untuk konsistensi.**

**Core Philosophy:**
- **Minimalist** - No fancy decorations, focus on functionality
- **Clean & Readable** - Code dan UI harus mudah dibaca
- **Consistent** - Spacing, typography, dan pattern yang sama di semua page
- **Functional-First** - Prioritas pada komponen dan data, bukan estetika berlebihan

### Layout Guidelines

**1. Spacing:**
- Root container: `space-y-8` untuk vertical rhythm
- Section spacing: `mt-8` atau gunakan parent `space-y-8`
- Element spacing: `mt-2`, `mt-4` untuk spacing kecil
- Grid gaps: `gap-6` untuk grid layouts

**2. Typography:**
```blade
{{-- Page Title --}}
<h1 class="text-2xl font-bold">Page Title</h1>

{{-- Section Title --}}
<h2 class="text-xl font-semibold">Section Title</h2>

{{-- Output/Info Text --}}
<p class="text-sm">Information text</p>
```

**3. Containers:**
```blade
{{-- Simple section - NO borders/backgrounds by default --}}
<div>
    <x-component />
    <p class="mt-2 text-sm">Output</p>
</div>

{{-- Only add styling when necessary for grouping --}}
<div class="p-4 border rounded-lg">
    {{-- Content --}}
</div>
```

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

### Color Scheme

**Default Colors Only:**
- Text: Default Tailwind (`text-gray-800`, `dark:text-white`)
- Backgrounds: Clean white/dark mode auto
- Borders: `border`, `border-gray-200`, `dark:border-gray-700`
- Accents: Only when necessary (`text-blue-600`, `text-green-600`)

**Avoid:**
- ❌ Custom gradient backgrounds
- ❌ Multiple accent colors dalam satu section
- ❌ Heavy shadows (`shadow-2xl`, `shadow-lg`)
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

### Reference Example

**Good Example (Testing Page):**
```blade
<div class="space-y-8">
    <h1 class="text-2xl font-bold">Page Title</h1>

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

**Tab-based Navigation:**

| Tab Component | Fungsi |
|---------------|--------|
| `Index.php` | Coordinator dengan tab navigation |
| `OverviewTab.php` | Summary cash flow (income vs expense) |
| `IncomeTab.php` | List pemasukan dengan filter |
| `ExpensesTab.php` | List pengeluaran dengan filter |
| `AdjustmentsTab.php` | Penyesuaian saldo |
| `TransfersTab.php` | Transfer antar rekening |

**Supporting Components:**

| Component | Fungsi |
|-----------|--------|
| `CreateIncome.php` | Form catat pemasukan |
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
| `Delete.php` | Modal hapus transaksi |
| `Categorize.php` | Assign kategori ke transaksi |
| `Transfer.php` | Transfer antar bank account |

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
**Route:** `/settings/profile`, `/settings/password`, `/settings/appearance`, `/settings/company`
**Components:** `app/Livewire/Settings/`

| Component | Fungsi |
|-----------|--------|
| `Profile.php` | Edit profile user |
| `Password.php` | Ganti password |
| `Appearance.php` | Theme settings |
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
| `Notifications/Bell.php` | Notification dropdown |
| `Actions/Logout.php` | Logout action |
| `Admin/RoleManagement.php` | Role management page |
| `TestingPage.php` | Testing/development page |
| `Traits/Alert.php` | Reusable alert trait |

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

### Permission Categories (60+ permissions)

```
Clients:         view, create, edit, delete
Services:        view, create, edit, delete
Invoices:        view, create, edit, delete, send
Payments:        view, create, edit, delete
Bank Accounts:   view, create, edit, delete
Cash Flow:       view
Transactions:    view, create, edit, delete, categorize
Categories:      view, create, edit, delete
Recurring:       view, create, edit, delete, publish
Reimbursements:  view, create, edit, delete, approve, pay
Loans:           view, create, edit, delete, pay
Receivables:     view, create, edit, delete, approve, pay
Feedbacks:       view, create, edit, delete, respond
Permissions:     view, delete
Users:           manage (admin only)
Roles:           create, update, delete
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
├── Livewire/                         # 113 components
│   ├── Accounts/                     # (5) Bank account management
│   ├── Actions/                      # (1) Logout
│   ├── Admin/                        # (1) RoleManagement
│   ├── Auth/                         # (6) Authentication flows
│   ├── CashFlow/                     # (8) Cash flow tracking
│   ├── Clients/                      # (6) Client CRUD + relationships
│   ├── Dashboard.php
│   ├── Feedbacks/                    # (8) Feedback system
│   ├── FloatingFeedbackButton.php
│   ├── Invoices/                     # (6) Invoice management
│   ├── LanguageSwitcher.php
│   ├── Loans/                        # (5) Loan tracking
│   ├── Notifications/                # (1) Bell
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
│   ├── Settings/                     # (5) User & company settings
│   ├── TestingPage.php
│   ├── Traits/                       # (1) Alert trait
│   ├── Transactions/                 # (6) Bank transactions
│   ├── TransactionsCategories/       # (4) Category management
│   └── Users/                        # (4) User management
├── Models/                           # 21 Eloquent models
├── Services/                         # Business logic services
│   ├── InvoicePrintService.php
│   ├── InvoiceExportService.php
│   └── PaymentExportService.php
└── Providers/

database/
├── migrations/                       # 37 migrations
└── seeders/
    ├── DatabaseSeeder.php            # Orchestrator
    ├── MasterPermissionSeeder.php    # 60+ permissions, 3 roles
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
3. `MasterPermissionSeeder` - 60+ permissions & 3 roles

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
