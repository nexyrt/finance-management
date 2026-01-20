# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel 12 Finance Management System built with Livewire 3, TallStackUI, and Spatie Permission. Manages invoicing, payments, recurring billings, bank accounts, loans, receivables, and reimbursements for Indonesian business contexts (uses Terbilang for number-to-words conversion, supports NPWP/PKP tax features).

## Common Commands

### Development
```bash
# Start development servers (artisan + queue + vite)
composer dev
# Or individual servers:
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database with users, company profile, and permissions
php artisan db:seed

# Fresh migration with seeders
php artisan migrate:fresh --seed
```

### Testing
```bash
# Run tests
php artisan test
# Or
./vendor/bin/phpunit
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# View logs in real-time
php artisan pail
```

### Assets
```bash
# Build for production
npm run build

# Install dependencies
composer install
npm install
```

## High-Level Architecture

### Livewire-First Approach
The application uses **102 Livewire components** organized by business domain with minimal traditional controllers. Components follow consistent patterns:
- **Index.php** - Dashboard/coordinator with statistics
- **Listing.php** - Filterable lists with search and pagination
- **Create.php** - Form for creation with validation
- **Edit.php** - Form for updates
- **Delete.php** - Confirmation dialogs
- **Show.php** - Detail views

### Key Patterns

**Computed Properties for Performance**
```php
#[Computed]
public function stats(): array
{
    // Heavy calculations cached until component refresh
    return [...];
}
```

**Event-Driven Refreshing**
```php
#[On('invoice-created')]
public function refreshStats(): void
{
    unset($this->stats); // Force recompute
}
```

**Currency Storage**
All monetary values stored as **integers in cents/smallest unit** to avoid floating-point precision issues.
```php
// Database stores 150000 (cents)
// Display as Rp 1,500.00
```

### Permission System (Spatie\Permission)

**Three Roles:**
1. **admin** - Full access
2. **finance manager** - All operations except user management
3. **staff** - Limited to viewing/creating own records

**Route Protection:**
```php
Route::middleware('can:view invoices')->name('invoices.index')
Route::middleware('can:create invoices')->name('invoices.create')
```

**Permission Categories:**
- Clients, Services, Invoices, Payments
- Bank Accounts, Transactions, Cash Flow
- Recurring Invoices (view, create, edit, delete, publish)
- Reimbursements (view, create, edit, delete, approve, pay)
- Loans, Receivables
- Permissions, Users

### Core Models & Relationships

**Invoice Model** (`app/Models/Invoice.php`)
- Invoice number format: `INV/{seq}/KSN/{mm}.{yy}`
- Status flow: `draft → partially_paid → paid`
- Auto-calculates: `amount_paid`, `amount_remaining`, profit metrics (COGS, gross/outstanding/paid profit)
- Relationships: `belongsTo(Client)`, `hasMany(InvoiceItem)`, `hasMany(Payment)`

**RecurringTemplate & RecurringInvoice Models**
- Templates store JSON configuration for scheduled invoices
- RecurringInvoice = monthly draft instances waiting to be published
- Publishing converts draft → actual Invoice with auto-generated number
- Frequencies: monthly, quarterly, semi-annual, annual

**Reimbursement Model** (Complex Workflow)
- Status flow: `draft → pending → approved/rejected → paid`
- Supports partial payments with payment status tracking
- Methods: `submit()`, `approve()`, `reject()`, `recordPayment()`
- Attachment management with auto-cleanup on deletion

**Polymorphic Relationships**
- `Receivable` model uses `debtor: morphTo()` (can be User or Client)
- Allows tracking receivables from either employees or external clients

**BankAccount Model**
- Balance calculated dynamically: `initial_balance + credits - debits`
- Not stored; computed from `BankTransaction` and `Payment` relationships

**Client Model**
- Types: `individual` or `company`
- Supports ownership relationships via `belongsToMany(Client)` (owners ↔ ownedCompanies)
- Custom `delete()` cascade handles invoices → items → relationships

### PDF Generation

**Service:** `app/Services/InvoicePrintService.php` (uses **DomPDF**)

**Key Methods:**
- `generateSingleInvoicePdf(Invoice $invoice, ?int $dpAmount, ?int $pelunasanAmount)`
- `downloadSingleInvoice(Invoice $invoice)`

**Templates:**
- `resources/views/pdf/invoice.blade.php` - Generic
- `resources/views/pdf/kisantra-invoice.blade.php` - Branded for PT. Kinara Sadayatra Nusantara
- `resources/views/pdf/agsa-invoice.blade.php` - Alternative branded

**Features:**
- Supports DP (down payment) and Pelunasan (settlement) breakdown
- Tax deposit item handling (excluded from profit calculations)
- Terbilang (Indonesian number-to-words) via `ngekoding/terbilang` package
- Embeds company logo/signature/stamp as base64
- PPN calculation if company is PKP

**Routes:**
```php
GET /invoice/{invoice}/download?dp_amount=X&pelunasan_amount=Y
GET /invoice/{invoice}/preview
```

### Recurring Invoice System

**Template-Based Generation:**
1. **RecurringTemplate** - Master configuration (frequency, items, amounts)
2. **RecurringInvoice** - Monthly instances (draft state, contains snapshot)
3. **Publishing** - Converts draft to actual `Invoice` with auto-generated number

**Generation Flow:**
- User creates template with items and frequency
- System calculates `next_generation_date` automatically
- MonthlyTab Livewire component can trigger manual generation
- Draft invoices reviewed before publishing
- `publishInvoice(invoiceId)` → creates Invoice + InvoiceItems

**No Scheduled Task** - Generation triggered manually via UI button in `MonthlyTab.php`

### Financial Calculations

**Profit Tracking:**
- Items have `cogs` (cost of goods sold) field
- Tax deposit items marked with `is_tax_deposit` (excluded from profit)
- Calculated metrics:
  - `total_cogs` - Sum of all item COGS
  - `gross_profit` - Total revenue minus COGS
  - `outstanding_profit` - Profit from unpaid invoices
  - `paid_profit` - Realized profit from paid invoices

**Tax Handling:**
- Company profile has `is_pkp` flag (PKP = Pengusaha Kena Pajak)
- `ppn_rate` stored as integer (e.g., 11 for 11%)
- Tax deposit items tracked separately in invoice items

## Directory Structure

```
app/
├── Http/Controllers/Api/     # Minimal API endpoints
├── Livewire/                 # 102 components organized by domain
│   ├── Accounts/             # Bank account management
│   ├── Admin/                # Role management
│   ├── Auth/                 # Authentication
│   ├── CashFlow/             # Cash flow tracking
│   ├── Clients/              # Client CRUD
│   ├── Dashboard.php         # Main dashboard
│   ├── Invoices/             # Invoice management (Create, Edit, Index, Listing, Show)
│   ├── Loans/                # Loan tracking
│   ├── Payments/             # Payment processing
│   ├── Receivables/          # Receivables management
│   ├── RecurringInvoices/    # Template & monthly invoice management
│   ├── Reimbursements/       # Employee reimbursement workflow
│   ├── Services/             # Service definitions
│   ├── Settings/             # User & company settings
│   ├── Transactions/         # Bank transactions
│   ├── TransactionsCategories/
│   └── Users/                # User management
├── Models/                   # 19 Eloquent models
├── Services/                 # InvoicePrintService, ExportService
├── Exports/                  # Maatwebsite Excel export classes
└── Providers/

database/
├── migrations/               # 20+ migrations
└── seeders/
    ├── DatabaseSeeder.php    # Main orchestrator
    ├── MasterPermissionSeeder.php    # 60+ permissions, 3 roles
    ├── UserSeeder.php
    ├── CompanyProfileSeeder.php
    └── ...

resources/views/
├── livewire/                 # Component views
├── pdf/                      # Invoice PDF templates
│   ├── invoice.blade.php
│   ├── kisantra-invoice.blade.php
│   └── agsa-invoice.blade.php
└── exports/                  # PDF export views

routes/
├── web.php                   # Main routes (all use permission middleware)
└── auth.php                  # Authentication routes

config/
├── permission.php            # Spatie Permission config
├── dompdf.php                # PDF generation settings
├── excel.php                 # Maatwebsite Excel config
├── tallstackui.php           # UI component library
└── wireui.php
```

## Working with Invoices

### Creating Invoices
- Use `app/Livewire/Invoices/Create.php` component
- Invoice number auto-generated on save (format: `INV/{seq}/KSN/{mm}.{yy}`)
- Items support: quantity, unit_price, discount, COGS, is_tax_deposit flag
- Status defaults to `draft`

### Invoice Status Management
- Status auto-updates via `updateStatus()` method when payments recorded
- Logic: checks `amount_paid` vs `total_amount`
- Transitions: `draft → partially_paid → paid`

### Editing Invoices
- Only `draft` invoices can be edited
- Published invoices immutable (create new or use credit notes)

### Generating PDFs
```php
// In controller/component:
$service = new InvoicePrintService();
$pdf = $service->generateSingleInvoicePdf($invoice, $dpAmount, $pelunasanAmount);
return $pdf->stream(); // or ->download()
```

## Working with Recurring Invoices

### Creating Templates
1. Navigate to `RecurringInvoices/CreateTemplate.php`
2. Select client, frequency (monthly/quarterly/semi-annual/annual)
3. Add items with prices
4. System calculates `next_generation_date` automatically

### Generating Monthly Invoices
1. Navigate to MonthlyTab (`RecurringInvoices/MonthlyTab.php`)
2. Click "Generate Invoices" button
3. System creates draft `RecurringInvoice` records for current month
4. Review draft invoices in listing

### Publishing Invoices
1. Select draft invoice from MonthlyTab
2. Click "Publish" button
3. System creates actual `Invoice` with auto-generated number
4. Invoice items copied from template snapshot
5. RecurringInvoice status → `published`

## Working with Reimbursements

### Workflow States
1. **Draft** - Employee creating request
2. **Pending** - Submitted for approval
3. **Approved/Rejected** - Manager decision
4. **Paid** - Finance records payment

### Key Methods
```php
$reimbursement->submit();      // draft → pending
$reimbursement->approve();     // pending → approved
$reimbursement->reject();      // pending → rejected
$reimbursement->recordPayment($amount, $bankAccountId, $notes, $attachment);
```

### Partial Payments
- `payment_status`: `unpaid → partial → paid`
- Track multiple payments via `ReimbursementPayment` model
- Auto-calculates `amount_paid` and `amount_remaining`

## Database Seeding Strategy

**Active Seeders (run on `php artisan db:seed`):**
1. `UserSeeder` - Test users with roles
2. `CompanyProfileSeeder` - Company info for PDF generation
3. `MasterPermissionSeeder` - Roles & 60+ permissions

**Commented Seeders (manual testing):**
- ServiceSeeder, ClientSeeder, InvoiceSeeder, RecurringTemplateSeeder, etc.

**To enable all seeders:**
Uncomment lines in `database/seeders/DatabaseSeeder.php`

## Troubleshooting

### Permission Issues
```bash
# Clear permission cache after role/permission changes
php artisan permission:cache-reset
```

### PDF Not Generating
- Check `storage/` directory writable
- Verify `public/storage` symlink exists: `php artisan storage:link`
- Check company profile has logo/signature paths set
- Verify DomPDF config in `config/dompdf.php`

### Livewire Component Not Refreshing
- Ensure computed properties unset when needing refresh: `unset($this->stats)`
- Verify event listeners use correct syntax: `#[On('event-name')]`
- Check browser console for Livewire errors

### Balance Calculations Incorrect
- BankAccount balance is computed dynamically, not stored
- Verify all transactions have correct `type` (credit/debit)
- Check `BankTransaction` and `Payment` relationships loaded

## Important Files to Know

- `app/Services/InvoicePrintService.php` - PDF generation logic
- `app/Models/Invoice.php` - Invoice status management, profit calculations
- `app/Models/RecurringTemplate.php` - Recurring logic, date calculations
- `database/seeders/MasterPermissionSeeder.php` - Permission structure
- `routes/web.php` - All route definitions with middleware
- `config/permission.php` - Spatie Permission configuration
- `resources/views/pdf/kisantra-invoice.blade.php` - Main invoice template

## Tech Stack Reference

**Backend:**
- Laravel 12, Livewire 3.6, PHP 8.2+
- Spatie\Permission (roles/permissions)
- Barryvdh\DomPDF (PDF generation)
- Maatwebsite\Excel (exports)
- ngekoding\Terbilang (Indonesian number words)

**Frontend:**
- TallStackUI & WireUI (Livewire component libraries)
- Tailwind CSS 4.1, DaisyUI
- Alpine.js 3.14
- Chart.js, ApexCharts (visualizations)
- Tiptap (rich text editor)
- Vite (build tool)
