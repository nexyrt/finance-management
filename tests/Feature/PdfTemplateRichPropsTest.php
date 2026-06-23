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
 * Sprint 5a — Rich text properties + image properties tests.
 *
 * Covers:
 *  - Text box: width + align=right + border + fill + non-default fontFamily renders in PDF
 *  - Text box: italic, underline, strikethrough, highlight, lineHeight, letterSpacing render
 *  - Text box: valign padding-top applied when height is set
 *  - Legacy text (no width): still renders with .text-legacy class + nowrap (backward-compat)
 *  - Image: opacity, border, borderRadius render in PDF output
 *  - Image: opacity=100 (default) renders no opacity style (clean output)
 *  - Sprint 1–4 regression: PDF endpoint still returns 200 for all element types
 */
class PdfTemplateRichPropsTest extends TestCase
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

    /** Minimal 1×1 base64 PNG (transparent). */
    private function stubBase64Png(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
    }

    /** Create a PdfTemplate with the given layout array. */
    private function makeTemplate(array $layout): PdfTemplate
    {
        return PdfTemplate::query()->create([
            'name' => 'Test Sprint 5a',
            'layout' => $layout,
            'is_default' => false,
        ]);
    }

    /**
     * Render the blade template directly (bypasses DomPDF) so we can assert
     * the CSS/HTML that would be fed into DomPDF.
     * The blade view only expects $elements; table rows are embedded in the table element.
     */
    private function renderBlade(array $layout): string
    {
        return view('pdf.template-builder', [
            'elements' => $layout,
        ])->render();
    }

    // ── Text box tests ─────────────────────────────────────────────────────────

    #[Test]
    public function text_box_with_width_and_right_align_renders_correct_styles(): void
    {
        $el = [
            'id' => 1,
            'type' => 'text',
            'x' => 50,
            'y' => 80,
            'content' => 'Jumlah',
            'fontSize' => 14,
            'bold' => true,
            'color' => '#0f172a',
            'fontFamily' => 'Times New Roman',
            'align' => 'right',
            'width' => 180,
            'lineHeight' => 1.4,
            'fill' => '#f8fafc',
            'borderWidth' => 1,
            'borderColor' => '#cbd5e1',
        ];

        $html = $this->renderBlade([$el]);

        // Font family mapped to DomPDF-safe value (HTML-encoded in blade output)
        $this->assertStringContainsString('Times New Roman', $html);

        // text-align: right
        $this->assertStringContainsString('text-align: right', $html);

        // width applied
        $this->assertStringContainsString('width: 180px', $html);

        // fill applied
        $this->assertStringContainsString('background-color: #f8fafc', $html);

        // border applied
        $this->assertStringContainsString('border: 1px solid #cbd5e1', $html);

        // line-height
        $this->assertStringContainsString('line-height: 1.4', $html);

        // box-sizing for box mode
        $this->assertStringContainsString('box-sizing: border-box', $html);

        // white-space pre-wrap for box mode
        $this->assertStringContainsString('white-space: pre-wrap', $html);

        // font-weight bold
        $this->assertStringContainsString('font-weight: 700', $html);

        // content present
        $this->assertStringContainsString('Jumlah', $html);

        // text-box CSS class applied
        $this->assertStringContainsString('text-box', $html);
    }

    #[Test]
    public function text_box_italic_underline_strikethrough_highlight_render(): void
    {
        $el = [
            'id' => 2,
            'type' => 'text',
            'x' => 60,
            'y' => 100,
            'content' => 'Catatan',
            'fontSize' => 12,
            'bold' => false,
            'color' => '#374151',
            'italic' => true,
            'underline' => true,
            'strikethrough' => true,
            'highlight' => '#fef08a',
            'letterSpacing' => 2,
            'width' => 200,
        ];

        $html = $this->renderBlade([$el]);

        $this->assertStringContainsString('font-style: italic', $html);
        $this->assertStringContainsString('underline', $html);
        $this->assertStringContainsString('line-through', $html);
        $this->assertStringContainsString('background-color: #fef08a', $html);
        $this->assertStringContainsString('letter-spacing: 2px', $html);
    }

    #[Test]
    public function text_box_with_height_and_valign_middle_applies_padding_top(): void
    {
        $el = [
            'id' => 3,
            'type' => 'text',
            'x' => 60,
            'y' => 100,
            'content' => 'Tengah',
            'fontSize' => 12,
            'bold' => false,
            'color' => '#0f172a',
            'width' => 150,
            'height' => 60,
            'valign' => 'middle',
            'lineHeight' => 1.2,
        ];

        $html = $this->renderBlade([$el]);

        // height applied
        $this->assertStringContainsString('height: 60px', $html);
        // overflow hidden for fixed height
        $this->assertStringContainsString('overflow: hidden', $html);
        // padding-top should be present (middle valign approximation)
        $this->assertStringContainsString('padding-top:', $html);
    }

    #[Test]
    public function legacy_text_without_width_gets_text_legacy_class_and_no_box_styles(): void
    {
        // Legacy element: no 'width' key at all — backward compat
        $el = [
            'id' => 10,
            'type' => 'text',
            'x' => 60,
            'y' => 80,
            'content' => 'Legacy Text',
            'fontSize' => 14,
            'bold' => false,
            'color' => '#0f172a',
        ];

        $html = $this->renderBlade([$el]);

        // Should still render the content
        $this->assertStringContainsString('Legacy Text', $html);

        // Legacy text uses .text-legacy CSS class (which sets white-space:nowrap)
        $this->assertStringContainsString('text-legacy', $html);

        // Should NOT use text-box CSS class on the element (class="el text-box")
        $this->assertStringNotContainsString('class="el text-box"', $html);

        // Verify the legacy element uses the correct CSS classes (not text-box)
        $this->assertStringContainsString('class="el text text-legacy"', $html);

        // Legacy inline style must include white-space: nowrap
        $this->assertStringContainsString('white-space: nowrap', $html);

        // The element inline style must NOT contain white-space: pre-wrap.
        // We verify this by checking the body section only (after </style>).
        $bodyPart = substr($html, (int) strpos($html, '</style>'));
        $this->assertStringNotContainsString('white-space: pre-wrap', $bodyPart);
    }

    #[Test]
    public function legacy_text_uses_helvetica_by_default(): void
    {
        $el = [
            'id' => 11,
            'type' => 'text',
            'x' => 60,
            'y' => 80,
            'content' => 'Default Font',
            'fontSize' => 14,
            'bold' => false,
            'color' => '#0f172a',
        ];

        $html = $this->renderBlade([$el]);

        // Should default to Helvetica font stack
        $this->assertStringContainsString('Helvetica', $html);
    }

    #[Test]
    public function text_box_dejavu_sans_font_family_renders(): void
    {
        $el = [
            'id' => 12,
            'type' => 'text',
            'x' => 60,
            'y' => 80,
            'content' => 'DejaVu Test',
            'fontSize' => 12,
            'bold' => false,
            'color' => '#0f172a',
            'fontFamily' => 'DejaVu Sans',
            'width' => 150,
        ];

        $html = $this->renderBlade([$el]);

        $this->assertStringContainsString('DejaVu Sans', $html);
    }

    #[Test]
    public function courier_font_family_renders_with_monospace_stack(): void
    {
        $el = [
            'id' => 13,
            'type' => 'text',
            'x' => 60,
            'y' => 80,
            'content' => 'Kode',
            'fontSize' => 11,
            'bold' => false,
            'color' => '#0f172a',
            'fontFamily' => 'Courier',
            'width' => 120,
        ];

        $html = $this->renderBlade([$el]);

        $this->assertStringContainsString('Courier', $html);
        $this->assertStringContainsString('monospace', $html);
    }

    // ── Image property tests ───────────────────────────────────────────────────

    #[Test]
    public function image_opacity_renders_in_pdf(): void
    {
        $el = [
            'id' => 20,
            'type' => 'image',
            'x' => 60,
            'y' => 80,
            'src' => $this->stubBase64Png(),
            'width' => 120,
            'height' => 80,
            'opacity' => 50,
        ];

        $html = $this->renderBlade([$el]);

        // opacity: 0.5
        $this->assertStringContainsString('opacity: 0.5', $html);
    }

    #[Test]
    public function image_border_and_radius_render_in_pdf(): void
    {
        $el = [
            'id' => 21,
            'type' => 'image',
            'x' => 60,
            'y' => 80,
            'src' => $this->stubBase64Png(),
            'width' => 120,
            'height' => 80,
            'borderWidth' => 2,
            'borderColor' => '#3b82f6',
            'borderRadius' => 8,
        ];

        $html = $this->renderBlade([$el]);

        $this->assertStringContainsString('border: 2px solid #3b82f6', $html);
        $this->assertStringContainsString('border-radius: 8px', $html);
    }

    #[Test]
    public function image_full_opacity_does_not_render_opacity_style(): void
    {
        $el = [
            'id' => 22,
            'type' => 'image',
            'x' => 60,
            'y' => 80,
            'src' => $this->stubBase64Png(),
            'width' => 120,
            'height' => 80,
            'opacity' => 100,
        ];

        $html = $this->renderBlade([$el]);

        // opacity:1 is default — the helper only emits opacity when < 100
        $this->assertStringNotContainsString('opacity:', $html);
    }

    #[Test]
    public function image_without_new_props_renders_like_before(): void
    {
        // Legacy image: no Sprint 5a fields
        $el = [
            'id' => 23,
            'type' => 'image',
            'x' => 60,
            'y' => 80,
            'src' => $this->stubBase64Png(),
            'width' => 120,
            'height' => 80,
        ];

        $html = $this->renderBlade([$el]);

        $this->assertStringContainsString('width: 120px', $html);
        $this->assertStringContainsString('height: 80px', $html);
        // No extra styles injected
        $this->assertStringNotContainsString('border-radius:', $html);
        $this->assertStringNotContainsString('opacity:', $html);
    }

    // ── PDF endpoint smoke tests ───────────────────────────────────────────────

    #[Test]
    public function pdf_endpoint_returns_200_with_rich_text_box_element(): void
    {
        $template = $this->makeTemplate([
            [
                'id' => 1,
                'type' => 'text',
                'x' => 50,
                'y' => 80,
                'content' => 'Total: {{invoice.total_amount}}',
                'fontSize' => 14,
                'bold' => true,
                'color' => '#0f172a',
                'fontFamily' => 'Times New Roman',
                'align' => 'right',
                'width' => 200,
                'borderWidth' => 1,
                'borderColor' => '#cbd5e1',
                'fill' => '#f8fafc',
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function pdf_endpoint_returns_200_with_rich_image_element(): void
    {
        $template = $this->makeTemplate([
            [
                'id' => 1,
                'type' => 'image',
                'x' => 60,
                'y' => 80,
                'src' => $this->stubBase64Png(),
                'width' => 100,
                'height' => 80,
                'opacity' => 70,
                'borderWidth' => 1,
                'borderColor' => '#000000',
                'borderRadius' => 4,
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function pdf_endpoint_returns_200_with_legacy_text_no_regression(): void
    {
        // Existing Sprint 1/2 text element with NO Sprint 5a fields: must render unchanged
        $template = $this->makeTemplate([
            [
                'id' => 1,
                'type' => 'text',
                'x' => 60,
                'y' => 80,
                'content' => 'Invoice #001',
                'fontSize' => 18,
                'bold' => true,
                'color' => '#0f172a',
            ],
        ]);

        $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/pdf")
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    // ── Blade-level regression: text box in below-zone ────────────────────────

    #[Test]
    public function text_box_in_below_zone_renders_new_styles(): void
    {
        // Layout: table at y=300, text box at y=820 → below-zone
        $layout = [
            [
                'id' => 1,
                'type' => 'table',
                'x' => 40,
                'y' => 300,
                'width' => 714,
                'columns' => [
                    ['key' => 'description', 'label' => 'Deskripsi', 'width' => 300, 'align' => 'left',  'format' => 'text'],
                    ['key' => 'amount',      'label' => 'Jumlah',    'width' => 130, 'align' => 'right', 'format' => 'rupiah'],
                ],
                'showFooterSum' => false,
                'rows' => [],
            ],
            [
                'id' => 2,
                'type' => 'text',
                'x' => 600,
                'y' => 820,
                'content' => 'Total',
                'fontSize' => 12,
                'bold' => true,
                'color' => '#0f172a',
                'fontFamily' => 'Times New Roman',
                'align' => 'right',
                'width' => 150,
                'fill' => '#f1f5f9',
            ],
        ];

        $html = $this->renderBlade($layout);

        // Text box should appear in below-flow zone
        $this->assertStringContainsString('below-flow', $html);
        $this->assertStringContainsString('Total', $html);
        // Font name present (may be HTML-entity encoded)
        $this->assertStringContainsString('Times New Roman', $html);
        $this->assertStringContainsString('text-align: right', $html);
        $this->assertStringContainsString('background-color: #f1f5f9', $html);
    }
}
