<?php

namespace App\Livewire\Accounts;

use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Listing extends Component
{
    use WithPagination, Interactions;

    public $search = '';
    public $selectedBank = '';
    public $selectedStatus = 'active';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedBank' => ['except' => ''],
        'selectedStatus' => ['except' => 'active'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedBank()
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
    }

    public function getBankAccountsProperty()
    {
        return BankAccount::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('account_name', 'like', '%' . $this->search . '%')
                      ->orWhere('account_number', 'like', '%' . $this->search . '%')
                      ->orWhere('bank_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedBank, function($query) {
                $query->where('bank_name', 'like', '%' . $this->selectedBank . '%');
            })
            ->when($this->selectedStatus !== '', function($query) {
                if ($this->selectedStatus === 'active') {
                    $query->whereNotNull('id'); // All active accounts
                } else {
                    $query->where('status', $this->selectedStatus);
                }
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getBankOptionsProperty()
    {
        return BankAccount::select('bank_name')
            ->distinct()
            ->orderBy('bank_name')
            ->get()
            ->map(fn($bank) => [
                'label' => $bank->bank_name,
                'value' => $bank->bank_name
            ])
            ->prepend(['label' => 'All Banks', 'value' => '']);
    }

    public function getStatusOptionsProperty()
    {
        return [
            ['label' => 'All Status', 'value' => ''],
            ['label' => 'Active', 'value' => 'active'],
            ['label' => 'Inactive', 'value' => 'inactive'],
        ];
    }

    public function createAccount()
    {
        return redirect()->route('bank-accounts.create');
    }

    public function viewAccount($accountId)
    {
        return redirect()->route('bank-accounts.show', $accountId);
    }

    public function editAccount($accountId)
    {
        return redirect()->route('bank-accounts.edit', $accountId);
    }

    public function addTransaction($accountId)
    {
        return redirect()->route('bank-transactions.create', [
            'account_id' => $accountId,
            'type' => 'manual'
        ]);
    }

    public function render()
    {
        return view('livewire.bank-accounts.listing', [
            'bankAccounts' => $this->bankAccounts,
            'bankOptions' => $this->bankOptions,
            'statusOptions' => $this->statusOptions,
        ]);
    }
}