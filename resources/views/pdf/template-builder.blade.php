@php
    /** @var array $elements */
    // ──────────────────────────────────────────────────────────────────────────
    // PDF rendering model (Sprint 4 — 3-zone)
    //
    // A4 @96dpi = 794×1123px. Three rendering zones:
    //
    // 1. HEADER zone (.paper, absolute, page 1):
    //    Text + image elements with y < tableY are positioned exactly inside the
    //    absolute .paper div, which is capped at height = tableY with overflow:hidden.
    //    Unchanged from Sprint 3.
    //
    // 2. TABLE zone (flow):
    //    The items <table> renders in normal flow, pushed down by padding-top = tableY
    //    so it visually starts where the table element was placed on page 1.
    //    DomPDF paginates across pages automatically; <thead> repeats.
    //    Unchanged from Sprint 3.
    //
    // 3. BELOW zone (flow, NEW):
    //    Elements with y >= tableY (text or image) must appear BELOW the last table
    //    row. They are collected and rendered in a position:relative container placed
    //    after the flow table. Inside that container each element is position:absolute
    //    with left = el.x  and  top = (el.y - tableY). The container is given an
    //    explicit height equal to the tallest below-element bottom edge so that the
    //    container is tall enough to show all content.
    //    estimatedHeight: image → el.height; text → fontSize * 1.4 (line-height ≈1.4).
    //
    // If the layout has NO table element, the whole document stays as a single
    // absolute page (Sprint 1/2 behaviour, fully backward-compatible).
    // If there are NO below-zone elements the below-zone container is omitted.
    // ──────────────────────────────────────────────────────────────────────────

    /** @var array|null $tableEl */
    $tableEl = collect($elements)->first(fn($el) => ($el['type'] ?? '') === 'table');

    $tableY = $tableEl ? (int) ($tableEl['y'] ?? 0) : null;

    // Header-zone elements: non-table AND (no table OR y < tableY)
    $headerEls = collect($elements)->filter(function ($el) use ($tableEl, $tableY) {
        if (in_array($el['type'] ?? '', ['table'])) {
            return false;
        }
        if ($tableEl === null) {
            return true; // no table → everything goes in the absolute page
        }
        return (int) ($el['y'] ?? 0) < $tableY;
    })->all();

    // Below-zone elements: non-table AND y >= tableY (only meaningful when table exists)
    $belowEls = [];
    $belowContainerHeight = 0;

    if ($tableEl !== null) {
        $belowEls = collect($elements)->filter(function ($el) use ($tableY) {
            if (($el['type'] ?? '') === 'table') {
                return false;
            }
            return (int) ($el['y'] ?? 0) >= $tableY;
        })->values()->all();

        if (count($belowEls) > 0) {
            // Compute the height needed for the below container
            $belowContainerHeight = collect($belowEls)->reduce(function (int $max, array $el) use ($tableY): int {
                $relTop = (int) ($el['y'] ?? 0) - $tableY;
                if ($el['type'] === 'image') {
                    $elHeight = (int) ($el['height'] ?? 40);
                } elseif ($el['type'] === 'grid') {
                    $rows = count($el['cells'] ?? []);
                    $bw = (int) (($el['border'] ?? [])['width'] ?? 1);
                    $elHeight = $rows * 24 + $bw * ($rows + 1);
                } else {
                    // text
                    $elHeight = (int) round(($el['fontSize'] ?? 14) * 1.4);
                }
                return max($max, $relTop + $elHeight);
            }, 0);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; }

        /* ── Zone 1: Absolute header layer (page 1) ── */
        .paper {
            position: relative;
            width: 793px;
            overflow: hidden;
            background: #fff;
        }
        .el { position: absolute; }
        .text { white-space: nowrap; line-height: 1; }

        /* ── Zone 2: Flow layer (items table, paginates) ── */
        .table-flow {
            width: 793px;
        }

        /* ── Zone 3: Below-zone flow container ── */
        .below-flow {
            position: relative;
            width: 793px;
        }

        /* ── Static grid element ── */
        .grid-el {
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
            font-family: 'Helvetica', Arial, sans-serif;
        }
        .grid-el td {
            vertical-align: middle;
            overflow: hidden;
            padding: 2px 4px;
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

{{-- ── Zone 1: Absolute header layer ── --}}
<div class="paper"
    @if($tableEl)
        style="height: {{ $tableY }}px;"
    @else
        style="height: 1122px;"
    @endif
>
    @foreach ($headerEls as $el)
        @if ($el['type'] === 'text')
            <div class="el text"
                 style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; font-size: {{ $el['fontSize'] ?? 14 }}px; font-weight: {{ ($el['bold'] ?? false) ? 700 : 400 }}; color: {{ $el['color'] ?? '#0f172a' }};">{{ $el['content'] }}</div>
        @elseif ($el['type'] === 'image' && ! empty($el['src']))
            <img class="el"
                 style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; width: {{ $el['width'] ?? 160 }}px;@isset($el['height']) height: {{ $el['height'] }}px;@endisset"
                 src="{{ $el['src'] }}">
        @elseif ($el['type'] === 'grid')
            @php
                $gridBw    = (int) (($el['border'] ?? [])['width'] ?? 1);
                $gridBc    = ($el['border'] ?? [])['color'] ?? '#cbd5e1';
                $gridCells = $el['cells'] ?? [];
                $gridColW  = $el['colWidths'] ?? [];
                $gridW     = (int) ($el['width'] ?? 300);
            @endphp
            <table class="el grid-el"
                   style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; width: {{ $gridW }}px;">
                <tbody>
                    @foreach ($gridCells as $rowIdx => $rowCells)
                        <tr>
                            @foreach ($rowCells as $colIdx => $cell)
                                @php
                                    $cw   = $gridColW[$colIdx] ?? 'auto';
                                    $ch   = 24;
                                    $fill = ($cell['fill'] ?? '') ?: 'transparent';
                                @endphp
                                <td style="width: {{ $cw }}px; height: {{ $ch }}px; border: {{ $gridBw }}px solid {{ $gridBc }}; text-align: {{ $cell['align'] ?? 'left' }}; font-weight: {{ ($cell['bold'] ?? false) ? 700 : 400 }}; color: {{ $cell['color'] ?? '#0f172a' }}; background-color: {{ $fill }};">{{ $cell['text'] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach
</div>

{{-- ── Zone 2: Flow layer (only when a table element exists) ── --}}
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

    {{-- ── Zone 3: Below-zone (elements placed at y >= tableY) ── --}}
    @if (count($belowEls) > 0)
        <div class="below-flow" style="height: {{ $belowContainerHeight }}px;">
            @foreach ($belowEls as $el)
                @php
                    $relTop = (int) ($el['y'] ?? 0) - $tableY;
                @endphp
                @if ($el['type'] === 'text')
                    <div class="el text"
                         style="left: {{ $el['x'] }}px; top: {{ $relTop }}px; font-size: {{ $el['fontSize'] ?? 14 }}px; font-weight: {{ ($el['bold'] ?? false) ? 700 : 400 }}; color: {{ $el['color'] ?? '#0f172a' }};">{{ $el['content'] }}</div>
                @elseif ($el['type'] === 'image' && ! empty($el['src']))
                    <img class="el"
                         style="left: {{ $el['x'] }}px; top: {{ $relTop }}px; width: {{ $el['width'] ?? 160 }}px;@isset($el['height']) height: {{ $el['height'] }}px;@endisset"
                         src="{{ $el['src'] }}">
                @elseif ($el['type'] === 'grid')
                    @php
                        $gridBw    = (int) (($el['border'] ?? [])['width'] ?? 1);
                        $gridBc    = ($el['border'] ?? [])['color'] ?? '#cbd5e1';
                        $gridCells = $el['cells'] ?? [];
                        $gridColW  = $el['colWidths'] ?? [];
                        $gridW     = (int) ($el['width'] ?? 300);
                    @endphp
                    <table class="el grid-el"
                           style="left: {{ $el['x'] }}px; top: {{ $relTop }}px; width: {{ $gridW }}px;">
                        <tbody>
                            @foreach ($gridCells as $rowIdx => $rowCells)
                                <tr>
                                    @foreach ($rowCells as $colIdx => $cell)
                                        @php
                                            $cw   = $gridColW[$colIdx] ?? 'auto';
                                            $ch   = 24;
                                            $fill = ($cell['fill'] ?? '') ?: 'transparent';
                                        @endphp
                                        <td style="width: {{ $cw }}px; height: {{ $ch }}px; border: {{ $gridBw }}px solid {{ $gridBc }}; text-align: {{ $cell['align'] ?? 'left' }}; font-weight: {{ ($cell['bold'] ?? false) ? 700 : 400 }}; color: {{ $cell['color'] ?? '#0f172a' }}; background-color: {{ $fill }};">{{ $cell['text'] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
        </div>
    @endif
@endif

</body>
</html>
