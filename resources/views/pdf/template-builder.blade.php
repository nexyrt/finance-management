@php
    /** @var array $elements */
    // ──────────────────────────────────────────────────────────────────────────
    // PDF rendering model — TWO PATHS
    //
    // PATH A — BANDED (B3, new):  $banded === true
    //   Passed vars: $paper, $headerBand, $tableEl, $footerFlowBand, $footerFixedBand
    //   Structure:
    //     @page { margin: T R B L }  ← from $paper['margins']
    //     Header band: position:relative div, height=headerBand.height, elements absolute at x/y
    //     Content: <table> in normal flow (DomPDF paginates, thead repeats)
    //     Footer-flow: position:relative div, height=footerFlowBand.height, page-break-inside:avoid
    //   footerFixed + header.repeat deferred to B4.
    //
    // PATH B — LEGACY FLAT-ARRAY (Sprint 1–6, backward-compat):  $banded not set
    //   A4 @96dpi = 794×1123px. Three rendering zones:
    //   1. HEADER zone (.paper, absolute, page 1)
    //   2. TABLE zone (flow, paginates)
    //   3. BELOW zone (flow, after table)
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
    /** @var bool $banded */
    $banded = $banded ?? false;
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

        if (isset($el['_z'])) { $style .= "z-index: {$el['_z']}; "; }

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

        if (isset($el['_z'])) { $style .= "z-index: {$el['_z']}; "; }

        return $style;
    };

    // ── LEGACY path only: zone split + z-index assignment ──────────────────
    // (Skipped on the banded path to avoid unnecessary work.)
    // NOTE: $tableEl is used as the legacy zone-split table element.
    // In the banded path, $tableEl is the view variable passed from the controller.
    // We alias it to $legacyTableEl to avoid stomping the banded view variable.
    $headerEls    = [];
    $legacyTableEl = null;
    $tableY        = null;
    $belowEls      = [];
    $belowContainerHeight = 0;
    $belowMinY     = 0;

    if (! $banded) {
        // Paint order: in the editor every element is absolute, so z-order = array
        // order (later = on top). In the PDF the items table is in normal flow, which
        // CSS paints BELOW positioned (absolute) elements regardless of DOM order. To
        // match the editor we give every element an explicit z-index = its array index
        // and make the table container positioned with the table's index, so DomPDF
        // paints strictly by array order.
        foreach ($elements as $i => $_el) {
            $elements[$i]['_z'] = $i;
        }

        /** @var array|null $legacyTableEl */
        $legacyTableEl = collect($elements)->first(fn($el) => ($el['type'] ?? '') === 'table');

        $tableY = $legacyTableEl ? (int) ($legacyTableEl['y'] ?? 0) : null;

        // Header-zone elements: non-table AND (no table OR y < tableY)
        $headerEls = collect($elements)->filter(function ($el) use ($legacyTableEl, $tableY) {
            if (in_array($el['type'] ?? '', ['table'])) {
                return false;
            }
            if ($legacyTableEl === null) {
                return true; // no table → everything goes in the absolute page
            }
            return (int) ($el['y'] ?? 0) < $tableY;
        })->all();

        // Below-zone elements: non-table AND y >= tableY (only meaningful when table exists)
        if ($legacyTableEl !== null) {
            $belowEls = collect($elements)->filter(function ($el) use ($tableY) {
                if (($el['type'] ?? '') === 'table') {
                    return false;
                }
                return (int) ($el['y'] ?? 0) >= $tableY;
            })->values()->all();

            // Below-zone elements are stacked right AFTER the flow table, so position
            // them relative to the TOPMOST below-element (not tableY). Using tableY
            // would add a spurious gap (belowMinY - tableY) that pushes content onto a
            // second page when an element sits near the page bottom.
            $belowMinY = (int) collect($belowEls)->min(fn (array $el) => (int) ($el['y'] ?? 0));

            if (count($belowEls) > 0) {
                // Compute the height needed for the below container
                $belowContainerHeight = collect($belowEls)->reduce(function (int $max, array $el) use ($belowMinY): int {
                    $relTop = (int) ($el['y'] ?? 0) - $belowMinY;
                    if ($el['type'] === 'image') {
                        $elHeight = (int) ($el['height'] ?? 40);
                    } elseif ($el['type'] === 'grid') {
                        $rows = count($el['cells'] ?? []);
                        $bw = (int) (($el['border'] ?? [])['width'] ?? 1);
                        $elHeight = $rows * 24 + $bw * ($rows + 1);
                    } elseif ($el['type'] === 'rect') {
                        $elHeight = (int) ($el['height'] ?? 40);
                    } elseif ($el['type'] === 'line') {
                        // horizontal line: height = thickness; vertical line: height = length
                        $orientation = $el['orientation'] ?? 'h';
                        $elHeight = $orientation === 'h'
                            ? (int) ($el['thickness'] ?? 1)
                            : (int) ($el['length'] ?? 100);
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
    } // end legacy path
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @if ($banded)
            @php
                $m = ($paper['margins'] ?? ['top' => 40, 'right' => 40, 'bottom' => 40, 'left' => 40]);
                $mTop    = (int) ($m['top']    ?? 40);
                $mRight  = (int) ($m['right']  ?? 40);
                $mBottom = (int) ($m['bottom'] ?? 40);
                $mLeft   = (int) ($m['left']   ?? 40);
            @endphp
            @page { size: A4 portrait; margin: {{ $mTop }}px {{ $mRight }}px {{ $mBottom }}px {{ $mLeft }}px; }
        @else
            @page { size: A4 portrait; margin: 0; }
        @endif
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; }

        /* ── Banded path: band containers ── */
        .band-header {
            position: relative;
            width: 100%;
            overflow: visible;
        }
        .band-footer-flow {
            position: relative;
            width: 100%;
            page-break-inside: avoid;
        }
        /* Banded items table: full printable-area width */
        .band-table-flow {
            width: 100%;
        }
        /* B4: running footer — DomPDF repeats position:fixed on every page */
        .band-footer-fixed {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        /* B4: running header (repeat mode) */
        .band-header-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
        }

        /* ── Legacy path: Zone 1: Absolute header layer (page 1) ── */
        .paper {
            position: relative;
            width: 793px;
            background: #fff;
            /* overflow set inline: hidden when no table (full page),
               visible when a table exists so tall header elements (e.g. a big
               image spanning past tableY) are NOT clipped at the table start.
               Off-page bleed is still clipped by the A4 page media box. */
        }
        .el { position: absolute; }
        .text { white-space: nowrap; line-height: 1; }

        /* ── Legacy path: Zone 2: Flow layer (items table, paginates) ── */
        .table-flow {
            width: 793px;
        }

        /* ── Legacy path: Zone 3: Below-zone flow container ── */
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

        /* ── Items table (shared banded + legacy) ── */
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

@php
    /**
     * Render a single positioned element (text/image/grid/rect/line) as HTML.
     * Used by both the banded header/footerFlow bands AND the legacy zone rendering.
     *
     * $overrideTop: when set (≥ 0) overrides the element's own y as the absolute top.
     *               Used in the legacy below-zone (relTop) and banded bands (el.y directly).
     */
    $renderElement = function (array $el, int $overrideTop = -1) use ($textStyle, $imgStyle): string {
        $x   = (int) ($el['x'] ?? 0);
        $top = $overrideTop >= 0 ? $overrideTop : (int) ($el['y'] ?? 0);
        $zi  = $el['_z'] ?? 0;

        if ($el['type'] === 'text') {
            $hasBox = array_key_exists('width', $el) && $el['width'] !== null;
            $cls    = $hasBox ? 'el text-box' : 'el text text-legacy';
            $style  = "left: {$x}px; top: {$top}px; " . $textStyle($el);
            $text   = htmlspecialchars((string) ($el['content'] ?? ''), ENT_QUOTES, 'UTF-8');
            return "<div class=\"{$cls}\" style=\"{$style}\">{$text}</div>";
        }

        if ($el['type'] === 'image' && ! empty($el['src'])) {
            $style = $imgStyle($el, $top);
            $src   = htmlspecialchars((string) $el['src'], ENT_QUOTES, 'UTF-8');
            return "<img class=\"el\" style=\"{$style}\" src=\"{$src}\">";
        }

        if ($el['type'] === 'grid') {
            $gridBw    = (int) (($el['border'] ?? [])['width'] ?? 1);
            $gridBc    = ($el['border'] ?? [])['color'] ?? '#cbd5e1';
            $gridCells = $el['cells'] ?? [];
            $gridColW  = $el['colWidths'] ?? [];
            $gridW     = (int) ($el['width'] ?? 300);

            $html = "<table class=\"el grid-el\" style=\"left: {$x}px; top: {$top}px; width: {$gridW}px; z-index: {$zi};\"><tbody>";
            foreach ($gridCells as $rowCells) {
                $html .= '<tr>';
                foreach ($rowCells as $colIdx => $cell) {
                    if ($cell['merged'] ?? false) { continue; }
                    $cw      = $gridColW[$colIdx] ?? 'auto';
                    $fill    = ($cell['fill'] ?? '') ?: 'transparent';
                    $colspan = (int) ($cell['colSpan'] ?? 1);
                    $rowspan = (int) ($cell['rowSpan'] ?? 1);
                    $csAttr  = $colspan > 1 ? " colspan=\"{$colspan}\"" : '';
                    $rsAttr  = $rowspan > 1 ? " rowspan=\"{$rowspan}\"" : '';
                    $align   = $cell['align'] ?? 'left';
                    $fw      = ($cell['bold'] ?? false) ? 700 : 400;
                    $color   = $cell['color'] ?? '#0f172a';
                    $cellTxt = htmlspecialchars((string) ($cell['text'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $html   .= "<td{$csAttr}{$rsAttr} style=\"width: {$cw}px; height: 24px; border: {$gridBw}px solid {$gridBc}; text-align: {$align}; font-weight: {$fw}; color: {$color}; background-color: {$fill};\">{$cellTxt}</td>";
                }
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
            return $html;
        }

        if ($el['type'] === 'rect') {
            $rW      = (int) ($el['width'] ?? 100);
            $rH      = (int) ($el['height'] ?? 40);
            $rFill   = $el['fill'] ?? null;
            $rBw     = (int) ($el['borderWidth'] ?? 0);
            $rBc     = $el['borderColor'] ?? '#000000';
            $rRadius = (int) ($el['borderRadius'] ?? 0);
            $s       = "position: absolute; left: {$x}px; top: {$top}px; width: {$rW}px; height: {$rH}px; box-sizing: border-box; z-index: {$zi};";
            if ($rFill)    { $s .= " background-color: {$rFill};"; }
            if ($rBw > 0)  { $s .= " border: {$rBw}px solid {$rBc};"; }
            if ($rRadius > 0) { $s .= " border-radius: {$rRadius}px;"; }
            return "<div style=\"{$s}\"></div>";
        }

        if ($el['type'] === 'line') {
            $orient = $el['orientation'] ?? 'h';
            $length = (int) ($el['length'] ?? 100);
            $thick  = (int) ($el['thickness'] ?? 1);
            $color  = $el['color'] ?? '#0f172a';
            $lW     = $orient === 'h' ? $length : $thick;
            $lH     = $orient === 'h' ? $thick  : $length;
            $s      = "position: absolute; left: {$x}px; top: {$top}px; width: {$lW}px; height: {$lH}px; background-color: {$color}; z-index: {$zi};";
            return "<div style=\"{$s}\"></div>";
        }

        return '';
    };

    /**
     * Render a band's items table element (shared by banded + legacy).
     * Returns HTML string for the table.
     */
    $renderItemsTable = function (array $tEl, string $wrapperClass = 'table-flow', string $wrapperExtraStyle = '') use (&$renderItemsTable): string {
        $columns  = $tEl['columns'] ?? [];
        $rows     = $tEl['rows'] ?? [];
        $showFoot = $tEl['showFooterSum'] ?? false;
        $tableW   = $tEl['width'] ?? 714;
        $tableX   = $tEl['x'] ?? 40;
        $zi       = $tEl['_z'] ?? 0;

        $wStyle = $wrapperExtraStyle;
        // Legacy wrapper style includes padding-left and z-index; banded uses plain band div
        $html = "<div class=\"{$wrapperClass}\" style=\"{$wStyle}\">";
        $html .= "<table class=\"items-table\" style=\"width: {$tableW}px;\">";

        // thead
        $html .= '<thead>';
        if (! empty($tEl['headerGroups'])) {
            $html .= '<tr>';
            foreach ($tEl['headerGroups'] as $group) {
                $gSpan = (int) ($group['span'] ?? 1);
                $gAlign = $group['align'] ?? 'center';
                $gLabel = htmlspecialchars((string) ($group['label'] ?? ''), ENT_QUOTES, 'UTF-8');
                $spanAttr = $gSpan > 1 ? " colspan=\"{$gSpan}\"" : '';
                $html .= "<th class=\"align-{$gAlign}\"{$spanAttr} style=\"padding: 6px 8px; font-weight: bold; font-size: 10px; border: 1px solid #cbd5e1; white-space: nowrap; background-color: #f1f5f9;\">{$gLabel}</th>";
            }
            $html .= '</tr>';
        }
        $html .= '<tr>';
        foreach ($columns as $col) {
            $align = $col['align'] ?? 'left';
            $label = htmlspecialchars((string) ($col['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            $width = $col['width'] ?? 'auto';
            $html .= "<th class=\"align-{$align}\" style=\"width: {$width}px;\">{$label}</th>";
        }
        $html .= '</tr></thead>';

        // tbody
        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($columns as $col) {
                $align = $col['align'] ?? 'left';
                $val   = htmlspecialchars((string) ($row[$col['key']] ?? ''), ENT_QUOTES, 'UTF-8');
                $html .= "<td class=\"align-{$align}\">{$val}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';

        // tfoot (sum row)
        if ($showFoot && count($rows) > 0) {
            $html .= '<tfoot><tr>';
            foreach ($columns as $colIdx => $col) {
                $isFirst = $colIdx === 0;
                $canSum  = in_array($col['format'] ?? '', ['rupiah', 'number']) && ! $isFirst;
                if ($isFirst) {
                    $html .= "<td class=\"align-{$col['align']}\">Total</td>";
                } elseif ($canSum) {
                    $sum = 0;
                    foreach ($rows as $r) { $sum += (int) preg_replace('/[^0-9]/', '', $r[$col['key']] ?? ''); }
                    $display = ($col['format'] === 'rupiah')
                        ? 'Rp ' . number_format($sum, 0, ',', '.')
                        : number_format($sum, 0, ',', '.');
                    $align = $col['align'] ?? 'right';
                    $html .= "<td class=\"align-{$align}\">{$display}</td>";
                } else {
                    $html .= '<td></td>';
                }
            }
            $html .= '</tr></tfoot>';
        }

        $html .= '</table></div>';
        return $html;
    };
@endphp

@if (! empty($banded))
{{-- ══════════════════════════════════════════════════════════════════════════
     PATH A — BANDED LAYOUT (B3 + B4)
     B4 additions:
       - header.repeat=true  → position:fixed top (running header every page) + padding-top on body
       - footerFixed elements → position:fixed bottom (running footer every page) + padding-bottom on body
     @page margins already applied via CSS @page rule above.
══════════════════════════════════════════════════════════════════════════ --}}

@php
    $hBand       = $headerBand ?? [];
    $hRepeat     = (bool) ($hBand['repeat'] ?? false);
    $hHeight     = (int) ($hBand['height'] ?? 180);
    $ffxBand     = $footerFixedBand ?? [];
    $ffxHeight   = (int) ($ffxBand['height'] ?? 0);
    $ffxHasEls   = count($ffxBand['elements'] ?? []) > 0;

    // B4: body padding reserves space so flow content never overlaps running bands.
    // padding-top only when header repeats; padding-bottom only when footerFixed has elements.
    $bodyPaddingTop    = $hRepeat    ? $hHeight  : 0;
    $bodyPaddingBottom = $ffxHasEls  ? $ffxHeight : 0;
@endphp

{{-- B4: inject body padding when running bands are active --}}
@if ($bodyPaddingTop > 0 || $bodyPaddingBottom > 0)
<style>
    body {
        @if ($bodyPaddingTop > 0) padding-top: {{ $bodyPaddingTop }}px; @endif
        @if ($bodyPaddingBottom > 0) padding-bottom: {{ $bodyPaddingBottom }}px; @endif
    }
</style>
@endif

{{-- ── B4: Footer-fixed running band (position:fixed bottom, every page) ── --}}
{{-- Rendered FIRST in DOM so DomPDF's fixed-positioning registers it before flow content --}}
@if ($ffxHasEls)
<div class="band-footer-fixed" style="height: {{ $ffxHeight }}px;">
    @foreach ($ffxBand['elements'] as $el)
        {!! $renderElement($el) !!}
    @endforeach
</div>
@endif

{{-- ── B4: Header-fixed running band (repeat=true → position:fixed top, every page) ── --}}
{{-- When repeat=false: rendered as first flow block (page 1 only, B3 behaviour). ── --}}
@if ($hRepeat)
<div class="band-header-fixed" style="height: {{ $hHeight }}px;">
    @foreach ($hBand['elements'] ?? [] as $el)
        {!! $renderElement($el) !!}
    @endforeach
</div>
@else
{{-- ── A1: Header band (page 1 only, flow) ── --}}
<div class="band-header" style="height: {{ $hHeight }}px;">
    @foreach ($hBand['elements'] ?? [] as $el)
        {!! $renderElement($el) !!}
    @endforeach
</div>
@endif

{{-- ── A2: Content — items table in normal flow (DomPDF paginates, thead repeats) ── --}}
@if (! empty($tableEl))
    {!! $renderItemsTable($tableEl, 'band-table-flow', 'width: 100%;') !!}
@endif

{{-- ── A3: Footer-flow band (follows last table row; page-break-inside:avoid) ── --}}
@php $ffBand = $footerFlowBand ?? []; @endphp
<div class="band-footer-flow" style="height: {{ (int) ($ffBand['height'] ?? 120) }}px;">
    @foreach ($ffBand['elements'] ?? [] as $el)
        {!! $renderElement($el) !!}
    @endforeach
</div>

@else
{{-- ══════════════════════════════════════════════════════════════════════════
     PATH B — LEGACY FLAT-ARRAY LAYOUT (Sprint 1–6, backward-compat)
══════════════════════════════════════════════════════════════════════════ --}}

{{-- ── Zone 1: Absolute header layer ── --}}
<div class="paper" style="height: {{ $legacyTableEl ? $tableY : 1122 }}px; overflow: {{ $legacyTableEl ? 'visible' : 'hidden' }};">
    @foreach ($headerEls as $el)
        {!! $renderElement($el) !!}
    @endforeach
</div>

{{-- ── Zone 2: Flow layer (only when a table element exists) ── --}}
@if ($legacyTableEl)
    @php
        $legacyTableStyle = "padding-left: {$legacyTableEl['x']}px; width: 793px; position: relative; z-index: {$legacyTableEl['_z']};";
    @endphp
    {!! $renderItemsTable($legacyTableEl, 'table-flow', $legacyTableStyle) !!}

    {{-- ── Zone 3: Below-zone (elements placed at y >= tableY) ── --}}
    @if (count($belowEls) > 0)
        <div class="below-flow" style="height: {{ $belowContainerHeight }}px;">
            @foreach ($belowEls as $el)
                @php $relTop = (int) ($el['y'] ?? 0) - $belowMinY; @endphp
                {!! $renderElement($el, $relTop) !!}
            @endforeach
        </div>
    @endif
@endif

@endif {{-- end banded / legacy branch --}}

</body>
</html>
