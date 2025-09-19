<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" href="{{ asset('images/kisantra.png') }}" type="image/png">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 15px;
        }

        /* Header dengan letterhead image */
        .header {
            background: white;
            margin: -15px -20px 10px -15px;
            padding: 0;
            text-align: center;
            width: 100%;
        }

        .letterhead-container {
            width: 100%;
            text-align: center;
            padding-left: 10px;
            padding-top: 10px;
        }

        .letterhead-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Invoice Title */
        .invoice-title {
            background: #42b2cc;
            color: white;
            text-align: center;
            padding: 0 12px;
            margin: 0px -20px 20px -15px;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Main Content */
        .main-content {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .left-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .right-section {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #42b2cc;
        }

        .client-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .info-grid {
            display: table;
            width: 100%;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            padding: 8px;
            font-weight: bold;
            background: #e5e7eb;
            border: 1px solid #d1d5db;
            width: 40%;
        }

        .info-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #d1d5db;
        }

        /* Items Table - Default spacing */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background: #42b2cc;
            color: white;
            border: 2px solid #36a3bd;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            line-height: 1.2;
        }

        .items-table td {
            border: 1px solid #374151;
            padding: 10px 8px;
            text-align: center;
            font-size: 15px;
            line-height: 1.3;
            vertical-align: middle;
        }

        /* Compact spacing only for multiple clients */
        .compact-table th {
            padding: 6px 4px;
            font-size: 13px;
        }

        .compact-table td {
            padding: 5px 4px;
            font-size: 13px;
        }

        .items-table .text-left {
            text-align: left;
        }

        .items-table .text-right {
            text-align: right;
        }

        /* Client cell styling */
        .client-cell {
            font-size: 11px;
            font-weight: bold;
            color: #42b2cc;
            text-align: left;
            padding: 3px 4px;
        }

        /* Tax deposit styling */
        .tax-deposit-row {
            background: #fef3cd !important;
        }

        .tax-deposit-label {
            background: #f59e0b;
            color: white;
            font-size: 9px;
            padding: 1px 4px;
            border-radius: 2px;
            font-weight: bold;
            margin-left: 5px;
        }

        /* Currency formatting */
        .currency-cell {
            display: table;
            width: 100%;
        }

        .currency-left {
            display: table-cell;
            text-align: left;
            width: 30%;
            font-size: 13px;
        }

        .currency-right {
            display: table-cell;
            text-align: right;
            width: 70%;
            font-size: 13px;
        }

        .compact-table .currency-left {
            font-size: 11px;
        }

        .compact-table .currency-right {
            font-size: 11px;
        }

        /* Total Section */
        .total-section {
            width: 100%;
            margin-bottom: 20px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .total-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            color: black;
            border-top: 1px solid #374151;
            border-bottom: 3px double #374151;
            font-style: italic;
        }

        .total-value {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            background: #42b2cc;
            color: white;
            border: 1px solid #42b2cc;
            font-style: italic;
        }

        .total-value-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        /* Footer Section Grid */
        .footer-section {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .grand-total-section {
            margin-bottom: 20px;
        }

        .grand-total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .grand-total-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px;
            background: #d1d5db;
            color: black;
            border: 1px solid #d1d5db;
            font-size: 16px;
        }

        .grand-total-value {
            display: table-cell;
            font-weight: bold;
            padding: 3px;
            background: #d1d5db;
            color: black;
            border: 1px solid #d1d5db;
            text-align: right;
            font-size: 16px;
        }

        .signature-section {
            text-align: center;
        }

        .payment-method {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #42b2cc;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #42b2cc;
        }

        .bank-info {
            font-size: 15px;
            line-height: 1.4;
        }

        .terbilang {
            font-style: italic;
            margin: 10px 0;
            padding: 8px;
            background: #f8f9fa;
            border-left: 3px solid #42b2cc;
        }

        .signature-box {
            padding: 60px 20px 20px 20px;
            margin-top: 20px;
            position: relative;
        }

        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .signature-position {
            font-size: 15px;
            color: #666;
        }

        .signature-image {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: auto;
        }

        .company-stamp {
            position: absolute;
            left: 210px;
            top: -25px;
            width: 120px;
            height: auto;
            opacity: 0.6;
        }

        /* Tax info note */
        .tax-info {
            background: #fef3cd;
            border: 1px solid #f59e0b;
            border-left: 4px solid #f59e0b;
            padding: 8px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #92400e;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header dengan Letterhead -->
        <div class="header">
            <div class="letterhead-container">
                @if ($company['logo_base64'])
                    <img src="{{ $company['logo_base64'] }}" class="letterhead-image"
                        alt="PT. Kinara Sadayatra Nusantara Letterhead">
                @else
                    <div style="padding: 20px; text-align: center; border: 2px dashed #42b2cc; color: #42b2cc;">
                        PT. KINARA SADAYATRA NUSANTARA<br>
                        LETTERHEAD PLACEHOLDER
                    </div>
                @endif
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">INVOICE</div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="left-section">
                <div class="section-title">TAGIHAN KEPADA :</div>
                <div class="client-name">{{ strtoupper($client->name) }}</div>

                @php
                    $uniqueClients = $items->pluck('client.name')->unique();
                    $hasMultipleClients = $uniqueClients->count() > 1;
                @endphp

                @if ($hasMultipleClients)
                    <div style="margin-top: 10px; font-size: 12px; color: #666; font-style: italic;">
                        * Invoice ini mencakup tagihan untuk {{ $uniqueClients->count() }} klien
                    </div>
                @endif

                @if ($financial_summary['has_tax_deposits'])
                    <div style="margin-top: 10px; font-size: 12px; color: #f59e0b; font-style: italic;">
                        * Termasuk titipan pajak sebesar Rp {{ number_format($financial_summary['tax_deposits_total'], 0, ',', '.') }}
                    </div>
                @endif
            </div>

            <div class="right-section">
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">TANGGAL</div>
                        <div class="info-value">{{ $invoice->issue_date->format('d M Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">No. INVOICE</div>
                        <div class="info-value">{{ $invoice->invoice_number }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">TERMIN</div>
                        <div class="info-value">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">DUE DATE</div>
                        <div class="info-value">{{ $invoice->due_date->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table {{ $hasMultipleClients ? 'compact-table' : '' }}">
            <thead>
                <tr>
                    <th style="width: 6%;">NO</th>
                    @if ($hasMultipleClients)
                        <th style="width: 18%;">KLIEN</th>
                        <th style="width: 38%;">DESKRIPSI PEKERJAAN</th>
                    @else
                        <th style="width: 56%;">DESKRIPSI PEKERJAAN</th>
                    @endif
                    <th style="width: 8%;">QTY</th>
                    <th style="width: 15%;">BIAYA SATUAN</th>
                    <th style="width: 15%;">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedItems = [];
                    $currentClient = null;
                    $currentGroup = [];

                    foreach ($items as $item) {
                        $clientName = $item->client->name ?? 'N/A';

                        if ($currentClient === null || $currentClient === $clientName) {
                            $currentClient = $clientName;
                            $currentGroup[] = $item;
                        } else {
                            $groupedItems[] = [
                                'client' => $currentClient,
                                'items' => $currentGroup,
                                'count' => count($currentGroup),
                            ];
                            $currentClient = $clientName;
                            $currentGroup = [$item];
                        }
                    }

                    if (!empty($currentGroup)) {
                        $groupedItems[] = [
                            'client' => $currentClient,
                            'items' => $currentGroup,
                            'count' => count($currentGroup),
                        ];
                    }

                    $rowIndex = 1;
                @endphp

                @foreach ($groupedItems as $group)
                    @foreach ($group['items'] as $itemIndex => $item)
                        <tr class="{{ $item->is_tax_deposit ? 'tax-deposit-row' : '' }}">
                            <td>{{ $rowIndex }}</td>
                            @if ($hasMultipleClients)
                                @if ($itemIndex === 0)
                                    <td class="client-cell" rowspan="{{ $group['count'] }}"
                                        style="vertical-align: middle; border-right: 2px solid #42b2cc; font-weight: bold; color: #42b2cc;">
                                        {{ $group['client'] }}
                                    </td>
                                @endif
                            @endif
                            <td class="text-left">
                                {{ $item->service_name }}
                                @if ($item->is_tax_deposit)
                                    <span class="tax-deposit-label">TITIPAN PAJAK</span>
                                @endif
                            </td>
                            <td>{{ number_format($item->quantity) }}</td>
                            <td>
                                <div class="currency-cell">
                                    <div class="currency-left">IDR</div>
                                    <div class="currency-right">{{ number_format($item->unit_price, 0, ',', '.') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="currency-cell">
                                    <div class="currency-left">IDR</div>
                                    <div class="currency-right">{{ number_format($item->amount, 0, ',', '.') }}</div>
                                </div>
                            </td>
                        </tr>
                        @php $rowIndex++; @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <!-- Footer Section Grid -->
        <div class="footer-section">
            <div class="footer-left">
                <!-- Payment Methods -->
                @foreach ($company['bank_accounts'] as $index => $bank)
                    <div class="payment-method">
                        <div class="payment-title">Metode Pembayaran #{{ $index + 1 }}</div>
                        <div class="bank-info">
                            <strong>Bank:</strong> {{ $bank['bank'] }}<br>
                            <strong>No. Rek:</strong> {{ $bank['account_number'] }}<br>
                            <strong>Atas Nama:</strong> {{ $bank['account_name'] }}
                        </div>
                    </div>
                @endforeach

                <!-- Terbilang -->
                <div class="terbilang">
                    Terbilang:<br>
                    <strong>{{ $terbilang }} Rupiah</strong>
                </div>

                <!-- Tax Information -->
                <div class="tax-info">
                    <strong>Catatan:</strong> PPh Final 0,5% sesuai PP No. 23/2018 untuk UMKM
                    @if ($financial_summary['has_tax_deposits'])
                        <br><strong>*</strong> Titipan pajak tidak termasuk dalam perhitungan PPh Final
                    @endif
                </div>
            </div>

            <div class="footer-right">
                @php
                    // Use net revenue (excluding tax deposits) for tax calculations
                    $netRevenue = $financial_summary['net_revenue'];
                    $netRevenueAfterDiscount = $netRevenue - ($invoice->discount_amount ?? 0);
                    $pph05Percent = $netRevenueAfterDiscount * 0.005;
                    $dpp = $netRevenueAfterDiscount - $pph05Percent;
                @endphp

                <!-- Subtotal Breakdown -->
                @if ($financial_summary['has_tax_deposits'])
                    <div class="grand-total-section">
                        <div class="grand-total-row">
                            <div class="grand-total-label">SUBTOTAL LAYANAN</div>
                            <div class="grand-total-value">IDR {{ number_format($netRevenue, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    
                    <div class="grand-total-section">
                        <div class="grand-total-row">
                            <div class="grand-total-label">TITIPAN PAJAK</div>
                            <div class="grand-total-value">IDR {{ number_format($financial_summary['tax_deposits_total'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Discount Section -->
                @if ($invoice->discount_amount > 0)
                    <div class="grand-total-section">
                        <div class="grand-total-row">
                            <div class="grand-total-label">DISKON</div>
                            <div class="grand-total-value">IDR -{{ number_format($invoice->discount_amount, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endif

                <!-- DPP (based on net revenue after discount) -->
                <div class="grand-total-section">
                    <div class="grand-total-row">
                        <div class="grand-total-label">DPP</div>
                        <div class="grand-total-value">IDR {{ number_format($dpp, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- PP 55 (0.5% from net revenue) -->
                <div class="grand-total-section">
                    <div class="grand-total-row">
                        <div class="grand-total-label">PP 55 (0,5%)</div>
                        <div class="grand-total-value">IDR {{ number_format($pph05Percent, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Grand Total -->
                <div class="grand-total-section">
                    <div class="grand-total-row">
                        <div class="grand-total-label">GRAND TOTAL</div>
                        <div class="grand-total-value">IDR {{ number_format($netRevenueAfterDiscount, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Jumlah Ditagih (full invoice amount including tax deposits) -->
                <div class="total-section">
                    <div class="total-row">
                        <div class="total-label">JUMLAH DITAGIH</div>
                        <div class="total-value">
                            <div class="total-value-content">
                                <span>IDR</span>
                                <span>{{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Signature Section -->
                <div class="signature-section">
                    <div style="font-weight: bold; margin-bottom: 10px;">PT. KINARA SADAYATRA NUSANTARA</div>

                    <div class="signature-box">
                        @if ($company['signature_base64'])
                            <img src="{{ $company['signature_base64'] }}" class="signature-image" alt="Signature">
                        @endif

                        @if ($company['stamp_base64'])
                            <img src="{{ $company['stamp_base64'] }}" class="company-stamp" alt="Company Stamp">
                        @endif

                        <div class="signature-name">{{ $company['signature']['name'] ?? 'Nama Penandatangan' }}</div>
                        <div class="signature-position">{{ $company['signature']['position'] ?? 'Direktur' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>