<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Transfer extends Component
{
    use Interactions;

    public $showModal = false;
    public $from_account_id = '';
    public $to_account_id = '';
    public $amount = '';
    public $description = '';
    public $admin_fee = 2500;
    public $transfer_date = '';

    protected $rules = [
        'from_account_id' => 'required|exists:bank_accounts,id|different:to_account_id',
        'to_account_id' => 'required|exists:bank_accounts,id',
        'amount' => 'required|numeric|min:1',
        'description' => 'required|string|max:255',
        'admin_fee' => 'required|numeric|min:0',
        'transfer_date' => 'required|date'
    ];

    #[On('open-transfer-modal')]
    public function openModal($fromAccountId = null)
    {
        $this->transfer_date = now()->format('Y-m-d');
        if ($fromAccountId) {
            $this->from_account_id = $fromAccountId;
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['from_account_id', 'to_account_id', 'amount', 'description', 'admin_fee', 'transfer_date']);
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            $amount = (int) str_replace(['.', ','], '', $this->amount);
            $adminFee = (int) str_replace(['.', ','], '', $this->admin_fee);
            $totalDebit = $amount + $adminFee;
            $refNumber = 'TRF' . time();
            
            // Debit from source (amount + admin fee)
            BankTransaction::create([
                'bank_account_id' => $this->from_account_id,
                'amount' => $totalDebit,
                'transaction_date' => $this->transfer_date,
                'transaction_type' => 'debit',
                'description' => 'Transfer + Admin Fee - ' . $this->description,
                'reference_number' => $refNumber,
            ]);

            // Credit to destination (only transfer amount)
            BankTransaction::create([
                'bank_account_id' => $this->to_account_id,
                'amount' => $amount,
                'transaction_date' => $this->transfer_date,
                'transaction_type' => 'credit',
                'description' => 'Transfer masuk - ' . $this->description,
                'reference_number' => $refNumber,
            ]);

            $this->closeModal();
            $this->dispatch('transfer-completed');
            
            $this->toast()
                ->success('Berhasil!', 'Transfer berhasil dilakukan.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan: ' . $e->getMessage())
                ->send();
        }
    }

    public function render()
    {
        $accounts = BankAccount::select('id', 'account_name', 'bank_name')->get();
        return view('livewire.transactions.transfer', compact('accounts'));
    }
}