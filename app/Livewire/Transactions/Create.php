<?php

namespace App\Livewire\Transactions;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions, WithFileUploads;

    public bool $modal = false;

    // Form properties
    public ?int $bank_account_id = null;
    public ?string $amount = null;
    public ?string $transaction_date = null;
    public string $transaction_type = 'credit';
    public ?string $description = null;
    public ?string $reference_number = null;
    public $attachment = null;

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
        $this->transaction_type = 'credit';
    }

    #[On('create-transaction')]
    public function open(?int $bankAccountId = null): void
    {
        $this->resetForm();

        // Auto-fill bank account if provided
        if ($bankAccountId) {
            $this->bank_account_id = $bankAccountId;
        }

        $this->modal = true;
    }

    private function resetForm(): void
    {
        $this->reset(['bank_account_id', 'amount', 'description', 'reference_number', 'attachment']);
        $this->transaction_date = now()->format('Y-m-d');
        $this->transaction_type = 'credit';
    }

    public function rules()
    {
        return [
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|string',
            'transaction_date' => 'required|date',
            'transaction_type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ];
    }

    protected $messages = [
        'bank_account_id.required' => 'Pilih rekening bank.',
        'bank_account_id.exists' => 'Rekening tidak valid.',
        'amount.required' => 'Jumlah wajib diisi.',
        'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
        'transaction_date.date' => 'Format tanggal tidak valid.',
        'transaction_type.required' => 'Pilih jenis transaksi.',
        'description.required' => 'Deskripsi wajib diisi.',
        'description.max' => 'Deskripsi maksimal 255 karakter.',
        'reference_number.max' => 'Nomor referensi maksimal 255 karakter.',
        'attachment.file' => 'File harus berupa file.',
        'attachment.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
        'attachment.max' => 'Ukuran file maksimal 2MB.'
    ];

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
                'amount' => BankTransaction::parseAmount($this->amount),
                'transaction_date' => $this->transaction_date,
                'transaction_type' => $this->transaction_type,
                'description' => $this->description,
                'reference_number' => $this->reference_number ?: null,
            ];

            // Handle file upload
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