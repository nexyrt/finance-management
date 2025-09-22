<?php

namespace App\Livewire\Transactions;

use App\Models\BankTransaction;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Storage;

class Delete extends Component
{
    use Interactions;

    public $transactionId;
    public $transactionDescription;
    public $transactionAmount;
    public $transactionType;

    #[On('delete-transaction')]
    public function confirmDelete($transactionId)
    {
        $transaction = BankTransaction::with('bankAccount')->find($transactionId);

        if (!$transaction) {
            $this->toast()->error('Error', 'Transaksi tidak ditemukan.')->send();
            return;
        }

        $this->transactionId = $transactionId;
        $this->transactionDescription = $transaction->description;
        $this->transactionAmount = $transaction->amount;
        $this->transactionType = $transaction->transaction_type;

        $typeText = $this->transactionType === 'credit' ? 'Pemasukan' : 'Pengeluaran';
        $amountFormatted = 'Rp ' . number_format($this->transactionAmount, 0, ',', '.');

        $message = "Yakin ingin menghapus transaksi <strong>'{$this->transactionDescription}'</strong>?";
        $message .= "<br><br><div class='text-center bg-zinc-50 dark:bg-dark-700 rounded-lg p-3 my-3'>";
        $message .= "<div class='text-sm text-dark-600 dark:text-dark-400'>{$typeText}</div>";
        $message .= "<div class='text-lg font-bold text-{$this->getColorClass()}'>{$amountFormatted}</div>";
        $message .= "<div class='text-xs text-dark-500 dark:text-dark-400'>Rekening: {$transaction->bankAccount->account_name}</div>";
        $message .= "</div>";

        // Add attachment warning if exists
        if ($transaction->hasAttachment()) {
            $message .= "<div class='text-sm text-orange-600 dark:text-orange-400 mb-2'><strong>Info:</strong> Attachment akan ikut terhapus.</div>";
        }

        $message .= "<div class='text-sm text-amber-600 dark:text-amber-400'><strong>Peringatan:</strong> Penghapusan akan mempengaruhi saldo rekening dan tidak dapat dibatalkan.</div>";

        $this->dialog()
            ->question('Hapus Transaksi?', $message)
            ->confirm('Hapus', 'deleteTransaction', 'Transaksi berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    public function deleteTransaction()
    {
        try {
            $transaction = BankTransaction::find($this->transactionId);

            if (!$transaction) {
                $this->toast()->error('Error', 'Transaksi tidak ditemukan.')->send();
                return;
            }

            // Check if this is a transfer transaction
            if ($transaction->reference_number && str_starts_with($transaction->reference_number, 'TRF')) {
                $this->deleteTransferGroup($transaction);
            } else {
                $this->deleteSingleTransaction($transaction);
            }

            $this->dispatch('transaction-deleted');

        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menghapus transaksi.')
                ->send();
        }
    }

    private function deleteTransferGroup(BankTransaction $transaction): void
    {
        // Get all transactions with same reference number
        $relatedTransactions = BankTransaction::where('reference_number', $transaction->reference_number)->get();

        // Delete shared attachment only once
        if ($transaction->hasAttachment() && Storage::exists($transaction->attachment_path)) {
            Storage::delete($transaction->attachment_path);
        }

        // Delete all related transactions
        BankTransaction::where('reference_number', $transaction->reference_number)->delete();

        $this->toast()
            ->success('Berhasil!', 'Transfer dan transaksi terkait berhasil dihapus.')
            ->send();
    }

    private function deleteSingleTransaction(BankTransaction $transaction): void
    {
        // Delete attachment if exists (handled by model boot method)
        $transaction->delete();

        $this->toast()
            ->success('Berhasil!', 'Transaksi berhasil dihapus.')
            ->send();
    }

    private function getColorClass()
    {
        return $this->transactionType === 'credit'
            ? 'green-600 dark:text-green-400'
            : 'red-600 dark:text-red-400';
    }

    public function render()
    {
        return view('livewire.transactions.delete');
    }
}