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
        $bankAccountId = $request->query('bank_account_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $month = $request->query('month');
        $year = $request->query('year');

        $pdf = $this->exportService->generatePdf(
            $bankAccountId,
            $startDate,
            $endDate,
            $month,
            $year
        );

        // Generate filename
        if ($startDate && $endDate) {
            $filename = sprintf(
                'cash-flow-%s-to-%s%s.pdf',
                date('Y-m-d', strtotime($startDate)),
                date('Y-m-d', strtotime($endDate)),
                $bankAccountId ? '-account-' . $bankAccountId : ''
            );
        } else {
            $filename = sprintf(
                'cash-flow-%s-%s%s.pdf',
                $year ?? now()->format('Y'),
                str_pad($month ?? now()->format('m'), 2, '0', STR_PAD_LEFT),
                $bankAccountId ? '-account-' . $bankAccountId : ''
            );
        }

        return $pdf->download($filename);
    }

    /**
     * Preview Cash Flow PDF in browser
     */
    public function previewPdf(Request $request)
    {
        $bankAccountId = $request->query('bank_account_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $month = $request->query('month');
        $year = $request->query('year');

        $pdf = $this->exportService->generatePdf(
            $bankAccountId,
            $startDate,
            $endDate,
            $month,
            $year
        );

        return $pdf->stream('cash-flow-preview.pdf');
    }
}
