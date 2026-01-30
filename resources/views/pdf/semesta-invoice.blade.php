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
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 5px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company-address {
            font-size: 9px;
            line-height: 1.3;
        }

        /* Invoice Title */
        .invoice-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
            margin: 20px 0;
            letter-spacing: 1px;
        }

        /* Invoice Info Section */
        .invoice-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-left {
            width: 60%;
            padding-right: 20px;
        }

        .info-right {
            width: 40%;
            text-align: left;
        }

        .info-label {
            width: 100px;
            font-weight: normal;
        }

        .info-separator {
            width: 10px;
            text-align: center;
        }

        .info-value {
            font-weight: normal;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        .items-table td.center {
            text-align: center;
        }

        .items-table td.right {
            text-align: right;
        }

        .items-table td.number {
            width: 30px;
            text-align: center;
        }

        .items-table td.description {
            width: 45%;
        }

        .items-table td.qty {
            width: 80px;
            text-align: right;
        }

        .items-table td.unit {
            width: 40px;
            text-align: center;
            font-size: 9px;
        }

        .items-table td.price {
            width: 100px;
            text-align: right;
        }

        .items-table td.amount {
            width: 120px;
            text-align: right;
        }

        /* Summary Section */
        .summary-section {
            width: 100%;
            margin-top: 10px;
        }

        .summary-table {
            float: right;
            width: 45%;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 10px;
            font-size: 11px;
        }

        .summary-table .label {
            font-weight: bold;
            text-align: left;
            width: 60%;
        }

        .summary-table .amount {
            text-align: right;
            width: 40%;
        }

        .summary-table .highlight {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .summary-table .deduction {
            color: #d32f2f;
            font-style: italic;
        }

        .summary-table .total-row {
            border-top: 2px solid #000;
            border-bottom: 2px double #000;
            font-weight: bold;
            font-size: 12px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-section {
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            float: right;
            width: 200px;
            text-align: center;
        }

        .signature-box .city-date {
            margin-bottom: 60px;
        }

        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 20px;
        }

        .signature-box .position {
            font-size: 10px;
            margin-top: 2px;
        }

        .clear {
            clear: both;
        }

        /* Bank Info */
        .bank-info {
            margin-top: 20px;
            font-size: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .bank-info h4 {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .bank-info .bank-item {
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($company['logo_base64'])
            <div class="company-logo">
                <img src="{{ $company['logo_base64'] }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            @endif
            <div class="company-name">{{ strtoupper($company['name']) }}</div>
            <div class="company-address">
                {{ $company['address'] }}<br>
                Email: {{ $company['email'] }} | Phone: {{ $company['phone'] }}
            </div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">
            <i>{{ __('invoice.invoice') }}</i>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <table class="info-table">
                <tr>
                    <td class="info-left">
                        <table style="width: 100%;">
                            <tr>
                                <td class="info-label">{{ __('invoice.bill_to') }}</td>
                                <td class="info-separator">:</td>
                                <td class="info-value" style="font-weight: bold;">
                                    {{ strtoupper($client->type === 'company' ? $client->company_name : $client->name) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label"></td>
                                <td class="info-separator"></td>
                                <td class="info-value">
                                    {{ $client->address ?? '-' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="info-right">
                        <table style="width: 100%;">
                            <tr>
                                <td class="info-label">{{ __('invoice.invoice_number') }}</td>
                                <td class="info-separator">:</td>
                                <td class="info-value">{{ $invoice->invoice_number }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">{{ __('invoice.invoice_date') }}</td>
                                <td class="info-separator">:</td>
                                <td class="info-value">{{ $invoice->issue_date->format('d F Y') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px;">{{ __('invoice.no') }}</th>
                    <th>{{ __('invoice.description') }}</th>
                    <th style="width: 80px;">{{ __('invoice.qty') }}</th>
                    <th style="width: 40px;">{{ __('invoice.unit') }}</th>
                    <th style="width: 120px;">{{ __('invoice.unit_price') }}</th>
                    <th style="width: 140px;">{{ __('invoice.amount') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="number">{{ $index + 1 }}.</td>
                    <td class="description">
                        {{ $item->service_name }}
                        @if($item->is_tax_deposit)
                        <br><small style="font-style: italic; color: #666;">({{ __('invoice.tax_deposit') }})</small>
                        @endif
                    </td>
                    <td class="qty">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="unit">{{ $item->unit ?? 'M³' }}</td>
                    <td class="price">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="amount">
                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                        @if($company['is_pkp'])
                        <br><small style="font-size: 8px;">PPN ({{ $company['ppn_rate'] }}%)</small>
                        @endif
                    </td>
                </tr>
                @endforeach

                <!-- Empty rows for spacing (optional) -->
                @for($i = count($items); $i < 3; $i++)
                <tr>
                    <td class="number">&nbsp;</td>
                    <td class="description">&nbsp;</td>
                    <td class="qty">&nbsp;</td>
                    <td class="unit">&nbsp;</td>
                    <td class="price">&nbsp;</td>
                    <td class="amount">&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <table class="summary-table">
                <tr class="highlight">
                    <td class="label">{{ __('invoice.subtotal') }}</td>
                    <td class="amount">Rp {{ number_format($display_amount, 0, ',', '.') }}</td>
                </tr>

                @if(isset($pph22_amount) && $pph22_amount > 0)
                <tr class="deduction">
                    <td class="label">{{ __('invoice.pph_22') }} ({{ $company['pph22_rate'] }}%)</td>
                    <td class="amount">Rp {{ number_format($pph22_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($dp_amount && $dp_amount > 0)
                <tr class="deduction">
                    <td class="label">{{ __('invoice.down_payment') }} {{ $dp_percentage ?? '' }}</td>
                    <td class="amount">Rp {{ number_format($dp_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                @if($invoice->discount_amount > 0)
                <tr class="deduction">
                    <td class="label">
                        {{ __('invoice.discount') }}
                        @if($invoice->discount_type === 'percentage')
                        ({{ number_format($invoice->discount_value / 100, 2) }}%)
                        @endif
                    </td>
                    <td class="amount">Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</td>
                </tr>
                @endif

                <tr class="total-row">
                    <td class="label">{{ __('invoice.total') }}</td>
                    <td class="amount">Rp {{ number_format($grand_total, 0, ',', '.') }}</td>
                </tr>
            </table>
            <div class="clear"></div>
        </div>

        <!-- Bank Information -->
        @if(isset($company['bank_accounts']) && count($company['bank_accounts']) > 0)
        <div class="bank-info">
            <h4>{{ __('invoice.payment_to') }}:</h4>
            @foreach($company['bank_accounts'] as $bank)
            <div class="bank-item">
                <strong>{{ $bank['bank'] ?? '' }}</strong> -
                {{ $bank['account_number'] ?? '' }}
                a/n {{ $bank['account_name'] ?? '' }}
            </div>
            @endforeach
        </div>
        @endif

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="city-date">
                    Samarinda, {{ now()->format('d F Y') }}
                </div>
                @if($company['signature_base64'])
                <img src="{{ $company['signature_base64'] }}" alt="Signature" style="width: 100px; margin-bottom: -20px;">
                @endif
                <div class="name">{{ $company['signature']['name'] ?? '' }}</div>
                <div class="position">{{ $company['signature']['position'] ?? 'Finance Manager' }}</div>
                @if($company['stamp_base64'])
                <img src="{{ $company['stamp_base64'] }}" alt="Stamp" style="width: 60px; margin-top: -40px; margin-left: -80px; position: absolute;">
                @endif
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>

</html>
