<?php

namespace App\Livewire\Reimbursements;

use Tallstackui\Traits\Interactions;
use App\Models\Reimbursement;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Interactions;

    // Pagination & Filters
    public ?int $quantity = 10;
    public ?string $search = null;
    public array $sort = ['column' => 'expense_date', 'direction' => 'desc'];
    public array $selected = [];

    // Filter properties
    public ?string $statusFilter = null;
    public ?string $categoryFilter = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public string $viewMode = 'all'; // all, my_requests, pending, approved

    // Table headers
    public array $headers = [
        ['index' => 'title', 'label' => 'Judul'],
        ['index' => 'user', 'label' => 'Pengaju', 'sortable' => false],
        ['index' => 'amount', 'label' => 'Jumlah'],
        ['index' => 'category', 'label' => 'Kategori'],
        ['index' => 'expense_date', 'label' => 'Tanggal'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.reimbursements.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Reimbursement::with(['user', 'reviewer', 'payer'])
            ->when($this->viewMode === 'my_requests', fn(Builder $query) => 
                $query->forUser(Auth::id())
            )
            ->when($this->viewMode === 'pending', fn(Builder $query) => 
                $query->pending()
            )
            ->when($this->viewMode === 'approved', fn(Builder $query) => 
                $query->approved()
            )
            ->when($this->search, fn(Builder $query) => 
                $query->where(function($q) {
                    $q->where('title', 'like', '%'.trim($this->search).'%')
                      ->orWhere('description', 'like', '%'.trim($this->search).'%')
                      ->orWhereHas('user', fn($user) => 
                          $user->where('name', 'like', '%'.trim($this->search).'%')
                      );
                })
            )
            ->when($this->statusFilter, fn(Builder $query) => 
                $query->byStatus($this->statusFilter)
            )
            ->when($this->categoryFilter, fn(Builder $query) => 
                $query->byCategory($this->categoryFilter)
            )
            ->when($this->dateFrom, fn(Builder $query) => 
                $query->whereDate('expense_date', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn(Builder $query) => 
                $query->whereDate('expense_date', '<=', $this->dateTo)
            )
            ->when($this->sort['column'] === 'user', fn(Builder $query) =>
                $query->join('users', 'reimbursements.user_id', '=', 'users.id')
                      ->orderBy('users.name', $this->sort['direction'])
                      ->select('reimbursements.*')
            , fn(Builder $query) => 
                $query->orderBy($this->sort['column'], $this->sort['direction'])
            )
            ->paginate($this->quantity)
            ->withQueryString();
    }

    #[Computed]
    public function stats(): array
    {
        $baseQuery = Reimbursement::query();
        
        if ($this->viewMode === 'my_requests') {
            $baseQuery->forUser(Auth::id());
        }

        return [
            'total' => $baseQuery->count(),
            'pending' => (clone $baseQuery)->pending()->count(),
            'approved' => (clone $baseQuery)->approved()->count(),
            'paid' => (clone $baseQuery)->paid()->count(),
            'total_amount' => (clone $baseQuery)->sum('amount'),
            'pending_amount' => (clone $baseQuery)->whereIn('status', ['pending', 'approved'])->sum('amount'),
        ];
    }

    // View mode switcher
    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'categoryFilter', 'dateFrom', 'dateTo', 'search']);
        $this->resetPage();
    }

    // Bulk Actions - Approve (Finance only)
    #[Renderless]
    public function confirmBulkApprove(): void
    {
        if (empty($this->selected)) return;

        $count = count($this->selected);
        $this->question("Setujui {$count} pengajuan?", "Pengajuan yang disetujui akan menunggu pembayaran.")
            ->confirm(method: 'bulkApprove')
            ->cancel()
            ->send();
    }

    public function bulkApprove(): void
    {
        if (empty($this->selected)) return;

        $count = Reimbursement::whereIn('id', $this->selected)
            ->pending()
            ->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

        $this->selected = [];
        $this->resetPage();
        
        if ($count > 0) {
            $this->success("{$count} pengajuan berhasil disetujui");
        } else {
            $this->warning("Tidak ada pengajuan yang dapat disetujui");
        }
    }

    // Bulk Actions - Reject (Finance only)
    #[Renderless]
    public function confirmBulkReject(): void
    {
        if (empty($this->selected)) return;

        $count = count($this->selected);
        $this->question("Tolak {$count} pengajuan?", "Pengajuan yang ditolak dapat diajukan kembali oleh karyawan.")
            ->confirm(method: 'bulkReject')
            ->cancel()
            ->send();
    }

    public function bulkReject(): void
    {
        if (empty($this->selected)) return;

        $count = Reimbursement::whereIn('id', $this->selected)
            ->pending()
            ->update([
                'status' => 'rejected',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => 'Ditolak via bulk action',
            ]);

        $this->selected = [];
        $this->resetPage();
        
        if ($count > 0) {
            $this->warning("{$count} pengajuan telah ditolak");
        } else {
            $this->warning("Tidak ada pengajuan yang dapat ditolak");
        }
    }

    // Bulk Delete (Staff - only draft)
    #[Renderless]
    public function confirmBulkDelete(): void
    {
        if (empty($this->selected)) return;

        $count = count($this->selected);
        $this->question("Hapus {$count} pengajuan?", "Data pengajuan yang dihapus tidak dapat dikembalikan.")
            ->confirm(method: 'bulkDelete')
            ->cancel()
            ->send();
    }

    public function bulkDelete(): void
    {
        if (empty($this->selected)) return;

        $reimbursements = Reimbursement::whereIn('id', $this->selected)
            ->forUser(Auth::id())
            ->where('status', 'draft')
            ->get();

        $count = $reimbursements->count();
        
        foreach ($reimbursements as $reimbursement) {
            $reimbursement->delete();
        }

        $this->selected = [];
        $this->resetPage();
        
        if ($count > 0) {
            $this->success("{$count} pengajuan berhasil dihapus");
        } else {
            $this->warning("Tidak ada pengajuan yang dapat dihapus");
        }
    }

    // Export functionality
    public function exportSelected(): void
    {
        if (empty($this->selected)) return;

        $count = count($this->selected);
        $this->success("Export {$count} pengajuan sedang diproses");
        
        // TODO: Implement export logic
    }
}