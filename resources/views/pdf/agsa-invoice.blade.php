<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }

        /* Header Grid: 2 Columns */
        .header-grid {
            width: 100%;
            margin-bottom: 20px;
            display: table;
        }

        .header-grid-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 15px;
        }

        .header-grid-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 15px;
            text-align: right;
        }

        .logo {
            width: 350px;
            height: auto;
        }

        .company-info {
            font-size: 8pt;
            line-height: 1.5;
            text-align: right;
        }

        .client-name {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 5px;
        }

        .invoice-meta {
            font-weight: bold;
            font-size: 8pt;
            text-align: right;
        }

        .periode {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 10px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .items-table thead th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 11pt;
            font-weight: bold;
        }

        .items-table tbody td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11pt;
        }

        .items-table tbody tr:last-child td {
            border-bottom: 2px solid #1e3a8a;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Summary Box */
        .summary-box {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary-row {
            width: 100%;
            display: table;
            margin-bottom: 3px;
        }

        .summary-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 15px;
            font-size: 11pt;
        }

        .summary-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-size: 11pt;
        }

        .summary-row.bold .summary-label,
        .summary-row.bold .summary-value {
            font-weight: bold;
        }

        .summary-row.total {
            border-top: 2px solid #1e3a8a;
            padding-top: 5px;
            margin-top: 5px;
        }

        .summary-row.total .summary-label,
        .summary-row.total .summary-value {
            font-size: 12pt;
            font-weight: bold;
        }

        /* Bank Section */
        .bank-section {
            margin-bottom: 30px;
        }

        .bank-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 10px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-table thead th {
            background-color: #1e3a8a;
            color: #fff;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
            font-weight: bold;
        }

        .bank-table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10pt;
        }

        /* Signature */
        .signature-section {
            margin-top: 40px;
        }

        .signature-location {
            text-align: right;
            font-size: 11pt;
            margin-bottom: 15px;
        }

        .signature-box {
            width: 200px;
            float: right;
            text-align: center;
        }

        .signature-image {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }

        .stamp-image {
            position: absolute;
            right: 50px;
            margin-top: -60px;
            max-width: 100px;
            height: auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11pt;
            text-decoration: underline;
        }

        .signature-position {
            font-size: 11pt;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Grid: 2 Columns -->
        <div class="header-grid">
            <!-- Left Column: Logo + Client + Periode -->
            <div class="header-grid-left">
                <!-- Logo -->
                <div style="margin-bottom: 20px;">
                    @if (!empty($company['logo_base64']))
                        <img src="{{ $company['logo_base64'] }}" class="logo" alt="Logo">
                    @else
                        {{-- Placeholder for design review --}}
                        <div
                            style="width: 350px; height: 80px; background: #e5e7eb; display: flex; align-items: center; justify-content: center; border: 1px solid #ccc;">
                            <span style="color: #666; font-size: 18pt; font-weight: bold;">AGSA LOGO</span>
                        </div>
                    @endif
                </div>

                <!-- Client Info -->
                <div style="margin-bottom: 20px;">
                    <div style="display: table; width: 100%;">
                        <div style="display: table-row;">
                            <div
                                style="display: table-cell; width: 30px; vertical-align: top; padding-right: 5px; font-size: 8pt;">
                                To:</div>
                            <div style="display: table-cell; vertical-align: top;">
                                <div class="client-name">PT. GANDA ALAM MAKMUR</div>
                            </div>
                        </div>
                        <div style="display: table-row;">
                            <div style="display: table-cell;"></div>
                            <div style="display: table-cell; vertical-align: top;">
                                <div style="font-size: 8pt;">DI SEMPAYAU</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Periode -->
                <div class="periode">Periode 21 SEPTEMBER 2025 - 20 OKTOBER 2025</div>
            </div>

            <!-- Right Column: Company Info + Invoice Meta -->
            <div class="header-grid-right">
                <!-- Company Info -->
                <div class="company-info" style="margin-bottom: 30px;">
                    Jalan AW Syahranie Perum Villa Tamara Blok L No. 9<br>
                    Samarinda, Kalimantan Timur - Indonesia<br>
                    0813 1177 1117<br>
                    <a href="/cdn-cgi/l/email-protection" class="__cf_email__"
                        data-cfemail="5d3c3a2f3c2d3c333c2e3c29243c3c3f3c3934732d291d3a303c3431733e3230">[email&#160;protected]</a>
                </div>

                <!-- Invoice Meta -->
                <div class="invoice-meta">
                    <div style="display: table; width: 100%;">
                        <div style="display: table-row;">
                            <div
                                style="display: table-cell; width: 110px; vertical-align: top; padding-right: 5px; font-size: 8pt;">
                                INVOICE NO.</div>
                            <div
                                style="display: table-cell; width: 10px; vertical-align: top; padding-right: 5px; font-size: 8pt;">
                                :</div>
                            <div style="display: table-cell; vertical-align: top; font-size: 8pt;">
                                075/AGSA-GAM/INVOICE/X/2025</div>
                        </div>
                        <div style="display: table-row;">
                            <div style="display: table-cell; padding-right: 5px; font-size: 8pt;">DATE</div>
                            <div style="display: table-cell; padding-right: 5px; font-size: 8pt;">:</div>
                            <div style="display: table-cell; font-size: 8pt;">21 Oktober 2025</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO.</th>
                    <th style="width: 70%;">DESCRIPTION</th>
                    <th style="width: 25%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-left">{{ $item->service_name }}</td>
                        <td class="text-right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Box -->
        <div class="summary-box">
            @php
                $subtotalI = $invoice->subtotal;
                $dpp = $subtotalI * 0.029;
                $ppn = $dpp * 0.12;
                $subtotalII = $subtotalI + $dpp + $ppn;
                $pph23 = $subtotalI * 0.02;
                $grandTotal = $subtotalII - $pph23;
            @endphp

            <div class="summary-row">
                <div class="summary-label">Sub Total I</div>
                <div class="summary-value">Rp {{ number_format($subtotalI, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">DPP (2,9%)</div>
                <div class="summary-value">Rp {{ number_format($dpp, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">PPN (12%)</div>
                <div class="summary-value">Rp {{ number_format($ppn, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row bold">
                <div class="summary-label">Sub Total II</div>
                <div class="summary-value">Rp {{ number_format($subtotalII, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row">
                <div class="summary-label">PPh 23 (2%)</div>
                <div class="summary-value">Rp {{ number_format($pph23, 0, ',', '.') }}</div>
            </div>

            <div class="summary-row total">
                <div class="summary-label">GRAND TOTAL</div>
                <div class="summary-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="bank-section">
            <div class="bank-title">Please Remit To :</div>
            <table class="bank-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama Perusahaan</th>
                        <th style="width: 20%;">Rekening Bank</th>
                        <th style="width: 25%;">Keterangan</th>
                        <th style="width: 20%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $biayaTenagaKerja = $subtotalI + $dpp + $ppn - $pph23;
                        $biayaOperasional = 0;
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>PT AGRAPANA SATYA ABADI</td>
                        <td>1234567890 (BCA)</td>
                        <td>Biaya Tenaga Kerja</td>
                        <td class="text-right">Rp {{ number_format($biayaTenagaKerja, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>PT AGRAPANA SATYA ABADI</td>
                        <td>0987654321 (Mandiri)</td>
                        <td>Biaya Operasional</td>
                        <td class="text-right">Rp {{ number_format($biayaOperasional, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right" style="font-weight: bold;">TOTAL</td>
                        <td class="text-right" style="font-weight: bold;">Rp
                            {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-location">
                SAMARINDA, 21-Okt-25
            </div>

            <div class="signature-box">
                @if (!empty($company['signature_base64']))
                    <img src="{{ $company['signature_base64'] }}" class="signature-image" alt="Signature">
                @else
                    {{-- Placeholder for signature --}}
                    <div style="height: 80px;"></div>
                @endif

                @if (!empty($company['stamp_base64']))
                    <img src="{{ $company['stamp_base64'] }}" class="stamp-image" alt="Stamp">
                @endif

                <div class="signature-name">DEDDY PUTRA PRATAMA, SE</div>
