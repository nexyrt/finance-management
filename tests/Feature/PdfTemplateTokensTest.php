<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Services\TemplateTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PdfTemplateTokensTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'manage pdf templates']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo('manage pdf templates');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ── TemplateTokens unit tests ─────────────────────────────────────────────

    public function test_token_catalog_contains_expected_paths(): void
    {
        $paths = array_column(TemplateTokens::catalog(), 'path');

        $this->assertContains('invoice.number', $paths);
        $this->assertContains('invoice.issue_date', $paths);
        $this->assertContains('invoice.due_date', $paths);
        $this->assertContains('invoice.total_amount', $paths);
        $this->assertContains('invoice.amount_paid', $paths);
        $this->assertContains('invoice.amount_remaining', $paths);
        $this->assertContains('client.name', $paths);
        $this->assertContains('client.npwp', $paths);
        $this->assertContains('client.address', $paths);
        $this->assertContains('company.name', $paths);
        $this->assertContains('company.npwp', $paths);
    }

    public function test_catalog_for_frontend_has_no_resolve_callable(): void
    {
        $catalog = TemplateTokens::catalogForFrontend();

        foreach ($catalog as $entry) {
            $this->assertArrayHasKey('path', $entry);
            $this->assertArrayHasKey('label', $entry);
            $this->assertArrayNotHasKey('resolve', $entry);
        }
    }

    public function test_resolve_text_replaces_invoice_tokens(): void
    {
        $client = Client::factory()->create([
            'name' => 'PT Jaya Abadi',
            'NPWP' => '01.111.222.3-456.000',
        ]);

        $invoice = Invoice::factory()->create([
            'billed_to_id' => $client->id,
            'invoice_number' => 'INV/007/KSN/VI.2026',
            'total_amount' => 3500000,
            'issue_date' => '2026-06-01',
            'due_date' => '2026-06-15',
            'status' => 'draft',
        ]);
        $invoice->load(['client', 'payments', 'items']);

        $resolved = TemplateTokens::resolveText('No: {{invoice.number}} | Client: {{client.name}}', $invoice);

        $this->assertStringContainsString('INV/007/KSN/VI.2026', $resolved);
        $this->assertStringContainsString('PT Jaya Abadi', $resolved);
    }

    public function test_resolve_text_formats_money_as_rupiah(): void
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'billed_to_id' => $client->id,
            'total_amount' => 5000000,
        ]);
        $invoice->load(['client', 'payments', 'items']);

        $resolved = TemplateTokens::resolveText('Total: {{invoice.total_amount}}', $invoice);

        $this->assertStringContainsString('Rp 5.000.000', $resolved);
    }

    public function test_resolve_text_formats_date_as_indonesian(): void
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'billed_to_id' => $client->id,
            'issue_date' => '2026-06-08',
        ]);
        $invoice->load(['client', 'payments', 'items']);

        $resolved = TemplateTokens::resolveText('Tanggal: {{invoice.issue_date}}', $invoice);

        $this->assertStringContainsString('08 Juni 2026', $resolved);
    }

    public function test_resolve_text_leaves_unknown_token_as_literal(): void
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        $invoice->load(['client', 'payments', 'items']);

        $resolved = TemplateTokens::resolveText('{{unknown.token}}', $invoice);

        $this->assertSame('{{unknown.token}}', $resolved);
    }

    public function test_resolve_text_resolves_client_tokens(): void
    {
        $client = Client::factory()->create([
            'name' => 'CV Maju Terus',
            'NPWP' => '99.888.777.6-543.000',
            'address' => 'Jl. Sudirman No. 5',
            'email' => 'cv@majuterus.id',
        ]);
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        $invoice->load(['client', 'payments', 'items']);

        $map = TemplateTokens::buildMap($invoice);

        $this->assertSame('CV Maju Terus', $map['client.name']);
        $this->assertSame('99.888.777.6-543.000', $map['client.npwp']);
        $this->assertStringContainsString('Jl. Sudirman', $map['client.address']);
    }

    public function test_resolve_text_resolves_company_tokens(): void
    {
        CompanyProfile::create([
            'name' => 'PT Kisantra',
            'npwp' => '12.345.678.9-000.001',
            'address' => 'Jl. Test No. 1, Jakarta',
            'phone' => '021-9999999',
            'email' => 'info@kisantra.id',
            'finance_manager_name' => 'Manajer Test',
        ]);

        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        $invoice->load(['client', 'payments', 'items']);

        $map = TemplateTokens::buildMap($invoice);

        $this->assertSame('PT Kisantra', $map['company.name']);
        $this->assertSame('12.345.678.9-000.001', $map['company.npwp']);
    }

    public function test_format_rupiah_uses_period_thousands_separator(): void
    {
        $this->assertSame('Rp 0', TemplateTokens::formatRupiah(0));
        $this->assertSame('Rp 1.000', TemplateTokens::formatRupiah(1000));
        $this->assertSame('Rp 1.500.000', TemplateTokens::formatRupiah(1500000));
        $this->assertSame('Rp 100.000.000', TemplateTokens::formatRupiah(100000000));
    }

    public function test_format_date_returns_indonesian_month_names(): void
    {
        $this->assertSame('01 Januari 2026', TemplateTokens::formatDate('2026-01-01'));
        $this->assertSame('15 Februari 2026', TemplateTokens::formatDate('2026-02-15'));
        $this->assertSame('08 Juni 2026', TemplateTokens::formatDate('2026-06-08'));
        $this->assertSame('31 Desember 2025', TemplateTokens::formatDate('2025-12-31'));
    }

    public function test_format_date_returns_empty_string_for_null(): void
    {
        $this->assertSame('', TemplateTokens::formatDate(null));
        $this->assertSame('', TemplateTokens::formatDate(''));
    }

    // ── sample invoice fallback ───────────────────────────────────────────────

    public function test_sample_invoice_has_all_required_fields(): void
    {
        $inv = TemplateTokens::sampleInvoice();

        $this->assertNotEmpty($inv->invoice_number);
        $this->assertNotNull($inv->total_amount);
        $this->assertNotNull($inv->client);
        $this->assertNotEmpty($inv->client->name);
    }

    public function test_sample_invoice_resolves_all_tokens_without_db(): void
    {
        // No DB records — sample invoice should still produce a full map without crashing.
        $inv = TemplateTokens::sampleInvoice();
        $map = TemplateTokens::buildMap($inv);

        $this->assertNotEmpty($map['invoice.number']);
        $this->assertNotEmpty($map['client.name']);
        // company tokens will be empty string since no CompanyProfile in DB — that's fine.
        $this->assertArrayHasKey('company.name', $map);
    }

    // ── Controller integration tests ─────────────────────────────────────────

    public function test_edit_action_passes_token_catalog_and_sample_data(): void
    {
        $template = PdfTemplate::query()->create([
            'name' => 'Test',
            'layout' => [],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/edit")
            ->assertOk();

        $response->assertInertia(fn ($page) => $page
            ->component('settings/pdf-templates/edit')
            ->has('tokenCatalog')
            ->has('sampleData')
            ->has('tokenCatalog.0.path')
            ->has('tokenCatalog.0.label')
        );
    }

    public function test_pdf_renders_with_real_invoice_data(): void
    {
        $client = Client::factory()->create(['name' => 'PT Nyata']);
        $invoice = Invoice::factory()->create([
            'billed_to_id' => $client->id,
            'invoice_number' => 'INV/099/KSN/VI.2026',
            'total_amount' => 7500000,
        ]);

        $template = PdfTemplate::query()->create([
            'name' => 'PDF Real',
            'layout' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20,
                    'content' => 'No: {{invoice.number}} | {{client.name}} | {{invoice.total_amount}}',
                    'fontSize' => 14, 'bold' => false, 'color' => '#000000'],
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_pdf_uses_latest_invoice_when_no_invoice_param(): void
    {
        Invoice::factory()->count(3)->create();

        $template = PdfTemplate::query()->create([
            'name' => 'PDF Latest',
            'layout' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20,
                    'content' => '{{invoice.number}}',
                    'fontSize' => 12, 'bold' => false, 'color' => '#000000'],
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_pdf_falls_back_to_sample_when_db_empty(): void
    {
        // No Client, no Invoice in DB — must not crash.
        $template = PdfTemplate::query()->create([
            'name' => 'PDF Empty DB',
            'layout' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20,
                    'content' => 'No: {{invoice.number}}',
                    'fontSize' => 12, 'bold' => false, 'color' => '#000000'],
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }
}
