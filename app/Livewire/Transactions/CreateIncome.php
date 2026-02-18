<?php

namespace App\Livewire\Transactions;

use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class CreateIncome extends Component
{
    use WithFileUploads;
    use Interactions;

    public $modal = false;

    public $bank_account_id = null;

    public $category_id = null;

    public $amount = null;

    public $transaction_date = null;

    public $description = null;

    public $reference_number = null;

    public $attachment = null;

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function rules()
    {
        return [
            'bank_account_id'  => 'required|exists:bank_accounts,id',
            'category_id'      => 'required|exists:transaction_categories,id',
            'amount'           => 'required',
            'transaction_date' => 'required|date',
            'description'      => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'attachment'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'bank_account_id'  => $this->bank_account_id,
            'category_id'      => $this->category_id,
            'amount'           => BankTransaction::parseAmount($this->amount),
            'transaction_date' => $this->transaction_date,
            'transaction_type' => 'credit',
            'description'      => $this->description,
            'reference_number' => $this->reference_number,
        ];

        if ($this->attachment) {
            $filename = time() . '_' . $this->attachment->getClientOriginalName();
            $path = $this->attachment->storeAs('transaction-attachments', $filename, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $this->attachment->getClientOriginalName();
        }

        BankTransaction::create($data);

        $this->dispatch('transaction-created');
        $this->toast()->success('Pemasukan berhasil disimpan!')->send();

        $this->reset(['bank_account_id', 'category_id', 'amount', 'description', 'reference_number', 'attachment']);
        $this->resetValidation();
        $this->transaction_date = now()->format('Y-m-d');

        $this->dispatch('currency-reset');
        $this->dispatch('file-upload-reset');
    }

    public function render()
    {
        return view('livewire.transactions.create-income');
    }
}
