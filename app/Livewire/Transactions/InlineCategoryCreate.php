<?php

namespace App\Livewire\Transactions;

use App\Livewire\Traits\Alert;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class InlineCategoryCreate extends Component
{
    use Alert;

    public string $transactionType = ''; // 'credit' or 'debit'
    public string $label = '';
    public ?int $parent_id = null;
    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.transactions.inline-category-create');
    }

    #[Computed]
    public function categoryType(): string
    {
        // Map transaction type to category type
        return $this->transactionType === 'credit' ? 'income' : 'expense';
    }

    #[Computed]
    public function parentOptions(): array
    {
        if (!$this->transactionType) {
            return [];
        }

        $categoryType = $this->categoryType;

        return TransactionCategory::whereNull('parent_id')
            ->where('type', $categoryType)
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->label,
                'value' => $cat->id
            ])
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:transaction_categories,id',
            ],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $category = TransactionCategory::create([
            'type' => $this->categoryType,
            'label' => $this->label,
            'parent_id' => $this->parent_id,
        ]);

        $this->dispatch('category-created', categoryId: $category->id);
        $this->modal = false;
        $this->reset(['label', 'parent_id']);
        $this->success('Kategori berhasil dibuat');
    }

    #[On('open-inline-category-modal')]
    public function openModal(string $transactionType): void
    {
        $this->transactionType = $transactionType;
        $this->modal = true;
    }
}
