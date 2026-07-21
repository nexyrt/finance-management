<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\CashFlowExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CashFlowExportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::firstOrCreate(['name' => 'view bank-accounts']);
        CompanyProfile::factory()->create();
    }

    private function userWithAccess(): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view bank-accounts');

        return $user;
    }

    private function transactionOn(BankAccount $account, string $date, string $type = 'debit', int $amount = 10000): BankTransaction
    {
        return BankTransaction::factory()->create([
            'bank_account_id' => $account->id,
            'transaction_date' => $date,
            'transaction_type' => $type,
            'amount' => $amount,
        ]);
    }

    public function test_export_accepts_date_from_and_date_to_params(): void
    {
        $account = BankAccount::factory()->create(['initial_balance' => 0]);
        $this->transactionOn($account, '2026-06-15');

        $response = $this->actingAs($this->userWithAccess())
            ->get('/cash-flow/export/pdf?date_from=2026-06-01&date_to=2026-06-30');

        $response->assertOk();
        $response->assertDownload('cash-flow-2026-06-01-to-2026-06-30.pdf');
    }

    public function test_export_still_accepts_start_date_and_end_date_params(): void
    {
        $account = BankAccount::factory()->create(['initial_balance' => 0]);
        $this->transactionOn($account, '2026-06-15');

        $response = $this->actingAs($this->userWithAccess())
            ->get('/cash-flow/export/pdf?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertOk();
        $response->assertDownload('cash-flow-2026-06-01-to-2026-06-30.pdf');
    }

    public function test_report_data_only_includes_transactions_within_date_range(): void
    {
        $account = BankAccount::factory()->create(['initial_balance' => 500000]);
        $this->transactionOn($account, '2026-06-15', 'debit', 100000);
        $this->transactionOn($account, '2026-07-10', 'debit', 25000);

        $data = app(CashFlowExportService::class)->buildReportData(
            bankAccountIds: null,
            startDate: '2026-07-01',
            endDate: '2026-07-31',
        );

        $this->assertCount(1, $data['transactions']);
        $this->assertSame(25000, $data['transactions'][0]['debit']);
        $this->assertSame(400000, $data['openingBalance']);
        $this->assertSame(375000, $data['closingBalance']);
    }

    public function test_report_data_filters_by_bank_accounts(): void
    {
        $accountA = BankAccount::factory()->create(['initial_balance' => 100000]);
        $accountB = BankAccount::factory()->create(['initial_balance' => 900000]);
        $this->transactionOn($accountA, '2026-07-05', 'debit', 10000);
        $this->transactionOn($accountB, '2026-07-06', 'debit', 20000);

        $data = app(CashFlowExportService::class)->buildReportData(
            bankAccountIds: [$accountA->id],
            startDate: '2026-07-01',
            endDate: '2026-07-31',
        );

        $this->assertCount(1, $data['transactions']);
        $this->assertSame(10000, $data['transactions'][0]['debit']);
        $this->assertSame(100000, $data['openingBalance']);
        $this->assertSame($accountA->id, $data['bankAccount']->id);
    }

    public function test_export_without_params_covers_all_time(): void
    {
        $account = BankAccount::factory()->create(['initial_balance' => 500000]);
        $this->transactionOn($account, '2025-01-15', 'debit', 100000);
        $this->transactionOn($account, '2026-06-10', 'debit', 25000);

        $data = app(CashFlowExportService::class)->buildReportData();

        $this->assertCount(2, $data['transactions']);
        $this->assertSame('SEMUA WAKTU', $data['periodText']);
        $this->assertSame(500000, $data['openingBalance']);
        $this->assertSame(375000, $data['closingBalance']);

        $this->actingAs($this->userWithAccess())
            ->get('/cash-flow/export/pdf')
            ->assertOk()
            ->assertDownload('cash-flow-semua-waktu.pdf');
    }

    public function test_export_with_month_still_uses_that_month(): void
    {
        $account = BankAccount::factory()->create(['initial_balance' => 0]);
        $this->transactionOn($account, '2026-06-15');

        $this->actingAs($this->userWithAccess())
            ->get('/cash-flow/export/pdf?month=06&year=2026')
            ->assertOk()
            ->assertDownload('cash-flow-2026-06.pdf');
    }

    public function test_export_accepts_comma_separated_bank_accounts_param(): void
    {
        $accountA = BankAccount::factory()->create(['initial_balance' => 0]);
        $accountB = BankAccount::factory()->create(['initial_balance' => 0]);
        $this->transactionOn($accountA, '2026-06-15');

        $response = $this->actingAs($this->userWithAccess())
            ->get("/cash-flow/export/pdf?date_from=2026-06-01&date_to=2026-06-30&bank_accounts={$accountA->id},{$accountB->id}");

        $response->assertOk();
        $response->assertDownload("cash-flow-2026-06-01-to-2026-06-30-account-{$accountA->id}-{$accountB->id}.pdf");
    }
}
