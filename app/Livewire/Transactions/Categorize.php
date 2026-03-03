<?php

namespace App\Livewire\Transactions;

use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Categorize extends Component
{
    use Interactions;

    public bool $modal = false;
    public bool $isBulk = false;

    // Single transaction — store ID only
    public ?int $transactionId = null;

    // Bulk transactions — store IDs only
    public array $transactionIds = [];

    // Form
    public ?int $category_id = null;

    #[On('categorize-transaction')]
    public function openSingle(int $id): void
    {
        $this->reset(['category_id', 'transactionIds', 'transactionId']);
        $this->isBulk = false;

        $tx = BankTransaction::findOrFail($id);
        $this->transactionId = $id;
        $this->category_id = $tx->category_id;
        $this->modal = true;
    }

    #[On('bulk-categorize')]
    public function openBulk($ids): void
    {
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?? [];
        }

        if (empty($ids)) {
            $this->toast()
                ->warning('Perhatian', 'Pilih transaksi yang ingin dikategorikan')
                ->send();
            return;
        }

        $this->reset(['category_id', 'transactionId']);
        $this->isBulk = true;
        $this->transactionIds = $ids;
        $this->modal = true;
    }

    #[Computed]
    public function transaction(): ?BankTransaction
    {
        return $this->transactionId
            ? BankTransaction::with(['bankAccount', 'category'])->find($this->transactionId)
            : null;
    }

    #[Computed]
    public function transactions()
    {
        return $this->isBulk && !empty($this->transactionIds)
            ? BankTransaction::with(['bankAccount', 'category'])->whereIn('id', $this->transactionIds)->get()
            : collect([]);
    }

    #[Computed]
    public function categoriesOptions(): array
    {
        $transactionType = $this->isBulk
            ? $this->transactions->first()?->transaction_type
            : $this->transaction?->transaction_type;

        if (!$transactionType) {
            return [];
        }

        $categoryTypes = match ($transactionType) {
            'credit' => ['income', 'adjustment', 'transfer'],
            'debit' => ['expense', 'adjustment', 'transfer'],
            default => []
        };

        $parents = TransactionCategory::whereNull('parent_id')
            ->whereIn('type', $categoryTypes)
            ->with('children')
            ->orderBy('type')
            ->orderBy('label')
            ->get();

        $options = [];

        foreach ($parents as $parent) {
            $options[] = [
                'label' => $parent->label,
                'value' => $parent->id,
                'disabled' => true,
            ];

            foreach ($parent->children as $child) {
                $options[] = [
                    'label' => '  ↳ ' . $child->label,
                    'value' => $child->id,
                    'disabled' => false,
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
                BankTransaction::whereIn('id', $this->transactionIds)
                    ->update(['category_id' => $this->category_id]);

                $count = count($this->transactionIds);

                $this->dispatch('transaction-categorized');
                $this->dispatch('clear-selection');

                $this->reset();
                $this->modal = false;

                $this->toast()
                    ->success('Berhasil!', "{$count} transaksi berhasil dikategorikan")
                    ->send();
            } else {
                BankTransaction::where('id', $this->transactionId)
                    ->update(['category_id' => $this->category_id]);

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

    public function render(): View
    {
        return view('livewire.transactions.categorize', [
            'transaction' => $this->transaction,
            'transactions' => $this->transactions,
        ]);
    }
}
