<?php

namespace App\Livewire\CashFlow;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use App\Models\TransactionCategory;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class CreateIncome extends Component
{
    use Interactions, WithFileUploads;

    public bool $modal = false;

    // Source type selection
    public ?string $source_type = 'transaction';

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
        $this->source_type = 'transaction';
    }

    public function rules(): array
    {
        $baseRules = [
            'source_type' => 'required|in:transaction,payment',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => ['required', 'integer', 'min:1'],
            'transaction_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        if ($this->source_type === 'transaction') {
            return array_merge($baseRules, [
                'category_id' => 'required|exists:transaction_categories,id',
                'description' => 'required|string|max:255',
            ]);
        }

        // Payment validation with dynamic max
        $rules = array_merge($baseRules, [
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        // Add max validation only if invoice is selected
        if ($this->invoice_id && $this->selectedInvoice) {
            $rules['amount'][] = 'max:' . $this->selectedInvoice->amount_remaining;
        }

        return $rules;
    }

    public function messages(): array
    {
        $remaining = $this->selectedInvoice?->amount_remaining ?? 0;

        return [
            'amount.max' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' .
                number_format($remaining, 0, ',', '.') . ')',
        ];
    }

    public function updatedSourceType()
    {
        // Reset specific fields when switching type
        $this->reset(['category_id', 'description', 'invoice_id', 'amount']);
        $this->resetValidation();
    }

    public function updatedInvoiceId()
    {
        // Auto-fill amount with remaining balance
        if ($this->selectedInvoice) {
            $this->amount = $this->selectedInvoice->amount_remaining;
        }
        $this->resetValidation('amount');
    }

    #[Computed]
    public function selectedInvoice(): ?Invoice
    {
        return $this->invoice_id ? Invoice::find($this->invoice_id) : null;
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::orderBy('bank_name')
            ->get()
            ->map(fn($account) => [
                'label' => $account->bank_name . ' - ' . $account->account_name,
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
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->id,
            ])
            ->toArray();
    }

    #[Computed]
    public function invoices()
    {
        return Invoice::with('client')
            ->whereIn('status', ['sent', 'partially_paid'])
            ->latest()
            ->get()
            ->map(fn($invoice) => [
                'label' => $invoice->invoice_number . ' - ' . $invoice->client->name .
                    ' (Sisa: Rp ' . number_format($invoice->amount_remaining, 0, ',', '.') . ')',
                'value' => $invoice->id,
            ])
            ->toArray();
    }

    public function save()
    {
        $this->validate();

        if ($this->source_type === 'transaction') {
            $this->createTransaction();
        } else {
            $this->createPayment();
        }

        $this->dispatch('income-created');
        $this->reset();
        $this->transaction_date = now()->format('Y-m-d');
        $this->source_type = 'transaction';

        $this->toast()
            ->success('Berhasil', 'Pemasukan berhasil ditambahkan')
            ->send();
    }

    private function createTransaction()
    {
        $data = [
            'bank_account_id' => $this->bank_account_id,
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'transaction_date' => $this->transaction_date,
            'transaction_type' => 'credit',
            'description' => $this->description,
            'reference_number' => $this->reference_number,
        ];

        if ($this->attachment) {
            $filename = time() . '_' . $this->attachment->getClientOriginalName();
            $path = $this->attachment->storeAs('transactions', $filename, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $this->attachment->getClientOriginalName();
        }

        BankTransaction::create($data);
    }

    private function createPayment()
    {
        $data = [
            'invoice_id' => $this->invoice_id,
            'bank_account_id' => $this->bank_account_id,
            'amount' => $this->amount,
            'payment_date' => $this->transaction_date,
            'payment_method' => 'bank_transfer',
            'reference_number' => $this->reference_number,
        ];

        if ($this->attachment) {
            $filename = time() . '_' . $this->attachment->getClientOriginalName();
            $path = $this->attachment->storeAs('payments', $filename, 'public');
            $data['attachment_path'] = $path;
            $data['attachment_name'] = $this->attachment->getClientOriginalName();
        }

        $payment = \App\Models\Payment::create($data);

        // Update invoice status
        $payment->invoice->updateStatus();
    }

    public function render()
    {
        return view('livewire.cash-flow.create-income');
    }
}