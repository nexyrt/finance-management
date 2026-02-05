<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rincian Dana Kas Kantor - {{ $periodText }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
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
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header .logo {
            margin-bottom: 10px;
        }

        .header .logo img {
            height: 60px;
        }

        .header .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header .company-details {
            font-size: 9pt;
            line-height: 1.6;
        }

        /* Title */
        .report-title {
            text-align: center;
            margin: 25px 0 20px 0;
        }

        .report-title h1 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .report-title .period {
            font-size: 11pt;
            font-weight: normal;
            margin-top: 5px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }

        table thead {
            background-color: #f0f0f0;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }

        table th {
            font-weight: bold;
            text-align: center;
            background-color: #e0e0e0;
        }

        table td.center {
            text-align: center;
        }

        table td.right {
            text-align: right;
        }

        /* Column widths */
        .col-no {
            width: 5%;
            text-align: center;
        }

        .col-date {
            width: 12%;
            text-align: center;
        }

        .col-description {
            width: 33%;
        }

        .col-amount {
            width: 15%;
            text-align: right;
        }

        /* Special rows */
        .opening-balance {
            font-weight: bold;
            background-color: #f9f9f9;
            font-style: italic;
        }

        .row-debit td {
            background-color: #fff5f5;
        }

        .row-credit td {
            background-color: #f5fff5;
        }

        /* Negative balance */
        .negative {
            color: #d32f2f;
        }

        /* Total row */
        .total-row {
            font-weight: bold;
            background-color: #fff9c4 !important;
            border-top: 3px double #000 !important;
        }

        .total-row td {
            padding: 10px 8px;
            font-size: 10pt;
        }

        /* Signature section */
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box .title {
            font-size: 9pt;
            margin-bottom: 5px;
        }

        .signature-box .name {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 60px;
            text-decoration: underline;
        }

        .signature-box .position {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }

        .signature-image {
            height: 50px;
            margin: 10px auto;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
            color: #999;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        @if ($company->logo_path)
            <div class="logo">
                <img src="{{ public_path('storage/' . $company->logo_path) }}" alt="Logo">
            </div>
        @endif
        <div class="company-name">{{ $company->name }}</div>
        <div class="company-details">
            {{ $company->address }}<br>
            @if ($company->phone)
                {{ $company->phone }} -
            @endif
            {{ $company->email }}
        </div>
    </div>

    {{-- Title --}}
    <div class="report-title">
        <h1>RINCIAN DANA KAS KANTOR</h1>
        <div class="period">
            <strong>PERIODE: {{ strtoupper($periodText) }}</strong>
        </div>
        @if ($bankAccount)
            <div class="period" style="font-size: 10pt; margin-top: 5px;">
                Rekening: {{ $bankAccount->account_name }} ({{ $bankAccount->account_number }})
            </div>
        @endif
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-date">TANGGAL</th>
                <th class="col-description">URAIAN</th>
                <th class="col-amount">UANG MASUK</th>
                <th class="col-amount">PENGELUARAN</th>
                <th class="col-amount">SISA SALDO</th>
            </tr>
        </thead>
        <tbody>
            {{-- Opening Balance --}}
            <tr class="opening-balance">
                <td colspan="3" style="text-align: left; padding-left: 15px;">
                    <strong>SALDO AWAL (TANGGAL {{ $startDate->copy()->subDay()->format('d/m/Y') }})</strong>
                </td>
                <td class="right"><strong>Rp</strong></td>
                <td class="right"><strong>{{ number_format($openingBalance, 0, ',', '.') }}</strong></td>
                <td colspan="4" class="right"></td>
            </tr>

            {{-- Transactions --}}
            @php
                $runningBalance = $openingBalance;
                $rowNumber = 1;
                $totalCredit = 0;
                $totalDebit = 0;
            @endphp

            @foreach ($transactions as $transaction)
                @php
                    $runningBalance += $transaction['credit'];
                    $runningBalance -= $transaction['debit'];
                    $totalCredit += $transaction['credit'];
                    $totalDebit += $transaction['debit'];
                    $rowClass = $transaction['debit'] > 0 ? 'row-debit' : 'row-credit';
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="center">{{ $rowNumber }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ strtoupper($transaction['description']) }}</strong>
                        @if ($transaction['category'])
                            <br><small style="color: #666;">{{ $transaction['category'] }}</small>
                        @endif
                    </td>
                    @if ($transaction['credit'] > 0)
                        <td class="right">Rp</td>
                        <td class="right">{{ number_format($transaction['credit'], 0, ',', '.') }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                    @if ($transaction['debit'] > 0)
                        <td class="right">Rp</td>
                        <td class="right">{{ number_format($transaction['debit'], 0, ',', '.') }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                    <td class="right {{ $runningBalance < 0 ? 'negative' : '' }}">
                        @if ($runningBalance < 0)-@endif Rp
                    </td>
                    <td class="right {{ $runningBalance < 0 ? 'negative' : '' }}">
                        {{ number_format(abs($runningBalance), 0, ',', '.') }}
                    </td>
                </tr>
                @php $rowNumber++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            {{-- Total Row --}}
            <tr class="total-row">
                <td colspan="3" style="text-align: center;"><strong>Total:</strong></td>
                <td class="right"><strong>Rp</strong></td>
                <td class="right"><strong>{{ number_format($totalCredit, 0, ',', '.') }}</strong></td>
                <td class="right"><strong>Rp</strong></td>
                <td class="right"><strong>{{ number_format($totalDebit, 0, ',', '.') }}</strong></td>
                <td class="right" style="background-color: #ffeb3b !important;">
                    <strong>@if ($closingBalance < 0)-@endif Rp</strong>
                </td>
                <td class="right" style="background-color: #ffeb3b !important;">
                    <strong>{{ number_format(abs($closingBalance), 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-box">
            <div class="title"><em>Diketahui Oleh,</em></div>
            @if ($company->signature_path)
                <img src="{{ public_path('storage/' . $company->signature_path) }}" alt="Signature"
                    class="signature-image">
            @endif
            <div class="name">{{ $company->finance_manager_name ?? 'DEWI SEPTYANINGRUM' }}</div>
            <div class="position">{{ $company->finance_manager_position ?? 'Finance Manager' }}</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p style="font-style: italic; margin-top: 2px;">
            🤖 Generated with <a href="https://claude.com/claude-code" style="color: #999;">Claude Code</a>
        </p>
    </div>
</body>

</html>
