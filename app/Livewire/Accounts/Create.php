<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions;

    // Modal state
    public $showModal = false;

    // Form properties
    public $account_name = '';
    public $account_number = '';
    public $bank_name = '';
    public $branch = '';
    public $initial_balance = 0; // Changed from string to int

    // Validation rules
    protected $rules = [
        'account_name' => 'required|string|max:255',
        'account_number' => 'required|string|unique:bank_accounts,account_number',
        'bank_name' => 'required|string|max:255',
        'branch' => 'nullable|string|max:255',
        'initial_balance' => 'required|integer|min:0' // Changed to integer
    ];

    protected $messages = [
        'account_name.required' => 'Nama rekening wajib diisi.',
        'account_name.max' => 'Nama rekening maksimal 255 karakter.',
        'account_number.required' => 'Nomor rekening wajib diisi.',
        'account_number.unique' => 'Nomor rekening sudah terdaftar.',
        'bank_name.required' => 'Nama bank wajib diisi.',
        'bank_name.max' => 'Nama bank maksimal 255 karakter.',
        'branch.max' => 'Nama cabang maksimal 255 karakter.',
        'initial_balance.required' => 'Saldo awal wajib diisi.',
        'initial_balance.integer' => 'Saldo harus berupa angka bulat.',
        'initial_balance.min' => 'Saldo tidak boleh negatif.'
    ];

    #[On('open-create-account-modal')]
    public function openModal()
    {
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
            BankAccount::create([
                'account_name' => $this->account_name,
                'account_number' => $this->account_number,
                'bank_name' => $this->bank_name,
                'branch' => $this->branch ?: null,
                'initial_balance' => $this->initial_balance // Direct assignment since it's already integer
            ]);

            $this->closeModal();
            
            // Dispatch to refresh the Index component
            $this->dispatch('account-created');
            
            $this->toast()
                ->success('Berhasil!', 'Rekening bank berhasil ditambahkan.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menyimpan rekening.')
                ->send();
        }
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    private function resetForm()
    {
        $this->account_name = '';
        $this->account_number = '';
        $this->bank_name = '';
        $this->branch = '';
        $this->initial_balance = 0; // Reset to 0 instead of empty string
    }

    public function render()
    {
        return view('livewire.accounts.create');
    }
}