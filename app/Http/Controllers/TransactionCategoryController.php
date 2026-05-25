<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionCategoryRequest;
use App\Http\Requests\UpdateTransactionCategoryRequest;
use App\Models\TransactionCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $perPage = (int) $request->input('per_page', 10);
        $sort = $request->input('sort', 'type');
        $direction = $request->input('direction', 'asc');

        $categories = TransactionCategory::with(['parent'])
            ->withCount(['transactions', 'children'])
            ->when($search, fn ($q) => $q->where('label', 'like', "%{$search}%"))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (TransactionCategory $cat) => [
                'id' => $cat->id,
                'type' => $cat->type,
                'pl_group' => $cat->pl_group,
                'label' => $cat->label,
                'parent_id' => $cat->parent_id,
                'parent_label' => $cat->parent?->label,
                'transactions_count' => $cat->transactions_count,
                'children_count' => $cat->children_count,
            ]);

        $all = TransactionCategory::withCount('transactions')->get();

        $stats = [
            'total' => $all->count(),
            'parents' => $all->whereNull('parent_id')->count(),
            'children' => $all->whereNotNull('parent_id')->count(),
            'with_transactions' => $all->where('transactions_count', '>', 0)->count(),
        ];

        $parentOptions = TransactionCategory::whereNull('parent_id')
            ->orderBy('type')
            ->orderBy('label')
            ->get()
            ->map(fn ($cat) => [
                'id' => $cat->id,
                'label' => $cat->label,
                'type' => $cat->type,
            ]);

        return Inertia::render('transaction-categories/index', [
            'categories' => $categories,
            'stats' => $stats,
            'parentOptions' => $parentOptions,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'per_page' => $perPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function store(StoreTransactionCategoryRequest $request): RedirectResponse|JsonResponse
    {
        $category = TransactionCategory::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'label' => $category->label,
                'type' => $category->type,
                'parent_id' => $category->parent_id,
            ], 201);
        }

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $transactionCategory): RedirectResponse
    {
        $transactionCategory->update($request->validated());

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(TransactionCategory $transactionCategory): RedirectResponse
    {
        if ($transactionCategory->children()->exists()) {
            return redirect()->back()->withErrors(['delete' => 'Kategori ini memiliki sub-kategori. Hapus sub-kategori terlebih dahulu.']);
        }

        if ($transactionCategory->transactions()->exists()) {
            return redirect()->back()->withErrors(['delete' => 'Kategori ini digunakan oleh transaksi dan tidak dapat dihapus.']);
        }

        $transactionCategory->delete();

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}
