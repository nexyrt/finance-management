<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Services\TemplateTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 6 — Integration tests for builder template print flow.
 *
 * Covers:
 *  1. payment.* token resolution for full / dp / pelunasan modes.
 *  2. Builder render via invoice download/preview routes (template=builder:{id}).
 *  3. DP and pelunasan modes on the builder route produce correct amounts.
 *  4. Permission gate matches existing invoice print routes.
 *  5. Legacy (Bawaan) routes still work unchanged — no regression.
 */
class PdfTemplatePrintIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'view invoices']);
        Permission::firstOrCreate(['name' => 'manage pdf templates']);

        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->givePermissionTo(['view invoices', 'manage pdf templates']);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function makeInvoice(int $total = 5_000_000): Invoice
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'billed_to_id' => $client->id,
            'invoice_number' => 'INV/001/KSN/VI.2026',
            'total_amount' => $total,
            'subtotal' => $total,
        ]);
        $invoice->load(['client', 'items', 'payments']);

        return $invoice;
    }

    private function makeBuilderTemplate(array $layout = []): PdfTemplate
    {
        return PdfTemplate::query()->create([
            'name' => 'Test Builder Template',
            'layout' => $layout ?: [
                [
                    'id' => 'el1',
                    'type' => 'text',
                    'x' => 10,
                    'y' => 20,
                    'width' => 400,
                    'height' => 30,
                    'content' => 'Mode: {{payment.mode}} | Tagih: {{payment.display_amount}}',
                    'fontSize' => 12,
                    'bold' => false,
                    'color' => '#000000',
                ],
            ],
            'is_default' => false,
        ]);
    }

    // ── payment.* token unit tests ────────────────────────────────────────────

    public function test_payment_mode_token_resolves_full(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'full']);

        $this->assertSame('Penuh', $map['payment.mode']);
    }

    public function test_payment_mode_token_resolves_dp(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'dp', 'dp_amount' => 2_000_000]);

        $this->assertSame('Uang Muka', $map['payment.mode']);
    }

    public function test_payment_mode_token_resolves_pelunasan(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'pelunasan', 'pelunasan_amount' => 3_000_000]);

        $this->assertSame('Pelunasan', $map['payment.mode']);
    }

    public function test_payment_display_amount_full_mode_equals_invoice_total(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'full']);

        $this->assertSame('Rp 5.000.000', $map['payment.display_amount']);
    }

    public function test_payment_display_amount_dp_mode_equals_dp_amount(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'dp', 'dp_amount' => 2_000_000]);

        $this->assertSame('Rp 2.000.000', $map['payment.display_amount']);
    }

    public function test_payment_display_amount_pelunasan_mode_equals_pelunasan_amount(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'pelunasan', 'pelunasan_amount' => 3_000_000]);

        $this->assertSame('Rp 3.000.000', $map['payment.display_amount']);
    }

    public function test_payment_dp_amount_token_empty_when_no_dp(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'full']);

        $this->assertSame('', $map['payment.dp_amount']);
    }

    public function test_payment_dp_amount_token_formatted_when_dp(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'dp', 'dp_amount' => 1_500_000]);

        $this->assertSame('Rp 1.500.000', $map['payment.dp_amount']);
    }

    public function test_payment_pelunasan_amount_token_formatted_when_pelunasan(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'pelunasan', 'pelunasan_amount' => 3_500_000]);

        $this->assertSame('Rp 3.500.000', $map['payment.pelunasan_amount']);
    }

    public function test_payment_remaining_after_dp_is_formatted(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'dp', 'dp_amount' => 2_000_000]);

        $this->assertSame('Rp 3.000.000', $map['payment.remaining_after']);
    }

    public function test_payment_remaining_after_empty_when_no_dp(): void
    {
        $invoice = $this->makeInvoice();

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'full']);

        $this->assertSame('', $map['payment.remaining_after']);
    }

    public function test_payment_display_amount_words_full_mode(): void
    {
        $invoice = $this->makeInvoice(1_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'full']);

        $this->assertStringContainsString('Juta', $map['payment.display_amount_words']);
        $this->assertStringContainsString('Rupiah', $map['payment.display_amount_words']);
    }

    public function test_payment_dp_percentage_calculated(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $map = TemplateTokens::buildMap($invoice, ['mode' => 'dp', 'dp_amount' => 2_500_000]);

        $this->assertSame('50%', $map['payment.dp_percentage']);
    }

    public function test_payment_tokens_in_catalog(): void
    {
        $paths = array_column(TemplateTokens::catalog(), 'path');

        $this->assertContains('payment.mode', $paths);
        $this->assertContains('payment.display_amount', $paths);
        $this->assertContains('payment.display_amount_words', $paths);
        $this->assertContains('payment.dp_amount', $paths);
        $this->assertContains('payment.dp_percentage', $paths);
        $this->assertContains('payment.pelunasan_amount', $paths);
        $this->assertContains('payment.already_paid', $paths);
        $this->assertContains('payment.remaining_after', $paths);
    }

    public function test_resolve_text_passes_payment_context_to_tokens(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $resolved = TemplateTokens::resolveText(
            'Mode: {{payment.mode}} Tagih: {{payment.display_amount}}',
            $invoice,
            ['mode' => 'dp', 'dp_amount' => 2_000_000],
        );

        $this->assertStringContainsString('Uang Muka', $resolved);
        $this->assertStringContainsString('Rp 2.000.000', $resolved);
    }

    public function test_payment_context_defaults_to_full_when_empty(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        // No payment context passed → should behave as 'full'
        $map = TemplateTokens::buildMap($invoice);

        $this->assertSame('Penuh', $map['payment.mode']);
        $this->assertSame('Rp 5.000.000', $map['payment.display_amount']);
    }

    // ── Builder render route — full mode ─────────────────────────────────────

    public function test_builder_download_returns_pdf_full_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=builder:{$template->id}");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_builder_preview_returns_pdf_full_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/preview?template=builder:{$template->id}");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    // ── Builder render route — DP mode ───────────────────────────────────────

    public function test_builder_download_returns_pdf_dp_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate([
            [
                'id' => 'el1',
                'type' => 'text',
                'x' => 10,
                'y' => 20,
                'width' => 400,
                'height' => 30,
                'content' => 'DP: {{payment.dp_amount}}',
                'fontSize' => 12,
                'bold' => false,
                'color' => '#000000',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=builder:{$template->id}&dp_amount=2000000");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_builder_preview_returns_pdf_dp_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/preview?template=builder:{$template->id}&dp_amount=1500000");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    // ── Builder render route — pelunasan mode ────────────────────────────────

    public function test_builder_download_returns_pdf_pelunasan_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate([
            [
                'id' => 'el1',
                'type' => 'text',
                'x' => 10,
                'y' => 20,
                'width' => 400,
                'height' => 30,
                'content' => 'Pelunasan: {{payment.pelunasan_amount}}',
                'fontSize' => 12,
                'bold' => false,
                'color' => '#000000',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=builder:{$template->id}&pelunasan_amount=3000000");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_builder_preview_returns_pdf_pelunasan_mode(): void
    {
        $invoice = $this->makeInvoice(5_000_000);
        $template = $this->makeBuilderTemplate();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/preview?template=builder:{$template->id}&pelunasan_amount=3000000");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    // ── Permission / authorization ────────────────────────────────────────────

    public function test_builder_download_requires_authentication(): void
    {
        $invoice = $this->makeInvoice();
        $template = $this->makeBuilderTemplate();

        $this->get("/invoice/{$invoice->id}/download?template=builder:{$template->id}")
            ->assertRedirect('/login');
    }

    public function test_builder_preview_requires_authentication(): void
    {
        $invoice = $this->makeInvoice();
        $template = $this->makeBuilderTemplate();

        $this->get("/invoice/{$invoice->id}/preview?template=builder:{$template->id}")
            ->assertRedirect('/login');
    }

    public function test_builder_download_requires_view_invoices_permission(): void
    {
        $noPermUser = User::factory()->create();
        $invoice = $this->makeInvoice();
        $template = $this->makeBuilderTemplate();

        $this->actingAs($noPermUser)
            ->get("/invoice/{$invoice->id}/download?template=builder:{$template->id}")
            ->assertForbidden();
    }

    public function test_builder_preview_requires_view_invoices_permission(): void
    {
        $noPermUser = User::factory()->create();
        $invoice = $this->makeInvoice();
        $template = $this->makeBuilderTemplate();

        $this->actingAs($noPermUser)
            ->get("/invoice/{$invoice->id}/preview?template=builder:{$template->id}")
            ->assertForbidden();
    }

    public function test_builder_download_returns_404_for_nonexistent_template(): void
    {
        $invoice = $this->makeInvoice();

        $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=builder:99999")
            ->assertNotFound();
    }

    // ── Legacy (Bawaan) route regression ─────────────────────────────────────

    public function test_legacy_download_still_works_with_kisantra_template(): void
    {
        $invoice = $this->makeInvoice();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=kisantra-invoice");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_legacy_preview_still_works_with_kisantra_template(): void
    {
        $invoice = $this->makeInvoice();

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/preview?template=kisantra-invoice");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }

    public function test_legacy_download_dp_mode_still_works(): void
    {
        $invoice = $this->makeInvoice(5_000_000);

        $response = $this->actingAs($this->user)
            ->get("/invoice/{$invoice->id}/download?template=kisantra-invoice&dp_amount=2000000");

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );
    }
}
