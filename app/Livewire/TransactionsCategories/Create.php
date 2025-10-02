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
    public string $code = '';
    public string $label = '';
    public ?string $parent_code = null;
    public bool $modal = false;

    public function render(): View
    {
        return view('livewire.transactions-categories.create');
    }

    public function updatedType(): void
    {
        // Reset parent when type changes
        $this->parent_code = null;
    }

    #[Computed]
    public function parentOptions(): array
    {
        if (!$this->type) {
            return [];
        }

        return TransactionCategory::whereNull('parent_code')
            ->where('type', $this->type)
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->label,
                'value' => $cat->code
            ])
            ->toArray();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense,adjustment,transfer'],
            'code' => [
                'required',
                'string',
                'unique:transaction_categories,code',
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
        $this->validate();

        TransactionCategory::create([
            'type' => $this->type,
            'code' => $this->code,
            'label' => $this->label,
            'parent_code' => $this->parent_code,
        ]);

        $this->dispatch('created');
        $this->reset();
        $this->success('Category berhasil dibuat');
    }
}