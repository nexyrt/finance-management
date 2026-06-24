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
        $layout = $request->input('layout');

        // Banded layout: { paper: {...}, bands: { header, content, footerFlow, footerFixed } }
        if (is_array($layout) && array_key_exists('bands', $layout)) {
            $request->validate([
                'layout' => ['present', 'array'],
                'layout.paper' => ['required', 'array'],
                'layout.paper.margins' => ['required', 'array'],
                'layout.paper.margins.top' => ['required', 'numeric', 'min:0'],
                'layout.paper.margins.right' => ['required', 'numeric', 'min:0'],
                'layout.paper.margins.bottom' => ['required', 'numeric', 'min:0'],
                'layout.paper.margins.left' => ['required', 'numeric', 'min:0'],
                'layout.bands' => ['required', 'array'],
                'layout.bands.header' => ['required', 'array'],
                'layout.bands.content' => ['required', 'array'],
                'layout.bands.footerFlow' => ['required', 'array'],
                'layout.bands.footerFixed' => ['required', 'array'],
            ]);
        } else {
            // Legacy flat-array layout — lenient, accept as-is.
            $request->validate([
                'layout' => ['present', 'array'],
            ]);
        }

        $pdfTemplate->update([
            'layout' => $layout,
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
     *
     * B3: branches on banded vs legacy layout.
     *   Banded  → resolves band elements + table, passes $banded=true + band vars.
     *   Legacy  → maps flat elements array (unchanged behaviour).
     */
    public function pdf(PdfTemplate $pdfTemplate, ?Invoice $invoice = null): HttpResponse
    {
        $invoice = $invoice ?? $this->resolvePreviewInvoice();

        // Sprint 5b: pass custom fonts so the blade can emit @font-face for DomPDF.
        $customFonts = CustomFont::query()
            ->orderBy('name')
            ->get()
            ->map(fn (CustomFont $f) => [
                'name' => $f->name,
                'path' => $f->diskPath(),
            ])
            ->all();

        $layout = $pdfTemplate->layout ?? [];

        // ── B3: Banded layout path ────────────────────────────────────────────
        if (is_array($layout) && array_key_exists('bands', $layout)) {
            return $this->pdfBanded($layout, $invoice, $customFonts);
        }

        // ── Legacy flat-array path (unchanged) ───────────────────────────────
        $elements = collect($layout)
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

        return Pdf::loadView('pdf.template-builder', [
            'elements' => $elements,
            'customFonts' => $customFonts,
        ])
            ->setPaper('A4', 'portrait')
            ->stream('template.pdf');
    }

    /**
     * Render a banded layout to PDF (B3).
     *
     * Resolves:
     *  - header.elements  → tokens in text content + grid cell text
     *  - content.table    → columns + rows via ItemColumns
     *  - footerFlow.elements → same as header
     *  footerFixed + header.repeat are passed through for B4; not rendered as fixed yet.
     *
     * @param  array<string, mixed>  $layout
     * @param  array<int, array{name: string, path: string}>  $customFonts
     */
    private function pdfBanded(array $layout, Invoice $invoice, array $customFonts): HttpResponse
    {
        $bands = $layout['bands'] ?? [];
        $paper = $layout['paper'] ?? [];
        $margins = $paper['margins'] ?? ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40];

        // Resolve a band's elements array (text tokens + grid cell tokens; images pass through).
        $resolveElements = function (array $elements) use ($invoice): array {
            return array_map(function (array $el) use ($invoice): array {
                if (($el['type'] ?? '') === 'text') {
                    return [
                        ...$el,
                        'content' => TemplateTokens::resolveText((string) ($el['content'] ?? ''), $invoice),
                    ];
                }

                if (($el['type'] ?? '') === 'grid') {
                    $cells = collect($el['cells'] ?? [])->map(
                        fn (array $row) => array_map(
                            fn (array $cell) => [
                                ...$cell,
                                'text' => TemplateTokens::resolveText((string) ($cell['text'] ?? ''), $invoice),
                            ],
                            $row
                        )
                    )->all();

                    return [...$el, 'cells' => $cells];
                }

                return $el; // image, rect, line — pass through
            }, $elements);
        };

        // Header band
        $headerBand = $bands['header'] ?? [];
        $headerHeight = (int) ($headerBand['height'] ?? 180);
        $headerElements = $resolveElements($headerBand['elements'] ?? []);
        $headerRepeat = (bool) ($headerBand['repeat'] ?? false);

        // Content band — items table (null if not set)
        $contentBand = $bands['content'] ?? [];
        $tableEl = $contentBand['table'] ?? null;
        if ($tableEl !== null) {
            $columns = $tableEl['columns'] ?? ItemColumns::defaultColumns();
            $rows = ItemColumns::resolveItems($columns, $invoice->items);
            $tableEl = [
                ...$tableEl,
                'columns' => $columns,
                'rows' => $rows,
            ];
        }

        // Footer-flow band
        $footerFlowBand = $bands['footerFlow'] ?? [];
        $footerFlowHeight = (int) ($footerFlowBand['height'] ?? 120);
        $footerFlowElements = $resolveElements($footerFlowBand['elements'] ?? []);

        // Footer-fixed band (B4 — pass through, not rendered as fixed yet)
        $footerFixedBand = $bands['footerFixed'] ?? [];

        return Pdf::loadView('pdf.template-builder', [
            // Banded path signal
            'banded' => true,
            // Paper
            'paper' => ['margins' => $margins],
            // Header band
            'headerBand' => [
                'height' => $headerHeight,
                'repeat' => $headerRepeat,
                'elements' => $headerElements,
            ],
            // Content / items table
            'tableEl' => $tableEl,
            // Footer-flow band
            'footerFlowBand' => [
                'height' => $footerFlowHeight,
                'elements' => $footerFlowElements,
            ],
            // Footer-fixed (B4 — passed but not rendered fixed yet)
            'footerFixedBand' => $footerFixedBand,
            // Custom fonts
            'customFonts' => $customFonts,
            // Legacy $elements must be present so the blade @else path doesn't error
            'elements' => [],
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
