<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Transfer extends Component
{
    use Interactions, WithFileUploads;

    public bool $modal = false;
    public $from_account_id = '';
    public $to_account_id = '';
    public $category_id = null;
    public $amount = '';
    public $description = '';
    public $admin_fee = '2500';
    public $transfer_date = '';
    public $attachment = null;

    protected $rules = [
        'from_account_id' => 'required|exists:bank_accounts,id|different:to_account_id',
        'to_account_id' => 'required|exists:bank_accounts,id',
        'category_id' => 'required|exists:transaction_categories,id',
        'amount' => 'required|numeric|min:1',
        'description' => 'required|string|max:255',
        'admin_fee' => 'required|numeric|min:0',
        'transfer_date' => 'required|date',
        'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf'
    ];

    protected $messages = [
        'category_id.required' => 'Pilih kategori transfer.',
        'category_id.exists' => 'Kategori tidak valid.',
    ];

    public function mount(): void
    {
        $this->transfer_date = now()->format('Y-m-d');
    }

    #[Computed]
    public function transferCategories(): array
    {
        // Get transfer categories (parent and children)
        $parents = TransactionCategory::whereNull('parent_code')
            ->where('type', 'transfer')
            ->orderBy('label')
            ->get();

        $options = [];

        foreach ($parents as $parent) {
            // Add parent - disabled (hanya sebagai header)
            $options[] = [
                'label' => $parent->label,
                'value' => $parent->id,
                'disabled' => true,
            ];

            // Add children - enabled (bisa dipilih)
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

    #[On('open-transfer-modal')]
    public function openModal($fromAccountId = null)
    {
        if ($fromAccountId) {
            $this->from_account_id = $fromAccountId;
        }
        $this->modal = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $amount = BankTransaction::parseAmount($this->amount);
            $adminFee = BankTransaction::parseAmount($this->admin_fee);
            $totalDebit = $amount + $adminFee;
            $refNumber = 'TRF' . time();

            // Handle attachment upload
            $attachmentPath = null;
            $attachmentName = null;

            if ($this->attachment) {
                $attachmentPath = $this->attachment->store('bank-transactions', 'public');
                $attachmentName = $this->attachment->getClientOriginalName();
            }

            // Debit from source (amount + admin fee)
            BankTransaction::create([
                'bank_account_id' => $this->from_account_id,
                'category_id' => $this->category_id,
                'amount' => $totalDebit,
                'transaction_date' => $this->transfer_date,
                'transaction_type' => 'debit',
                'description' => 'Transfer + Admin Fee - ' . $this->description,
                'reference_number' => $refNumber,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            // Credit to destination (only transfer amount)
            BankTransaction::create([
                'bank_account_id' => $this->to_account_id,
                'category_id' => $this->category_id,
                'amount' => $amount,
                'transaction_date' => $this->transfer_date,
                'transaction_type' => 'credit',
                'description' => 'Transfer masuk - ' . $this->description,
                'reference_number' => $refNumber,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            $this->dispatch('transfer-completed');
            $this->resetExcept('transfer_date');

            $this->toast()
                ->success('Berhasil!', 'Transfer berhasil dilakukan.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage())
                ->send();
        }
    }

    // Delete uploaded attachment
    public function deleteUpload(array $content): void
    {
        $this->attachment = null;
        $this->resetValidation('attachment');
    }

    #[Computed]
    public function accounts()
    {
        return BankAccount::select('id', 'account_name', 'bank_name')->get();
    }

    #[Computed]
    public function fromAccountOptions(): array
    {
        return $this->accounts->map(fn($account) => [
            'label' => $account->account_name . ' - ' . $account->bank_name,
            'value' => $account->id
        ])->toArray();
    }

    #[Computed]
    public function toAccountOptions(): array
    {
        return $this->accounts
            ->where('id', '!=', $this->from_account_id)
            ->map(fn($account) => [
                'label' => $account->account_name . ' - ' . $account->bank_name,
                'value' => $account->id
            ])->toArray();
    }

    public function render()
    {
        return view('livewire.transactions.transfer');
    }
}