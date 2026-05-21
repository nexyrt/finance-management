<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BankAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view bank-accounts', 'create bank-accounts', 'edit bank-accounts', 'delete bank-accounts'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions(['view bank-accounts']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('viewer');
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/bank-accounts')->assertRedirect('/login');
    }

    public function test_index_requires_view_bank_accounts_permission(): void
    {
        $noPermUser = User::factory()->create();
        $this->actingAs($noPermUser)->get('/bank-accounts')->assertForbidden();
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/bank-accounts')->assertOk();
    }

    public function test_store_creates_bank_account(): void
    {
        $this->actingAs($this->admin)->post('/bank-accounts', [
            'account_name' => 'Akun Operasional',
            'account_number' => '1234567890',
            'bank_name' => 'Bank Central Asia (BCA)',
            'initial_balance' => 5000000,
        ]);

        $this->assertDatabaseHas('bank_accounts', [
            'account_name' => 'Akun Operasional',
            'account_number' => '1234567890',
        ]);
    }

    public function test_store_requires_create_permission(): void
    {
        $this->actingAs($this->viewer)->post('/bank-accounts', [
            'account_name' => 'Test',
            'account_number' => '9999999999',
            'bank_name' => 'Bank Mandiri',
            'initial_balance' => 1000000,
        ])->assertForbidden();
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post('/bank-accounts', []);
        $response->assertSessionHasErrors(['account_name', 'account_number', 'bank_name', 'initial_balance']);
    }

    public function test_update_modifies_bank_account(): void
    {
        $account = BankAccount::factory()->create(['account_name' => 'Lama']);

        $this->actingAs($this->admin)->put("/bank-accounts/{$account->id}", [
            'account_name' => 'Baru',
            'account_number' => $account->account_number,
            'bank_name' => $account->bank_name,
            'initial_balance' => 2000000,
        ]);

        $this->assertDatabaseHas('bank_accounts', ['id' => $account->id, 'account_name' => 'Baru']);
    }

    public function test_update_requires_edit_permission(): void
    {
        $account = BankAccount::factory()->create();

        $this->actingAs($this->viewer)->put("/bank-accounts/{$account->id}", [
            'account_name' => 'X',
            'account_number' => $account->account_number,
            'bank_name' => $account->bank_name,
            'initial_balance' => 1000000,
        ])->assertForbidden();
    }

    public function test_destroy_deletes_bank_account(): void
    {
        $account = BankAccount::factory()->create();

        $this->actingAs($this->admin)->delete("/bank-accounts/{$account->id}");

        $this->assertDatabaseMissing('bank_accounts', ['id' => $account->id]);
    }

    public function test_destroy_requires_delete_permission(): void
    {
        $account = BankAccount::factory()->create();

        $this->actingAs($this->viewer)->delete("/bank-accounts/{$account->id}")->assertForbidden();
        $this->assertDatabaseHas('bank_accounts', ['id' => $account->id]);
    }
}
