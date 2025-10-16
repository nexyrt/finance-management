<?php

namespace App\Livewire\Transactions;

use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Categorize extends Component
{
    use Interactions;

    public bool $modal = false;
    public bool $isBulk = false;

    // Single transaction
    public ?BankTransaction $transaction = null;

    // Bulk transactions
    public array $transactionIds = [];
    public Collection $transactions;

    // Form
    public ?int $category_id = null;

    public function mount()
    {
        $this->transactions = collect([]);
    }

    #[On('categorize-transaction')]
    public function openSingle(int $id): void
    {
        $this->reset(['category_id', 'transactionIds']);
        $this->isBulk = false;

        $this->transaction = BankTransaction::with(['bankAccount', 'category'])
            ->findOrFail($id);

        $this->category_id = $this->transaction->category_id;
        $this->modal = true;
    }

    #[On('bulk-categorize')]
    public function openBulk(array $ids): void
    {
        if (empty($ids)) {
            $this->toast()
                ->warning('Perhatian', 'Pilih transaksi yang ingin dikategorikan')
                ->send();
            return;
        }

        $this->reset(['category_id', 'transaction']);
        $this->isBulk = true;
        $this->transactionIds = $ids;

        $this->transactions = BankTransaction::with(['bankAccount', 'category'])
            ->whereIn('id', $ids)
            ->get();

        $this->modal = true;
    }

    #[Computed]
    public function categoriesOptions(): array
    {
        // Determine transaction type
        $transactionType = $this->isBulk
            ? $this->transactions->first()?->transaction_type
            : $this->transaction?->transaction_type;

        if (!$transactionType) {
            return [];
        }

        // Map transaction_type to category types
        $categoryTypes = match ($transactionType) {
            'credit' => ['income', 'adjustment', 'transfer'],
            'debit' => ['expense', 'adjustment', 'transfer'],
            default => []
        };

        // Get parent categories
        $parents = TransactionCategory::whereNull('parent_code')
            ->whereIn('type', $categoryTypes)
            ->orderBy('type')
            ->orderBy('label')
            ->get();

        $options = [];

        foreach ($parents as $parent) {
            // Add parent
            $options[] = [
                'label' => $parent->label,
                'value' => $parent->id,
            ];

            // Add children with indentation
            foreach ($parent->children as $child) {
                $options[] = [
                    'label' => '  ↳ ' . $child->label,
                    'value' => $child->id,
                ];
            }
        }

        return $options;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:transaction_categories,id',
        ];
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->isBulk) {
                // Bulk update
                BankTransaction::whereIn('id', $this->transactionIds)
                    ->update(['category_id' => $this->category_id]);

                $count = count($this->transactionIds);

                $this->dispatch('transaction-categorized');
                $this->reset();
                $this->modal = false;

                $this->toast()
                    ->success('Berhasil!', "{$count} transaksi berhasil dikategorikan")
                    ->send();
            } else {
                // Single update
                $this->transaction->update(['category_id' => $this->category_id]);

                $this->dispatch('transaction-categorized');
                $this->reset();
                $this->modal = false;

                $this->toast()
                    ->success('Berhasil!', 'Transaksi berhasil dikategorikan')
                    ->send();
            }
        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menyimpan kategori')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.transactions.categorize');
    }
}