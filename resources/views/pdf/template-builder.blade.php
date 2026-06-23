@php
    /** @var array $elements */
    // ──────────────────────────────────────────────────────────────────────────
    // PDF rendering model (Sprint 3)
    //
    // A4 @96dpi = 794×1123px. Two rendering zones:
    //
    // 1. ABSOLUTE layer (.paper): text + image elements positioned exactly.
    //    This layer uses position:relative + overflow:hidden — same as before.
    //    It lives on page 1 only; if content is taller than A4 it clips.
    //
    // 2. FLOW layer (.table-flow): the items <table> is rendered in normal flow,
    //    sitting below the absolute layer via a wrapper with padding-top = table Y.
    //    DomPDF paginates the flow layer automatically across pages.
    //    <thead> repeats on each page because DomPDF supports the CSS property
    //    `table-header-group` / `thead { display: table-header-group }`.
    //
    // If the layout has no table element, the flow layer is omitted and the
    // document behaves exactly as before (single absolute page).
    // ──────────────────────────────────────────────────────────────────────────

    /** @var array|null $tableEl */
    $tableEl = collect($elements)->first(fn($el) => ($el['type'] ?? '') === 'table');

    // Only absolute (non-table) elements for the absolute layer.
    $absoluteEls = collect($elements)->filter(fn($el) => ($el['type'] ?? '') !== 'table')->all();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; }

        /* ── Absolute layer (page 1 header/footer/logo/text) ── */
        .paper {
            position: relative;
            width: 793px;
            /* No fixed height — let it be tall enough to clip content but DomPDF
               will still render the flow layer below it on the same first page.
               We use overflow:hidden so absolute elements don't bleed. */
            overflow: hidden;
            background: #fff;
        }
        .el { position: absolute; }
        .text { white-space: nowrap; line-height: 1; }

        /* ── Flow layer (items table, paginates across pages) ── */
        .table-flow {
            width: 793px;
            /* padding-top pushes the table start to the Y coordinate of the
               table element, aligning it below header content on page 1. */
        }

        /* ── Items table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        /* thead repeats on every page automatically (DomPDF table-header-group) */
        .items-table thead tr {
            background-color: #f1f5f9;
        }
        .items-table thead th {
            padding: 6px 8px;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #cbd5e1;
            white-space: nowrap;
        }
        .items-table tbody td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        /* Footer sum row */
        .items-table tfoot td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            background-color: #f1f5f9;
        }
        /* Alignment helpers */
        .align-left   { text-align: left; }
        .align-center { text-align: center; }
        .align-right  { text-align: right; }
    </style>
</head>
<body>

{{-- ── 1. Absolute layer ── --}}
<div class="paper"
    @if($tableEl)
        style="height: {{ $tableEl['y'] }}px;"
    @else
        style="height: 1122px;"
    @endif
>
    @foreach ($absoluteEls as $el)
        @if ($el['type'] === 'text')
            <div class="el text"
                 style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; font-size: {{ $el['fontSize'] ?? 14 }}px; font-weight: {{ ($el['bold'] ?? false) ? 700 : 400 }}; color: {{ $el['color'] ?? '#0f172a' }};">{{ $el['content'] }}</div>
        @elseif ($el['type'] === 'image' && ! empty($el['src']))
            <img class="el"
                 style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; width: {{ $el['width'] ?? 160 }}px;@isset($el['height']) height: {{ $el['height'] }}px;@endisset"
                 src="{{ $el['src'] }}">
        @endif
    @endforeach
</div>

{{-- ── 2. Flow layer (only when a table element exists) ── --}}
@if ($tableEl)
    @php
        $columns   = $tableEl['columns'] ?? [];
        $rows      = $tableEl['rows'] ?? [];
        $showFoot  = $tableEl['showFooterSum'] ?? false;
        $tableW    = $tableEl['width'] ?? 714;
        $tableX    = $tableEl['x'] ?? 40;
    @endphp
    <div class="table-flow" style="padding-left: {{ $tableX }}px; width: 793px;">
        <table class="items-table" style="width: {{ $tableW }}px;">
            <thead>
                <tr>
                    @foreach ($columns as $col)
                        <th class="align-{{ $col['align'] ?? 'left' }}"
                            style="width: {{ $col['width'] ?? 'auto' }}px;">
                            {{ $col['label'] ?? '' }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        @foreach ($columns as $col)
                            <td class="align-{{ $col['align'] ?? 'left' }}">
                                {{ $row[$col['key']] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
            @if ($showFoot && count($rows) > 0)
                <tfoot>
                    <tr>
                        @foreach ($columns as $colIdx => $col)
                            @php
                                // Only sum rupiah/number columns; show label for first col
                                $isFirst = $colIdx === 0;
                                $canSum  = in_array($col['format'] ?? '', ['rupiah', 'number']) && !$isFirst;
                            @endphp
                            @if ($isFirst)
                                <td class="align-{{ $col['align'] ?? 'left' }}">Total</td>
                            @elseif ($canSum)
                                @php
                                    // Raw sum: strip non-numeric, sum, re-format
                                    $sum = collect($rows)->sum(function($row) use ($col) {
                                        return (int) preg_replace('/[^0-9]/', '', $row[$col['key']] ?? '');
                                    });
                                    $display = ($col['format'] === 'rupiah')
                                        ? 'Rp ' . number_format($sum, 0, ',', '.')
                                        : number_format($sum, 0, ',', '.');
                                @endphp
                                <td class="align-{{ $col['align'] ?? 'right' }}">{{ $display }}</td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endif

</body>
</html>
