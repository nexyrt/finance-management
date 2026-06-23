<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\CustomFont;
use App\Models\Invoice;
use App\Models\PdfTemplate;
use App\Services\ItemColumns;
use App\Services\TemplateTokens;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PdfTemplateController extends Controller
{
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

    /**
     * Editor page — passes token catalog + resolved sample data to the frontend.
     */
    public function edit(PdfTemplate $pdfTemplate): InertiaResponse
    {
        $invoice = $this->resolvePreviewInvoice();
        $sampleData = TemplateTokens::buildMap($invoice);

        // Build resolved sample rows for the table element Preview mode.
        $defaultColumns = ItemColumns::defaultColumns();
        $sampleItems = ItemColumns::resolveItems($defaultColumns, $invoice->items);

        // Sprint 5b: global custom font library for the editor font picker.
        $customFonts = CustomFont::query()
            ->orderBy('name')
            ->get()
            ->map(fn (CustomFont $f) => [
                'id' => $f->id,
                'name' => $f->name,
                'url' => $f->browserUrl(),
            ])
            ->all();

        return Inertia::render('settings/pdf-templates/edit', [
            'template' => [
                'id' => $pdfTemplate->id,
                'name' => $pdfTemplate->name,
                'layout' => $pdfTemplate->layout ?? [],
            ],
            'tokenCatalog' => TemplateTokens::catalogForFrontend(),
            'sampleData' => $sampleData,
            'itemColumnCatalog' => ItemColumns::catalogForFrontend(),
            'sampleItems' => $sampleItems,
            'customFonts' => $customFonts,
        ]);
    }

    public function save(Request $request, PdfTemplate $pdfTemplate): RedirectResponse
    {
        $request->validate([
            'layout' => ['present', 'array'],
            'layout.*.id' => ['required'],
            'layout.*.type' => ['required', 'in:text,image,table,grid,rect,line'],
            'layout.*.x' => ['required', 'numeric'],
            'layout.*.y' => ['required', 'numeric'],
        ]);

        $pdfTemplate->update([
            'layout' => $request->input('layout'),
        ]);

        return back()->with('success', 'Layout tersimpan.');
    }

    /**
     * Render template as PDF, resolving tokens against a real Invoice.
     *
     * Route: GET /settings/pdf-templates/{template}/pdf/{invoice?}
     * - Invoice param provided → use that invoice.
     * - No param → latest invoice in DB.
     * - DB empty → in-memory sample (never crashes).
     */
    public function pdf(PdfTemplate $pdfTemplate, ?Invoice $invoice = null): HttpResponse
    {
        $invoice = $invoice ?? $this->resolvePreviewInvoice();

        $elements = collect($pdfTemplate->layout ?? [])
            ->map(function (array $el) use ($invoice): array {
                if ($el['type'] === 'text') {
                    return [
                        ...$el,
                        'content' => TemplateTokens::resolveText((string) ($el['content'] ?? ''), $invoice),
                    ];
                }

                if ($el['type'] === 'table') {
                    $columns = $el['columns'] ?? ItemColumns::defaultColumns();
                    $rows = ItemColumns::resolveItems($columns, $invoice->items);

                    return [
                        ...$el,
                        'columns' => $columns,
                        'rows' => $rows,
                    ];
                }

                if ($el['type'] === 'grid') {
                    // Resolve {{tokens}} in every cell's text — single resolve path.
                    $cells = collect($el['cells'] ?? [])->map(
                        fn (array $row) => array_map(
                            fn (array $cell) => [
                                ...$cell,
                                'text' => TemplateTokens::resolveText((string) ($cell['text'] ?? ''), $invoice),
                            ],
                            $row
                        )
                    )->all();

                    return [
                        ...$el,
                        'cells' => $cells,
                    ];
                }

                // image — pass through unchanged
                return $el;
            })
            ->all();

        // Sprint 5b: pass custom fonts so the blade can emit @font-face for DomPDF.
        $customFonts = CustomFont::query()
            ->orderBy('name')
            ->get()
            ->map(fn (CustomFont $f) => [
                'name' => $f->name,
                'path' => $f->diskPath(),
            ])
            ->all();

        return Pdf::loadView('pdf.template-builder', [
            'elements' => $elements,
            'customFonts' => $customFonts,
        ])
            ->setPaper('A4', 'portrait')
            ->stream('template.pdf');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Auto-pick the latest invoice (with client eager-loaded) for preview/PDF.
     * Falls back to an in-memory sample when the DB has no invoices yet.
     */
    private function resolvePreviewInvoice(): Invoice
    {
        $invoice = Invoice::with(['client', 'payments', 'items'])
            ->latest()
            ->first();

        return $invoice ?? TemplateTokens::sampleInvoice();
    }
}
