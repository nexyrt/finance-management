<?php

namespace App\Livewire\Transactions\Types;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use TallStackUi\Traits\Interactions;
use Carbon\Carbon;

class ManualTransaction extends Component
{
    use Interactions;

    public $showModal = false;
    public $bank_account_id = '';
    public $amount = '';
    public $transaction_type = 'credit';
    public $description = '';
    public $transaction_date;
    public $reference_number = '';

    protected $listeners = [
        'open-manual-transaction-modal' => 'openModal'
    ];

    protected $rules = [
        'bank_account_id' => 'required|exists:bank_accounts,id',
        'amount' => 'required|numeric|min:1',
        'transaction_type' => 'required|in:credit,debit',
        'description' => 'required|string|max:255',
        'transaction_date' => 'required|date',
        'reference_number' => 'nullable|string|max:100',
    ];

    public function mount($bank_account_id = null)
    {
        $this->bank_account_id = $bank_account_id ?? '';
        $this->transaction_date = Carbon::today()->format('Y-m-d');
        $this->generateReference();
    }

    public function generateReference()
    {
        $this->reference_number = 'TXN-' . now()->format('Ymd-His');
    }

    public function getBankAccountsProperty()
    {
        return BankAccount::orderBy('bank_name')->get()->map(fn($account) => [
            'label' => $account->bank_name . ' - ' . $account->account_name,
            'value' => $account->id
        ]);
    }

    public function getTransactionTypesProperty()
    {
        return [
            ['label' => 'Income (Credit)', 'value' => 'credit'],
            ['label' => 'Expense (Debit)', 'value' => 'debit'],
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['amount', 'description']);
        $this->transaction_date = Carbon::today()->format('Y-m-d');
        $this->transaction_type = 'credit';
        $this->generateReference();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $amount = BankAccount::parseAmount($this->amount);

        BankTransaction::create([
            'bank_account_id' => $this->bank_account_id,
            'amount' => $amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => $this->transaction_type,
            'description' => $this->description,
            'reference_number' => $this->reference_number,
        ]);

        $this->toast()
            ->success('Success!', 'Transaction recorded successfully.')
            ->send();

        $this->closeModal();
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.transactions.types.manual-transaction', [
            'bankAccounts' => $this->bankAccounts,
            'transactionTypes' => $this->transactionTypes,
        ]);
    }
}