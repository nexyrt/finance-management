<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');
        $perPage = (int) $request->input('per_page', 10);
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $clients = Client::query()
            ->when($search, fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('NPWP', 'like', "%{$search}%")
            )
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->withCount('invoices')
            ->with(['invoices' => fn ($q) => $q->select('id', 'billed_to_id', 'total_amount', 'status')])
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Client $client) => [
                'id' => $client->id,
                'name' => $client->name,
                'type' => $client->type,
                'email' => $client->email,
                'NPWP' => $client->NPWP,
                'KPP' => $client->KPP,
                'EFIN' => $client->EFIN,
                'status' => $client->status,
                'account_representative' => $client->account_representative,
                'ar_phone_number' => $client->ar_phone_number,
                'person_in_charge' => $client->person_in_charge,
                'address' => $client->address,
                'invoices_count' => $client->invoices_count,
                'total_invoice_amount' => $client->invoices->sum('total_amount'),
                'paid_invoice_amount' => $client->invoices->where('status', 'paid')->sum('total_amount'),
            ]);

        $stats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'Active')->count(),
            'individual' => Client::where('type', 'individual')->count(),
            'company' => Client::where('type', 'company')->count(),
        ];

        return Inertia::render('clients/index', [
            'clients' => $clients,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'status' => $status,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $nullable = array_map(fn ($v) => $v ?: null, $validated);
        Client::create(array_merge($nullable, ['name' => $validated['name'], 'type' => $validated['type'], 'status' => $validated['status']]));

        return redirect()->back()->with('success', 'Klien berhasil ditambahkan.');
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $validated = $request->validated();
        $nullable = array_map(fn ($v) => $v ?: null, $validated);
        $client->update(array_merge($nullable, ['name' => $validated['name'], 'type' => $validated['type'], 'status' => $validated['status']]));

        return redirect()->back()->with('success', 'Klien berhasil diperbarui.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()->back()->with('success', 'Klien berhasil dihapus.');
    }
}
