<?php

namespace App\Services;

use App\Models\CustomFont;
use App\Models\Invoice;
use App\Models\PdfTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;

/**
 * Renders an Invoice using a PdfTemplate (builder) layout.
 *
 * Supports both banded layouts (new, with 'bands' key) and legacy flat-array
 * layouts (Sprint 1–6 backward-compat).
 *
 * Payment modes (mirrors InvoicePrintService):
 *   - mode 'dp'        → $dpAmount is the amount being billed now
 *   - mode 'pelunasan' → $pelunasanAmount is the remaining balance being settled
 *   - mode 'full'      → full invoice total
 */
class BuilderInvoicePrinter
{
    /**
     * Render the template+invoice to a DomPDF instance ready to stream/download.
     *
     * @param  int|null  $dpAmount  Raw integer (whole Rupiah); null = not a DP print
     * @param  int|null  $pelunasanAmount  Raw integer; null = not a pelunasan print
     */
    public function render(
        PdfTemplate $template,
        Invoice $invoice,
        ?int $dpAmount = null,
        ?int $pelunasanAmount = null,
    ): DomPDF {
        $invoice->loadMissing(['client', 'items', 'payments']);

        $mode = 'full';
        if ($dpAmount !== null && $dpAmount > 0) {
            $mode = 'dp';
        } elseif ($pelunasanAmount !== null && $pelunasanAmount > 0) {
            $mode = 'pelunasan';
        }

        $paymentContext = [
            'mode' => $mode,
            'dp_amount' => $dpAmount,
            'pelunasan_amount' => $pelunasanAmount,
        ];

        $customFonts = CustomFont::query()
            ->orderBy('name')
            ->get()
            ->map(fn (CustomFont $f) => [
                'name' => $f->name,
                'path' => $f->diskPath(),
            ])
            ->all();

        $layout = $template->layout ?? [];

        if (isset($layout['bands'])) {
            return $this->renderBanded($layout, $invoice, $paymentContext, $customFonts);
        }

        return $this->renderFlat($layout, $invoice, $paymentContext, $customFonts);
    }

    /**
     * Derive a safe filename for the downloaded PDF.
     */
    public function filename(Invoice $invoice, ?int $dpAmount, ?int $pelunasanAmount): string
    {
        $prefix = $dpAmount ? 'DP-' : ($pelunasanAmount ? 'Pelunasan-' : '');
        $safe = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', (string) $invoice->invoice_number);

        return $prefix.'Invoice-'.$safe.'.pdf';
    }

    // ── Private ───────────────────────────────────────────────────────────────

    /**
     * Render a banded layout (header/content/footerFlow/footerFixed bands).
     * Mirrors PdfTemplateController::pdfBanded() but with payment context support.
     *
     * @param  array<string, mixed>  $layout
     * @param  array<string, mixed>  $paymentContext
     * @param  array<int, array{name: string, path: string}>  $customFonts
     */
    private function renderBanded(array $layout, Invoice $invoice, array $paymentContext, array $customFonts): DomPDF
    {
        $bands = $layout['bands'] ?? [];
        $paper = $layout['paper'] ?? [];
        $margins = $paper['margins'] ?? ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40];

        $resolveElements = function (array $elements) use ($invoice, $paymentContext): array {
            return array_map(function (array $el) use ($invoice, $paymentContext): array {
                if (($el['type'] ?? '') === 'text') {
                    return [
                        ...$el,
                        'content' => TemplateTokens::resolveText(
                            (string) ($el['content'] ?? ''),
                            $invoice,
                            $paymentContext,
                        ),
                    ];
                }

                if (($el['type'] ?? '') === 'grid') {
                    $cells = collect($el['cells'] ?? [])->map(
                        fn (array $row) => array_map(
                            fn (array $cell) => [
                                ...$cell,
                                'text' => TemplateTokens::resolveText(
                                    (string) ($cell['text'] ?? ''),
                                    $invoice,
                                    $paymentContext,
                                ),
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
        $headerElements = $resolveElements($headerBand['elements'] ?? []);
        $headerRepeat = (bool) ($headerBand['repeat'] ?? false);

        // Content band — TRB or legacy column table
        $contentBand = $bands['content'] ?? [];
        $tableEl = $contentBand['table'] ?? null;
        $trbItems = null;

        if ($tableEl !== null) {
            $isTrb = isset($tableEl['rows']) && is_array($tableEl['rows']);

            if ($isTrb) {
                $trbItems = $invoice->items;
            } else {
                $columns = $tableEl['columns'] ?? ItemColumns::defaultColumns();
                $rows = ItemColumns::resolveItems($columns, $invoice->items);
                $tableEl = [...$tableEl, 'columns' => $columns, 'rows' => $rows];
            }
        }

        // Footer-flow band
        $footerFlowBand = $bands['footerFlow'] ?? [];
        $footerFlowElements = $resolveElements($footerFlowBand['elements'] ?? []);

        // Footer-fixed band
        $footerFixedRaw = $bands['footerFixed'] ?? [];
        $footerFixedBand = [
            'height' => (int) ($footerFixedRaw['height'] ?? 0),
            'elements' => $resolveElements($footerFixedRaw['elements'] ?? []),
        ];

        return Pdf::loadView('pdf.template-builder', [
            'banded' => true,
            'paper' => ['margins' => $margins],
            'headerBand' => [
                'height' => (int) ($headerBand['height'] ?? 180),
                'repeat' => $headerRepeat,
                'elements' => $headerElements,
            ],
            'tableEl' => $tableEl,
            'trbItems' => $trbItems,
            'footerFlowBand' => [
                'height' => (int) ($footerFlowBand['height'] ?? 120),
                'elements' => $footerFlowElements,
            ],
            'footerFixedBand' => $footerFixedBand,
            'customFonts' => $customFonts,
            'elements' => [],
        ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
    }

    /**
     * Render a legacy flat-array layout (Sprint 1–6 backward-compat).
     *
     * @param  array<int, array<string, mixed>>  $layout
     * @param  array<string, mixed>  $paymentContext
     * @param  array<int, array{name: string, path: string}>  $customFonts
     */
    private function renderFlat(array $layout, Invoice $invoice, array $paymentContext, array $customFonts): DomPDF
    {
        $elements = collect($layout)
            ->map(function (array $el) use ($invoice, $paymentContext): array {
                if ($el['type'] === 'text') {
                    return [
                        ...$el,
                        'content' => TemplateTokens::resolveText(
                            (string) ($el['content'] ?? ''),
                            $invoice,
                            $paymentContext,
                        ),
                    ];
                }

                if ($el['type'] === 'table') {
                    $columns = $el['columns'] ?? ItemColumns::defaultColumns();
                    $rows = ItemColumns::resolveItems($columns, $invoice->items);

                    return [...$el, 'columns' => $columns, 'rows' => $rows];
                }

                if ($el['type'] === 'grid') {
                    $cells = collect($el['cells'] ?? [])->map(
                        fn (array $row) => array_map(
                            fn (array $cell) => [
                                ...$cell,
                                'text' => TemplateTokens::resolveText(
                                    (string) ($cell['text'] ?? ''),
                                    $invoice,
                                    $paymentContext,
                                ),
                            ],
                            $row
                        )
                    )->all();

                    return [...$el, 'cells' => $cells];
                }

                return $el;
            })
            ->all();

        return Pdf::loadView('pdf.template-builder', [
            'elements' => $elements,
            'customFonts' => $customFonts,
        ])
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
    }
}
