<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\PdfTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PdfTemplateController extends Controller
{
    /** Sample data for token resolution (same as sandbox). */
    private const SAMPLE = [
        'invoice' => ['number' => 'INV/001/KSN/06.26', 'date' => '08 Jun 2026', 'due_date' => '22 Jun 2026', 'total' => 'Rp 5.000.000'],
        'client' => ['name' => 'PT Maju Jaya', 'npwp' => '01.234.567.8-901.000'],
        'company' => ['name' => 'Kisantra'],
    ];

    public function index(): InertiaResponse
    {
        $templates = PdfTemplate::query()
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(fn (PdfTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'description' => $t->description,
                'is_default' => $t->is_default,
                'updated_at' => $t->updated_at?->toISOString(),
            ]);

        return Inertia::render('settings/pdf-templates/index', [
            'templates' => $templates,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $template = PdfTemplate::query()->create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'layout' => [],
            'is_default' => false,
        ]);

        return redirect()->route('settings.pdf-templates.edit', $template)
            ->with('success', 'Template berhasil dibuat.');
    }

    public function update(Request $request, PdfTemplate $pdfTemplate): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $pdfTemplate->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        if ($request->boolean('is_default')) {
            $pdfTemplate->setAsDefault();
        }

        return redirect()->back()->with('success', 'Template berhasil diperbarui.');
    }

    public function destroy(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $pdfTemplate->delete();

        return redirect()->route('settings.pdf-templates.index')
            ->with('success', 'Template berhasil dihapus.');
    }

    public function duplicate(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $copy = PdfTemplate::query()->create([
            'name' => $pdfTemplate->name.' (Salinan)',
            'description' => $pdfTemplate->description,
            'layout' => $pdfTemplate->layout ?? [],
            'is_default' => false,
        ]);

        return redirect()->route('settings.pdf-templates.edit', $copy)
            ->with('success', 'Template berhasil diduplikat.');
    }

    public function setDefault(PdfTemplate $pdfTemplate): RedirectResponse
    {
        $pdfTemplate->setAsDefault();

        return redirect()->back()->with('success', 'Template default berhasil diubah.');
    }

    public function edit(PdfTemplate $pdfTemplate): InertiaResponse
    {
        return Inertia::render('settings/pdf-templates/edit', [
            'template' => [
                'id' => $pdfTemplate->id,
                'name' => $pdfTemplate->name,
                'layout' => $pdfTemplate->layout ?? [],
            ],
        ]);
    }

    public function save(Request $request, PdfTemplate $pdfTemplate): RedirectResponse
    {
        $request->validate([
            'layout' => ['present', 'array'],
            'layout.*.id' => ['required'],
            'layout.*.type' => ['required', 'in:text,image'],
            'layout.*.x' => ['required', 'numeric'],
            'layout.*.y' => ['required', 'numeric'],
        ]);

        $pdfTemplate->update([
            'layout' => $request->input('layout'),
        ]);

        return back()->with('success', 'Layout tersimpan.');
    }

    public function pdf(PdfTemplate $pdfTemplate): HttpResponse
    {
        $elements = collect($pdfTemplate->layout ?? [])
            ->map(fn (array $el) => [
                ...$el,
                'content' => isset($el['content']) ? $this->resolve($el['content']) : null,
            ])
            ->all();

        return Pdf::loadView('pdf.template-builder', ['elements' => $elements])
            ->setPaper('A4', 'portrait')
            ->stream('template.pdf');
    }

    /** Replace {{path}} tokens with values from sample data. */
    private function resolve(string $text): string
    {
        return preg_replace_callback('/\{\{([\w.]+)\}\}/', function (array $m): string {
            $value = data_get(self::SAMPLE, $m[1]);

            return $value === null ? $m[0] : (string) $value;
        }, $text);
    }
}
