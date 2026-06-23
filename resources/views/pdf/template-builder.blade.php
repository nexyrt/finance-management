@php
    /** @var array $elements */
    // Editor & DomPDF sama-sama 96dpi → koordinat px dipakai langsung. A4 = 794x1123px.
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: 'Helvetica', Arial, sans-serif; }
        /* A4 @96dpi = 793,7 x 1122,5px. Pakai nilai DI BAWAH itu + overflow:hidden
           agar tidak meluap ~0,5pt ke halaman kedua. */
        .paper { position: relative; width: 793px; height: 1122px; overflow: hidden; background: #fff; }
        .el { position: absolute; }
        .text { white-space: nowrap; line-height: 1; }
    </style>
</head>
<body>
    <div class="paper">
        @foreach ($elements as $el)
            @if ($el['type'] === 'text')
                <div class="el text" style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; font-size: {{ $el['fontSize'] ?? 14 }}px; font-weight: {{ ($el['bold'] ?? false) ? 700 : 400 }}; color: {{ $el['color'] ?? '#0f172a' }};">{{ $el['content'] }}</div>
            @elseif ($el['type'] === 'image' && ! empty($el['src']))
                <img class="el" style="left: {{ $el['x'] }}px; top: {{ $el['y'] }}px; width: {{ $el['width'] ?? 160 }}px;" src="{{ $el['src'] }}">
            @endif
        @endforeach
    </div>
</body>
</html>
