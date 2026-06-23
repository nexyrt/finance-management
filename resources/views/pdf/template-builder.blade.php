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

    // ── Font map (mirrored from FONT_MAP in edit.tsx) ────────────────────────
    // Maps the friendly label → DomPDF-safe CSS font-family value.
    $fontFamilyMap = [
        'Helvetica / Arial' => "'Helvetica', Arial, sans-serif",
        'Times New Roman'   => "'Times New Roman', Times, serif",
        'Courier'           => "'Courier New', Courier, monospace",
        'DejaVu Sans'       => "'DejaVu Sans', sans-serif",
    ];

    // Sprint 5b: custom fonts passed from PdfTemplateController::pdf().
    // Each entry: ['name' => string, 'path' => absolute_path_to_.ttf]
    // For a custom font, fontFamily label = name → @font-face emitted below.
    // We extend $fontFamilyMap so $textStyle() resolves them to their CSS name.
    /** @var array $customFonts */
    $customFonts = $customFonts ?? [];
    foreach ($customFonts as $cf) {
        $cfName = $cf['name'] ?? '';
        if ($cfName !== '' && file_exists($cf['path'] ?? '')) {
            // The CSS font-family name is the display name itself (same on both sides).
            $fontFamilyMap[$cfName] = "'{$cfName}'";
        }
    }

    /**
     * Build inline CSS for a text element (Sprint 5a).
     * If el['width'] is NOT set → legacy mode (nowrap, line-height:1).
     * If el['width'] IS set → box mode (wraps, all new props apply).
     */
    $textStyle = function (array $el) use ($fontFamilyMap): string {
        $hasBox = array_key_exists('width', $el) && $el['width'] !== null;

        $fontFamily   = $fontFamilyMap[$el['fontFamily'] ?? ''] ?? "'Helvetica', Arial, sans-serif";
        $fontSize     = (int) ($el['fontSize'] ?? 14);
        $fontWeight   = ($el['bold'] ?? false) ? 700 : 400;
        $fontStyle    = ($el['italic'] ?? false) ? 'italic' : 'normal';
        $color        = $el['color'] ?? '#0f172a';
        $lineHeight   = $hasBox ? ($el['lineHeight'] ?? 1.2) : 1;
        $letterSpacing = ($el['letterSpacing'] ?? 0);

        // text-decoration: combine underline + line-through
        $decorations = [];
        if ($el['underline'] ?? false) { $decorations[] = 'underline'; }
        if ($el['strikethrough'] ?? false) { $decorations[] = 'line-through'; }
        $textDecoration = count($decorations) ? implode(' ', $decorations) : 'none';

        $textAlign    = $el['align'] ?? 'left';

        $style  = "font-family: {$fontFamily}; font-size: {$fontSize}px; font-weight: {$fontWeight}; ";
        $style .= "font-style: {$fontStyle}; color: {$color}; text-decoration: {$textDecoration}; ";
        $style .= "text-align: {$textAlign}; line-height: {$lineHeight}; ";
        if ($letterSpacing) { $style .= "letter-spacing: {$letterSpacing}px; "; }

        $highlight = $el['highlight'] ?? null;
        if ($highlight) { $style .= "background-color: {$highlight}; "; }

        if ($hasBox) {
            $width    = (int) $el['width'];
            $padding  = (int) ($el['padding'] ?? 0);
            $bw       = (int) ($el['borderWidth'] ?? 0);
            $bc       = $el['borderColor'] ?? '#000000';
            $fill     = $el['fill'] ?? null;

            $style .= "width: {$width}px; box-sizing: border-box; ";
            if ($padding > 0)       { $style .= "padding: {$padding}px; "; }
            if ($bw > 0)            { $style .= "border: {$bw}px solid {$bc}; "; }
            if ($fill)              { $style .= "background-color: {$fill}; "; }
            if (isset($el['height'])) {
                $height = (int) $el['height'];
                $style .= "height: {$height}px; overflow: hidden; ";
                // vertical-align in DomPDF: achieved via display:table-cell
                // ponytail note: DomPDF does not support flex; we use padding-top approximation
                // for valign=middle/bottom since display:table-cell is also unreliable.
                // We use the same padding-top trick that DomPDF handles best.
                $valign = $el['valign'] ?? 'top';
                if ($valign === 'middle' || $valign === 'bottom') {
                    // Approximate line count: height / (fontSize * lineHeight)
                    $lineH     = max(1, (float) ($el['lineHeight'] ?? 1.2));
                    $linePixels = $fontSize * $lineH;
                    // Rough single-line height; if content wraps this won't be pixel-perfect but
                    // is the best DomPDF-safe approach without JS.
                    $paddingTop = $valign === 'middle'
                        ? max(0, round(($height - $linePixels) / 2))
                        : max(0, round($height - $linePixels));
                    $paddingTop = min($paddingTop, $height - $fontSize); // clamp
                    if ($paddingTop > 0 && $padding === 0) {
                        $style .= "padding-top: {$paddingTop}px; ";
                    }
                }
            }
            $style .= "white-space: pre-wrap; word-break: break-word; ";
        } else {
            $style .= "white-space: nowrap; ";
        }

        return $style;
    };

    /**
     * Build inline CSS for an image element (Sprint 5a).
     */
    $imgStyle = function (array $el, int $relTop = -1, bool $absolute = true): string {
        $x      = (int) ($el['x'] ?? 0);
        $top    = $relTop >= 0 ? $relTop : (int) ($el['y'] ?? 0);
        $width  = (int) ($el['width'] ?? 160);
        $height = isset($el['height']) ? (int) $el['height'] : null;

        $style  = $absolute ? "position: absolute; left: {$x}px; top: {$top}px; " : '';
        $style .= "width: {$width}px; ";
        if ($height !== null) { $style .= "height: {$height}px; "; }

        $opacity = $el['opacity'] ?? 100;
        if ($opacity < 100) {
            $opacityVal = round($opacity / 100, 2);
            $style .= "opacity: {$opacityVal}; ";
        }

        $bw = (int) ($el['borderWidth'] ?? 0);
        $bc = $el['borderColor'] ?? '#000000';
        if ($bw > 0) { $style .= "border: {$bw}px solid {$bc}; box-sizing: border-box; "; }

        $radius = (int) ($el['borderRadius'] ?? 0);
        if ($radius > 0) { $style .= "border-radius: {$radius}px; "; }

        return $style;
    };

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
                    // text: use explicit height if set (box mode), else fontSize*lineHeight estimate
                    if (isset($el['height'])) {
                        $elHeight = (int) $el['height'];
                    } else {
                        $lh = (float) ($el['lineHeight'] ?? 1.4);
                        $elHeight = (int) round(($el['fontSize'] ?? 14) * $lh);
                    }
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

        /* ── Text box (Sprint 5a) ── */
        .text-box {
            overflow: hidden;
            box-sizing: border-box;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .text-legacy {
            white-space: nowrap;
            line-height: 1;
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

        @foreach ($customFonts as $cf)
            @php
                $cfName = $cf['name'] ?? '';
                $cfPath = $cf['path'] ?? '';
            @endphp
            @if ($cfName !== '' && file_exists($cfPath))
                @font-face {
                    font-family: '{{ $cfName }}';
                    src: url('{{ $cfPath }}') format('truetype');
                    font-weight: normal;
                    font-style: normal;
                }
            @endif
        @endforeach
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
            @php $hasBox = array_key_exists('width', $el) && $el['width'] !== null; @endphp
            <div class="el {{ $hasBox ? 'text-box' : 'text text-legacy' }}"
                 style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; {{ $textStyle($el) }}">{{ $el['content'] }}</div>
        @elseif ($el['type'] === 'image' && ! empty($el['src']))
            <img class="el"
                 style="{{ $imgStyle($el) }}"
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
                                @if (!($cell['merged'] ?? false))
                                    @php
                                        $cw       = $gridColW[$colIdx] ?? 'auto';
                                        $ch       = 24;
                                        $fill     = ($cell['fill'] ?? '') ?: 'transparent';
                                        $colSpan  = (int) ($cell['colSpan'] ?? 1);
                                        $rowSpan  = (int) ($cell['rowSpan'] ?? 1);
                                    @endphp
                                    <td @if($colSpan > 1) colspan="{{ $colSpan }}" @endif @if($rowSpan > 1) rowspan="{{ $rowSpan }}" @endif style="width: {{ $cw }}px; height: {{ $ch }}px; border: {{ $gridBw }}px solid {{ $gridBc }}; text-align: {{ $cell['align'] ?? 'left' }}; font-weight: {{ ($cell['bold'] ?? false) ? 700 : 400 }}; color: {{ $cell['color'] ?? '#0f172a' }}; background-color: {{ $fill }};">{{ $cell['text'] ?? '' }}</td>
                                @endif
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
                @if (!empty($tableEl['headerGroups']))
                    <tr>
                        @foreach ($tableEl['headerGroups'] as $group)
                            @php $gSpan = (int)($group['span'] ?? 1); @endphp
                            <th class="align-{{ $group['align'] ?? 'center' }}"
                                @if($gSpan > 1) colspan="{{ $gSpan }}" @endif
                                style="padding: 6px 8px; font-weight: bold; font-size: 10px; border: 1px solid #cbd5e1; white-space: nowrap; background-color: #f1f5f9;">
                                {{ $group['label'] ?? '' }}
                            </th>
                        @endforeach
                    </tr>
                @endif
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
                    @php $hasBox = array_key_exists('width', $el) && $el['width'] !== null; @endphp
                    <div class="el {{ $hasBox ? 'text-box' : 'text text-legacy' }}"
                         style="left: {{ $el['x'] }}px; top: {{ $relTop }}px; {{ $textStyle($el) }}">{{ $el['content'] }}</div>
                @elseif ($el['type'] === 'image' && ! empty($el['src']))
                    <img class="el"
                         style="{{ $imgStyle($el, $relTop) }}"
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
                                        @if (!($cell['merged'] ?? false))
                                            @php
                                                $cw       = $gridColW[$colIdx] ?? 'auto';
                                                $ch       = 24;
                                                $fill     = ($cell['fill'] ?? '') ?: 'transparent';
                                                $colSpan  = (int) ($cell['colSpan'] ?? 1);
                                                $rowSpan  = (int) ($cell['rowSpan'] ?? 1);
                                            @endphp
                                            <td @if($colSpan > 1) colspan="{{ $colSpan }}" @endif @if($rowSpan > 1) rowspan="{{ $rowSpan }}" @endif style="width: {{ $cw }}px; height: {{ $ch }}px; border: {{ $gridBw }}px solid {{ $gridBc }}; text-align: {{ $cell['align'] ?? 'left' }}; font-weight: {{ ($cell['bold'] ?? false) ? 700 : 400 }}; color: {{ $cell['color'] ?? '#0f172a' }}; background-color: {{ $fill }};">{{ $cell['text'] ?? '' }}</td>
                                        @endif
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
