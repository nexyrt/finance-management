<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomFont;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomFontController extends Controller
{
    /**
     * List all custom fonts — consumed by the editor via Inertia shared prop
     * (see PdfTemplateController::edit()) or as JSON fallback.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            CustomFont::query()
                ->orderBy('name')
                ->get()
                ->map(fn (CustomFont $f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'url' => $f->browserUrl(),
                ])
        );
    }

    /**
     * Upload a .ttf font file and register it in the global library.
     *
     * Validation:
     *  - file required, mimes:ttf, max 2 MB
     *  - name required, string, max 80, unique in custom_fonts
     *
     * Storage: public disk → fonts/custom/{slug}_{hash}.ttf
     * DomPDF can load from diskPath() (within chroot = base_path()).
     * Browser URL served via /storage symlink.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:custom_fonts,name'],
            // mimetypes covers all SFNT/TTF mime variants finfo may detect.
            // Extension check (.ttf) is enforced via mimes as a second line of defence.
            // max:5120 = 5 MB (fonts can be up to ~500 KB, leave headroom).
            'file' => [
                'required',
                'file',
                'max:5120',
                'mimetypes:font/ttf,font/sfnt,application/x-font-ttf,application/x-font-truetype,application/octet-stream',
                function ($attribute, $value, $fail) {
                    /** @var UploadedFile $value */
                    if (strtolower($value->getClientOriginalExtension()) !== 'ttf') {
                        $fail('File harus berformat .ttf.');
                    }
                },
            ],
        ]);

        $uploadedFile = $request->file('file');

        // Build a deterministic, safe filename: slug + 8-char hash to avoid collisions.
        $slug = Str::slug($request->input('name'));
        $hash = substr(md5($uploadedFile->getClientOriginalName().microtime()), 0, 8);
        $filename = "{$slug}_{$hash}.ttf";

        // Ensure the target directory exists on the public disk.
        Storage::disk('public')->makeDirectory('fonts/custom');

        // Store using putFileAs so we control the filename.
        Storage::disk('public')->putFileAs('fonts/custom', $uploadedFile, $filename);

        CustomFont::query()->create([
            'name' => $request->input('name'),
            'filename' => $filename,
        ]);

        return back()->with('success', "Font \"{$request->input('name')}\" berhasil diunggah.");
    }

    /**
     * Delete a custom font — removes the DB row and the file from disk.
     */
    public function destroy(CustomFont $customFont): RedirectResponse
    {
        $path = "fonts/custom/{$customFont->filename}";

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $customFont->delete();

        return back()->with('success', "Font \"{$customFont->name}\" berhasil dihapus.");
    }
}
