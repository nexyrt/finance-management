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

    protected function messages()
    {
        return [
            'account_name.required' => __('validation.required', ['attribute' => __('pages.account_name')]),
            'account_name.max' => __('validation.max.string', ['attribute' => __('pages.account_name'), 'max' => 255]),
            'account_number.required' => __('validation.required', ['attribute' => __('pages.account_number')]),
            'account_number.unique' => __('validation.unique', ['attribute' => __('pages.account_number')]),
            'bank_name.required' => __('validation.required', ['attribute' => __('pages.bank_name')]),
            'bank_name.max' => __('validation.max.string', ['attribute' => __('pages.bank_name'), 'max' => 255]),
            'branch.max' => __('validation.max.string', ['attribute' => __('pages.branch'), 'max' => 255]),
            'initial_balance.required' => __('validation.required', ['attribute' => __('pages.initial_balance')]),
            'initial_balance.numeric' => __('validation.numeric', ['attribute' => __('pages.initial_balance')]),
            'initial_balance.min' => __('validation.min.numeric', ['attribute' => __('pages.initial_balance'), 'min' => 0])
        ];
    }

    #[On('edit-account')]
    public function openModal($accountId)
    {
        $account = BankAccount::find($accountId);
        
        if (!$account) {
            $this->toast()->error(__('common.error'), __('pages.account_not_found'))->send();
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
                ->success(__('common.success'), __('pages.account_updated_successfully'))
                ->send();

        } catch (\Exception $e) {
            $this->toast()
                ->error(__('common.error'), __('pages.account_update_failed'))
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