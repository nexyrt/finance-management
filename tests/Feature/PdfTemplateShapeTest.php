<?php

namespace Tests\Feature;

use App\Models\PdfTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 5c — Shape elements: rect + line.
 *
 * Covers:
 *  - rect: save/load round-trip (fill, border, radius)
 *  - line: save/load round-trip (orientation, length, thickness, color)
 *  - rect: PDF blade renders border, fill, radius in header zone
 *  - line: PDF blade renders horizontal dimensions correctly in header zone
 *  - line: PDF blade renders vertical dimensions correctly in header zone
 *  - rect in below-zone (y >= tableY): renders in .below-flow container, not absolute header
 *  - line in below-zone: renders in .below-flow container, correct style
 *  - rect in header-zone (y < tableY): renders in absolute .paper layer
 *  - line in header-zone: renders in absolute .paper layer
 *  - save() endpoint accepts rect and line types (no validation rejection)
 *  - below-zone container height accounts for rect height
 *  - below-zone container height accounts for vertical line length
 *  - No regression: existing element types still render (smoke test)
 */
class PdfTemplateShapeTest extends TestCase
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

    /** Create a PdfTemplate with the given layout array. */
    private function makeTemplate(array $layout): PdfTemplate
    {
        return PdfTemplate::query()->create([
            'name' => 'Test 5c Shapes',
            'layout' => $layout,
            'is_default' => false,
        ]);
    }

    /**
     * Render the blade template directly (bypasses DomPDF) to assert
     * the CSS/HTML that would be fed into DomPDF.
     */
    private function renderBlade(array $layout): string
    {
        return view('pdf.template-builder', [
            'elements' => $layout,
        ])->render();
    }

    // ── Save / load round-trip ─────────────────────────────────────────────────

    #[Test]
    public function rect_save_and_load_roundtrip(): void
    {
        $rectEl = [
            'id' => 1,
            'type' => 'rect',
            'x' => 50,
            'y' => 100,
            'width' => 200,
            'height' => 60,
            'fill' => '#f0f4ff',
            'borderWidth' => 2,
            'borderColor' => '#3b82f6',
            'borderRadius' => 8,
        ];

        $template = $this->makeTemplate([$rectEl]);

        // Reload from DB
        $template->refresh();
        $saved = $template->layout[0];

        $this->assertSame('rect', $saved['type']);
        $this->assertSame(200, $saved['width']);
        $this->assertSame(60, $saved['height']);
        $this->assertSame('#f0f4ff', $saved['fill']);
        $this->assertSame(2, $saved['borderWidth']);
        $this->assertSame('#3b82f6', $saved['borderColor']);
        $this->assertSame(8, $saved['borderRadius']);
    }

    #[Test]
    public function line_save_and_load_roundtrip(): void
    {
        $lineEl = [
            'id' => 2,
            'type' => 'line',
            'x' => 40,
            'y' => 200,
            'length' => 500,
            'thickness' => 2,
            'color' => '#ef4444',
            'orientation' => 'h',
        ];

        $template = $this->makeTemplate([$lineEl]);
        $template->refresh();
        $saved = $template->layout[0];

        $this->assertSame('line', $saved['type']);
        $this->assertSame(500, $saved['length']);
        $this->assertSame(2, $saved['thickness']);
        $this->assertSame('#ef4444', $saved['color']);
        $this->assertSame('h', $saved['orientation']);
    }

    #[Test]
    public function vertical_line_save_and_load_roundtrip(): void
    {
        $lineEl = [
            'id' => 3,
            'type' => 'line',
            'x' => 40,
            'y' => 100,
            'length' => 120,
            'thickness' => 3,
            'color' => '#0f172a',
            'orientation' => 'v',
        ];

        $template = $this->makeTemplate([$lineEl]);
        $template->refresh();
        $saved = $template->layout[0];

        $this->assertSame('v', $saved['orientation']);
        $this->assertSame(120, $saved['length']);
    }

    // ── PDF blade output — header zone ─────────────────────────────────────────

    #[Test]
    public function rect_renders_border_fill_and_radius_in_header_zone(): void
    {
        $el = [
            'id' => 1,
            'type' => 'rect',
            'x' => 50,
            'y' => 100,
            'width' => 200,
            'height' => 60,
            'fill' => '#f0f4ff',
            'borderWidth' => 2,
            'borderColor' => '#3b82f6',
            'borderRadius' => 8,
        ];

        $html = $this->renderBlade([$el]);

        // The rect is a <div> with the expected styles
        $this->assertStringContainsString('width: 200px', $html);
        $this->assertStringContainsString('height: 60px', $html);
        $this->assertStringContainsString('background-color: #f0f4ff', $html);
        $this->assertStringContainsString('border: 2px solid #3b82f6', $html);
        $this->assertStringContainsString('border-radius: 8px', $html);
        // Must be in the .paper zone (absolute)
        $this->assertStringContainsString('left: 50px', $html);
        $this->assertStringContainsString('top: 100px', $html);
    }

    #[Test]
    public function rect_without_fill_renders_no_background_color(): void
    {
        $el = [
            'id' => 1,
            'type' => 'rect',
            'x' => 50,
            'y' => 100,
            'width' => 150,
            'height' => 40,
            'fill' => null,
            'borderWidth' => 1,
            'borderColor' => '#000000',
            'borderRadius' => 0,
        ];

        $html = $this->renderBlade([$el]);

        // No fill → no background-color in the rect div style
        // (the CSS .paper background is white, that's fine — we check the element style)
        $bodyPart = substr($html, (int) strpos($html, '</style>'));
        $this->assertStringNotContainsString('background-color: null', $bodyPart);
    }

    #[Test]
    public function horizontal_line_renders_correct_width_and_height_in_header_zone(): void
    {
        $el = [
            'id' => 2,
            'type' => 'line',
            'x' => 40,
            'y' => 200,
            'length' => 400,
            'thickness' => 2,
            'color' => '#ef4444',
            'orientation' => 'h',
        ];

        $html = $this->renderBlade([$el]);

        // Horizontal line: width = length, height = thickness
        $this->assertStringContainsString('width: 400px', $html);
        $this->assertStringContainsString('height: 2px', $html);
        $this->assertStringContainsString('background-color: #ef4444', $html);
        $this->assertStringContainsString('left: 40px', $html);
        $this->assertStringContainsString('top: 200px', $html);
    }

    #[Test]
    public function vertical_line_renders_correct_width_and_height_in_header_zone(): void
    {
        $el = [
            'id' => 3,
            'type' => 'line',
            'x' => 100,
            'y' => 50,
            'length' => 300,
            'thickness' => 3,
            'color' => '#0f172a',
            'orientation' => 'v',
        ];

        $html = $this->renderBlade([$el]);

        // Vertical line: width = thickness, height = length
        $this->assertStringContainsString('width: 3px', $html);
        $this->assertStringContainsString('height: 300px', $html);
        $this->assertStringContainsString('background-color: #0f172a', $html);
    }

    // ── 3-zone: header vs below ────────────────────────────────────────────────

    #[Test]
    public function rect_in_header_zone_renders_in_paper_absolute_layer(): void
    {
        // Table at y=400, rect at y=100 → header zone
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40, 'y' => 400,
                'width' => 714,
                'columns' => [
                    ['key' => 'description', 'label' => 'Deskripsi', 'width' => 300, 'align' => 'left', 'format' => 'text'],
                ],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'rect',
                'x' => 50, 'y' => 100,
                'width' => 200, 'height' => 40,
                'fill' => '#e0f2fe',
                'borderWidth' => 0, 'borderColor' => '#000000', 'borderRadius' => 0,
            ],
        ];

        $html = $this->renderBlade($layout);

        // below-flow container must be present (table exists) but have 0 height since rect is header
        $this->assertStringContainsString('class="paper"', $html);
        $this->assertStringContainsString('background-color: #e0f2fe', $html);

        // The rect should appear BEFORE the below-flow div (i.e., in the paper layer)
        $paperPos = (int) strpos($html, 'class="paper"');
        $belowPos = (int) strpos($html, 'class="below-flow"');
        $rectStylePos = (int) strpos($html, 'background-color: #e0f2fe');

        if ($belowPos > 0) {
            // Rect style appears before below-flow, confirming header-zone placement
            $this->assertLessThan($belowPos, $rectStylePos);
        }
        $this->assertGreaterThan($paperPos, $rectStylePos);
    }

    #[Test]
    public function rect_in_below_zone_renders_after_table_in_below_flow(): void
    {
        // Table at y=300, rect at y=700 → below-zone
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40, 'y' => 300,
                'width' => 714,
                'columns' => [
                    ['key' => 'description', 'label' => 'Deskripsi', 'width' => 300, 'align' => 'left', 'format' => 'text'],
                ],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'rect',
                'x' => 500, 'y' => 700,
                'width' => 200, 'height' => 50,
                'fill' => '#fef9c3',
                'borderWidth' => 1, 'borderColor' => '#ca8a04', 'borderRadius' => 4,
            ],
        ];

        $html = $this->renderBlade($layout);

        // below-flow container must be present
        $this->assertStringContainsString('class="below-flow"', $html);

        // Rect fill appears in the below-flow section
        $belowPos = (int) strpos($html, 'class="below-flow"');
        $rectFillPos = (int) strpos($html, 'background-color: #fef9c3');
        $this->assertGreaterThan($belowPos, $rectFillPos);

        // relTop = 700 - 300 = 400 → top: 400px in below-flow
        $this->assertStringContainsString('top: 400px', $html);

        // Border and radius
        $this->assertStringContainsString('border: 1px solid #ca8a04', $html);
        $this->assertStringContainsString('border-radius: 4px', $html);
    }

    #[Test]
    public function line_in_below_zone_renders_in_below_flow_container(): void
    {
        // Table at y=300, line at y=800 → below-zone
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40, 'y' => 300,
                'width' => 714,
                'columns' => [
                    ['key' => 'description', 'label' => 'Deskripsi', 'width' => 300, 'align' => 'left', 'format' => 'text'],
                ],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'line',
                'x' => 40, 'y' => 800,
                'length' => 600,
                'thickness' => 1,
                'color' => '#64748b',
                'orientation' => 'h',
            ],
        ];

        $html = $this->renderBlade($layout);

        $this->assertStringContainsString('class="below-flow"', $html);

        // Horizontal line in below-flow: width=600, height=1
        $belowPos = (int) strpos($html, 'class="below-flow"');
        $lineW = (int) strpos($html, 'width: 600px');
        $lineH = (int) strpos($html, 'height: 1px');
        $this->assertGreaterThan($belowPos, $lineW);
        $this->assertGreaterThan($belowPos, $lineH);
        $this->assertStringContainsString('background-color: #64748b', $html);

        // relTop = 800 - 300 = 500
        $this->assertStringContainsString('top: 500px', $html);
    }

    // ── Below-zone container height estimate ───────────────────────────────────

    #[Test]
    public function below_zone_container_height_accounts_for_rect_height(): void
    {
        // Table at y=300, rect at y=700 with height=80 → relTop=400, bottom=480
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40, 'y' => 300,
                'width' => 714,
                'columns' => [['key' => 'description', 'label' => 'Desc', 'width' => 300, 'align' => 'left', 'format' => 'text']],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'rect',
                'x' => 40, 'y' => 700,
                'width' => 200, 'height' => 80,
                'fill' => null, 'borderWidth' => 1, 'borderColor' => '#000', 'borderRadius' => 0,
            ],
        ];

        $html = $this->renderBlade($layout);

        // relTop = 700 - 300 = 400; height = 80 → container height = 480
        $this->assertStringContainsString('height: 480px', $html);
    }

    #[Test]
    public function below_zone_container_height_accounts_for_vertical_line_length(): void
    {
        // Table at y=300, vertical line at y=600 with length=200 → relTop=300, bottom=500
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40, 'y' => 300,
                'width' => 714,
                'columns' => [['key' => 'description', 'label' => 'Desc', 'width' => 300, 'align' => 'left', 'format' => 'text']],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'line',
                'x' => 40, 'y' => 600,
                'length' => 200, 'thickness' => 2,
                'color' => '#000',
                'orientation' => 'v',
            ],
        ];

        $html = $this->renderBlade($layout);

        // relTop = 600 - 300 = 300; vertical line height = length = 200 → container = 500
        $this->assertStringContainsString('height: 500px', $html);
    }

    // ── save() validation accepts rect and line ─────────────────────────────────

    #[Test]
    public function save_endpoint_accepts_rect_element(): void
    {
        $template = $this->makeTemplate([]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [
                    [
                        'id' => 1,
                        'type' => 'rect',
                        'x' => 50,
                        'y' => 100,
                        'width' => 200,
                        'height' => 60,
                        'fill' => '#ffffff',
                        'borderWidth' => 1,
                        'borderColor' => '#000000',
                        'borderRadius' => 0,
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertCount(1, $template->layout);
        $this->assertSame('rect', $template->layout[0]['type']);
    }

    #[Test]
    public function save_endpoint_accepts_line_element(): void
    {
        $template = $this->makeTemplate([]);

        $this->actingAs($this->admin)
            ->post("/settings/pdf-templates/{$template->id}/save", [
                'layout' => [
                    [
                        'id' => 2,
                        'type' => 'line',
                        'x' => 40,
                        'y' => 200,
                        'length' => 500,
                        'thickness' => 1,
                        'color' => '#0f172a',
                        'orientation' => 'h',
                    ],
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $template->refresh();
        $this->assertCount(1, $template->layout);
        $this->assertSame('line', $template->layout[0]['type']);
    }

    // ── PDF endpoint smoke test ────────────────────────────────────────────────

    #[Test]
    public function pdf_endpoint_returns_200_with_rect_element(): void
    {
        $template = $this->makeTemplate([
            [
                'id' => 1,
                'type' => 'rect',
                'x' => 50,
                'y' => 100,
                'width' => 200,
                'height' => 40,
                'fill' => '#e0f2fe',
                'borderWidth' => 1,
                'borderColor' => '#0284c7',
                'borderRadius' => 4,
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function pdf_endpoint_returns_200_with_line_element(): void
    {
        $template = $this->makeTemplate([
            [
                'id' => 1,
                'type' => 'line',
                'x' => 40,
                'y' => 200,
                'length' => 600,
                'thickness' => 2,
                'color' => '#334155',
                'orientation' => 'h',
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    // ── No regression: existing elements still work ────────────────────────────

    #[Test]
    public function mixed_layout_with_shapes_and_text_renders_without_error(): void
    {
        $html = $this->renderBlade([
            [
                'id' => 1,
                'type' => 'text',
                'x' => 60, 'y' => 60,
                'content' => 'Invoice', 'fontSize' => 20, 'bold' => true, 'color' => '#0f172a',
                'width' => 200,
            ],
            [
                'id' => 2,
                'type' => 'rect',
                'x' => 40, 'y' => 90,
                'width' => 714, 'height' => 2,
                'fill' => '#0f172a',
                'borderWidth' => 0, 'borderColor' => '#000', 'borderRadius' => 0,
            ],
            [
                'id' => 3,
                'type' => 'line',
                'x' => 40, 'y' => 120,
                'length' => 714, 'thickness' => 1,
                'color' => '#94a3b8',
                'orientation' => 'h',
            ],
        ]);

        $this->assertStringContainsString('Invoice', $html);
        $this->assertStringContainsString('width: 714px', $html);
        $this->assertStringContainsString('background-color: #0f172a', $html);
        $this->assertStringContainsString('background-color: #94a3b8', $html);
    }
}
