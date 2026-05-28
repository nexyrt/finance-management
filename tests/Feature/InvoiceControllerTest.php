<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $viewer;

    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = ['view invoices', 'create invoices', 'edit invoices', 'delete invoices'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions(['view invoices']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->viewer = User::factory()->create();
        $this->viewer->assignRole('viewer');

        $this->client = Client::factory()->create();
    }

    public function test_index_requires_authentication(): void
    {
        $this->get('/invoices')->assertRedirect('/login');
    }

    public function test_index_requires_view_invoices_permission(): void
    {
        $noPermUser = User::factory()->create();
        $this->actingAs($noPermUser)->get('/invoices')->assertForbidden();
    }

    public function test_index_renders_for_authorized_user(): void
    {
        $this->actingAs($this->admin)->get('/invoices')->assertOk();
    }

    public function test_create_page_renders(): void
    {
        $this->actingAs($this->admin)->get('/invoices/create')->assertOk();
    }

    public function test_create_page_requires_create_invoices_permission(): void
    {
        $this->actingAs($this->viewer)->get('/invoices/create')->assertForbidden();
    }

    public function test_store_creates_draft_invoice(): void
    {
        $this->actingAs($this->admin)->post('/invoices', [
            'client_id' => $this->client->id,
            'issue_date' => '2026-03-01',
            'due_date' => '2026-03-31',
            'items' => [
                [
                    'service_name' => 'Jasa Konsultasi',
                    'quantity' => 1,
                    'unit' => 'pcs',
                    'unit_price' => 1000000,
                    'cogs_amount' => 0,
                    'is_tax_deposit' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('invoices', [
            'billed_to_id' => $this->client->id,
            'status' => 'draft',
            'total_amount' => 1000000,
        ]);
    }

    public function test_store_requires_create_invoices_permission(): void
    {
        $this->actingAs($this->viewer)->post('/invoices', [
            'client_id' => $this->client->id,
            'issue_date' => '2026-03-01',
            'due_date' => '2026-03-31',
            'items' => [['service_name' => 'Test', 'quantity' => 1, 'unit_price' => 1000]],
        ])->assertForbidden();
    }

    public function test_edit_page_renders(): void
    {
        $invoice = Invoice::factory()->draft()->create(['billed_to_id' => $this->client->id]);

        $this->actingAs($this->admin)->get("/invoices/{$invoice->id}/edit")->assertOk();
    }

    public function test_destroy_deletes_invoice(): void
    {
        $invoice = Invoice::factory()->draft()->create(['billed_to_id' => $this->client->id]);

        $this->actingAs($this->admin)->delete("/invoices/{$invoice->id}");

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_destroy_requires_delete_invoices_permission(): void
    {
        $invoice = Invoice::factory()->draft()->create(['billed_to_id' => $this->client->id]);

        $this->actingAs($this->viewer)->delete("/invoices/{$invoice->id}")->assertForbidden();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id]);
    }

    public function test_send_changes_status_to_sent(): void
    {
        $invoice = Invoice::factory()->draft()->create([
            'billed_to_id' => $this->client->id,
            'issue_date' => '2026-03-01',
        ]);

        $this->actingAs($this->admin)->post("/invoices/{$invoice->id}/send", [
            'invoice_number' => '001/INV/SPI-XX/III/2026',
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
            'invoice_number' => '001/INV/SPI-XX/III/2026',
        ]);
    }

    public function test_send_rejects_non_draft_invoice(): void
    {
        $invoice = Invoice::factory()->sent()->create(['billed_to_id' => $this->client->id]);

        $response = $this->actingAs($this->admin)->post("/invoices/{$invoice->id}/send", [
            'invoice_number' => '002/INV/SPI-XX/III/2026',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'sent']);
    }

    public function test_rollback_returns_sent_invoice_to_draft(): void
    {
        $invoice = Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'invoice_number' => '001/INV/SPI-XX/III/2026',
            'issue_date' => '2026-03-01',
        ]);

        $this->actingAs($this->admin)->post("/invoices/{$invoice->id}/rollback");

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'draft',
            'invoice_number' => null,
        ]);
    }
}
