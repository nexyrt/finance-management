<?php

namespace App\Livewire\Receivables;

use App\Livewire\Traits\Alert;
use App\Models\Receivable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Alert;

    // Filters - NO TYPE DECLARATION
    public $quantity = 10;
    public $search = null;
    public array $sort = ['column' => 'created_at', 'direction' => 'desc'];

    public $statusFilter = null;
    public $typeFilter = null;

    // Bulk Actions
    public array $selected = [];

    public array $headers = [
        ['index' => 'receivable_number', 'label' => 'No. Piutang', 'sortable' => false],
        ['index' => 'debtor', 'label' => 'Peminjam', 'sortable' => false],
        ['index' => 'principal_amount', 'label' => 'Pokok'],
        ['index' => 'interest_amount', 'label' => 'Bunga', 'sortable' => false],
        ['index' => 'installment_months', 'label' => 'Tenor', 'sortable' => false],
        ['index' => 'loan_date', 'label' => 'Tanggal'],
        ['index' => 'status', 'label' => 'Status'],
        ['index' => 'action', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.receivables.index');
    }

    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        return Receivable::with(['debtor', 'payments'])
            ->when(
                $this->search,
                fn(Builder $query) =>
                $query->where('receivable_number', 'like', '%' . trim($this->search) . '%')
                    ->orWhere('purpose', 'like', '%' . trim($this->search) . '%')
            )
            ->when(
                $this->statusFilter,
                fn(Builder $query) =>
                $query->where('status', $this->statusFilter)
            )
            ->when(
                $this->typeFilter,
                fn(Builder $query) =>
                $query->where('type', $this->typeFilter)
            )
            ->orderBy($this->sort['column'], $this->sort['direction'])
            ->paginate($this->quantity)
            ->withQueryString();
    }

    // Index.php
    public function submitReceivable($id): void
    {
        $receivable = Receivable::findOrFail($id);

        if ($receivable->status !== 'draft') {
            $this->error('Hanya draft yang bisa diajukan');
            return;
        }

        $receivable->update(['status' => 'pending_approval']);
        $this->success('Berhasil diajukan');
    }

    #[Computed]
    public function statusOptions(): array
    {
        return [
            ['label' => 'Draft', 'value' => 'draft'],
            ['label' => 'Menunggu Persetujuan', 'value' => 'pending_approval'],
            ['label' => 'Aktif', 'value' => 'active'],
            ['label' => 'Lunas', 'value' => 'paid_off'],
            ['label' => 'Ditolak', 'value' => 'rejected'],
        ];
    }

    #[Computed]
    public function typeOptions(): array
    {
        return [
            ['label' => 'Pinjaman Karyawan', 'value' => 'employee_loan'],
            ['label' => 'Pinjaman Perusahaan', 'value' => 'company_loan'],
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['statusFilter', 'typeFilter', 'search']);
        $this->resetPage();
    }
}