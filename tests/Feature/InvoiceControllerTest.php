<?php

namespace Tests\Feature;

use App\Exports\InvoiceRecapExport;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
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

    public function test_index_reports_total_outstanding(): void
    {
        // sent invoice 1,000,000 with a 400,000 payment → 600,000 outstanding
        $invoice = Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'total_amount' => 1_000_000,
        ]);
        Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'amount' => 400_000,
        ]);

        // paid invoice should NOT count toward outstanding
        Invoice::factory()->paid()->create([
            'billed_to_id' => $this->client->id,
            'total_amount' => 500_000,
        ]);

        $this->actingAs($this->admin)
            ->get('/invoices')
            ->assertInertia(fn ($page) => $page->where('stats.total_outstanding', 600_000));
    }

    public function test_export_excel_downloads_spreadsheet(): void
    {
        Invoice::factory()->sent()->create(['billed_to_id' => $this->client->id, 'total_amount' => 1_000_000]);

        $response = $this->actingAs($this->admin)->get('/invoices/export/excel?period_mode=range');

        $response->assertOk();
        $this->assertStringContainsString('rekap-invoice-', $response->headers->get('content-disposition') ?? '');
    }

    public function test_export_pdf_downloads_pdf(): void
    {
        Invoice::factory()->sent()->create(['billed_to_id' => $this->client->id, 'total_amount' => 1_000_000]);

        $response = $this->actingAs($this->admin)->get('/invoices/export/pdf?period_mode=range');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_requires_view_invoices_permission(): void
    {
        $noPermUser = User::factory()->create();
        $this->actingAs($noPermUser)->get('/invoices/export/excel')->assertForbidden();
        $this->actingAs($noPermUser)->get('/invoices/export/pdf')->assertForbidden();
    }

    public function test_export_includes_all_invoices_when_month_is_empty(): void
    {
        // "Semua" period: month='' must NOT fall back to the current-month default,
        // otherwise the export is empty while the listing shows everything.
        Invoice::factory()->paid()->create([
            'billed_to_id' => $this->client->id,
            'issue_date' => now()->subMonths(3)->toDateString(),
            'total_amount' => 1_000_000,
        ]);
        Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'issue_date' => now()->subMonths(2)->toDateString(),
            'total_amount' => 2_000_000,
        ]);

        $this->travelTo(Carbon::parse('2026-05-01 10:00:00'));
        Excel::fake();

        $this->actingAs($this->admin)
            ->get('/invoices/export/excel?period_mode=month&month=')
            ->assertOk();

        Excel::assertDownloaded(
            'rekap-invoice-20260501-100000.xlsx',
            fn (InvoiceRecapExport $export) => str_contains(
                collect($export->array())->flatten()->implode('|'),
                'TOTAL (2 invoice)'
            )
        );
    }

    public function test_date_range_overrides_month_in_export(): void
    {
        $this->travelTo(Carbon::parse('2026-05-01 10:00:00'));

        Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'invoice_number' => 'INV/MAR/KSN',
            'issue_date' => '2026-03-10',
            'total_amount' => 1_000_000,
        ]);
        Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'invoice_number' => 'INV/MAY/KSN',
            'issue_date' => '2026-05-10',
            'total_amount' => 2_000_000,
        ]);

        Excel::fake();

        // month=May is set, but a March range is also set → the range must win.
        $this->actingAs($this->admin)
            ->get('/invoices/export/excel?month=2026-05&date_from=2026-03-01&date_to=2026-03-31')
            ->assertOk();

        Excel::assertDownloaded(
            'rekap-invoice-20260501-100000.xlsx',
            function (InvoiceRecapExport $export) {
                $flat = collect($export->array())->flatten()->implode('|');

                return str_contains($flat, 'INV/MAR/KSN')
                    && ! str_contains($flat, 'INV/MAY/KSN');
            }
        );
    }

    public function test_export_excel_respects_active_filters(): void
    {
        // Freeze time so the timestamped filename is deterministic.
        $this->travelTo(Carbon::parse('2026-05-01 10:00:00'));

        Invoice::factory()->paid()->create([
            'billed_to_id' => $this->client->id,
            'invoice_number' => 'INV/PAID/KSN/05.26',
            'issue_date' => '2026-05-10',
            'total_amount' => 1_000_000,
        ]);
        Invoice::factory()->sent()->create([
            'billed_to_id' => $this->client->id,
            'invoice_number' => 'INV/SENT/KSN/05.26',
            'issue_date' => '2026-05-12',
            'total_amount' => 2_000_000,
        ]);

        Excel::fake();

        $this->actingAs($this->admin)
            ->get('/invoices/export/excel?period_mode=range&status=paid')
            ->assertOk();

        Excel::assertDownloaded(
            'rekap-invoice-20260501-100000.xlsx',
            function (InvoiceRecapExport $export) {
                $flat = collect($export->array())->flatten()->implode('|');

                // The paid invoice is included; the sent one is filtered out.
                return str_contains($flat, 'INV/PAID/KSN/05.26')
                    && ! str_contains($flat, 'INV/SENT/KSN/05.26');
            }
        );
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
