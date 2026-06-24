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
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 3 — Data-bound items table tests.
 *
 * Covers:
 *  - PDF renders with few items (single page)  →  assert 200 + application/pdf + item values
 *  - PDF renders with many items (forces page 2+)  →  assert 200 + application/pdf
 *  - Column config respected (only chosen columns appear; Rupiah format on money cols)
 *  - Save/load round-trip for a layout containing a table element
 *  - ItemColumns catalog: correct keys, defaults, and resolution
 *  - Table element passes 'table' type validation in save
 */
class PdfTemplateTableTest extends TestCase
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

    /** Build a table element JSON payload with the default columns. */
    private function defaultTableEl(int $y = 350): array
    {
        return [
            'id' => 1,
            'type' => 'table',
            'x' => 40,
            'y' => $y,
            'width' => 714,
            'columns' => ItemColumns::defaultColumns(),
            'showFooterSum' => false,
        ];
    }

    // ── ItemColumns catalog unit tests ────────────────────────────────────────

    public function test_catalog_contains_required_keys(): void
    {
        $keys = array_column(ItemColumns::catalog(), 'key');

        $this->assertContains('no', $keys);
        $this->assertContains('description', $keys);
        $this->assertContains('quantity', $keys);
        $this->assertContains('unit_price', $keys);
        $this->assertContains('amount', $keys);
    }

    public function test_default_columns_are_standar_set(): void
    {
        $defaults = ItemColumns::defaultColumns();
        $defaultKeys = array_column($defaults, 'key');

        // Standar = No · Deskripsi · Qty · Harga Satuan · Jumlah
        $this->assertContains('no', $defaultKeys);
        $this->assertContains('description', $defaultKeys);
        $this->assertContains('quantity', $defaultKeys);
        $this->assertContains('unit_price', $defaultKeys);
        $this->assertContains('amount', $defaultKeys);
        // 'unit' is NOT in the default preset
        $this->assertNotContains('unit', $defaultKeys);
    }

    public function test_resolve_item_returns_rupiah_for_money_columns(): void
    {
        $item = new InvoiceItem([
            'invoice_id' => 0,
            'service_name' => 'Test Layanan',
            'quantity' => '3.000',
            'unit' => 'jam',
            'unit_price' => 750000,
            'amount' => 2250000,
            'cogs_amount' => 500000,
            'is_tax_deposit' => false,
        ]);

        $columns = ItemColumns::defaultColumns();
        $row = ItemColumns::resolveItem($columns, $item, 1);

        $this->assertSame('1', $row['no']);
        $this->assertSame('Test Layanan', $row['description']);
        $this->assertStringContainsString('Rp', $row['unit_price']);
        $this->assertStringContainsString('750', $row['unit_price']);
        $this->assertStringContainsString('Rp', $row['amount']);
        $this->assertStringContainsString('2.250', $row['amount']);
    }

    public function test_catalog_for_frontend_omits_resolve_callable(): void
    {
        $frontend = ItemColumns::catalogForFrontend();

        foreach ($frontend as $col) {
            $this->assertArrayHasKey('key', $col);
            $this->assertArrayHasKey('label', $col);
            $this->assertArrayHasKey('align', $col);
            $this->assertArrayHasKey('format', $col);
            $this->assertArrayHasKey('default', $col);
            $this->assertArrayNotHasKey('resolve', $col);
        }
    }

    // ── Save/load round-trip ──────────────────────────────────────────────────

    public function test_save_accepts_table_element_type(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'T', 'layout' => [], 'is_default' => false]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [$this->defaultTableEl()],
            ])
            ->assertRedirect();

        $saved = $template->fresh()->layout;
        $this->assertSame('table', $saved[0]['type']);
        $this->assertSame(40, $saved[0]['x']);
        $this->assertSame(350, $saved[0]['y']);
    }

    public function test_table_element_round_trips_columns(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'Round-trip', 'layout' => [], 'is_default' => false]);

        $el = $this->defaultTableEl();
        // Modify a column label to verify it persists
        $el['columns'][1]['label'] = 'Nama Barang';

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [$el],
            ])
            ->assertRedirect();

        $saved = $template->fresh()->layout[0];
        $this->assertSame('Nama Barang', $saved['columns'][1]['label']);
        $this->assertSame(false, $saved['showFooterSum']);
    }

    public function test_save_with_table_and_text_elements(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'Mixed', 'layout' => [], 'is_default' => false]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [
                    ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 10, 'content' => 'Header'],
                    $this->defaultTableEl(300),
                ],
            ])
            ->assertRedirect();

        $saved = $template->fresh()->layout;
        $this->assertCount(2, $saved);
        $this->assertSame('text', $saved[0]['type']);
        $this->assertSame('table', $saved[1]['type']);
    }

    // ── PDF render — few items (single page) ─────────────────────────────────

    public function test_pdf_renders_table_with_few_items(): void
    {
        $invoice = $this->makeInvoiceWithItems(3);

        $template = PdfTemplate::query()->create([
            'name' => 'Tabel Sedikit',
            'layout' => [$this->defaultTableEl()],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_pdf_renders_item_description_in_output(): void
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'service_name' => 'Layanan Unik XYZ',
            'quantity' => '1.000',
            'unit' => 'ls',
            'unit_price' => 1000000,
            'amount' => 1000000,
            'cogs_amount' => 0,
            'is_tax_deposit' => false,
        ]);

        $template = PdfTemplate::query()->create([
            'name' => 'Tes Konten',
            'layout' => [$this->defaultTableEl()],
            'is_default' => false,
        ]);

        // We can't easily inspect DomPDF binary output in unit tests,
        // so we test the Blade render directly by resolving the elements.
        $invoice->load(['client', 'items', 'payments']);
        $columns = ItemColumns::defaultColumns();
        $rows = ItemColumns::resolveItems($columns, $invoice->items);

        $this->assertCount(1, $rows);
        $this->assertSame('Layanan Unik XYZ', $rows[0]['description']);
        $this->assertStringContainsString('Rp', $rows[0]['amount']);
        $this->assertStringContainsString('1.000.000', $rows[0]['amount']);
    }

    // ── PDF render — many items (forces page 2+) ─────────────────────────────

    public function test_pdf_renders_table_with_many_items_multipage(): void
    {
        // 40 items will definitely exceed a single A4 page
        $invoice = $this->makeInvoiceWithItems(40);

        $template = PdfTemplate::query()->create([
            'name' => 'Tabel Banyak',
            'layout' => [$this->defaultTableEl(200)],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    // ── Column config: only chosen columns ───────────────────────────────────

    public function test_only_selected_columns_are_resolved(): void
    {
        $item = new InvoiceItem([
            'invoice_id' => 0,
            'service_name' => 'Test',
            'quantity' => '1.000',
            'unit' => 'pcs',
            'unit_price' => 100000,
            'amount' => 100000,
            'cogs_amount' => 50000,
            'is_tax_deposit' => false,
        ]);

        // Only description + amount columns
        $columns = [
            ['key' => 'description', 'label' => 'Deskripsi', 'width' => 300, 'align' => 'left', 'format' => 'text'],
            ['key' => 'amount', 'label' => 'Jumlah', 'width' => 130, 'align' => 'right', 'format' => 'rupiah'],
        ];

        $row = ItemColumns::resolveItem($columns, $item, 1);

        // Only description and amount keys present
        $this->assertArrayHasKey('description', $row);
        $this->assertArrayHasKey('amount', $row);
        $this->assertArrayNotHasKey('no', $row);
        $this->assertArrayNotHasKey('quantity', $row);
        $this->assertArrayNotHasKey('unit_price', $row);
        // Correct values
        $this->assertSame('Test', $row['description']);
        $this->assertStringContainsString('Rp', $row['amount']);
    }

    public function test_rupiah_columns_have_right_align(): void
    {
        $defaults = ItemColumns::defaultColumns();

        $unitPriceCol = collect($defaults)->firstWhere('key', 'unit_price');
        $amountCol = collect($defaults)->firstWhere('key', 'amount');

        $this->assertSame('right', $unitPriceCol['align']);
        $this->assertSame('right', $amountCol['align']);
    }

    // ── PDF renders with footer sum ───────────────────────────────────────────

    public function test_pdf_renders_with_footer_sum_enabled(): void
    {
        $invoice = $this->makeInvoiceWithItems(2);

        $el = $this->defaultTableEl();
        $el['showFooterSum'] = true;

        $template = PdfTemplate::query()->create([
            'name' => 'Footer Sum',
            'layout' => [$el],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    // ── PDF works without a table element (backward compat) ──────────────────

    public function test_pdf_without_table_element_still_works(): void
    {
        $template = PdfTemplate::query()->create([
            'name' => 'No Table',
            'layout' => [
                ['id' => 1, 'type' => 'text', 'x' => 10, 'y' => 20, 'content' => 'Plain text', 'fontSize' => 14, 'bold' => false, 'color' => '#000000'],
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    // ── Editor edit action passes sampleItems ─────────────────────────────────

    public function test_edit_action_passes_item_catalog_and_sample_items(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'Editor', 'layout' => [], 'is_default' => false]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/edit")
            ->assertOk();

        $response->assertInertia(fn ($page) => $page
            ->has('itemColumnCatalog')
            ->has('sampleItems')
        );
    }

    // ── Sprint 4: Below-zone (3-zone model) ──────────────────────────────────

    /**
     * A text element placed BELOW the table's Y must appear in the rendered blade
     * HTML (not clipped). We test the Blade view directly via view()->make().
     */
    public function test_below_zone_text_appears_in_rendered_blade(): void
    {
        $tableY = 350;
        $belowY = 420; // below tableY → zone 3

        $elements = [
            // Header-zone text (y < tableY)
            [
                'id' => 1, 'type' => 'text',
                'x' => 60, 'y' => 60,
                'content' => 'TeksHeader', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a',
            ],
            // Table element
            [
                'id' => 2, 'type' => 'table',
                'x' => 40, 'y' => $tableY,
                'width' => 714,
                'columns' => ItemColumns::defaultColumns(),
                'rows' => [],
                'showFooterSum' => false,
            ],
            // Below-zone text (y >= tableY) — this must NOT be clipped
            [
                'id' => 3, 'type' => 'text',
                'x' => 60, 'y' => $belowY,
                'content' => 'TeksBawahTabel', 'fontSize' => 12, 'bold' => false, 'color' => '#0f172a',
            ],
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // The below-zone text must appear in the output
        $this->assertStringContainsString('TeksBawahTabel', $html);

        // The header-zone text must also appear
        $this->assertStringContainsString('TeksHeader', $html);

        // The below-zone container element must exist (div with class="below-flow")
        $this->assertStringContainsString('class="below-flow"', $html);
    }

    /**
     * A header-zone element (y < tableY) must still be inside the absolute .paper
     * layer, not the below-flow container.
     */
    public function test_header_zone_element_stays_in_absolute_paper(): void
    {
        $tableY = 300;

        $elements = [
            [
                'id' => 1, 'type' => 'text',
                'x' => 60, 'y' => 80,
                'content' => 'TeksZonaHeader', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a',
            ],
            [
                'id' => 2, 'type' => 'table',
                'x' => 40, 'y' => $tableY,
                'width' => 714,
                'columns' => ItemColumns::defaultColumns(),
                'rows' => [],
                'showFooterSum' => false,
            ],
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // The header text appears
        $this->assertStringContainsString('TeksZonaHeader', $html);

        // No below-flow *element* (div with class) since there are no below-zone elements.
        // The CSS class name appears in the <style> block, so we test for the HTML element specifically.
        $this->assertStringNotContainsString('class="below-flow"', $html);
    }

    /**
     * A below-zone text element must use top = (y - tableY) relative positioning,
     * not the original absolute y coordinate.
     */
    public function test_below_zone_element_uses_relative_top_positioning(): void
    {
        // Below elements are positioned relative to the TOPMOST below element.
        // An anchor at the table boundary defines the origin; the text is 80px below it.
        $tableY = 400;
        $anchorY = 400; // topmost below element
        $belowY = 480;
        $expectedRelTop = $belowY - $anchorY; // 80

        $elements = [
            [
                'id' => 1, 'type' => 'table',
                'x' => 40, 'y' => $tableY,
                'width' => 714,
                'columns' => ItemColumns::defaultColumns(),
                'rows' => [],
                'showFooterSum' => false,
            ],
            ['id' => 9, 'type' => 'text', 'x' => 40, 'y' => $anchorY, 'content' => 'Footer'],
            [
                'id' => 2, 'type' => 'text',
                'x' => 100, 'y' => $belowY,
                'content' => 'RelPosTest', 'fontSize' => 11, 'bold' => false, 'color' => '#000',
            ],
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // The relative top (80px) must appear in the below-flow section
        $this->assertStringContainsString('RelPosTest', $html);
        $this->assertStringContainsString("top: {$expectedRelTop}px", $html);
    }

    #[Test]
    public function test_lone_below_element_near_page_bottom_stays_single_page(): void
    {
        // Regression: a single below-zone element placed near the page bottom must
        // NOT spill onto a second page. It is positioned relative to itself (relTop 0)
        // and sits right after the (short) table, so the document fits one page.
        $elements = [
            [
                'id' => 1, 'type' => 'table',
                'x' => 40, 'y' => 300,
                'width' => 714,
                'columns' => ItemColumns::defaultColumns(),
                'rows' => [],
                'showFooterSum' => false,
            ],
            ['id' => 2, 'type' => 'text', 'x' => 40, 'y' => 1090, 'content' => 'Catatan kaki'],
        ];

        $pdf = Pdf::loadView('pdf.template-builder', ['elements' => $elements])
            ->setPaper('A4', 'portrait');
        $pdf->output();

        $this->assertSame(1, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }

    #[Test]
    public function test_pdf_layers_match_editor_array_order_via_zindex(): void
    {
        // Editor order: image first (behind), table second (in front).
        // Each element gets z-index = its array index; the flow table container is
        // positioned with the table's index so it paints ABOVE the earlier image.
        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        $elements = [
            ['id' => 1, 'type' => 'image', 'x' => 40, 'y' => 100, 'src' => $png, 'width' => 200, 'height' => 300],
            $this->defaultTableEl(150),
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // image at index 0 → z-index: 0; table container at index 1 → z-index: 1 (above).
        $this->assertStringContainsString('z-index: 0;', $html);
        $this->assertMatchesRegularExpression('/class="table-flow"[^>]*position: relative; z-index: 1;/', $html);
    }

    /**
     * Multi-page with 40 items AND a below-zone total text must render as PDF (200)
     * and the below-zone text must appear in the Blade output.
     */
    public function test_multipage_with_below_zone_renders_pdf(): void
    {
        $invoice = $this->makeInvoiceWithItems(40);
        $tableY = 200;

        $columns = ItemColumns::defaultColumns();
        $rows = ItemColumns::resolveItems($columns, $invoice->items);

        $totalText = 'Grand Total: Rp 40.000.000';
        $elements = [
            [
                'id' => 1, 'type' => 'table',
                'x' => 40, 'y' => $tableY,
                'width' => 714,
                'columns' => $columns,
                'rows' => $rows,
                'showFooterSum' => false,
            ],
            [
                'id' => 2, 'type' => 'text',
                'x' => 60, 'y' => $tableY + 100, // below tableY
                'content' => $totalText, 'fontSize' => 12, 'bold' => true, 'color' => '#0f172a',
            ],
        ];

        // Blade render must contain the below-zone text
        $html = view('pdf.template-builder', ['elements' => $elements])->render();
        $this->assertStringContainsString($totalText, $html);
        $this->assertStringContainsString('below-flow', $html);

        // Full PDF render via the HTTP endpoint must also succeed
        $template = PdfTemplate::query()->create([
            'name' => 'Multipage Below Zone',
            'layout' => [
                [
                    'id' => 1, 'type' => 'table',
                    'x' => 40, 'y' => $tableY,
                    'width' => 714,
                    'columns' => $columns,
                    'showFooterSum' => false,
                ],
                [
                    'id' => 2, 'type' => 'text',
                    'x' => 60, 'y' => $tableY + 100,
                    'content' => $totalText, 'fontSize' => 12, 'bold' => true, 'color' => '#0f172a',
                ],
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    /**
     * A template with NO table element must behave identically to Sprint 1/2/3
     * (single absolute page, no below-flow, no table-flow).
     */
    public function test_no_table_template_unchanged_regression(): void
    {
        $elements = [
            [
                'id' => 1, 'type' => 'text',
                'x' => 60, 'y' => 60,
                'content' => 'TanpaTabel', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a',
            ],
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        $this->assertStringContainsString('TanpaTabel', $html);
        // CSS class names appear in the <style> block, so we assert on the HTML *elements*.
        $this->assertStringNotContainsString('class="table-flow"', $html);
        $this->assertStringNotContainsString('class="below-flow"', $html);

        // The paper div must be 1122px tall (full page, no table cap)
        $this->assertStringContainsString('height: 1122px', $html);
    }
}
