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
            $this->toast()->error(__('common.error'), __('pages.account_not_found'))->send();
            return;
        }

        $this->accountId = $accountId;
        $this->accountName = $account->account_name;

        // Get transaction and payment counts
        $transactionCount = $account->transactions()->count();
        $paymentCount = $account->payments()->count();

        $message = __('pages.confirm_delete_account', ['name' => "<strong>'{$this->accountName}'</strong>"]);

        if ($transactionCount > 0 || $paymentCount > 0) {
            $message .= "<br><br>" . __('pages.account_has_data');
            if ($transactionCount > 0) {
                $message .= "<br>• <span class='text-red-600'>" . __('pages.transaction_count', ['count' => $transactionCount]) . "</span>";
            }
            if ($paymentCount > 0) {
                $message .= "<br>• <span class='text-red-600'>" . __('pages.payment_count', ['count' => $paymentCount]) . "</span>";
            }
            $message .= "<br><br><em>" . __('pages.delete_warning_cascade') . "</em>";
        }

        // Show confirmation dialog
        $this->dialog()
            ->question(__('pages.delete_account_title'), $message)
            ->confirm(__('common.delete'), 'deleteAccount', __('pages.account_deleted_successfully'))
            ->cancel(__('common.cancel'))
            ->send();
    }

    public function deleteAccount()
    {
        try {
            $account = BankAccount::find($this->accountId);
            $account->delete();
            
            $this->dispatch('account-deleted');
            
            $this->toast()
                ->success(__('common.success'), __('pages.account_deleted_successfully'))
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error(__('common.error'), __('pages.account_delete_failed'))
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.accounts.delete');
    }
}