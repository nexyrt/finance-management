<?php

namespace App\Livewire\Payments;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\Attributes\On;
use TallStackUi\Traits\Interactions;

class Create extends Component
{
    use Interactions;

    public ?Invoice $invoice = null;
    public bool $showModal = false;

    // Form properties
    public $amount = null; // Changed to support currency component
    public string $payment_date = '';
    public string $payment_method = 'bank_transfer';
    public string $bank_account_id = '';
    public string $reference_number = '';

    protected array $rules = [
        'amount' => 'required|numeric|min:1',
        'payment_date' => 'required|date',
        'payment_method' => 'required|in:cash,bank_transfer',
        'bank_account_id' => 'required|exists:bank_accounts,id',
        'reference_number' => 'nullable|string|max:255',
    ];

    #[On('record-payment')]
    public function recordPayment(int $invoiceId): void
    {
        $this->invoice = Invoice::with('client')->find($invoiceId);
        
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        // Auto-fill dengan remaining amount
        $remainingAmount = $this->invoice->amount_remaining;
        $this->amount = $remainingAmount > 0 ? $remainingAmount : null; // Set as numeric value
        
        // Set default tanggal hari ini
        $this->payment_date = now()->format('Y-m-d');
        
        $this->showModal = true;
    }

    public function resetData(): void
    {
        $this->invoice = null;
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->amount = null;
        $this->payment_date = '';
        $this->payment_method = 'bank_transfer';
        $this->bank_account_id = '';
        $this->reference_number = '';
        $this->resetValidation();
    }

    // Remove updatedAmount method since WireUI handles formatting

    public function save(): void
    {
        if (!$this->invoice) {
            $this->toast()->error('Error', 'Invoice tidak ditemukan')->send();
            return;
        }

        $this->validate();

        try {
            // Amount sudah dalam format numeric dari WireUI Currency
            $amountInteger = (int) $this->amount;
            
            // Validasi amount tidak melebihi sisa tagihan
            $remainingAmount = $this->invoice->amount_remaining;
            if ($amountInteger > $remainingAmount) {
                $this->addError('amount', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan: Rp ' . number_format($remainingAmount, 0, ',', '.'));
                return;
            }

            // Create payment
            Payment::create([
                'invoice_id' => $this->invoice->id,
                'bank_account_id' => $this->bank_account_id,
                'amount' => $amountInteger,
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'reference_number' => $this->reference_number ?: null,
            ]);

            // Update invoice status
            $this->invoice->refresh();
            $this->invoice->updateStatus();

            $this->toast()->success('Berhasil', 'Pembayaran berhasil dicatat')->send();
            $this->resetData();
            
            // Dispatch events untuk refresh components
            $this->dispatch('payment-created');
            $this->dispatch('invoice-updated'); 
            $this->dispatch('invoice-payment-updated'); // Tambahan untuk spesifik payment updates

        } catch (\Exception $e) {
            $this->toast()->error('Error', 'Gagal menyimpan pembayaran: ' . $e->getMessage())->send();
        }
    }

    public function getBankAccountsProperty()
    {
        return BankAccount::select('id', 'account_name', 'bank_name', 'account_number')
            ->orderBy('bank_name')
            ->get()
            ->map(fn($account) => [
                'label' => "{$account->bank_name} - {$account->account_name} ({$account->account_number})",
                'value' => $account->id
            ]);
    }

    public function render()
    {
        return view('livewire.payments.create');
    }
}