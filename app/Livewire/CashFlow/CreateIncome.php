<?php

namespace App\Livewire\CashFlow;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\TransactionCategory;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class CreateIncome extends Component
{
    use Interactions, WithFileUploads;

    public bool $modal = false;

    // Source type selection
    public ?string $source_type = 'transaction'; // 'transaction' or 'payment'

    // Common fields
    public ?int $bank_account_id = null;

    public ?int $amount = null;

    public ?string $transaction_date = null;

    public ?string $reference_number = null;

    public $attachment = null;

    // Transaction-specific fields
    public ?int $category_id = null;

    public ?string $description = null;

    // Payment-specific fields
    public ?int $invoice_id = null;

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::orderBy('bank_name')
            ->get()
            ->map(fn ($account) => [
                'label' => $account->bank_name.' - '.$account->account_name,
                'value' => $account->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function incomeCategories()
    {
        return TransactionCategory::where('type', 'income')
            ->orderBy('label')
            ->get()
            ->map(fn ($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function invoices()
    {
        return \App\Models\Invoice::with('client')
            ->whereIn('status', ['sent', 'partially_paid'])
            ->latest()
            ->get()
            ->map(fn ($invoice) => [
                'label' => $invoice->invoice_number.' - '.$invoice->client->name.' (Sisa: Rp '.number_format($invoice->amount_remaining, 0, ',', '.').')',
                'value' => $invoice->id,
            ])
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'bank_account_id' => $this->bank_account_id,
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => 'credit',
            'description' => $this->description,
            'reference_number' => $this->reference_number,
        ];

        // Handle file upload
        if ($this->attachment) {
            $filename = time().'_'.$this->attachment->getClientOriginalName();
            $path = $this->attachment->storeAs('transactions', $filename, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $this->attachment->getClientOriginalName();
        }

        BankTransaction::create($data);

        $this->dispatch('income-created');
        $this->reset();
        $this->transaction_date = now()->format('Y-m-d');

        $this->toast()
            ->success('Berhasil', 'Pemasukan berhasil ditambahkan')
            ->send();
    }

    public function render()
    {
        return view('livewire.cash-flow.create-income');
    }
}
