<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Edit extends Component
{
    use Interactions;

    // Modal state
    public $showModal = false;
    public $accountId;

    // Form properties
    public $account_name = '';
    public $account_number = '';
    public $bank_name = '';
    public $branch = '';
    public $initial_balance = '';

    // Validation rules
    protected function rules()
    {
        return [
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts,account_number,' . $this->accountId,
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric|min:0'
        ];
    }

    protected $messages = [
        'account_name.required' => 'Nama rekening wajib diisi.',
        'account_name.max' => 'Nama rekening maksimal 255 karakter.',
        'account_number.required' => 'Nomor rekening wajib diisi.',
        'account_number.unique' => 'Nomor rekening sudah terdaftar.',
        'bank_name.required' => 'Nama bank wajib diisi.',
        'bank_name.max' => 'Nama bank maksimal 255 karakter.',
        'branch.max' => 'Nama cabang maksimal 255 karakter.',
        'initial_balance.required' => 'Saldo awal wajib diisi.',
        'initial_balance.numeric' => 'Saldo harus berupa angka.',
        'initial_balance.min' => 'Saldo tidak boleh negatif.'
    ];

    #[On('edit-account')]
    public function openModal($accountId)
    {
        $account = BankAccount::find($accountId);
        
        if (!$account) {
            $this->toast()->error('Error', 'Rekening tidak ditemukan.')->send();
            return;
        }

        $this->accountId = $accountId;
        $this->account_name = $account->account_name;
        $this->account_number = $account->account_number;
        $this->bank_name = $account->bank_name;
        $this->branch = $account->branch ?? '';
        $this->initial_balance = $account->initial_balance;
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        try {
            $account = BankAccount::find($this->accountId);
            
            $account->update([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch ?: null,
                'initial_balance' => BankAccount::parseAmount($this->initial_balance)
            ]);

            $this->closeModal();
            
            $this->dispatch('account-updated');
            
            $this->toast()
                ->success('Berhasil!', 'Rekening bank berhasil diperbarui.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat memperbarui rekening.')
                ->send();
        }
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    private function resetForm()
    {
        $this->accountId = null;
        $this->account_name = '';
        $this->account_number = '';
        $this->bank_name = '';
        $this->branch = '';
        $this->initial_balance = '';
    }

    public function render()
    {
        return view('livewire.accounts.edit');
    }
}