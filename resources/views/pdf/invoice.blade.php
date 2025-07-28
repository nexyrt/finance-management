<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: right;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 11px;
            color: #666;
            line-height: 1.4;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 14px;
            font-weight: bold;
            color: #3b82f6;
            font-family: monospace;
        }

        /* Invoice Info Section */
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .invoice-info-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-title {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content {
            font-size: 12px;
            line-height: 1.4;
        }

        .client-type {
            display: inline-block;
            background: #f3f4f6;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            color: #666;
            margin-left: 8px;
        }

        /* Status Badge */
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft { 
            background: #f3f4f6; 
            color: #6b7280; 
        }
        
        .status-sent { 
            background: #dbeafe; 
            color: #2563eb; 
        }
        
        .status-paid { 
            background: #dcfce7; 
            color: #16a34a; 
        }
        
        .status-partially_paid { 
            background: #fef3c7; 
            color: #d97706; 
        }
        
        .status-overdue { 
            background: #fee2e2; 
            color: #dc2626; 
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #374151;
        }

        .items-table td {
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            font-size: 11px;
            vertical-align: top;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .client-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }

        .client-individual { 
            background: #3b82f6; 
        }
        
        .client-company { 
            background: #8b5cf6; 
        }

        /* Summary Section */
        .summary {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            width: 60%;
            font-size: 12px;
            color: #666;
            padding-right: 20px;
        }

        .summary-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }

        .summary-total {
            border-top: 2px solid #3b82f6;
            padding-top: 10px;
            margin-top: 10px;
        }

        .summary-total .summary-label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .summary-total .summary-value {
            font-size: 16px;
            color: #3b82f6;
        }

        /* Payments Section */
        .payments-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .payments-table th {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            color: #166534;
        }

        .payments-table td {
            border: 1px solid #bbf7d0;
            padding: 8px;
            font-size: 11px;
        }

        .payment-total-row {
            background: #f0fdf4;
            font-weight: bold;
        }

        .payment-remaining-row {
            background: #fef2f2;
            font-weight: bold;
        }

        /* Payment Instructions */
        .payment-instructions {
            margin-top: 30px;
            padding: 15px;
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 8px;
        }

        .payment-instructions-title {
            font-size: 13px;
            font-weight: bold;
            color: #0369a1;
            margin-bottom: 10px;
        }

        .payment-instructions-content {
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }

        /* Notes Section */
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background: #fafafa;
            border-left: 4px solid #3b82f6;
        }

        .notes-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }

        .notes-content {
            font-size: 11px;
            line-height: 1.5;
            color: #666;
        }

        /* Terms & Conditions */
        .terms-section {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            line-height: 1.4;
        }

        .terms-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .terms-list {
            margin-left: 15px;
            list-style-type: disc;
        }

        .terms-list li {
            margin-bottom: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }

        /* Utility Classes */
        .font-bold { 
            font-weight: bold; 
        }
        
        .text-center { 
            text-align: center; 
        }
        
        .text-right { 
            text-align: right; 
        }

        .mb-5 { 
            margin-bottom: 5px; 
        }

        /* Print Specific */
        @media print {
            body { 
                margin: 0; 
            }
            .container { 
                padding: 15px; 
            }
            .no-print { 
                display: none; 
            }
        }

        /* Page Break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Company Header --}}
        <div class="header">
            <div class="header-left">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-info">
                    {{ $company['address'] }}<br>
                    Tel: {{ $company['phone'] }} | Email: {{ $company['email'] }}<br>
                    Website: {{ $company['website'] }}
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div style="margin-top: 10px;">
                    <span class="status status-{{ $invoice->status }}">
                        @switch($invoice->status)
                            @case('draft') 
                                Draft 
                                @break
                            @case('sent') 
                                Terkirim 
                                @break
                            @case('paid') 
                                Lunas 
                                @break
                            @case('partially_paid') 
                                Sebagian 
                                @break
                            @case('overdue') 
                                Terlambat 
                                @break
                            @default
                                {{ ucfirst($invoice->status) }}
                        @endswitch
                    </span>
                </div>
            </div>
        </div>

        {{-- Invoice Information --}}
        <div class="invoice-info">
            <div class="invoice-info-left">
                {{-- Client Information --}}
                <div class="info-section">
                    <div class="info-title">Tagihan Kepada:</div>
                    <div class="info-content">
                        <strong>{{ $client->name }}</strong>
                        <span class="client-type">
                            {{ $client->type === 'individual' ? 'Individu' : 'Perusahaan' }}
                        </span>
                        
                        @if($options['show_client_details'])
                            <br><br>
                            @if($client->email)
                                <strong>Email:</strong> {{ $client->email }}<br>
                            @endif
                            @if($client->NPWP)
                                <strong>NPWP:</strong> {{ $client->NPWP }}<br>
                            @endif
                            @if($client->KPP)
                                <strong>KPP:</strong> {{ $client->KPP }}<br>
                            @endif
                            @if($client->address)
                                <strong>Alamat:</strong><br>{{ $client->address }}<br>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="invoice-info-right">
                {{-- Invoice Dates & Details --}}
                <div class="info-section">
                    <div class="info-title">Informasi Invoice:</div>
                    <div class="info-content">
                        <strong>Tanggal Invoice:</strong> {{ $invoice->issue_date->format('d F Y') }}<br>
                        <strong>Jatuh Tempo:</strong> {{ $invoice->due_date->format('d F Y') }}<br>
                        <strong>Total Item:</strong> {{ $items->count() }} item<br>
                        <strong>Status:</strong> 
                        @switch($invoice->status)
                            @case('draft') 
                                Draft
                                @break
                            @case('sent') 
                                Terkirim
                                @break
                            @case('paid') 
                                Lunas
                                @break
                            @case('partially_paid') 
                                Dibayar Sebagian
                                @break
                            @case('overdue') 
                                Terlambat
                                @break
                        @endswitch
                        <br>
                        
                        @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                            <br>
                            <span style="color: #dc2626; font-weight: bold;">
                                ⚠️ Terlambat: {{ $invoice->due_date->diffInDays(now()) }} hari
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Klien</th>
                    <th style="width: 35%;">Layanan/Produk</th>
                    <th style="width: 8%;" class="text-center">Qty</th>
                    <th style="width: 12%;" class="text-right">Harga Satuan</th>
                    <th style="width: 15%;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <span class="client-indicator {{ $item->client->type === 'individual' ? 'client-individual' : 'client-company' }}"></span>
                            <strong>{{ $item->client->name }}</strong>
                            <br>
                            <small style="color: #666;">
                                {{ $item->client->type === 'individual' ? 'Individu' : 'Perusahaan' }}
                            </small>
                        </td>
                        <td>
                            <strong>{{ $item->service_name }}</strong>
                        </td>
                        <td class="text-center">{{ number_format($item->quantity) }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Invoice Summary --}}
        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Subtotal ({{ $items->count() }} item):</div>
                <div class="summary-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
            </div>
            
            @if($invoice->discount_amount > 0)
                <div class="summary-row">
                    <div class="summary-label">
                        Diskon 
                        @if($invoice->discount_type === 'percentage')
                            ({{ number_format($invoice->discount_value / 100, 1) }}%)
                        @else
                            (Tetap)
                        @endif:
                        @if($invoice->discount_reason)
                            <br><small style="color: #666; font-style: italic;">{{ $invoice->discount_reason }}</small>
                        @endif
                    </div>
                    <div class="summary-value" style="color: #d97706;">
                        -Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}
                    </div>
                </div>
            @endif
            
            <div class="summary-row summary-total">
                <div class="summary-label">TOTAL INVOICE:</div>
                <div class="summary-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Payment History --}}
        @if($options['show_payments'] && $payments->count() > 0)
            <div class="payments-section">
                <div class="info-title">Riwayat Pembayaran:</div>
                <table class="payments-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Metode Pembayaran</th>
                            <th>Bank/Rekening</th>
                            <th>No. Referensi</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td>
                                    @if($payment->bankAccount)
                                        {{ $payment->bankAccount->bank_name }}<br>
                                        <small>{{ $payment->bankAccount->account_number }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td class="text-right font-bold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        
                        {{-- Total Payments --}}
                        <tr class="payment-total-row">
                            <td colspan="4" class="font-bold">Total Terbayar:</td>
                            <td class="text-right font-bold" style="color: #16a34a;">
                                Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}
                            </td>
                        </tr>
                        
                        {{-- Remaining Amount --}}
                        @if($invoice->amount_remaining > 0)
                            <tr class="payment-remaining-row">
                                <td colspan="4" class="font-bold">Sisa Tagihan:</td>
                                <td class="text-right font-bold" style="color: #dc2626;">
                                    Rp {{ number_format($invoice->amount_remaining, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Payment Instructions (if unpaid) --}}
        @if($invoice->status !== 'paid' && $invoice->amount_remaining > 0)
            <div class="payment-instructions">
                <div class="payment-instructions-title">Instruksi Pembayaran:</div>
                <div class="payment-instructions-content">
                    <p><strong>Jumlah yang harus dibayar:</strong> Rp {{ number_format($invoice->amount_remaining, 0, ',', '.') }}</p>
                    <p><strong>Batas waktu pembayaran:</strong> {{ $invoice->due_date->format('d F Y') }}</p>
                    <p><strong>Metode pembayaran:</strong> Transfer bank atau tunai</p>
                    <p><strong>Keterangan transfer:</strong> {{ $invoice->invoice_number }}</p>
                    <br>
                    <p>Untuk konfirmasi pembayaran, silakan hubungi kami di {{ $company['phone'] }} atau {{ $company['email'] }}.</p>
                </div>
            </div>
        @endif

        {{-- Additional Notes --}}
        @if($options['notes'])
            <div class="notes-section">
                <div class="notes-title">Catatan:</div>
                <div class="notes-content">{{ $options['notes'] }}</div>
            </div>
        @endif

        {{-- Terms & Conditions --}}
        <div class="terms-section">
            <div class="terms-title">Syarat & Ketentuan:</div>
            <ul class="terms-list">
                <li>Pembayaran diharapkan dilakukan sebelum tanggal jatuh tempo yang tercantum</li>
                <li>Keterlambatan pembayaran dapat dikenakan denda sesuai ketentuan yang berlaku</li>
                <li>Barang/jasa yang sudah diberikan tidak dapat dikembalikan kecuali ada kesepakatan tertulis</li>
                <li>Invoice ini merupakan dokumen resmi dan sah tanpa memerlukan tanda tangan basah</li>
                <li>Untuk pertanyaan terkait invoice ini, silakan hubungi kami di kontak yang tertera</li>
            </ul>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p><strong>Terima kasih atas kepercayaan Anda kepada {{ $company['name'] }}</strong></p>
            <p>Invoice ini digenerate secara otomatis pada {{ now()->format('d F Y \p\u\k\u\l H:i') }} WIB</p>
        </div>
    </div>
</body>
</html>