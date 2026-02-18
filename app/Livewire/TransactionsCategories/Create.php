<?php

namespace App\Livewire\TransactionsCategories;

use App\Livewire\Traits\Alert;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Create extends Component
{
    use Alert;

    public string $type = '';
    public string $label = '';
    public ?int $parent_id = null;
    public bool $modal = false;
    public string $buttonLabel = '';
    public string $buttonIcon = 'plus';

    public function mount(string $buttonLabel = '', string $buttonIcon = 'plus'): void
    {
        $this->buttonLabel = $buttonLabel;
        $this->buttonIcon = $buttonIcon;
    }

    public function render(): View
    {
        return view('livewire.transactions-categories.create');
    }

    public function updatedType(): void
    {
        // Reset parent when type changes
        $this->parent_id = null;
    }

    #[Computed]
    public function parentOptions(): array
    {
        if (!$this->type) {
            return [];
        }

        return TransactionCategory::whereNull('parent_id')
            ->where('type', $this->type)
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
            'type' => ['required', 'in:income,expense,adjustment,transfer'],
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

        TransactionCategory::create([
            'type' => $this->type,
            'label' => $this->label,
            'parent_id' => $this->parent_id,
        ]);

        $this->dispatch('created');
        $this->reset();
        $this->success('Category berhasil dibuat');
    }
}