@php
    /** @var \Illuminate\Support\Collection $rows */
    /** @var array $summary */
    /** @var string $period */
    /** @var \App\Models\CompanyProfile|null $company */

    $rp = fn ($v) => 'Rp '.number_format((int) $v, 0, ',', '.');

    $statusLabels = [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'partially_paid' => 'Sebagian',
        'paid' => 'Lunas',
    ];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Invoice — {{ $period }}</title>
    <style>
        @page { margin: 14mm 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #1e293b;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header .company { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .header .meta { font-size: 8.5pt; color: #475569; margin-top: 3px; }

        .title-block { text-align: center; margin-bottom: 12px; }
        .title-block h1 { font-size: 13pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .title-block .period { font-size: 9.5pt; color: #475569; margin-top: 3px; font-style: italic; }

        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #e2e8f0; }
        th, td { border: 1px solid #94a3b8; padding: 5px 7px; font-size: 8.5pt; }
        th { font-weight: bold; text-align: center; }
        td.center { text-align: center; }
        td.right { text-align: right; font-variant-numeric: tabular-nums; }

        tfoot td, tr.total td { font-weight: bold; background-color: #f1f5f9; }

        .footer-meta { margin-top: 14px; font-size: 8pt; color: #64748b; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company">{{ $company->name ?? 'Perusahaan' }}</div>
        @if ($company)
            <div class="meta">
                @if ($company->address) {{ $company->address }} @endif
                @if ($company->npwp) &nbsp;·&nbsp; NPWP: {{ $company->npwp }} @endif
            </div>
        @endif
    </div>

    <div class="title-block">
        <h1>Rekap Invoice</h1>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:17%;">No. Invoice</th>
                <th style="width:23%;">Klien</th>
                <th style="width:11%;">Tgl Invoice</th>
                <th style="width:11%;">Jatuh Tempo</th>
                <th style="width:9%;">Status</th>
                <th>Total</th>
                <th>Terbayar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $row['invoice_number'] ?? '(draft)' }}</td>
                    <td>{{ $row['client_name'] }}</td>
                    <td class="center">{{ $row['issue_date'] }}</td>
                    <td class="center">{{ $row['due_date'] }}</td>
                    <td class="center">{{ $statusLabels[$row['status']] ?? $row['status'] }}</td>
                    <td class="right">{{ $rp($row['total_amount']) }}</td>
                    <td class="right">{{ $rp($row['amount_paid']) }}</td>
                    <td class="right">{{ $rp($row['amount_remaining']) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="center" style="padding:16px;color:#94a3b8;font-style:italic;">Tidak ada invoice pada periode/filter ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="6" class="right">TOTAL ({{ $summary['count'] }} invoice)</td>
                <td class="right">{{ $rp($summary['total_amount']) }}</td>
                <td class="right">{{ $rp($summary['total_paid']) }}</td>
                <td class="right">{{ $rp($summary['total_outstanding']) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-meta">
        Dicetak: {{ now()->isoFormat('D MMMM Y HH:mm') }} WIB
    </div>

</body>
</html>
