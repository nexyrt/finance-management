<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use App\Services\ProfitLossService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfitLossReportController extends Controller
{
    public function index(Request $request, ProfitLossService $service): Response
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        // Default: tahun berjalan (1 Januari tahun ini sampai hari ini).
        $start = isset($validated['start_date'])
            ? Carbon::parse($validated['start_date'])->startOfDay()
            : Carbon::now()->startOfYear();

        $end = isset($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->endOfDay()
            : Carbon::now()->endOfDay();

        $report = $service->generate($start, $end);

        $company = CompanyProfile::first();

        return Inertia::render('reports/profit-loss/index', [
            'report' => $report,
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ],
            'company' => $company ? [
                'name' => $company->name,
                'address' => $company->address,
                'npwp' => $company->npwp,
            ] : null,
        ]);
    }
}
