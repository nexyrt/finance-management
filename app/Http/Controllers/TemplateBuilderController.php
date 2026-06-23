<?php

namespace App\Http\Controllers;

use App\Models\PdfTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Sandbox WYSIWYG template builder: persist a single layout (JSON) and render
 * it to PDF with sample data. One template row for now (the sandbox draft).
 */
class TemplateBuilderController extends Controller
{
    /** Sample data for token resolution; mirrors DATA in the React sandbox. */
    private const SAMPLE = [
        'invoice' => ['number' => 'INV/001/KSN/06.26', 'date' => '08 Jun 2026', 'due_date' => '22 Jun 2026', 'total' => 'Rp 5.000.000'],
        'client' => ['name' => 'PT Maju Jaya', 'npwp' => '01.234.567.8-901.000'],
        'company' => ['name' => 'Kisantra'],
    ];

    public function index(): InertiaResponse
    {
        $template = PdfTemplate::query()->first();

        return Inertia::render('template-builder-test', [
            'layout' => $template?->layout ?? [],
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $request->validate([
            'layout' => ['present', 'array'],
            'layout.*.id' => ['required'],
            'layout.*.type' => ['required', 'in:text,image'],
            'layout.*.x' => ['required', 'numeric'],
            'layout.*.y' => ['required', 'numeric'],
        ]);

        $template = PdfTemplate::query()->firstOrNew([]);
        $template->name = 'Sandbox';
        // Simpan layout PENUH (validate() membuang field tanpa rule spt content/src).
        $template->layout = $request->input('layout');
        $template->save();

        return back();
    }

    public function pdf(): HttpResponse
    {
        $template = PdfTemplate::query()->first();
        $elements = collect($template?->layout ?? [])
            ->map(fn (array $el) => [...$el, 'content' => isset($el['content']) ? $this->resolve($el['content']) : null])
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
