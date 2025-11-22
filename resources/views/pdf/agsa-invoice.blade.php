<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
            padding-right: 10px;
        }

        .company-info {
            font-size: 12pt;
            line-height: 1.4;
        }

        /* Billing Section */
        .billing-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .billing-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .billing-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        .client-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .invoice-meta {
            font-size: 11pt;
            line-height: 1.5;
        }

        /* Periode */
        .periode {
            font-weight: bold;
            font-size: 11pt;
            margin: 15px 0 10px 0;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .items-table th {
            background: #FFD966;
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11pt;
            vertical-align: top;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Summary Box */
        .summary-box {
            border: 1px solid #000;
            border-top: none;
            padding: 8px;
            font-size: 11pt;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .summary-label {
            display: table-cell;
            padding-right: 10px;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            padding-right: 10px;
        }

        .summary-amount {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }

        .pph-negative {
            color: #C00000;
        }

        .grand-total {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 3px;
        }

        /* Terbilang */
        .terbilang {
            font-style: italic;
            font-size: 11pt;
            margin: 10px 0;
            padding: 5px;
            background: #f5f5f5;
        }

        /* Bank Table */
        .bank-section {
            margin: 15px 0;
        }

        .bank-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .bank-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bank-table th {
            background: #f5f5f5;
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }

        .bank-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11pt;
        }

        /* Signature */
        .signature-section {
            margin-top: 30px;
            text-align: center;
        }

        .signature-location {
            text-align: right;
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            border: 2px solid #000;
            padding: 60px 100px 15px 100px;
            margin-top: 10px;
            position: relative;
        }

        .signature-image {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: auto;
        }

        .signature-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .signature-position {
            font-size: 11pt;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if (!empty($company['logo_base64']))
                    <img src="{{ $company['logo_base64'] }}" class="logo" alt="Logo">
                @endif
            </div>
            <div class="header-right">
                <div class="company-info">
                    {{ $company['address'] }}<br>
                    {{ $company['phone'] }}<br>
                    {{ $company['email'] }}
                </div>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="billing-section">
            <div class="billing-left">
                <div style="font-size: 11pt; margin-bottom: 3px;">To:</div>
                <div class="client-name">{{ strtoupper($client->name) }}</div>
                <div style="font-size: 11pt;">{{ strtoupper($client->address ?? 'DI SEMPAYAU') }}</div>
            </div>
            <div class="billing-right">
                <div class="invoice-meta">
                    INVOICE NO. : {{ $invoice->invoice_number }}<br>
                    DATE : {{ $invoice->issue_date->format('d F Y') }}
                </div>
            </div>
        </div>

        <!-- Periode -->
        @if (!empty($periode))
            <div class="periode">Periode {{ $periode }}</div>
        @endif

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
                <div class="summary-label">I</div>
                <div class="summary-value">Sub total I :</div>
                <div class="summary-amount">Rp {{ number_format($subtotalI, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">II</div>
                <div class="summary-value">DPP</div>
                <div class="summary-amount">Rp {{ number_format($dpp, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">III</div>
                <div class="summary-value">PPN</div>
                <div class="summary-amount">Rp {{ number_format($ppn, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">IV</div>
                <div class="summary-value">Sub total II :</div>
                <div class="summary-amount">Rp {{ number_format($subtotalII, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">V</div>
                <div class="summary-value">PPh ps 23 (2%) :</div>
                <div class="summary-amount pph-negative">Rp ({{ number_format($pph23, 0, ',', '.') }})</div>
            </div>
            <div class="summary-row grand-total">
                <div class="summary-label">VI</div>
                <div class="summary-value">Grand Total :</div>
                <div class="summary-amount">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>

            <div class="terbilang">
                Saya : <strong>{{ $terbilang }} Rupiah</strong>
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
                        $biayaOperasional = 0; // Sesuaikan jika ada
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $company['bank_accounts'][0]['account_name'] ?? $company['name'] }}</td>
                        <td>{{ $company['bank_accounts'][0]['account_number'] ?? '' }}</td>
                        <td>Biaya Tenaga Kerja</td>
                        <td class="text-right">Rp {{ number_format($biayaTenagaKerja, 0, ',', '.') }}</td>
                    </tr>
                    @if (count($company['bank_accounts']) > 1)
                        <tr>
                            <td class="text-center">2</td>
                            <td>{{ $company['bank_accounts'][1]['account_name'] ?? $company['name'] }}</td>
                            <td>{{ $company['bank_accounts'][1]['account_number'] ?? '' }}</td>
                            <td>Biaya Operasional</td>
                            <td class="text-right">Rp {{ number_format($biayaOperasional, 0, ',', '.') }}</td>
                        </tr>
                    @endif
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
                {{ strtoupper($company['city'] ?? 'SEMPAYAU') }}, {{ $invoice->issue_date->format('d-M-y') }}
            </div>

            <div class="signature-box">
                @if (!empty($company['signature_base64']))
                    <img src="{{ $company['signature_base64'] }}" class="signature-image" alt="Signature">
                @endif

                <div class="signature-name">{{ $company['signature']['name'] ?? 'Nama Penandatangan' }}</div>
                <div class="signature-position">{{ $company['signature']['position'] ?? 'HR Foreman' }}</div>
            </div>
        </div>
    </div>
</body>

</html>
