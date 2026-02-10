<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekap Pengajuan Dana - {{ $periodLabel }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a1a;
        }

        /* ===== PAGE LAYOUT ===== */
        .page {
            margin: 30px 35px 30px 40px;
        }

        /* ===== LETTERHEAD ===== */
        .letterhead {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 2.5px solid #1e3a5f;
        }

        .letterhead-logo-cell {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            padding-right: 16px;
        }

        .letterhead-logo-cell img {
            width: 70px;
            height: auto;
            display: block;
        }

        .letterhead-logo-placeholder {
            width: 70px;
            height: 70px;
            background: #e8f0fe;
            border: 1px solid #c5d5f5;
            display: table;
            text-align: center;
        }

        .letterhead-logo-placeholder span {
            display: table-cell;
            vertical-align: middle;
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .letterhead-info-cell {
            display: table-cell;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 9.5px;
            color: #444;
            margin-bottom: 1px;
        }

        .company-contact {
            font-size: 9.5px;
            color: #444;
        }

        .company-contact span {
            margin-right: 14px;
        }

        /* ===== DOCUMENT TITLE ===== */
        .doc-title-block {
            text-align: center;
            margin-bottom: 16px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-subtitle {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        .doc-underline {
            width: 60px;
            height: 3px;
            background: #1e3a5f;
            margin: 6px auto 0;
        }

        /* ===== PERIOD & META INFO ===== */
        .meta-block {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            background: #f5f7fa;
            border: 1px solid #dde3ec;
            padding: 8px 12px;
        }

        .meta-left {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .meta-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            text-align: right;
        }

        .meta-row {
            margin-bottom: 2px;
        }

        .meta-label {
            font-size: 9.5px;
            color: #666;
            display: inline-block;
            width: 90px;
        }

        .meta-value {
            font-size: 9.5px;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* ===== TABLE ===== */
        .fund-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 9.5px;
        }

        .fund-table thead tr {
            background: #1e3a5f;
            color: white;
        }

        .fund-table thead th {
            padding: 7px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9.5px;
            border: 1px solid #1e3a5f;
        }

        .fund-table thead th.center {
            text-align: center;
        }

        .fund-table tbody tr {
            background: #ffffff;
        }

        .fund-table tbody tr.alt {
            background: #f8fafd;
        }

        .fund-table tbody td {
            padding: 6px 6px;
            border: 1px solid #d8dde8;
            vertical-align: top;
            color: #222;
        }

        .fund-table tbody td.center {
            text-align: center;
        }

        .fund-table tbody td.right {
            text-align: right;
        }

        .fund-table tbody td.number {
            font-weight: bold;
            color: #1e3a5f;
            white-space: nowrap;
        }

        .fund-table tbody td.mono {
            font-family: 'Courier New', monospace;
            font-size: 9px;
        }

        /* Total row */
        .fund-table tfoot tr {
            background: #eef2f9;
        }

        .fund-table tfoot td {
            padding: 7px 6px;
            border: 1px solid #c5cfe0;
            font-weight: bold;
        }

        .fund-table tfoot td.right {
            text-align: right;
        }

        /* Checkmark / empty box */
        .check-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1.5px solid #555;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .check-box.checked {
            background: #1e7a4a;
            border-color: #1e7a4a;
            color: white;
        }

        /* ===== SIGNATURE SECTION ===== */
        .signature-section {
            margin-top: 32px;
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-title {
            font-size: 9.5px;
            color: #555;
            margin-bottom: 4px;
        }

        .signature-name-area {
            min-height: 60px;
            border-bottom: 1px solid #555;
            margin-bottom: 6px;
            position: relative;
        }

        .signature-img {
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            max-height: 50px;
            max-width: 120px;
        }

        .signature-name {
            font-size: 9.5px;
            font-weight: bold;
            color: #1a1a1a;
            text-align: center;
        }

        .signature-position {
            font-size: 9px;
            color: #666;
            text-align: center;
        }

        /* ===== FOOTER ===== */
        .page-footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            text-align: center;
            font-size: 8.5px;
            color: #888;
        }

        /* ===== STATUS BADGE TEXT ===== */
        .status-text {
            font-size: 8.5px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 3px;
        }

        .status-draft     { color: #555; background: #eee; }
        .status-pending   { color: #7a4f00; background: #fff3cd; }
        .status-approved  { color: #0a5c2e; background: #d4edda; }
        .status-rejected  { color: #721c24; background: #f8d7da; }
        .status-disbursed { color: #084298; background: #cfe2ff; }
    </style>
</head>

<body>
<div class="page">

    {{-- ===== LETTERHEAD ===== --}}
    <div class="letterhead">
        <div class="letterhead-logo-cell">
            @if ($company && $company->logo_base64)
                <img src="{{ $company->logo_base64 }}" alt="Logo">
            @else
                <div class="letterhead-logo-placeholder">
                    <span>{{ $company ? strtoupper(substr($company->name, 0, 2)) : 'CO' }}</span>
                </div>
            @endif
        </div>
        <div class="letterhead-info-cell">
            <div class="company-name">{{ $company ? strtoupper($company->name) : 'PERUSAHAAN' }}</div>
            @if ($company && $company->address)
                <div class="company-address">{{ $company->address }}</div>
            @endif
            <div class="company-contact">
                @if ($company && $company->phone)
                    <span>Telp: {{ $company->phone }}</span>
                @endif
                @if ($company && $company->email)
                    <span>Email: {{ $company->email }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== DOCUMENT TITLE ===== --}}
    <div class="doc-title-block">
        <div class="doc-title">Rekap Pengajuan Dana</div>
        <div class="doc-subtitle">Periode: {{ $periodLabel }}</div>
        <div class="doc-underline"></div>
    </div>

    {{-- ===== META INFO ===== --}}
    <div class="meta-block">
        <div class="meta-left">
            <div class="meta-row">
                <span class="meta-label">Periode</span>
                <span class="meta-value">: {{ $periodLabel }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">Total Pengajuan</span>
                <span class="meta-value">: {{ $fundRequests->count() }} pengajuan</span>
            </div>
            @if ($filterStatus)
                <div class="meta-row">
                    <span class="meta-label">Filter Status</span>
                    <span class="meta-value">: {{ $filterStatusLabel }}</span>
                </div>
            @endif
            @if ($filterRequestor)
                <div class="meta-row">
                    <span class="meta-label">Filter Pengaju</span>
                    <span class="meta-value">: {{ $filterRequestorName }}</span>
                </div>
            @endif
        </div>
        <div class="meta-right">
            <div class="meta-row">
                <span class="meta-label" style="width: auto;">Dicetak pada</span>
                <span class="meta-value">: {{ now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label" style="width: auto;">Dicetak oleh</span>
                <span class="meta-value">: {{ $printedBy }}</span>
            </div>
        </div>
    </div>

    {{-- ===== TABLE ===== --}}
    <table class="fund-table">
        <thead>
            <tr>
                <th style="width: 26px;" class="center">No</th>
                <th style="width: 72px;">Tgl Pengajuan</th>
                <th style="width: 120px;">No. Pengajuan</th>
                <th>Judul / Keterangan</th>
                <th style="width: 100px;" class="right">Nominal (Rp)</th>
                <th style="width: 44px;" class="center">Disetujui</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($fundRequests as $index => $req)
                <tr class="{{ $index % 2 === 1 ? 'alt' : '' }}">
                    {{-- No --}}
                    <td class="center">{{ $index + 1 }}</td>

                    {{-- Tanggal Pengajuan --}}
                    <td>{{ $req->created_at->format('d/m/Y') }}</td>

                    {{-- No. Pengajuan --}}
                    <td class="mono">{{ $req->request_number ?? '-' }}</td>

                    {{-- Judul / Keterangan --}}
                    <td>
                        <div style="font-weight: bold; margin-bottom: 1px;">{{ $req->title }}</div>
                        <div style="color: #555; font-size: 8.5px;">{{ Str::limit($req->purpose, 90) }}</div>
                        @if ($showRequestor)
                            <div style="color: #777; font-size: 8.5px; margin-top: 1px;">Pengaju: {{ $req->user->name }}</div>
                        @endif
                    </td>

                    {{-- Nominal --}}
                    <td class="right number">{{ number_format($req->total_amount, 0, ',', '.') }}</td>

                    {{-- Disetujui --}}
                    <td class="center">
                        @if (in_array($req->status, ['approved', 'disbursed']))
                            <span class="check-box checked">&#10003;</span>
                        @else
                            <span class="check-box"></span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center" style="padding: 20px; color: #888; font-style: italic;">
                        Tidak ada data pengajuan dana untuk periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="right" style="color: #1e3a5f;">
                    <strong>TOTAL</strong>
                </td>
                <td class="right" style="color: #1e3a5f; font-size: 10px;">
                    Rp {{ number_format($fundRequests->sum('total_amount'), 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- ===== SIGNATURE SECTION ===== --}}
    <div class="signature-section">
        {{-- Col 1: Dibuat oleh (printed by) --}}
        <div class="signature-col">
            <div class="signature-title">Dibuat oleh,</div>
            <div class="signature-name-area"></div>
            <div class="signature-name">{{ $printedBy }}</div>
            <div class="signature-position">{{ now()->format('d/m/Y') }}</div>
        </div>

        {{-- Col 2: Mengetahui (Finance Manager) --}}
        <div class="signature-col">
            <div class="signature-title">Mengetahui,</div>
            <div class="signature-name-area">
                @if ($company && $company->signature_base64)
                    <img src="{{ $company->signature_base64 }}" class="signature-img" alt="Tanda Tangan">
                @endif
            </div>
            <div class="signature-name">{{ $company && $company->finance_manager_name ? $company->finance_manager_name : '________________________' }}</div>
            <div class="signature-position">{{ $company && $company->finance_manager_position ? $company->finance_manager_position : 'Finance Manager' }}</div>
        </div>

        {{-- Col 3: Menyetujui (cap/stamp) --}}
        <div class="signature-col">
            <div class="signature-title">Menyetujui,</div>
            <div class="signature-name-area">
                @if ($company && $company->stamp_base64)
                    <img src="{{ $company->stamp_base64 }}" class="signature-img" alt="Stempel">
                @endif
            </div>
            <div class="signature-name">________________________</div>
            <div class="signature-position">Pimpinan</div>
        </div>
    </div>

    {{-- ===== PAGE FOOTER ===== --}}
    <div class="page-footer">
        Dokumen ini dicetak secara otomatis dari sistem &mdash;
        {{ $company ? $company->name : 'Perusahaan' }} &mdash; {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
</body>
</html>
