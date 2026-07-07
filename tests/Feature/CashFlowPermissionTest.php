<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CashFlowPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view bank-accounts',
            'view income', 'create income', 'edit income', 'delete income',
            'view expense', 'create expense', 'edit expense', 'delete expense',
            'view transfer', 'create transfer', 'edit transfer', 'delete transfer',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
    }

    /** @param  array<int,string>  $perms */
    private function userWith(array $perms): User
    {
        $user = User::factory()->create();
        $user->givePermissionTo($perms);

        return $user;
    }

    private function category(string $type): TransactionCategory
    {
        return TransactionCategory::create(['type' => $type, 'label' => ucfirst($type).' Category']);
    }

    /** @return array<string,mixed> */
    private function storePayload(int $accountId, int $categoryId, string $type): array
    {
        return [
            'bank_account_id' => $accountId,
            'category_id' => $categoryId,
            'amount' => 10000,
            'transaction_date' => '2026-07-01',
            'transaction_type' => $type,
            'description' => 'Test transaction',
        ];
    }

    public function test_create_income_permission_allows_credit_but_forbids_debit(): void
    {
        $user = $this->userWith(['view bank-accounts', 'create income']);
        $account = BankAccount::factory()->create();
        $category = $this->category('income');

        $this->actingAs($user)
            ->post('/bank-transactions', $this->storePayload($account->id, $category->id, 'credit'))
            ->assertRedirect();

        $this->actingAs($user)
            ->post('/bank-transactions', $this->storePayload($account->id, $category->id, 'debit'))
            ->assertForbidden();
    }

    public function test_create_expense_permission_allows_debit_but_forbids_credit(): void
    {
        $user = $this->userWith(['view bank-accounts', 'create expense']);
        $account = BankAccount::factory()->create();
        $category = $this->category('expense');

        $this->actingAs($user)
            ->post('/bank-transactions', $this->storePayload($account->id, $category->id, 'debit'))
            ->assertRedirect();

        $this->actingAs($user)
            ->post('/bank-transactions', $this->storePayload($account->id, $category->id, 'credit'))
            ->assertForbidden();
    }

    public function test_store_does_not_require_view_bank_accounts(): void
    {
        $user = $this->userWith(['create income']); // no view bank-accounts, only cash-flow perm
        $account = BankAccount::factory()->create();
        $category = $this->category('income');

        $this->actingAs($user)
            ->post('/bank-transactions', $this->storePayload($account->id, $category->id, 'credit'))
            ->assertRedirect();
    }

    public function test_edit_permission_is_feature_scoped(): void
    {
        $account = BankAccount::factory()->create();
        $income = BankTransaction::factory()->credit()->create([
            'bank_account_id' => $account->id, 'reference_number' => null,
        ]);
        $expense = BankTransaction::factory()->debit()->create([
            'bank_account_id' => $account->id, 'reference_number' => null,
        ]);

        $editor = $this->userWith(['view bank-accounts', 'edit income']);
        // Mirror the real edit form, which always submits amount + category alongside.
        $update = [
            'transaction_date' => '2026-07-02',
            'description' => 'Updated',
            'amount' => 20000,
            'category_id' => $this->category('income')->id,
        ];

        $this->actingAs($editor)->put("/bank-transactions/{$income->id}", $update)->assertRedirect();
        $this->actingAs($editor)->put("/bank-transactions/{$expense->id}", $update)->assertForbidden();
    }

    public function test_delete_is_feature_scoped_including_transfer(): void
    {
        $account = BankAccount::factory()->create();
        $expense = BankTransaction::factory()->debit()->create([
            'bank_account_id' => $account->id, 'reference_number' => null,
        ]);
        $transferLeg = BankTransaction::factory()->debit()->create([
            'bank_account_id' => $account->id, 'reference_number' => 'TRF123',
        ]);

        $expenseDeleter = $this->userWith(['view bank-accounts', 'delete expense']);
        // The TRF leg resolves to the transfer feature, so a pure expense-deleter is blocked.
        $this->actingAs($expenseDeleter)->delete("/bank-transactions/{$transferLeg->id}")->assertForbidden();
        $this->actingAs($expenseDeleter)->delete("/bank-transactions/{$expense->id}")->assertRedirect();

        $transferDeleter = $this->userWith(['view bank-accounts', 'delete transfer']);
        $anotherTransfer = BankTransaction::factory()->debit()->create([
            'bank_account_id' => $account->id, 'reference_number' => 'TRF999',
        ]);
        $this->actingAs($transferDeleter)->delete("/bank-transactions/{$anotherTransfer->id}")->assertRedirect();
    }

    public function test_bulk_delete_requires_delete_permission_for_every_feature_present(): void
    {
        $account = BankAccount::factory()->create();
        $income = BankTransaction::factory()->credit()->create([
            'bank_account_id' => $account->id, 'reference_number' => null,
        ]);
        $expense = BankTransaction::factory()->debit()->create([
            'bank_account_id' => $account->id, 'reference_number' => null,
        ]);

        $incomeOnly = $this->userWith(['view bank-accounts', 'delete income']);
        $this->actingAs($incomeOnly)
            ->post('/bank-transactions/bulk-delete', ['ids' => [$income->id, $expense->id]])
            ->assertForbidden();

        $both = $this->userWith(['view bank-accounts', 'delete income', 'delete expense']);
        $this->actingAs($both)
            ->post('/bank-transactions/bulk-delete', ['ids' => [$income->id, $expense->id]])
            ->assertRedirect();
    }

    public function test_transfer_requires_create_transfer_permission(): void
    {
        $from = BankAccount::factory()->create();
        $to = BankAccount::factory()->create();
        $category = $this->category('transfer');
        $payload = [
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'category_id' => $category->id,
            'amount' => 10000,
            'admin_fee' => 0,
            'description' => 'Move funds',
            'transfer_date' => '2026-07-01',
        ];

        $noPerm = $this->userWith(['view bank-accounts', 'create income']);
        $this->actingAs($noPerm)->post('/bank-transactions/transfer', $payload)->assertForbidden();

        $transferer = $this->userWith(['view bank-accounts', 'create transfer']);
        $this->actingAs($transferer)->post('/bank-transactions/transfer', $payload)->assertRedirect();
    }

    public function test_cash_flow_tabs_are_gated_per_feature(): void
    {
        $incomeViewer = $this->userWith(['view income']);
        $this->actingAs($incomeViewer)->get('/cash-flow/income')->assertOk();
        $this->actingAs($incomeViewer)->get('/cash-flow/expenses')->assertForbidden();
        $this->actingAs($incomeViewer)->get('/cash-flow/transfers')->assertForbidden();

        $expenseViewer = $this->userWith(['view expense']);
        $this->actingAs($expenseViewer)->get('/cash-flow/expenses')->assertOk();
        $this->actingAs($expenseViewer)->get('/cash-flow/income')->assertForbidden();
    }

    public function test_cash_flow_index_redirects_to_first_allowed_tab(): void
    {
        $expenseViewer = $this->userWith(['view expense']);
        $this->actingAs($expenseViewer)->get('/cash-flow')->assertRedirect(route('cash-flow.expenses'));
    }
}
