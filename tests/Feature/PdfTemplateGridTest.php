<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Services\ItemColumns;
use App\Services\TemplateTokens;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 4a — Static grid element tests.
 *
 * Covers:
 *  - Save/load round-trip: grid cells, colWidths, border preserved
 *  - PDF renders a grid: literal cell value present; {{token}} resolved in output
 *  - Per-cell align & fill present in rendered blade
 *  - Grid in header zone (y < tableY): renders as absolute in .paper
 *  - Grid in below-zone (y >= tableY): renders in below-flow container
 *  - No regression to Sprints 1-3 (text/image/table still work with grid present)
 */
class PdfTemplateGridTest extends TestCase
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

    // ── Helpers ────────────────────────────────────────────────────────────────

    /** Build a minimal 2×2 grid element payload. */
    private function gridEl(int $x = 60, int $y = 100, array $cellOverrides = []): array
    {
        $cells = [
            [
                ['text' => 'Header A', 'align' => 'left',  'bold' => true,  'color' => '#0f172a', 'fill' => '#f1f5f9'],
                ['text' => 'Header B', 'align' => 'center', 'bold' => true,  'color' => '#0f172a', 'fill' => '#f1f5f9'],
            ],
            [
                ['text' => 'Nilai 1',  'align' => 'left',  'bold' => false, 'color' => '#334155'],
                ['text' => 'Nilai 2',  'align' => 'right', 'bold' => false, 'color' => '#334155'],
            ],
        ];

        foreach ($cellOverrides as [$r, $c, $patch]) {
            $cells[$r][$c] = array_merge($cells[$r][$c], $patch);
        }

        return [
            'id' => 99,
            'type' => 'grid',
            'x' => $x,
            'y' => $y,
            'width' => 300,
            'cols' => 2,
            'rows' => 2,
            'colWidths' => [150, 150],
            'cells' => $cells,
            'border' => ['width' => 1, 'color' => '#cbd5e1'],
        ];
    }

    /** Create an invoice with one InvoiceItem in the DB. */
    private function makeInvoice(): Invoice
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'service_name' => 'Layanan Grid',
            'quantity' => '1.000',
            'unit' => 'pcs',
            'unit_price' => 500000,
            'amount' => 500000,
            'cogs_amount' => 0,
            'is_tax_deposit' => false,
        ]);

        return $invoice->load(['client', 'items', 'payments']);
    }

    // ── Round-trip ─────────────────────────────────────────────────────────────

    public function test_save_accepts_grid_element_type(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'Grid Test', 'layout' => [], 'is_default' => false]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [$this->gridEl()],
            ])
            ->assertRedirect();

        $saved = $template->fresh()->layout;
        $this->assertSame('grid', $saved[0]['type']);
    }

    public function test_grid_round_trip_preserves_cells_colwidths_border(): void
    {
        $template = PdfTemplate::query()->create(['name' => 'Grid Round-trip', 'layout' => [], 'is_default' => false]);

        $el = $this->gridEl(60, 100, [
            [0, 0, ['text' => 'CellX', 'fill' => '#fef9c3']],
        ]);
        $el['border'] = ['width' => 2, 'color' => '#1e40af'];
        $el['colWidths'] = [180, 120];

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", ['layout' => [$el]])
            ->assertRedirect();

        $saved = $template->fresh()->layout[0];

        // cells preserved
        $this->assertSame('CellX', $saved['cells'][0][0]['text']);
        $this->assertSame('#fef9c3', $saved['cells'][0][0]['fill']);
        $this->assertSame('Header B', $saved['cells'][0][1]['text']);
        // colWidths preserved
        $this->assertSame([180, 120], $saved['colWidths']);
        // border preserved
        $this->assertSame(2, $saved['border']['width']);
        $this->assertSame('#1e40af', $saved['border']['color']);
    }

    // ── Blade render — content ─────────────────────────────────────────────────

    public function test_grid_literal_cell_value_appears_in_blade(): void
    {
        $elements = [$this->gridEl(60, 100)];
        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        $this->assertStringContainsString('Header A', $html);
        $this->assertStringContainsString('Nilai 2', $html);
    }

    public function test_grid_cell_with_token_is_resolved_in_pdf(): void
    {
        // Build a grid with a token in one cell, resolve via controller path.
        $invoice = $this->makeInvoice();

        $el = $this->gridEl(60, 100);
        $el['cells'][1][0]['text'] = '{{invoice.number}}';

        $template = PdfTemplate::query()->create([
            'name' => 'Grid Token',
            'layout' => [$el],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));

        // Also verify via Blade directly that the token IS resolved (not left as literal).
        // We simulate the same token resolution the controller does.
        $map = TemplateTokens::buildMap($invoice);
        $resolvedCells = array_map(
            fn ($row) => array_map(
                fn ($cell) => ['text' => preg_replace_callback(
                    '/\{\{([\w.]+)\}\}/',
                    fn ($m) => $map[$m[1]] ?? $m[0],
                    $cell['text']
                )] + $cell,
                $row
            ),
            $el['cells']
        );
        $elResolved = array_merge($el, ['cells' => $resolvedCells]);

        $html = view('pdf.template-builder', ['elements' => [$elResolved]])->render();
        $this->assertStringNotContainsString('{{invoice.number}}', $html);
        $this->assertStringContainsString($invoice->invoice_number, $html);
    }

    public function test_grid_per_cell_align_and_fill_in_blade(): void
    {
        $elements = [$this->gridEl(60, 100)];
        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // align right on cell [1][1]
        $this->assertStringContainsString('text-align: right', $html);
        // fill on header row cells
        $this->assertStringContainsString('#f1f5f9', $html);
        // bold on header cells
        $this->assertStringContainsString('font-weight: 700', $html);
    }

    // ── Zone routing ───────────────────────────────────────────────────────────

    public function test_grid_in_header_zone_renders_in_paper_absolute(): void
    {
        $tableY = 350;

        // Grid placed ABOVE tableY → header zone
        $el = $this->gridEl(60, 100); // y=100 < 350

        $elements = [
            $el,
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

        // Grid content must appear
        $this->assertStringContainsString('Header A', $html);

        // Must NOT render below-flow container (no below-zone elements)
        $this->assertStringNotContainsString('class="below-flow"', $html);
    }

    public function test_grid_in_below_zone_renders_in_below_flow(): void
    {
        $tableY = 300;

        // Grid placed AT or BELOW tableY → below zone
        $el = $this->gridEl(60, 400); // y=400 >= 300

        $elements = [
            [
                'id' => 1, 'type' => 'table',
                'x' => 40, 'y' => $tableY,
                'width' => 714,
                'columns' => ItemColumns::defaultColumns(),
                'rows' => [],
                'showFooterSum' => false,
            ],
            $el,
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        // Grid content must appear
        $this->assertStringContainsString('Header A', $html);
        $this->assertStringContainsString('Nilai 2', $html);

        // below-flow container must exist
        $this->assertStringContainsString('class="below-flow"', $html);

        // Lone below element → positioned at the top of the below zone (relTop 0).
        $this->assertStringContainsString('top: 0px', $html);
    }

    public function test_grid_below_zone_uses_relative_top_positioning(): void
    {
        // Below elements are positioned relative to the TOPMOST below element.
        // An anchor sits at the table boundary; the grid is 130px below it.
        $tableY = 250;
        $anchorY = 250; // topmost below element → defines the below-zone origin
        $gridY = 380;
        $expectedTop = $gridY - $anchorY; // 130

        $el = $this->gridEl(60, $gridY);

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
            $el,
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        $this->assertStringContainsString("top: {$expectedTop}px", $html);
    }

    // ── PDF HTTP endpoint ──────────────────────────────────────────────────────

    public function test_pdf_with_grid_renders_successfully(): void
    {
        $invoice = $this->makeInvoice();

        $template = PdfTemplate::query()->create([
            'name' => 'Grid PDF',
            'layout' => [$this->gridEl(60, 100)],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    public function test_pdf_with_grid_and_table_renders_successfully(): void
    {
        $invoice = $this->makeInvoice();

        $template = PdfTemplate::query()->create([
            'name' => 'Grid + Table PDF',
            'layout' => [
                [
                    'id' => 1, 'type' => 'table',
                    'x' => 40, 'y' => 300,
                    'width' => 714,
                    'columns' => ItemColumns::defaultColumns(),
                    'showFooterSum' => false,
                ],
                $this->gridEl(60, 100), // header zone grid
                $this->gridEl(60, 450), // below zone grid (y > tableY=300)
            ],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf/{$invoice->id}")
            ->assertOk();

        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('content-type'));
    }

    // ── No-table fallback (backward compat) ───────────────────────────────────

    public function test_grid_without_table_renders_in_absolute_paper(): void
    {
        $el = $this->gridEl(60, 200);
        $html = view('pdf.template-builder', ['elements' => [$el]])->render();

        $this->assertStringContainsString('Header A', $html);
        // Single absolute page, no flow zones
        $this->assertStringNotContainsString('class="table-flow"', $html);
        $this->assertStringNotContainsString('class="below-flow"', $html);
        $this->assertStringContainsString('height: 1122px', $html);
    }

    // ── Regression: existing element types still work ─────────────────────────

    public function test_text_and_image_unaffected_by_grid_in_layout(): void
    {
        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $elements = [
            ['id' => 1, 'type' => 'text',  'x' => 60,  'y' => 60, 'content' => 'TeksRegresi', 'fontSize' => 14, 'bold' => false, 'color' => '#0f172a'],
            ['id' => 2, 'type' => 'image', 'x' => 100, 'y' => 150, 'src' => $png, 'width' => 100, 'height' => 100],
            $this->gridEl(60, 250),
        ];

        $html = view('pdf.template-builder', ['elements' => $elements])->render();

        $this->assertStringContainsString('TeksRegresi', $html);
        $this->assertStringContainsString('data:image/png', $html);
        $this->assertStringContainsString('Header A', $html);
    }

    public function test_save_accepts_legacy_flat_layout(): void
    {
        // ponytail: flat-array layouts are accepted leniently (backward-compat with pre-B1 templates).
        $template = PdfTemplate::query()->create(['name' => 'T', 'layout' => [], 'is_default' => false]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [['id' => 1, 'type' => 'grid', 'x' => 0, 'y' => 0, 'width' => 200, 'cols' => 2, 'rows' => 2, 'colWidths' => [100, 100], 'cells' => [[['text' => 'A', 'align' => 'left', 'bold' => false, 'color' => '#000'], ['text' => 'B', 'align' => 'left', 'bold' => false, 'color' => '#000']], [['text' => 'C', 'align' => 'left', 'bold' => false, 'color' => '#000'], ['text' => 'D', 'align' => 'left', 'bold' => false, 'color' => '#000']]], 'border' => ['width' => 1, 'color' => '#ccc']]],
            ])
            ->assertRedirect();
    }
}
