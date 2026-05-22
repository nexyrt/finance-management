<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Invoice $invoice;

    protected BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view invoices', 'create invoices', 'edit invoices'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $client = Client::factory()->create();
        $this->invoice = Invoice::factory()->sent()->create([
            'billed_to_id' => $client->id,
            'total_amount' => 1000000,
        ]);

        $this->bankAccount = BankAccount::factory()->create();
    }

    public function test_store_records_payment_against_a_bank_account(): void
    {
        $this->actingAs($this->admin)
            ->postJson("/invoices/{$this->invoice->id}/payments", [
                'amount' => 500000,
                'payment_date' => '2026-03-10',
                'payment_method' => 'bank_transfer',
                'bank_account_id' => $this->bankAccount->id,
            ])
            ->assertOk();

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 500000,
            'bank_account_id' => $this->bankAccount->id,
        ]);
    }

    public function test_store_requires_a_bank_account(): void
    {
        $this->actingAs($this->admin)
            ->postJson("/invoices/{$this->invoice->id}/payments", [
                'amount' => 500000,
                'payment_date' => '2026-03-10',
                'payment_method' => 'bank_transfer',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('bank_account_id');

        $this->assertDatabaseMissing('payments', [
            'invoice_id' => $this->invoice->id,
            'amount' => 500000,
        ]);
    }

    public function test_recorded_payment_increases_the_bank_account_balance(): void
    {
        $initial = $this->bankAccount->balance;

        $this->actingAs($this->admin)
            ->postJson("/invoices/{$this->invoice->id}/payments", [
                'amount' => 750000,
                'payment_date' => '2026-03-10',
                'payment_method' => 'bank_transfer',
                'bank_account_id' => $this->bankAccount->id,
            ])
            ->assertOk();

        $this->assertSame($initial + 750000, $this->bankAccount->fresh()->balance);
    }
}
