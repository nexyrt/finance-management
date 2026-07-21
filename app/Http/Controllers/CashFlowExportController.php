<?php

namespace App\Http\Controllers;

use App\Services\CashFlowExportService;
use Illuminate\Http\Request;

class CashFlowExportController extends Controller
{
    public function __construct(
        private CashFlowExportService $exportService
    ) {}

    /**
     * Export Cash Flow as PDF
     */
    public function exportPdf(Request $request)
    {
        [$bankAccountIds, $startDate, $endDate, $month, $year] = $this->parseFilters($request);

        $pdf = $this->exportService->generatePdf(
            $bankAccountIds,
            $startDate,
            $endDate,
            $month,
            $year
        );

        if ($startDate && $endDate) {
            $filename = sprintf(
                'cash-flow-%s-to-%s%s.pdf',
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate)),
                $bankAccountIds ? '-account-'.implode('-', $bankAccountIds) : ''
            );
        } else {
            $filename = sprintf(
                'cash-flow-%s-%s%s.pdf',
                $year ?? now()->format('Y'),
                str_pad($month ?? now()->format('m'), 2, '0', STR_PAD_LEFT),
                $bankAccountIds ? '-account-'.implode('-', $bankAccountIds) : ''
            );
        }

        return $pdf->download($filename);
    }

    /**
     * Preview Cash Flow PDF in browser
     */
    public function previewPdf(Request $request)
    {
        [$bankAccountIds, $startDate, $endDate, $month, $year] = $this->parseFilters($request);

        $pdf = $this->exportService->generatePdf(
            $bankAccountIds,
            $startDate,
            $endDate,
            $month,
            $year
        );

        return $pdf->stream('cash-flow-preview.pdf');
    }

    /**
     * Normalize export filters. Accepts both the legacy `start_date`/`end_date`
     * params and the `date_from`/`date_to` params sent by the cash-flow pages,
     * plus a single `bank_account_id` or a comma-separated `bank_accounts` list.
     *
     * @return array{0: array<int,int>|null, 1: string|null, 2: string|null, 3: string|null, 4: string|null}
     */
    private function parseFilters(Request $request): array
    {
        $startDate = $request->query('start_date') ?: $request->query('date_from');
        $endDate = $request->query('end_date') ?: $request->query('date_to');

        $bankAccountIds = [];
        if ($request->filled('bank_account_id')) {
            $bankAccountIds[] = (int) $request->query('bank_account_id');
        }
        if ($request->filled('bank_accounts')) {
            foreach (explode(',', (string) $request->query('bank_accounts')) as $id) {
                $bankAccountIds[] = (int) $id;
            }
        }
        $bankAccountIds = array_values(array_unique(array_filter($bankAccountIds)));

        return [
            $bankAccountIds ?: null,
            $startDate ?: null,
            $endDate ?: null,
            $request->query('month') ?: null,
            $request->query('year') ?: null,
        ];
    }
}
