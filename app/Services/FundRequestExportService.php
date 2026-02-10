<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\FundRequest;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class FundRequestExportService
{
    /**
     * Generate PDF for fund requests based on filters.
     *
     * @param  array  $filters  ['month' => 'YYYY-MM', 'status' => string|null, 'priority' => string|null, 'user_id' => int|null, 'search' => string|null]
     * @param  bool   $showRequestor  Whether to show requestor column (for AllRequests view)
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(array $filters, bool $showRequestor = false): \Barryvdh\DomPDF\PDF
    {
        $query = FundRequest::with(['user', 'items'])
            ->orderBy('created_at', 'asc');

        // Apply month filter
        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month);
        }

        // Apply status filter
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply priority filter
        if (! empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Apply user filter (for AllRequests)
        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        // Apply search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('request_number', 'like', "%{$search}%");
            });
        }

        $fundRequests = $query->get();

        // Determine period label
        $periodLabel = $this->buildPeriodLabel($filters);

        // Status label
        $statusLabels = [
            'draft'     => 'Draft',
            'pending'   => 'Menunggu Review',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            'disbursed' => 'Dicairkan',
        ];

        // Requestor name
        $filterRequestorName = null;
        if (! empty($filters['user_id'])) {
            $user = User::find($filters['user_id']);
            $filterRequestorName = $user?->name;
        }

        $data = [
            'company'             => CompanyProfile::current(),
            'fundRequests'        => $fundRequests,
            'periodLabel'         => $periodLabel,
            'filterStatus'        => $filters['status'] ?? null,
            'filterStatusLabel'   => $statusLabels[$filters['status'] ?? ''] ?? null,
            'filterRequestor'     => $filters['user_id'] ?? null,
            'filterRequestorName' => $filterRequestorName,
            'showRequestor'       => $showRequestor,
            'printedBy'           => auth()->user()?->name ?? 'System',
        ];

        $pdf = Pdf::loadView('pdf.fund-requests', $data)
            ->setPaper('a4', 'landscape');

        return $pdf;
    }

    private function buildPeriodLabel(array $filters): string
    {
        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            return $date->translatedFormat('F Y');
        }

        return 'Semua Periode';
    }
}
