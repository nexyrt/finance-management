<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 24px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #6b7280;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        .info-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }

        .stat-card {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-card.green {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-card.blue {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .stat-card.purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .stat-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            opacity: 0.9;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th {
            background: #f1f5f9;
            color: #374151;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            border: 1px solid #d1d5db;
            font-size: 11px;
        }

        .table td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .table tr:nth-child(even) {
            background: #fafafa;
        }

        .amount {
            text-align: right;
            font-weight: bold;
            color: #059669;
        }

        .invoice-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #3b82f6;
        }

        .method-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .method-transfer {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .method-cash {
            background: #d1fae5;
            color: #059669;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-paid {
            background: #d1fae5;
            color: #059669;
        }

        .status-partial {
            background: #fef3c7;
            color: #d97706;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>📊 Laporan Pembayaran</h1>
        <p>Periode: {{ $exportDate->format('d F Y') }}</p>
        @if (!empty($filters['dateRange']) && count($filters['dateRange']) >= 2)
            <p>Filter Tanggal: {{ \Carbon\Carbon::parse($filters['dateRange'][0])->format('d/m/Y') }} -
                {{ \Carbon\Carbon::parse($filters['dateRange'][1])->format('d/m/Y') }}</p>
        @endif
    </div>

    {{-- Export Info --}}
    <div class="info-grid no-break">
        <div class="info-row">
            <div class="info-cell">
                <div class="info-box">
                    <div class="info-title">📋 Informasi Laporan</div>
                    <div>Diekspor pada: {{ $exportDate->format('d F Y H:i') }}</div>
                    <div>Total Record: {{ $payments->count() }} pembayaran</div>
                    @if (!empty($filters['search']))
                        <div>Pencarian: "{{ $filters['search'] }}"</div>
                    @endif
                </div>
            </div>
            <div class="info-cell">
                <div class="info-box">
                    <div class="info-title">🔍 Filter Aktif</div>
                    @if (!empty($filters['paymentMethodFilter']))
                        <div>Metode:
                            {{ $filters['paymentMethodFilter'] === 'bank_transfer' ? 'Transfer Bank' : 'Tunai' }}</div>
                    @endif
                    @if (!empty($filters['invoiceStatusFilter']))
                        <div>Status Invoice: {{ ucfirst($filters['invoiceStatusFilter']) }}</div>
                    @endif
                    @if (empty(array_filter($filters)))
                        <div>Tidak ada filter diterapkan</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="stats-grid no-break">
        <div class="stats-row">
            <div class="stats-cell">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($stats['total_count']) }}</div>
                    <div class="stat-label">Total Pembayaran</div>
                </div>
            </div>
            <div class="stats-cell">
                <div class="stat-card green">
                    <div class="stat-value">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</div>
                    <div class="stat-label">Total Nilai</div>
                </div>
            </div>
            <div class="stats-cell">
                <div class="stat-card blue">
                    <div class="stat-value">Rp {{ number_format($stats['by_method']['bank_transfer'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Transfer Bank</div>
                </div>
            </div>
            <div class="stats-cell">
                <div class="stat-card purple">
                    <div class="stat-value">Rp {{ number_format($stats['by_method']['cash'], 0, ',', '.') }}</div>
                    <div class="stat-label">Tunai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payments Table --}}
    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 15%">No. Invoice</th>
                <th style="width: 20%">Klien</th>
                <th style="width: 15%">Jumlah</th>
                <th style="width: 10%">Metode</th>
                <th style="width: 15%">Bank</th>
                <th style="width: 8%">Status</th>
                <th style="width: 5%">Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                    <td class="invoice-number">{{ $payment->invoice_number }}</td>
                    <td>
                        <div>{{ $payment->client_name }}</div>
                        <div style="color: #6b7280; font-size: 9px;">
                            {{ $payment->client_type === 'individual' ? 'Individu' : 'Perusahaan' }}
                        </div>
                    </td>
                    <td class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>
                        <span
                            class="method-badge {{ $payment->payment_method === 'bank_transfer' ? 'method-transfer' : 'method-cash' }}">
                            {{ $payment->payment_method === 'bank_transfer' ? 'Transfer' : 'Tunai' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $payment->bank_name }}</div>
                        <div style="color: #6b7280; font-size: 9px;">{{ $payment->account_name }}</div>
                    </td>
                    <td>
                        @php
                            $statusConfig = [
                                'paid' => ['class' => 'status-paid', 'text' => 'Lunas'],
                                'partially_paid' => ['class' => 'status-partial', 'text' => 'Sebagian'],
                                'sent' => ['class' => 'status-partial', 'text' => 'Terkirim'],
                                'overdue' => ['class' => 'status-partial', 'text' => 'Terlambat'],
                            ];
                            $config = $statusConfig[$payment->invoice_status] ?? [
                                'class' => 'status-partial',
                                'text' => ucfirst($payment->invoice_status),
                            ];
                        @endphp
                        <span class="status-badge {{ $config['class'] }}">{{ $config['text'] }}</span>
                    </td>
                    <td style="font-family: 'Courier New', monospace; font-size: 9px;">
                        {{ $payment->reference_number ? substr($payment->reference_number, 0, 8) . '...' : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #6b7280; font-style: italic;">
                        Tidak ada data pembayaran ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary Footer --}}
    @if ($payments->count() > 0)
        <div style="margin-top: 25px; padding: 15px; background: #f8fafc; border-radius: 8px;" class="no-break">
            <div style="font-weight: bold; margin-bottom: 10px; color: #374151;">📊 Ringkasan Pembayaran</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div><strong>Metode Transfer Bank:</strong></div>
                        <div>{{ $payments->where('payment_method', 'bank_transfer')->count() }} pembayaran</div>
                        <div>Rp {{ number_format($stats['by_method']['bank_transfer'], 0, ',', '.') }}</div>
                    </div>
                    <div class="info-cell">
                        <div><strong>Metode Tunai:</strong></div>
                        <div>{{ $payments->where('payment_method', 'cash')->count() }} pembayaran</div>
                        <div>Rp {{ number_format($stats['by_method']['cash'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div>Laporan ini dibuat secara otomatis oleh Finance Management System</div>
        <div>{{ $exportDate->format('d F Y H:i:s') }}</div>
    </div>
</body>

</html>
