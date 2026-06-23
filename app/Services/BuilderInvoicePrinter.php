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
 * Mirrors InvoicePrintService semantics for DP / pelunasan modes:
 *   - mode 'dp'        → $dpAmount is the amount being billed now
 *   - mode 'pelunasan' → $pelunasanAmount is the remaining balance being settled
 *   - mode 'full'      → full invoice total
 *
 * Token resolution delegates to TemplateTokens::resolveText() with a
 * $paymentContext array so {{payment.*}} tokens resolve correctly.
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

        // Build payment context — mirrors InvoicePrintService semantics.
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

        $elements = collect($template->layout ?? [])
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

                    return [
                        ...$el,
                        'columns' => $columns,
                        'rows' => $rows,
                    ];
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
                            $row,
                        )
                    )->all();

                    return [
                        ...$el,
                        'cells' => $cells,
                    ];
                }

                // image / rect / line — pass through unchanged
                return $el;
            })
            ->all();

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
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
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
}
