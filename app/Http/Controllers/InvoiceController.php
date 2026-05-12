<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $clientId = $request->input('client_id');
        $month = $request->input('month', now()->format('Y-m'));
        $perPage = (int) $request->input('per_page', 25);
        $sort = $request->input('sort', 'issue_date');
        $direction = $request->input('direction', 'desc');

        $query = Invoice::query()
            ->join('clients', 'invoices.billed_to_id', '=', 'clients.id')
            ->leftJoin(
                DB::raw('(SELECT invoice_id, COALESCE(SUM(amount), 0) as amount_paid FROM payments GROUP BY invoice_id) as p'),
                'invoices.id', '=', 'p.invoice_id'
            )
            ->select([
                'invoices.*',
                'clients.name as client_name',
                'clients.type as client_type',
                DB::raw('COALESCE(p.amount_paid, 0) as amount_paid'),
            ])
            ->when($search, fn ($q) => $q->where(function ($query) use ($search) {
                $query->where('invoices.invoice_number', 'like', "%{$search}%")
                    ->orWhere('clients.name', 'like', "%{$search}%");
            }))
            ->when($status, fn ($q) => $q->where('invoices.status', $status))
            ->when($clientId, fn ($q) => $q->where('invoices.billed_to_id', $clientId))
            ->when($month, fn ($q) => $q
                ->whereYear('invoices.issue_date', substr($month, 0, 4))
                ->whereMonth('invoices.issue_date', substr($month, 5, 2))
            );

        match ($sort) {
            'client_name' => $query->orderBy('clients.name', $direction),
            default => $query->orderBy("invoices.{$sort}", $direction),
        };

        $invoices = $query->paginate($perPage)->withQueryString()
            ->through(fn ($invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'client_name' => $invoice->client_name,
                'client_type' => $invoice->client_type,
                'issue_date' => $invoice->issue_date,
                'due_date' => $invoice->due_date,
                'total_amount' => $invoice->total_amount,
                'amount_paid' => (int) $invoice->amount_paid,
                'amount_remaining' => $invoice->total_amount - (int) $invoice->amount_paid,
                'status' => $invoice->status,
                'faktur' => $invoice->faktur,
            ]);

        // Stats (exclude drafts from revenue calculations, match Listing.php behaviour)
        $statsIds = Invoice::query()
            ->whereNotIn('status', ['draft'])
            ->when($month, fn ($q) => $q
                ->whereYear('issue_date', substr($month, 0, 4))
                ->whereMonth('issue_date', substr($month, 5, 2))
            )
            ->pluck('id');

        $basicStats = DB::table('invoices')
            ->whereIn('id', $statsIds)
            ->selectRaw('COUNT(*) as invoice_count, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $itemStats = DB::table('invoice_items')
            ->whereIn('invoice_id', $statsIds)
            ->selectRaw('COALESCE(SUM(CASE WHEN is_tax_deposit = 0 THEN cogs_amount ELSE 0 END), 0) as total_cogs')
            ->first();

        $paidThisMonth = (int) DB::table('payments')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $totalRevenue = (int) $basicStats->total_revenue;
        $totalCogs = (int) $itemStats->total_cogs;

        $stats = [
            'invoice_count' => (int) $basicStats->invoice_count,
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'gross_profit' => $totalRevenue - $totalCogs,
            'paid_this_month' => $paidThisMonth,
            'draft_count' => Invoice::where('status', 'draft')->count(),
            'sent_count' => Invoice::where('status', 'sent')->count(),
            'partially_paid_count' => Invoice::where('status', 'partially_paid')->count(),
            'paid_count' => Invoice::where('status', 'paid')->count(),
        ];

        $clients = Client::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => ['label' => $c->name, 'value' => $c->id]);

        // Rollback-eligible invoice IDs (max seq per year-month for sent invoices)
        $maxSeqPerMonth = DB::table('invoices')
            ->whereNotNull('invoice_number')
            ->where('invoice_number', 'LIKE', '%/INV/%')
            ->selectRaw("YEAR(issue_date) as yr, MONTH(issue_date) as mo, MAX(CAST(SUBSTRING_INDEX(invoice_number, '/INV/', 1) AS UNSIGNED)) as max_seq")
            ->groupBy(DB::raw('YEAR(issue_date)'), DB::raw('MONTH(issue_date)'));

        $rollbackableIds = DB::table('invoices')
            ->where('invoices.status', 'sent')
            ->whereNotNull('invoices.invoice_number')
            ->where('invoices.invoice_number', 'LIKE', '%/INV/%')
            ->joinSub($maxSeqPerMonth, 'mx', function ($join) {
                $join->whereRaw('YEAR(invoices.issue_date) = mx.yr')
                    ->whereRaw('MONTH(invoices.issue_date) = mx.mo')
                    ->whereRaw("CAST(SUBSTRING_INDEX(invoices.invoice_number, '/INV/', 1) AS UNSIGNED) = mx.max_seq");
            })
            ->pluck('invoices.id')
            ->all();

        return Inertia::render('invoices/index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'clients' => $clients,
            'rollbackableIds' => $rollbackableIds,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'client_id' => $clientId ? (int) $clientId : null,
                'month' => $month,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['client', 'items', 'payments.bankAccount']);

        return response()->json([
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'status' => $invoice->status,
            'issue_date' => $invoice->issue_date?->format('Y-m-d'),
            'due_date' => $invoice->due_date?->format('Y-m-d'),
            'subtotal' => $invoice->subtotal,
            'discount_amount' => $invoice->discount_amount,
            'discount_type' => $invoice->discount_type,
            'discount_value' => $invoice->discount_value,
            'discount_reason' => $invoice->discount_reason,
            'total_amount' => $invoice->total_amount,
            'amount_paid' => $invoice->amount_paid,
            'amount_remaining' => $invoice->amount_remaining,
            'faktur' => $invoice->faktur,
            'client' => [
                'id' => $invoice->client->id,
                'name' => $invoice->client->name,
                'email' => $invoice->client->email,
                'NPWP' => $invoice->client->NPWP,
                'address' => $invoice->client->address,
            ],
            'items' => $invoice->items->map(fn ($item) => [
                'id' => $item->id,
                'service_name' => $item->service_name,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
                'cogs_amount' => $item->cogs_amount,
                'is_tax_deposit' => $item->is_tax_deposit,
            ]),
            'payments' => $invoice->payments->map(fn ($payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date?->format('Y-m-d'),
                'bank_account' => $payment->bankAccount
                    ? $payment->bankAccount->account_name.' ('.$payment->bankAccount->bank_name.')'
                    : null,
                'notes' => $payment->notes,
            ]),
        ]);
    }

    public function create(): Response
    {
        $clients = Client::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email]);

        $services = Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price, 'type' => $s->type]);

        $nextSeq = Invoice::getMaxSequenceFromDb(now()) + 1;
        $companyInitials = Invoice::extractCompanyInitials(
            optional(CompanyProfile::first())->name ?? 'SPI'
        ) ?: 'SPI';

        return Inertia::render('invoices/create', [
            'clients' => $clients,
            'services' => $services,
            'nextSeq' => $nextSeq,
            'companyInitials' => $companyInitials,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'items.*.cogs_amount' => ['nullable', 'integer', 'min:0'],
            'items.*.is_tax_deposit' => ['boolean'],
            'discount_type' => ['nullable', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $parsedItems = [];

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (int) $item['unit_price'];
                $amount = (int) round($unitPrice * $quantity);
                $cogsAmount = (int) ($item['cogs_amount'] ?? 0);

                $parsedItems[] = [
                    'client_id' => $validated['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'pcs',
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => (bool) ($item['is_tax_deposit'] ?? false),
                ];

                $subtotal += $amount;
            }

            $discountType = $validated['discount_type'] ?? 'fixed';
            $discountValue = (int) ($validated['discount_value'] ?? 0);
            $discountAmount = $discountType === 'percentage'
                ? (int) round($subtotal * $discountValue / 100)
                : $discountValue;

            $totalAmount = max(0, $subtotal - $discountAmount);

            $invoice = Invoice::create([
                'billed_to_id' => $validated['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'total_amount' => $totalAmount,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'status' => 'draft',
            ]);

            foreach ($parsedItems as $itemData) {
                InvoiceItem::create(array_merge($itemData, ['invoice_id' => $invoice->id]));
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil dibuat.');
    }

    public function edit(Invoice $invoice): Response
    {
        $invoice->load(['items']);

        $clients = Client::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'email' => $c->email]);

        $services = Service::orderBy('name')
            ->get(['id', 'name', 'price', 'type'])
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'price' => $s->price, 'type' => $s->type]);

        return Inertia::render('invoices/edit', [
            'invoice' => [
                'id' => $invoice->id,
                'client_id' => $invoice->billed_to_id,
                'issue_date' => $invoice->issue_date?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'discount_type' => $invoice->discount_type ?? 'fixed',
                'discount_value' => $invoice->discount_value ?? 0,
                'discount_reason' => $invoice->discount_reason,
                'items' => $invoice->items->map(fn ($item) => [
                    'service_name' => $item->service_name,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'cogs_amount' => $item->cogs_amount,
                    'is_tax_deposit' => $item->is_tax_deposit,
                ]),
            ],
            'clients' => $clients,
            'services' => $services,
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit' => ['nullable', 'string', 'max:20'],
            'items.*.unit_price' => ['required', 'integer', 'min:0'],
            'items.*.cogs_amount' => ['nullable', 'integer', 'min:0'],
            'items.*.is_tax_deposit' => ['boolean'],
            'discount_type' => ['nullable', 'in:fixed,percentage'],
            'discount_value' => ['nullable', 'numeric', 'min:0'],
            'discount_reason' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $subtotal = 0;
            $parsedItems = [];

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (int) $item['unit_price'];
                $amount = (int) round($unitPrice * $quantity);
                $cogsAmount = (int) ($item['cogs_amount'] ?? 0);

                $parsedItems[] = [
                    'invoice_id' => $invoice->id,
                    'client_id' => $validated['client_id'],
                    'service_name' => $item['service_name'],
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? 'pcs',
                    'unit_price' => $unitPrice,
                    'amount' => $amount,
                    'cogs_amount' => $cogsAmount,
                    'is_tax_deposit' => (bool) ($item['is_tax_deposit'] ?? false),
                ];

                $subtotal += $amount;
            }

            $discountType = $validated['discount_type'] ?? 'fixed';
            $discountValue = (int) ($validated['discount_value'] ?? 0);
            $discountAmount = $discountType === 'percentage'
                ? (int) round($subtotal * $discountValue / 100)
                : $discountValue;

            $totalAmount = max(0, $subtotal - $discountAmount);

            $invoice->update([
                'billed_to_id' => $validated['client_id'],
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_reason' => $validated['discount_reason'] ?? null,
                'total_amount' => $totalAmount,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
            ]);

            $invoice->items()->delete();
            foreach ($parsedItems as $itemData) {
                InvoiceItem::create($itemData);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        });

        return redirect()->back()->with('success', 'Invoice berhasil dihapus.');
    }

    public function send(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'invoice_number' => ['required', 'string', 'max:100', "unique:invoices,invoice_number,{$invoice->id}"],
        ]);

        if ($invoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Hanya invoice draft yang dapat dikirim.');
        }

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'status' => 'sent',
        ]);

        return redirect()->back()->with('success', 'Invoice berhasil dikirim: '.$request->invoice_number);
    }

    public function rollback(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'sent') {
            return redirect()->back()->with('error', 'Hanya invoice yang sudah terkirim yang bisa di-rollback.');
        }

        if (! Invoice::isInvoiceLatestInMonth($invoice)) {
            return redirect()->back()->with('error', 'Hanya invoice terbaru di bulan ini yang bisa di-rollback.');
        }

        $invoice->update(['invoice_number' => null, 'status' => 'draft']);

        return redirect()->back()->with('success', 'Invoice berhasil dikembalikan ke draft.');
    }
}
