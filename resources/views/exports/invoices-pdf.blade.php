<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .export-info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .export-info h3 {
            margin: 0 0 10px 0;
            color: #374151;
        }
        .filter-item {
            display: inline-block;
            background: #e5e7eb;
            padding: 5px 10px;
            border-radius: 4px;
            margin: 3px;
            font-size: 11px;
        }
        .summary {
            background: #eff6ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #3b82f6;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }
        .summary-label {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            font-size: 11px;
        }
        .table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            font-size: 11px;
        }
        .table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .status {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }
        .status-draft { background: #f3f4f6; color: #374151; }
        .status-sent { background: #dbeafe; color: #1d4ed8; }
        .status-paid { background: #dcfce7; color: #16a34a; }
        .status-partially_paid { background: #fef3c7; color: #d97706; }
        .status-overdue { background: #fee2e2; color: #dc2626; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Invoice</h1>
        <p>{{ $exportDate->format('d F Y, H:i') }} WIB</p>
    </div>

    <div class="export-info">
        <h3>Filter yang Diterapkan:</h3>
        @if(!empty($filters['statusFilter']) || !empty($filters['clientFilter']) || !empty($filters['dateRange']) || !empty($filters['search']))
            @if(!empty($filters['search']))
                <span class="filter-item">Pencarian: {{ $filters['search'] }}</span>
            @endif
            @if(!empty($filters['statusFilter']))
                <span class="filter-item">Status: {{ ucfirst($filters['statusFilter']) }}</span>
            @endif
            @if(!empty($filters['clientFilter']))
                @php $client = \App\Models\Client::find($filters['clientFilter']); @endphp
                <span class="filter-item">Klien: {{ $client->name ?? 'Unknown' }}</span>
            @endif
            @if(!empty($filters['dateRange']) && count($filters['dateRange']) >= 2)
                <span class="filter-item">
                    Periode: {{ \Carbon\Carbon::parse($filters['dateRange'][0])->format('d/m/Y') }} - 
                    {{ \Carbon\Carbon::parse($filters['dateRange'][1])->format('d/m/Y') }}
                </span>
            @endif
        @else
            <span class="filter-item">Semua Data</span>
        @endif
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value">{{ $invoices->count() }}</div>
                <div class="summary-label">Total Invoice</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($invoices->sum('total_amount'), 0, ',', '.') }}</div>
                <div class="summary-label">Total Nilai</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($invoices->sum('amount_paid'), 0, ',', '.') }}</div>
                <div class="summary-label">Total Terbayar</div>
            </div>
            <div class="summary-item">
                <div class="summary-value">Rp {{ number_format($invoices->sum('total_amount') - $invoices->sum('amount_paid'), 0, ',', '.') }}</div>
                <div class="summary-label">Total Outstanding</div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%">No. Invoice</th>
                <th style="width: 20%">Klien</th>
                <th style="width: 10%">Tanggal</th>
                <th style="width: 10%">Jatuh Tempo</th>
                <th style="width: 10%">Status</th>
                <th style="width: 15%">Total</th>
                <th style="width: 13%">Terbayar</th>
                <th style="width: 10%">Sisa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>{{ $invoice->issue_date->format('d/m/Y') }}</td>
                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="status status-{{ $invoice->status }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($invoice->total_amount - $invoice->amount_paid, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #6b7280;">
                        Tidak ada data invoice yang ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Finance Management</p>
        <p>Dicetak pada: {{ $exportDate->format('d F Y, H:i:s') }} WIB</p>
    </div>
</body>
</html>