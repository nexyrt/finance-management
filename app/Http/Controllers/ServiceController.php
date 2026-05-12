<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public const TYPES = [
        'Perizinan',
        'Administrasi Perpajakan',
        'Digital Marketing',
        'Sistem Digital',
    ];

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $perPage = (int) $request->input('per_page', 10);
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $services = Service::query()
            ->select(['id', 'name', 'type', 'price', 'created_at'])
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (Service $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'type' => $s->type,
                'price' => $s->price,
                'created_at' => $s->created_at?->toDateString(),
            ]);

        $aggregate = Service::selectRaw('COUNT(*) as total, AVG(price) as avg_price, MAX(price) as highest_price')
            ->toBase()->first();

        $stats = [
            'total' => (int) ($aggregate->total ?? 0),
            'avg_price' => (int) ($aggregate->avg_price ?? 0),
            'highest_price' => (int) ($aggregate->highest_price ?? 0),
            'by_type' => Service::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderByDesc('count')
                ->pluck('count', 'type'),
        ];

        return Inertia::render('services/index', [
            'services' => $services,
            'stats' => $stats,
            'types' => self::TYPES,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', self::TYPES)],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        Service::create($validated);

        return redirect()->back()->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', self::TYPES)],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $service->update($validated);

        return redirect()->back()->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->back()->with('success', 'Layanan berhasil dihapus.');
    }
}
