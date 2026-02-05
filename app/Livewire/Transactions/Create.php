<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions, WithFileUploads;

    public bool $modal = false;
    public array $allowedTypes = ['credit', 'debit']; // default both

    // Form properties
    public ?int $bank_account_id = null;
    public ?int $category_id = null;
    public ?string $amount = null;
    public ?string $transaction_date = null;
    public string $transaction_type = 'credit';
    public ?string $description = null;
    public ?string $reference_number = null;
    public $attachment = null;

    public function mount(?array $allowedTypes = null)
    {
        if ($allowedTypes) {
            $this->allowedTypes = $allowedTypes;
            $this->transaction_type = $allowedTypes[0];
        }

        $this->transaction_date = now()->format('Y-m-d');
    }

    #[On('create-transaction')]
    public function open(?int $bankAccountId = null, ?array $allowedTypes = null): void
    {
        $this->resetForm();

        if ($allowedTypes) {
            $this->allowedTypes = $allowedTypes;
            $this->transaction_type = $allowedTypes[0];
        }

        if ($bankAccountId) {
            $this->bank_account_id = $bankAccountId;
        }

        $this->modal = true;
    }

    private function resetForm(): void
    {
        $this->reset(['bank_account_id', 'category_id', 'amount', 'description', 'reference_number', 'attachment']);
        $this->transaction_date = now()->format('Y-m-d');

        if (!empty($this->allowedTypes)) {
            $this->transaction_type = $this->allowedTypes[0];
        }
    }

    public function updatedTransactionType(): void
    {
        $this->category_id = null;
    }

    #[Computed]
    public function categoriesOptions(): array
    {
        $categoryTypes = match ($this->transaction_type) {
            'credit' => ['income', 'adjustment', 'transfer'],
            'debit' => ['expense', 'adjustment', 'transfer'],
            default => []
        };

        $parents = TransactionCategory::whereNull('parent_id')
            ->whereIn('type', $categoryTypes)
            ->orderBy('type')
            ->orderBy('label')
            ->get();

        $options = [];

        foreach ($parents as $parent) {
            // Parent category - disabled (hanya sebagai header)
            $options[] = [
                'label' => $parent->label,
                'value' => $parent->id,
                'disabled' => true, // Disable parent category
            ];

            // Child categories - enabled (bisa dipilih)
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

    public function rules()
    {
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ];
    }

    #[Computed]
    public function accounts()
    {
        return BankAccount::orderBy('account_name')->get();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'bank_account_id' => $this->bank_account_id,
                'category_id' => $this->category_id,
                'amount' => BankTransaction::parseAmount($this->amount),
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->transaction_type,
                'description' => $this->description,
                'reference_number' => $this->reference_number ?: null,
            ];

            if ($this->attachment) {
                $filename = time() . '_' . $this->attachment->getClientOriginalName();
                $path = $this->attachment->storeAs('transaction-attachments', $filename, 'public');

                $data['attachment_path'] = $path;
                $data['attachment_name'] = $this->attachment->getClientOriginalName();
            }

            BankTransaction::create($data);

            $this->dispatch('transaction-created');
            $this->resetForm();
            $this->modal = false;

            $this->toast()->success('Berhasil!', 'Transaksi berhasil ditambahkan.')->send();

        } catch (\Exception $e) {
            $this->toast()->error('Gagal!', 'Terjadi kesalahan saat menyimpan transaksi.')->send();
        }
    }

    public function render()
    {
        return view('livewire.transactions.create');
    }
}