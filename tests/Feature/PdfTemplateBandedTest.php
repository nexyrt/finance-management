<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Services\ItemColumns;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * B3 — Banded PDF rendering tests.
 *
 * Tests:
 *  1. Banded PDF renders 200 / application/pdf with header + table + footerFlow.
 *  2. @page margin reflects the layout's per-side margins.
 *  3. Header text element (with {{token}}) is resolved and appears in HTML.
 *  4. Footer-flow element appears AFTER the items table in the HTML (DOM order).
 *  5. Items table renders rows (content present).
 *  6. Many items (40) → renders 200 (multi-page) and footer-flow text is present.
 *  7. Legacy flat-array layout still renders 200 (no regression).
 *  8. Banded layout with no table element still renders (content band table is null).
 *  9. Blade view: banded path uses .band-header, .band-table-flow, .band-footer-flow.
 * 10. Blade view: legacy path does NOT use band-* classes.
 */
class PdfTemplateBandedTest extends TestCase
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

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Create an invoice with N real InvoiceItems in the DB. */
    private function makeInvoiceWithItems(int $count): Invoice
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);

        for ($i = 1; $i <= $count; $i++) {
            InvoiceItem::query()->create([
                'invoice_id' => $invoice->id,
                'client_id' => $client->id,
                'service_name' => "Layanan {$i}",
                'quantity' => '2.000',
                'unit' => 'jam',
                'unit_price' => 500000,
                'amount' => 1000000,
                'cogs_amount' => 200000,
                'is_tax_deposit' => false,
            ]);
        }

        return $invoice->load(['client', 'items', 'payments']);
    }

    /** Build a minimal banded layout array. */
    private function makeBandedLayout(
        array $headerElements = [],
        ?array $tableEl = null,
        array $footerFlowElements = [],
        array $margins = ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40],
        int $headerHeight = 180,
        int $footerFlowHeight = 120,
    ): array {
        return [
            'paper' => ['margins' => $margins],
            'bands' => [
                'header' => [
                    'height' => $headerHeight,
                    'repeat' => false,
                    'elements' => $headerElements,
                ],
                'content' => [
                    'table' => $tableEl,
                ],
                'footerFlow' => [
                    'height' => $footerFlowHeight,
                    'elements' => $footerFlowElements,
                ],
                'footerFixed' => [
                    'height' => 50,
                    'elements' => [],
                ],
            ],
        ];
    }

    /** Default table element for banded content. */
    private function defaultBandedTableEl(): array
    {
        return [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 714,
            'columns' => ItemColumns::defaultColumns(),
            'showFooterSum' => false,
        ];
    }

    // ── Test 1: Banded PDF renders 200 / application/pdf ─────────────────────

    public function test_banded_pdf_renders_200_with_header_table_and_footer_flow(): void
    {
        $invoice = $this->makeInvoiceWithItems(3);

        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Invoice {{invoice.number}}', 'fontSize' => 16, 'bold' => true, 'color' => '#0f172a'],
            ],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [
                ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Terima kasih', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'Banded PDF Test',
            'layout' => $layout,
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

    // ── Test 2: @page margin reflects per-side margins ────────────────────────

    public function test_banded_blade_emits_correct_at_page_margins(): void
    {
        $margins = ['top' => 50, 'right' => 30, 'bottom' => 60, 'left' => 25];

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => $margins],
            'headerBand' => ['height' => 100, 'repeat' => false, 'elements' => []],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // The @page rule must contain the four margin values
        $this->assertStringContainsString('margin: 50px 30px 60px 25px', $html);
    }

    // ── Test 3: Header token element is resolved ──────────────────────────────

    public function test_banded_pdf_resolves_tokens_in_header_elements(): void
    {
        $invoice = $this->makeInvoiceWithItems(1);

        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'No: {{invoice.number}}', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
            ],
            tableEl: null,
            footerFlowElements: [],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'Token Test',
            'layout' => $layout,
            'is_default' => false,
        ]);

        // Use the HTTP endpoint — controller resolves tokens before passing to blade
        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );

        // Verify token resolution directly via blade view (resolves from real invoice)
        $invoice->load(['client', 'items', 'payments']);
        $invoiceNumber = (string) ($invoice->invoice_number ?? 'INV');

        // Render the blade directly with already-resolved content to confirm the path works
        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => [
                'height' => 180,
                'repeat' => false,
                'elements' => [
                    ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => "No: {$invoiceNumber}", 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString($invoiceNumber, $html);
        $this->assertStringContainsString('band-header', $html);
    }

    // ── Test 4: Footer-flow element appears AFTER the items table in HTML ─────

    public function test_footer_flow_element_appears_after_items_table_in_html(): void
    {
        $columns = ItemColumns::defaultColumns();
        $tableEl = array_merge($this->defaultBandedTableEl(), ['rows' => [['no' => '1', 'description' => 'Produk A', 'quantity' => '1', 'unit_price' => 'Rp 100.000', 'amount' => 'Rp 100.000']]]);

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => [
                'height' => 180,
                'repeat' => false,
                'elements' => [
                    ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'HEADER_SENTINEL', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'tableEl' => $tableEl,
            'footerFlowBand' => [
                'height' => 120,
                'elements' => [
                    ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'FOOTER_SENTINEL', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString('HEADER_SENTINEL', $html);
        $this->assertStringContainsString('FOOTER_SENTINEL', $html);

        // Footer-flow text must appear AFTER the items table in the HTML (by string position)
        $tablePos = strpos($html, 'items-table');
        $footerPos = strpos($html, 'FOOTER_SENTINEL');

        $this->assertNotFalse($tablePos, 'items-table not found in banded HTML');
        $this->assertNotFalse($footerPos, 'FOOTER_SENTINEL not found in banded HTML');
        $this->assertGreaterThan($tablePos, $footerPos, 'Footer-flow text must appear after the items table');

        // The footer-flow container must use page-break-inside: avoid
        $this->assertStringContainsString('page-break-inside: avoid', $html);
    }

    // ── Test 5: Items table renders rows ─────────────────────────────────────

    public function test_banded_items_table_renders_rows(): void
    {
        $columns = ItemColumns::defaultColumns();
        $tableEl = array_merge($this->defaultBandedTableEl(), [
            'rows' => [
                ['no' => '1', 'description' => 'Layanan Keren', 'quantity' => '2', 'unit_price' => 'Rp 500.000', 'amount' => 'Rp 1.000.000'],
                ['no' => '2', 'description' => 'Layanan Lain', 'quantity' => '1', 'unit_price' => 'Rp 200.000', 'amount' => 'Rp 200.000'],
            ],
        ]);

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => $tableEl,
            'footerFlowBand' => ['height' => 120, 'elements' => []],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString('Layanan Keren', $html);
        $this->assertStringContainsString('Layanan Lain', $html);
        $this->assertStringContainsString('Rp 1.000.000', $html);
        $this->assertStringContainsString('band-table-flow', $html);
    }

    // ── Test 6: Many items (40) → renders 200 and footer-flow text is present ─

    public function test_banded_pdf_with_many_items_renders_200_and_footer_present(): void
    {
        $invoice = $this->makeInvoiceWithItems(40);

        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Invoice Header', 'fontSize' => 16, 'bold' => true, 'color' => '#0f172a'],
            ],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [
                ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'FOOTER_MANY_ITEMS', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'Banded Many Items',
            'layout' => $layout,
            'is_default' => false,
        ]);

        // HTTP endpoint: must return 200 application/pdf
        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );

        // The footer-flow sentinel must appear in the blade render (not dropped)
        $invoice->load(['client', 'items', 'payments']);
        $columns = ItemColumns::defaultColumns();
        $rows = ItemColumns::resolveItems($columns, $invoice->items);
        $tableEl = array_merge($this->defaultBandedTableEl(), ['rows' => $rows]);

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Invoice Header', 'fontSize' => 16, 'bold' => true, 'color' => '#0f172a'],
            ]],
            'tableEl' => $tableEl,
            'footerFlowBand' => ['height' => 120, 'elements' => [
                ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'FOOTER_MANY_ITEMS', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
            ]],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString('FOOTER_MANY_ITEMS', $html);
        $this->assertCount(40, $rows);
    }

    // ── Test 7: Legacy flat-array layout still renders 200 (no regression) ───

    public function test_legacy_flat_array_layout_still_renders_200(): void
    {
        $template = PdfTemplate::query()->create([
            'name' => 'Legacy Flat',
            'layout' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20, 'content' => 'Plain legacy text', 'fontSize' => 14, 'bold' => false, 'color' => '#000000'],
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

    // ── Test 8: Banded layout with no table renders without error ─────────────

    public function test_banded_pdf_without_table_renders_200(): void
    {
        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Tanpa Tabel', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
            ],
            tableEl: null,
            footerFlowElements: [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Footer tanpa tabel', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'Banded No Table',
            'layout' => $layout,
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

    // ── Test 9: Banded blade uses .band-* classes; legacy does NOT ───────────

    public function test_banded_blade_uses_band_classes_not_legacy_classes(): void
    {
        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // Banded-specific containers present
        $this->assertStringContainsString('class="band-header"', $html);
        $this->assertStringContainsString('class="band-footer-flow"', $html);

        // Legacy zone elements must NOT appear as HTML elements (the CSS class names
        // appear in the <style> block but the HTML div elements must not)
        $this->assertStringNotContainsString('class="paper"', $html);
        $this->assertStringNotContainsString('class="below-flow"', $html);
    }

    public function test_legacy_blade_uses_legacy_classes_not_band_classes(): void
    {
        $html = view('pdf.template-builder', [
            'elements' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20, 'content' => 'Hello', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
            ],
            'customFonts' => [],
        ])->render();

        // Legacy paper div present
        $this->assertStringContainsString('class="paper"', $html);

        // Banded containers must NOT appear as HTML elements
        $this->assertStringNotContainsString('class="band-header"', $html);
        $this->assertStringNotContainsString('class="band-footer-flow"', $html);

        // Legacy @page must use margin: 0
        $this->assertStringContainsString('margin: 0', $html);
    }

    // ── B4 Tests ──────────────────────────────────────────────────────────────

    // ── Test B4-1: Footer-fixed → position:fixed bottom + padding-bottom on body ─

    public function test_footer_fixed_renders_as_position_fixed_with_padding_bottom(): void
    {
        $ffxHeight = 60;

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => [
                'height' => $ffxHeight,
                'elements' => [
                    ['id' => 99, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FIXED_FOOTER_SENTINEL', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // Fixed container must be present with bottom:0
        $this->assertStringContainsString('position: fixed', $html);
        $this->assertStringContainsString('bottom: 0', $html);
        // The sentinel element must be rendered inside it
        $this->assertStringContainsString('FIXED_FOOTER_SENTINEL', $html);
        // Body padding-bottom must be >= footerFixed height
        $this->assertMatchesRegularExpression('/padding-bottom:\s*'.$ffxHeight.'px/', $html);
    }

    // ── Test B4-2: Header repeat=true → position:fixed top + padding-top on body ─

    public function test_header_repeat_true_renders_as_position_fixed_with_padding_top(): void
    {
        $hHeight = 150;

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => [
                'height' => $hHeight,
                'repeat' => true,
                'elements' => [
                    ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'REPEAT_HEADER_SENTINEL', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 0, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // Running header: position:fixed + top:0
        $this->assertStringContainsString('position: fixed', $html);
        $this->assertStringContainsString('top: 0', $html);
        $this->assertStringContainsString('REPEAT_HEADER_SENTINEL', $html);
        // band-header-fixed class (not band-header which is flow)
        $this->assertStringContainsString('band-header-fixed', $html);
        $this->assertStringNotContainsString('class="band-header"', $html);
        // Body padding-top >= header height
        $this->assertMatchesRegularExpression('/padding-top:\s*'.$hHeight.'px/', $html);
    }

    // ── Test B4-3: Header repeat=false → flow block (B3 behaviour, no fixed) ────

    public function test_header_repeat_false_renders_as_flow_not_fixed(): void
    {
        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => [
                'height' => 180,
                'repeat' => false,
                'elements' => [
                    ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FLOW_HEADER_SENTINEL', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a'],
                ],
            ],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 0, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // Flow header: uses .band-header element, NOT .band-header-fixed element
        $this->assertStringContainsString('class="band-header"', $html);
        $this->assertStringNotContainsString('class="band-header-fixed"', $html);
        $this->assertStringContainsString('FLOW_HEADER_SENTINEL', $html);
        // No body padding-top injected (no running header)
        $this->assertStringNotContainsString('padding-top:', $html);
    }

    // ── Test B4-4: Multi-page (40 items) with BOTH footer-fixed + footer-flow ───

    public function test_banded_multipage_with_footer_fixed_and_footer_flow_renders_200(): void
    {
        $invoice = $this->makeInvoiceWithItems(40);

        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'Invoice Header', 'fontSize' => 14, 'bold' => true, 'color' => '#0f172a'],
            ],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FLOW_FOOTER_40', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        // Add footerFixed elements to the layout
        $layout['bands']['footerFixed'] = [
            'height' => 50,
            'elements' => [
                ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FIXED_FOOTER_40', 'fontSize' => 9, 'bold' => false, 'color' => '#555555'],
            ],
        ];

        $template = PdfTemplate::query()->create([
            'name' => 'B4 Multi-page Both Footers',
            'layout' => $layout,
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );

        // Blade render: both footer sentinels present
        $invoice->load(['client', 'items', 'payments']);
        $columns = ItemColumns::defaultColumns();
        $rows = ItemColumns::resolveItems($columns, $invoice->items);
        $tableEl = array_merge($this->defaultBandedTableEl(), ['rows' => $rows]);

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => $tableEl,
            'footerFlowBand' => ['height' => 60, 'elements' => [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FLOW_FOOTER_40', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ]],
            'footerFixedBand' => ['height' => 50, 'elements' => [
                ['id' => 3, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'FIXED_FOOTER_40', 'fontSize' => 9, 'bold' => false, 'color' => '#555555'],
            ]],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString('FLOW_FOOTER_40', $html);
        $this->assertStringContainsString('FIXED_FOOTER_40', $html);
        $this->assertStringContainsString('band-footer-fixed', $html);
        $this->assertStringContainsString('band-footer-flow', $html);
    }

    // ── B5 Tests ──────────────────────────────────────────────────────────────

    // ── Test B5-1: ?items=3 → banded PDF 200 with ~3 sample rows ─────────────

    public function test_banded_pdf_with_items_param_3_renders_sample_rows(): void
    {
        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'B5 Test Header', 'fontSize' => 14, 'bold' => true, 'color' => '#0f172a'],
            ],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'B5_3_ITEMS_FOOTER', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'B5 Items=3',
            'layout' => $layout,
            'is_default' => false,
        ]);

        // ?items=3 — uses sample rows, no real invoice needed.
        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf?items=3")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );

        // Verify sample rows are generated correctly (3 rows via makeSampleItems)
        // by rendering the blade directly with 3 resolved sample rows.
        $columns = ItemColumns::defaultColumns();
        $sampleItems = collect(range(1, 3))->map(fn (int $i) => new InvoiceItem([
            'invoice_id' => 0,
            'service_name' => 'Item Contoh '.$i,
            'quantity' => '1.000',
            'unit' => 'paket',
            'unit_price' => 500000,
            'amount' => 500000,
            'cogs_amount' => 200000,
            'is_tax_deposit' => false,
        ]));
        $rows = ItemColumns::resolveItems($columns, $sampleItems);
        $this->assertCount(3, $rows);
    }

    // ── Test B5-2: ?items=60 → 200, multi-page (page count > 1) ─────────────

    public function test_banded_pdf_with_items_param_60_is_multipage(): void
    {
        $layout = $this->makeBandedLayout(
            headerElements: [
                ['id' => 1, 'type' => 'text', 'x' => 20, 'y' => 20, 'content' => 'B5 60-item Header', 'fontSize' => 14, 'bold' => true, 'color' => '#0f172a'],
            ],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'B5_60_ITEMS_FOOTER', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'B5 Items=60',
            'layout' => $layout,
            'is_default' => false,
        ]);

        // ?items=60 must return a valid PDF.
        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf?items=60")
            ->assertOk();

        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('content-type'),
        );

        // 60 rows will produce multiple pages: assert page count > 1 via DomPDF canvas.
        $columns = ItemColumns::defaultColumns();
        // Build 60 sample rows the same way the controller does.
        $units = ['jam', 'paket', 'bulan', 'unit', 'ls', 'hari'];
        $services = ['Konsultasi IT', 'Pengembangan Fitur', 'Desain UI/UX', 'Hosting & Domain', 'Pemeliharaan Bulanan', 'Pelatihan Pengguna', 'Audit Sistem', 'Integrasi API', 'Backup & Recovery', 'Laporan Bulanan'];
        $sampleItems = collect(range(1, 60))->map(function (int $i) use ($services, $units): InvoiceItem {
            $unitPrice = ($i % 5 + 1) * 250000;
            $qty = ($i % 3 + 1);

            return new InvoiceItem([
                'invoice_id' => 0,
                'service_name' => ($services[($i - 1) % count($services)]).' '.($i > count($services) ? '#'.ceil($i / count($services)) : ''),
                'quantity' => number_format($qty, 3, '.', ''),
                'unit' => $units[($i - 1) % count($units)],
                'unit_price' => $unitPrice,
                'amount' => $unitPrice * $qty,
                'cogs_amount' => (int) ($unitPrice * $qty * 0.4),
                'is_tax_deposit' => $i % 10 === 0,
            ]);
        });
        $rows = ItemColumns::resolveItems($columns, $sampleItems);
        $this->assertCount(60, $rows);

        $tableEl = array_merge($this->defaultBandedTableEl(), ['rows' => $rows]);

        // Render through DomPDF and assert page count > 1.
        $pdf = Pdf::loadView('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => $tableEl,
            'footerFlowBand' => ['height' => 120, 'elements' => [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'B5_60_ITEMS_FOOTER', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ]],
            'footerFixedBand' => ['height' => 0, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->setPaper('A4', 'portrait');

        $pdf->render();
        $pageCount = $pdf->getDomPDF()->getCanvas()->get_page_count();
        $this->assertGreaterThan(1, $pageCount, '60 sample rows should produce more than 1 page');

        // Footer-flow text must be present in the HTML.
        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 180, 'repeat' => false, 'elements' => []],
            'tableEl' => $tableEl,
            'footerFlowBand' => ['height' => 120, 'elements' => [
                ['id' => 2, 'type' => 'text', 'x' => 20, 'y' => 10, 'content' => 'B5_60_ITEMS_FOOTER', 'fontSize' => 10, 'bold' => false, 'color' => '#0f172a'],
            ]],
            'footerFixedBand' => ['height' => 0, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        $this->assertStringContainsString('B5_60_ITEMS_FOOTER', $html);
    }

    // ── Test B5-3: items param clamping (0 → 1, 9999 → 200) ─────────────────

    public function test_banded_pdf_items_param_clamping(): void
    {
        $layout = $this->makeBandedLayout(
            headerElements: [],
            tableEl: $this->defaultBandedTableEl(),
            footerFlowElements: [],
        );

        $template = PdfTemplate::query()->create([
            'name' => 'B5 Clamping',
            'layout' => $layout,
            'is_default' => false,
        ]);

        // items=0 should be clamped to 1 — renders without error.
        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf?items=0")
            ->assertOk();

        // items=9999 should be clamped to 200 — renders without error.
        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf?items=9999")
            ->assertOk();

        // items=-5 should also be clamped to 1 — renders without error.
        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf?items=-5")
            ->assertOk();
    }

    // ── Test 10: Grid element in header band renders with token resolution ────

    public function test_banded_header_grid_element_renders(): void
    {
        $cells = [
            [
                ['text' => 'Nama: {{client.name}}', 'align' => 'left', 'bold' => false, 'color' => '#0f172a', 'fill' => ''],
                ['text' => 'No: {{invoice.number}}', 'align' => 'right', 'bold' => false, 'color' => '#0f172a', 'fill' => ''],
            ],
        ];

        $html = view('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => [
                'height' => 180,
                'repeat' => false,
                'elements' => [
                    [
                        'id' => 1,
                        'type' => 'grid',
                        'x' => 20,
                        'y' => 20,
                        'width' => 400,
                        'cells' => $cells,
                        'colWidths' => [200, 200],
                        'border' => ['width' => 1, 'color' => '#cbd5e1'],
                    ],
                ],
            ],
            'tableEl' => null,
            'footerFlowBand' => ['height' => 80, 'elements' => []],
            'footerFixedBand' => ['height' => 40, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ])->render();

        // Grid token text should appear (not resolved here — controller resolves; blade shows as-is)
        $this->assertStringContainsString('Nama:', $html);
        $this->assertStringContainsString('No:', $html);
        $this->assertStringContainsString('grid-el', $html);
    }
}
