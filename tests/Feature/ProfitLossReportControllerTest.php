<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProfitLossReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'view profit-loss']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo('view profit-loss');

        Role::firstOrCreate(['name' => 'staff']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('staff');
    }

    public function test_requires_authentication(): void
    {
        $this->get('/reports/profit-loss')->assertRedirect('/login');
    }

    public function test_requires_view_profit_loss_permission(): void
    {
        $this->actingAs($this->staff)->get('/reports/profit-loss')->assertForbidden();
    }

    public function test_renders_with_default_period_year_to_date(): void
    {
        $today = now();
        $expectedStart = $today->copy()->startOfYear()->toDateString();
        $expectedEnd = $today->copy()->toDateString();

        $this->actingAs($this->admin)
            ->get('/reports/profit-loss')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('reports/profit-loss/index')
                ->where('filters.start_date', $expectedStart)
                ->where('filters.end_date', $expectedEnd)
                ->has('report.period')
                ->has('report.revenue')
                ->has('report.cogs')
                ->has('report.gross_profit')
                ->has('report.opex')
                ->has('report.net_profit')
                ->has('report.unclassified')
            );
    }

    public function test_accepts_custom_period_filter(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports/profit-loss?start_date=2026-01-01&end_date=2026-01-31')
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('filters.start_date', '2026-01-01')
                ->where('filters.end_date', '2026-01-31')
            );
    }

    public function test_rejects_end_before_start(): void
    {
        $this->actingAs($this->admin)
            ->get('/reports/profit-loss?start_date=2026-03-01&end_date=2026-01-31')
            ->assertSessionHasErrors('end_date');
    }

    public function test_report_aggregates_data_in_period(): void
    {
        $bank = BankAccount::factory()->create(['initial_balance' => 0]);
        $catOpex = TransactionCategory::create(['type' => 'expense', 'pl_group' => 'opex', 'label' => 'Operasional']);

        BankTransaction::create([
            'bank_account_id' => $bank->id,
            'category_id' => $catOpex->id,
            'amount' => 25_000,
            'transaction_type' => 'debit',
            'transaction_date' => '2026-01-10',
            'description' => 'Listrik',
        ]);

        $this->actingAs($this->admin)
            ->get('/reports/profit-loss?start_date=2026-01-01&end_date=2026-01-31')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('report.opex.total', 25_000)
                ->where('report.net_profit', -25_000)
            );
    }

    public function test_unclassified_type_map_is_passed_to_view(): void
    {
        // Two unclassified categories with transactions in period — one income, one expense.
        $bank = BankAccount::factory()->create(['initial_balance' => 0]);
        $catIncome = TransactionCategory::create(['type' => 'income', 'label' => 'Belum Klas Income']);
        $catExpense = TransactionCategory::create(['type' => 'expense', 'label' => 'Belum Klas Expense']);

        BankTransaction::create([
            'bank_account_id' => $bank->id,
            'category_id' => $catIncome->id,
            'amount' => 5_000,
            'transaction_type' => 'credit',
            'transaction_date' => '2026-01-10',
            'description' => 'in',
        ]);
        BankTransaction::create([
            'bank_account_id' => $bank->id,
            'category_id' => $catExpense->id,
            'amount' => 3_000,
            'transaction_type' => 'debit',
            'transaction_date' => '2026-01-10',
            'description' => 'out',
        ]);

        $this->actingAs($this->admin)
            ->get('/reports/profit-loss?start_date=2026-01-01&end_date=2026-01-31')
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('unclassifiedTypes.'.$catIncome->id, 'income')
                ->where('unclassifiedTypes.'.$catExpense->id, 'expense')
            );
    }

    public function test_pdf_download_returns_pdf_attachment(): void
    {
        $response = $this->actingAs($this->admin)
            ->get('/reports/profit-loss/pdf?start_date=2026-01-01&end_date=2026-01-31');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition') ?? '');
        $this->assertStringContainsString('laporan-laba-rugi-2026-01-01-2026-01-31.pdf', $response->headers->get('content-disposition') ?? '');
    }

    public function test_pdf_requires_view_profit_loss_permission(): void
    {
        $this->actingAs($this->staff)
            ->get('/reports/profit-loss/pdf')
            ->assertForbidden();
    }
}
