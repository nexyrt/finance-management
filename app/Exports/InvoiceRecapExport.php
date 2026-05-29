<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Invoice recap (rekap) export — a single sheet with a period header,
 * the filtered invoice rows, and a totals footer. Mirrors the on-screen
 * listing so the user gets the same data they filtered.
 */
class InvoiceRecapExport implements FromArray, WithColumnWidths, WithStyles, WithTitle
{
    private const STATUS_LABELS = [
        'draft' => 'Draft',
        'sent' => 'Terkirim',
        'partially_paid' => 'Sebagian',
        'paid' => 'Lunas',
    ];

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<string, mixed>  $summary
     */
    public function __construct(
        private Collection $rows,
        private array $summary,
        private string $period,
    ) {}

    public function title(): string
    {
        return 'Rekap Invoice';
    }

    public function array(): array
    {
        $rp = fn ($v) => 'Rp '.number_format((int) $v, 0, ',', '.');

        $grid = [];
        $grid[] = ['REKAP INVOICE', '', '', '', '', '', '', ''];
        $grid[] = ['Periode: '.$this->period, '', '', '', '', '', '', ''];
        $grid[] = ['Dicetak: '.now()->isoFormat('D MMMM Y HH:mm').' WIB', '', '', '', '', '', '', ''];
        $grid[] = ['', '', '', '', '', '', '', ''];
        $grid[] = ['No. Invoice', 'Klien', 'Tgl Invoice', 'Jatuh Tempo', 'Status', 'Total', 'Terbayar', 'Sisa'];

        foreach ($this->rows as $row) {
            $grid[] = [
                $row['invoice_number'] ?? '(draft)',
                $row['client_name'],
                $row['issue_date'],
                $row['due_date'],
                self::STATUS_LABELS[$row['status']] ?? $row['status'],
                $rp($row['total_amount']),
                $rp($row['amount_paid']),
                $rp($row['amount_remaining']),
            ];
        }

        $grid[] = ['', '', '', '', '', '', '', ''];
        $grid[] = [
            'TOTAL ('.$this->summary['count'].' invoice)', '', '', '', '',
            $rp($this->summary['total_amount']),
            $rp($this->summary['total_paid']),
            $rp($this->summary['total_outstanding']),
        ];

        return $grid;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 30, 'C' => 14, 'D' => 14,
            'E' => 12, 'F' => 20, 'G' => 20, 'H' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $headerRow = 5;
        $totalRow = $sheet->getHighestRow();

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E2E8F0');
        $sheet->getStyle("A{$totalRow}:H{$totalRow}")->getFont()->setBold(true);

        // Right-align the three money columns for all data rows.
        $sheet->getStyle("F{$headerRow}:H{$totalRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }
}
