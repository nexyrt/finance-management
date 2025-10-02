<?php

namespace App\Livewire\TransactionsCategories;

use App\Livewire\Traits\Alert;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Update extends Component
{
    use Alert;

    public ?int $categoryId = null;
    public string $type = '';
    public string $code = '';
    public string $label = '';
    public ?string $parent_code = null;
    public int $transactionsCount = 0;
    public int $childrenCount = 0;
    public bool $modal = false;
    public string $originalType = '';

    public function render(): View
    {
        return view('livewire.transactions-categories.update');
    }

    #[On('load::category')]
    public function load(TransactionCategory $category): void
    {
        $category->loadCount(['transactions', 'children']);
        
        $this->categoryId = $category->id;
        $this->type = $category->type;
        $this->code = $category->code;
        $this->label = $category->label;
        $this->parent_code = $category->parent_code;
        $this->transactionsCount = $category->transactions_count;
        $this->childrenCount = $category->children_count;
        $this->originalType = $category->type;
        $this->modal = true;
    }

    public function updatedType(): void
    {
        // Reset parent when type changes
        if ($this->type !== $this->originalType) {
            $this->parent_code = null;
        }
    }

    #[Computed]
    public function parentOptions(): array
    {
        if (!$this->type) {
            return [];
        }

        return TransactionCategory::whereNull('parent_code')
            ->where('type', $this->type)
            ->where('id', '!=', $this->categoryId) // Exclude self
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->label,
                'value' => $cat->code
            ])
            ->toArray();
    }

    #[Computed]
    public function canChangeType(): bool
    {
        return $this->transactionsCount === 0;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense,adjustment,transfer'],
            'code' => [
                'required',
                'string',
                'unique:transaction_categories,code,' . $this->categoryId,
                'regex:/^[A-Z0-9_]+$/',
                'max:50'
            ],
            'label' => ['required', 'string', 'max:255'],
            'parent_code' => [
                'nullable',
                'exists:transaction_categories,code',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'code.regex' => 'Code harus uppercase, hanya huruf, angka, dan underscore',
            'code.unique' => 'Code sudah digunakan',
        ];
    }

    public function save(): void
    {
        // Prevent type change if has transactions
        if (!$this->canChangeType && $this->type !== $this->originalType) {
            $this->error('Cannot change type: category has ' . $this->transactionsCount . ' transactions');
            return;
        }

        $this->validate();
        
        $category = TransactionCategory::findOrFail($this->categoryId);
        $category->update([
            'type' => $this->type,
            'code' => $this->code,
            'label' => $this->label,
            'parent_code' => $this->parent_code,
        ]);

        $this->dispatch('updated');
        $this->reset();
        $this->success('Category berhasil diperbarui');
    }
}