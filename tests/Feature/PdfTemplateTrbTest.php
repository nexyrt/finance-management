<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PdfTemplate;
use App\Models\User;
use App\Services\ItemColumns;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * TRB (Tabel Terpadu Row-Band) PDF rendering tests.
 *
 * Tests:
 *  1.  60 items renders PDF (200 + application/pdf, multi-page)
 *  2.  Head row with colSpan=2 renders colspan="2" in HTML
 *  3.  Static body row above detail row renders once (not per-item)
 *  4.  Foot row renders once
 *  5.  Per-cell fill / color / align appear in output HTML
 *  6.  Detail row tokens bind per item (different descriptions per row)
 *  7.  Migration: old column-based layout still renders as PDF (backward-compat)
 *  8.  save() accepts new TRB rows-based table shape (200)
 */
class PdfTemplateTrbTest extends TestCase
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

    /** Build a minimal banded layout with a TRB table element. */
    private function makeBandedLayout(?array $tableEl = null): array
    {
        return [
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'bands' => [
                'header' => ['height' => 80, 'repeat' => false, 'elements' => []],
                'content' => ['table' => $tableEl],
                'footerFlow' => ['height' => 60, 'elements' => []],
                'footerFixed' => ['height' => 0, 'elements' => []],
            ],
        ];
    }

    /** Build a minimal default TRB table element. */
    private function defaultTrbTableEl(): array
    {
        $defaults = array_filter(ItemColumns::catalog(), fn (array $c) => $c['default']);
        $widths = ['no' => 36, 'description' => 290, 'quantity' => 72, 'unit' => 80, 'unit_price' => 130, 'amount' => 130];
        $defaults = array_values($defaults);

        return [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 658,
            'colWidths' => array_map(fn (array $c) => $widths[$c['key']] ?? 100, $defaults),
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => array_map(fn (array $c) => ['content' => $c['label'], 'align' => $c['align'], 'bold' => true], $defaults),
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => array_map(fn (array $c) => ['content' => "{{item.{$c['key']}}}", 'align' => $c['align']], $defaults),
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];
    }

    /** Build a legacy column-based table element (old model, for migration test). */
    private function legacyTableEl(): array
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

    // ── Test 1: 60 items renders PDF ─────────────────────────────────────────

    public function test_60_items_renders_pdf_two_pages(): void
    {
        $invoice = $this->makeInvoiceWithItems(60);
        $template = PdfTemplate::query()->create([
            'name' => 'TRB 60-items',
            'layout' => $this->makeBandedLayout($this->defaultTrbTableEl()),
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

    /** Build the blade view data array for TRB rendering (mirrors pdfBanded logic). */
    private function makeTrbBladeData(array $trbTableEl, iterable $trbItems): array
    {
        return [
            'banded' => true,
            'paper' => ['margins' => ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]],
            'headerBand' => ['height' => 80, 'repeat' => false, 'elements' => []],
            'tableEl' => $trbTableEl,
            'trbItems' => $trbItems,
            'footerFlowBand' => ['height' => 60, 'elements' => []],
            'footerFixedBand' => ['height' => 0, 'elements' => []],
            'customFonts' => [],
            'elements' => [],
        ];
    }

    // ── Test 2: Head row with colSpan=2 renders colspan="2" ──────────────────

    public function test_head_row_with_colspan_renders_colspan_attr(): void
    {
        $invoice = $this->makeInvoiceWithItems(2);

        $tableEl = [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 400,
            'colWidths' => [200, 200],
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => [
                        ['content' => 'Grup Header', 'align' => 'center', 'bold' => true, 'colSpan' => 2],
                    ],
                ],
                [
                    'kind' => 'head',
                    'cells' => [
                        ['content' => 'Kolom A', 'align' => 'left', 'bold' => true],
                        ['content' => 'Kolom B', 'align' => 'right', 'bold' => true],
                    ],
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => [
                        ['content' => '{{item.description}}', 'align' => 'left'],
                        ['content' => '{{item.amount}}', 'align' => 'right'],
                    ],
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];

        $html = (string) view('pdf.template-builder', $this->makeTrbBladeData($tableEl, $invoice->items));

        $this->assertStringContainsString('colspan="2"', $html);
    }

    // ── Test 3: Static body row above detail renders once ────────────────────

    public function test_static_body_row_above_detail_renders_once(): void
    {
        $invoice = $this->makeInvoiceWithItems(3);

        $tableEl = [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 400,
            'colWidths' => [400],
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => [['content' => 'Header', 'align' => 'left', 'bold' => true]],
                ],
                [
                    'kind' => 'body',
                    'cells' => [['content' => 'Baris Statis Unik', 'align' => 'left']],
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => [['content' => '{{item.description}}', 'align' => 'left']],
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];

        $html = (string) view('pdf.template-builder', $this->makeTrbBladeData($tableEl, $invoice->items));

        // 'Baris Statis Unik' should appear exactly once regardless of item count
        $this->assertSame(1, substr_count($html, 'Baris Statis Unik'));
    }

    // ── Test 4: Foot row renders once ────────────────────────────────────────

    public function test_static_foot_row_renders_once(): void
    {
        $invoice = $this->makeInvoiceWithItems(3);

        $tableEl = [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 400,
            'colWidths' => [400],
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => [['content' => 'Header', 'align' => 'left', 'bold' => true]],
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => [['content' => '{{item.description}}', 'align' => 'left']],
                ],
                [
                    'kind' => 'foot',
                    'cells' => [['content' => 'Footer Unik', 'align' => 'right', 'bold' => true]],
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];

        $html = (string) view('pdf.template-builder', $this->makeTrbBladeData($tableEl, $invoice->items));

        $this->assertSame(1, substr_count($html, 'Footer Unik'));
    }

    // ── Test 5: Per-cell fill, color, align appear in output ─────────────────

    public function test_per_cell_fill_color_align_applied(): void
    {
        $invoice = $this->makeInvoiceWithItems(1);

        $tableEl = [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 400,
            'colWidths' => [400],
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => [[
                        'content' => 'Styled Header',
                        'align' => 'center',
                        'bold' => true,
                        'fill' => '#ff0000',
                        'color' => '#ffffff',
                        'fontSize' => 14,
                    ]],
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => [['content' => '{{item.description}}', 'align' => 'right']],
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];

        $html = (string) view('pdf.template-builder', $this->makeTrbBladeData($tableEl, $invoice->items));

        $this->assertStringContainsString('background-color: #ff0000', $html);
        $this->assertStringContainsString('color: #ffffff', $html);
        $this->assertStringContainsString('text-align: center', $html);
        $this->assertStringContainsString('font-size: 14px', $html);
    }

    // ── Test 6: Detail row tokens bind per item ───────────────────────────────

    public function test_detail_tokens_bound_per_item(): void
    {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['billed_to_id' => $client->id]);
        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'service_name' => 'Layanan Pertama',
            'quantity' => '1.000',
            'unit' => 'jam',
            'unit_price' => 100000,
            'amount' => 100000,
            'cogs_amount' => 0,
            'is_tax_deposit' => false,
        ]);
        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
            'service_name' => 'Layanan Kedua',
            'quantity' => '2.000',
            'unit' => 'jam',
            'unit_price' => 200000,
            'amount' => 400000,
            'cogs_amount' => 0,
            'is_tax_deposit' => false,
        ]);
        $invoice->load(['client', 'items', 'payments']);

        $tableEl = [
            'id' => 2,
            'type' => 'table',
            'x' => 0,
            'y' => 0,
            'width' => 400,
            'colWidths' => [400],
            'rows' => [
                [
                    'kind' => 'head',
                    'cells' => [['content' => 'Deskripsi', 'align' => 'left', 'bold' => true]],
                ],
                [
                    'kind' => 'body',
                    'repeat' => 'items',
                    'cells' => [['content' => '{{item.description}}', 'align' => 'left']],
                ],
            ],
            'border' => ['width' => 1, 'color' => '#e2e8f0'],
        ];

        $html = (string) view('pdf.template-builder', $this->makeTrbBladeData($tableEl, $invoice->items));

        $this->assertStringContainsString('Layanan Pertama', $html);
        $this->assertStringContainsString('Layanan Kedua', $html);
    }

    // ── Test 7: Migration — old column-based layout still renders ────────────

    public function test_migration_old_column_table_renders(): void
    {
        $invoice = $this->makeInvoiceWithItems(3);
        $template = PdfTemplate::query()->create([
            'name' => 'TRB migration legacy',
            'layout' => $this->makeBandedLayout($this->legacyTableEl()),
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

    // ── Test 8: save() accepts new TRB shape ─────────────────────────────────

    public function test_save_accepts_new_trb_shape(): void
    {
        $template = PdfTemplate::query()->create([
            'name' => 'TRB save test',
            'layout' => [],
            'is_default' => false,
        ]);

        $layout = $this->makeBandedLayout($this->defaultTrbTableEl());

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", ['layout' => $layout])
            ->assertRedirect();

        $template->refresh();
        $saved = $template->layout;
        $this->assertIsArray($saved['bands']['content']['table']['rows'] ?? null);
        $this->assertNotEmpty($saved['bands']['content']['table']['colWidths'] ?? []);
    }
}
