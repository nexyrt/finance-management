<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions;

    // Modal state
    public $showModal = false;
    public $selectedAccountId = null;

    // Form properties
    public $bank_account_id = '';
    public $amount = '';
    public $transaction_date = '';
    public $transaction_type = 'credit';
    public $description = '';
    public $reference_number = '';

    protected $rules = [
        'bank_account_id' => 'required|exists:bank_accounts,id',
        'amount' => 'required|numeric|min:1',
        'transaction_date' => 'required|date',
        'transaction_type' => 'required|in:credit,debit',
        'description' => 'required|string|max:255',
        'reference_number' => 'nullable|string|max:255'
    ];

    protected $messages = [
        'bank_account_id.required' => 'Pilih rekening bank.',
        'bank_account_id.exists' => 'Rekening tidak valid.',
        'amount.required' => 'Jumlah wajib diisi.',
        'amount.numeric' => 'Jumlah harus berupa angka.',
        'amount.min' => 'Jumlah minimal 1.',
        'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        'transaction_date.date' => 'Format tanggal tidak valid.',
        'transaction_type.required' => 'Pilih jenis transaksi.',
        'description.required' => 'Deskripsi wajib diisi.',
        'description.max' => 'Deskripsi maksimal 255 karakter.',
        'reference_number.max' => 'Nomor referensi maksimal 255 karakter.'
    ];

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    #[On('open-transaction-modal')]
    public function openModal($accountId = null)
    {
        if ($accountId) {
            $this->selectedAccountId = $accountId;
            $this->bank_account_id = $accountId;
        }
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
            BankTransaction::create([
                'bank_account_id' => $this->bank_account_id,
                'amount' => BankTransaction::parseAmount($this->amount),
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->transaction_type,
                'description' => $this->description,
                'reference_number' => $this->reference_number ?: null,
            ]);

            $this->closeModal();
            $this->dispatch('transaction-created');
            
            $this->toast()
                ->success('Berhasil!', 'Transaksi berhasil ditambahkan.')
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menyimpan transaksi.')
                ->send();
        }
    }

    public function updated($property)
    {
        $this->validateOnly($property);
    }

    private function resetForm()
    {
        $this->selectedAccountId = null;
        $this->bank_account_id = '';
        $this->amount = '';
        $this->transaction_date = now()->format('Y-m-d');
        $this->transaction_type = 'credit';
        $this->description = '';
        $this->reference_number = '';
    }

    public function render()
    {
        $accounts = BankAccount::orderBy('account_name')->get();
        
        return view('livewire.transactions.create', [
            'accounts' => $accounts
        ]);
    }
}