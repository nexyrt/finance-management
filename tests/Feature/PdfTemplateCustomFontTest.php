<?php

namespace Tests\Feature;

use App\Models\CustomFont;
use App\Models\PdfTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Sprint 5b — Custom font upload, listing, deletion, editor prop, and PDF rendering.
 *
 * Fixture: tests/Fixtures/test-font.ttf — a real DejaVu TTF (font/sfnt via finfo).
 * The controller accepts font/sfnt via `mimetypes` + .ttf extension check.
 *
 * Tests:
 *  - Valid .ttf upload creates DB row + stores file on disk.
 *  - Duplicate name is rejected (unique validation).
 *  - Non-TTF extension is rejected.
 *  - Permission gate: user without 'manage pdf templates' gets 403.
 *  - edit() action exposes customFonts to the page.
 *  - Blade emits @font-face for a custom font whose file exists on disk.
 *  - PDF endpoint returns 200 with a text element using a custom font family.
 *  - Delete endpoint removes DB row and file.
 */
class PdfTemplateCustomFontTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    /** Path to the real TTF fixture (DejaVu, font/sfnt). */
    private string $fixturePath;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'manage pdf templates']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo('manage pdf templates');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->fixturePath = base_path('tests/Fixtures/test-font.ttf');
    }

    // ── Upload tests ───────────────────────────────────────────────────────────

    #[Test]
    public function valid_ttf_upload_creates_db_row_and_stores_file(): void
    {
        Storage::fake('public');

        $file = new UploadedFile(
            path: $this->fixturePath,
            originalName: 'test-font.ttf',
            mimeType: 'font/sfnt',
            error: UPLOAD_ERR_OK,
            test: true,
        );

        $this->actingAs($this->admin)
            ->post('/settings/pdf-templates/custom-fonts', [
                'name' => 'TestFont',
                'file' => $file,
            ])
            ->assertRedirect();

        // DB row created
        $this->assertDatabaseHas('custom_fonts', ['name' => 'TestFont']);

        // File stored on public disk under fonts/custom/
        $font = CustomFont::query()->where('name', 'TestFont')->first();
        $this->assertNotNull($font);
        Storage::disk('public')->assertExists("fonts/custom/{$font->filename}");
    }

    #[Test]
    public function duplicate_name_is_rejected(): void
    {
        Storage::fake('public');

        // First upload succeeds
        CustomFont::query()->create(['name' => 'DuplicateFont', 'filename' => 'dummy.ttf']);

        $file = new UploadedFile(
            path: $this->fixturePath,
            originalName: 'test-font.ttf',
            mimeType: 'font/sfnt',
            error: UPLOAD_ERR_OK,
            test: true,
        );

        $this->actingAs($this->admin)
            ->post('/settings/pdf-templates/custom-fonts', [
                'name' => 'DuplicateFont',
                'file' => $file,
            ])
            ->assertSessionHasErrors('name');
    }

    #[Test]
    public function non_ttf_extension_is_rejected(): void
    {
        Storage::fake('public');

        // A PNG file with .png extension — rejected by extension closure
        $pngFile = UploadedFile::fake()->image('logo.png');

        $this->actingAs($this->admin)
            ->post('/settings/pdf-templates/custom-fonts', [
                'name' => 'BadFont',
                'file' => $pngFile,
            ])
            ->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('custom_fonts', ['name' => 'BadFont']);
    }

    #[Test]
    public function user_without_permission_gets_403_on_upload(): void
    {
        Storage::fake('public');

        $noPermUser = User::factory()->create();

        $file = new UploadedFile(
            path: $this->fixturePath,
            originalName: 'test-font.ttf',
            mimeType: 'font/sfnt',
            error: UPLOAD_ERR_OK,
            test: true,
        );

        $this->actingAs($noPermUser)
            ->post('/settings/pdf-templates/custom-fonts', [
                'name' => 'SomeFont',
                'file' => $file,
            ])
            ->assertForbidden();
    }

    #[Test]
    public function user_without_permission_gets_403_on_list(): void
    {
        $noPermUser = User::factory()->create();

        $this->actingAs($noPermUser)
            ->get('/settings/pdf-templates/custom-fonts')
            ->assertForbidden();
    }

    // ── Delete tests ───────────────────────────────────────────────────────────

    #[Test]
    public function delete_removes_db_row_and_file(): void
    {
        Storage::fake('public');

        // Seed a font row + fake file
        Storage::disk('public')->makeDirectory('fonts/custom');
        Storage::disk('public')->put('fonts/custom/myfont_abc12345.ttf', 'fake-ttf-content');

        $font = CustomFont::query()->create([
            'name' => 'MyFont',
            'filename' => 'myfont_abc12345.ttf',
        ]);

        $this->actingAs($this->admin)
            ->delete("/settings/pdf-templates/custom-fonts/{$font->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('custom_fonts', ['id' => $font->id]);
        Storage::disk('public')->assertMissing('fonts/custom/myfont_abc12345.ttf');
    }

    // ── Editor prop tests ──────────────────────────────────────────────────────

    #[Test]
    public function edit_action_exposes_custom_fonts_to_page(): void
    {
        // Seed two custom fonts
        CustomFont::query()->create(['name' => 'FontA', 'filename' => 'fonta_00000001.ttf']);
        CustomFont::query()->create(['name' => 'FontB', 'filename' => 'fontb_00000002.ttf']);

        $template = PdfTemplate::query()->create([
            'name' => 'Test',
            'layout' => [],
            'is_default' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/settings/pdf-templates/{$template->id}/edit");

        $response->assertStatus(200);

        // customFonts prop present with both fonts
        $response->assertInertia(
            fn ($page) => $page
                ->component('settings/pdf-templates/edit')
                ->has('customFonts', 2)
                ->where('customFonts.0.name', 'FontA')
                ->where('customFonts.1.name', 'FontB')
        );
    }

    // ── Blade / PDF tests ──────────────────────────────────────────────────────

    #[Test]
    public function blade_emits_font_face_for_custom_font_when_file_exists(): void
    {
        // Use the real fixture path so file_exists() passes in the blade
        $html = view('pdf.template-builder', [
            'elements' => [],
            'customFonts' => [
                ['name' => 'MyCustomFont', 'path' => $this->fixturePath],
            ],
        ])->render();

        $this->assertStringContainsString('@font-face', $html);
        $this->assertStringContainsString("font-family: 'MyCustomFont'", $html);
        $this->assertStringContainsString($this->fixturePath, $html);
    }

    #[Test]
    public function blade_skips_font_face_when_file_does_not_exist(): void
    {
        $html = view('pdf.template-builder', [
            'elements' => [],
            'customFonts' => [
                ['name' => 'GhostFont', 'path' => '/nonexistent/ghost.ttf'],
            ],
        ])->render();

        // @font-face should NOT be emitted for a missing file
        $this->assertStringNotContainsString('GhostFont', $html);
    }

    #[Test]
    public function blade_resolves_custom_font_family_in_text_element(): void
    {
        // Text element using a custom font name as fontFamily
        $elements = [
            [
                'id' => 1,
                'type' => 'text',
                'x' => 60,
                'y' => 80,
                'content' => 'Halo',
                'fontSize' => 14,
                'bold' => false,
                'color' => '#0f172a',
                'fontFamily' => 'MyCustomFont',
                'width' => 200,
            ],
        ];

        $html = view('pdf.template-builder', [
            'elements' => $elements,
            'customFonts' => [
                ['name' => 'MyCustomFont', 'path' => $this->fixturePath],
            ],
        ])->render();

        // @font-face emitted
        $this->assertStringContainsString('@font-face', $html);
        // font-family resolved to the custom name in the inline style
        $this->assertStringContainsString("font-family: 'MyCustomFont'", $html);
    }

    #[Test]
    public function pdf_endpoint_returns_200_with_custom_font_text_element(): void
    {
        // DomPDF needs real filesystem access to register @font-face and write .ufm cache
        // to storage/fonts/ — so we use the real public disk (not faked) for this test.
        // We copy the fixture TTF to the real public disk and clean up after.
        $filename = 'testfont_pdf_test_'.uniqid().'.ttf';
        $destDir = storage_path('app/public/fonts/custom');
        $destPath = $destDir.'/'.$filename;

        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }
        copy($this->fixturePath, $destPath);

        try {
            $font = CustomFont::query()->create([
                'name' => 'TestFontPdf_'.uniqid(),
                'filename' => $filename,
            ]);

            $template = PdfTemplate::query()->create([
                'name' => 'Custom Font PDF Test',
                'layout' => [
                    [
                        'id' => 1,
                        'type' => 'text',
                        'x' => 60,
                        'y' => 80,
                        'content' => 'Invoice',
                        'fontSize' => 18,
                        'bold' => false,
                        'color' => '#0f172a',
                        'fontFamily' => $font->name,
                        'width' => 200,
                    ],
                ],
                'is_default' => false,
            ]);

            $this->actingAs($this->admin)
                ->get("/settings/pdf-templates/{$template->id}/pdf")
                ->assertStatus(200)
                ->assertHeader('Content-Type', 'application/pdf');
        } finally {
            // Cleanup: remove the copied TTF from the real disk
            if (file_exists($destPath)) {
                unlink($destPath);
            }
        }
    }
}
