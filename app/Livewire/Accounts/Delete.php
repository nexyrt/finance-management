<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Delete extends Component
{
    use Interactions;

    public $accountId;
    public $accountName;

    #[On('delete-account')]
    public function confirmDelete($accountId)
    {
        $account = BankAccount::find($accountId);
        
        if (!$account) {
            $this->toast()->error('Error', 'Rekening tidak ditemukan.')->send();
            return;
        }

        $this->accountId = $accountId;
        $this->accountName = $account->account_name;

        // Get transaction and payment counts
        $transactionCount = $account->transactions()->count();
        $paymentCount = $account->payments()->count();
        
        $message = "Yakin ingin menghapus rekening <strong>'{$this->accountName}'</strong>?";
        
        if ($transactionCount > 0 || $paymentCount > 0) {
            $message .= "<br><br>Rekening ini memiliki:";
            if ($transactionCount > 0) {
                $message .= "<br>• <span class='text-red-600'>{$transactionCount} transaksi</span>";
            }
            if ($paymentCount > 0) {
                $message .= "<br>• <span class='text-red-600'>{$paymentCount} pembayaran</span>";
            }
            $message .= "<br><br><em>Semua data terkait akan ikut terhapus dan tidak dapat dikembalikan.</em>";
        }

        // Show confirmation dialog
        $this->dialog()
            ->question('Hapus Rekening?', $message)
            ->confirm('Hapus', 'deleteAccount', 'Rekening berhasil dihapus')
            ->cancel('Batal')
            ->send();
    }

    public function deleteAccount()
    {
        try {
            $account = BankAccount::find($this->accountId);
            $account->delete();
            
            $this->dispatch('account-deleted');
            
            $this->toast()
                ->success('Berhasil!', 'Rekening berhasil dihapus.')
                ->send();
                
        } catch (\Exception $e) {
            $this->toast()
                ->error('Gagal!', 'Terjadi kesalahan saat menghapus rekening.')
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.accounts.delete');
    }
}