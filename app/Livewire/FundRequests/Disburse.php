<?php

namespace App\Livewire\FundRequests;

use App\Livewire\Traits\Alert;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\FundRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Disburse extends Component
{
    use Alert;
    public bool $modal = false;
    public ?int $fundRequestId = null;
    public string $bankAccountId = '';
    public string $disbursementDate = '';
    public string $disbursementNotes = '';

    protected function rules(): array
    {
        return [
            'bankAccountId' => 'required|exists:bank_accounts,id',
            'disbursementDate' => 'required|date|before_or_equal:today',
            'disbursementNotes' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'bankAccountId.required' => __('pages.bank_account_required'),
            'disbursementDate.required' => __('pages.disbursement_date_required'),
            'disbursementDate.before_or_equal' => __('pages.disbursement_date_not_future'),
        ];
    }

    #[On('disburse::fund-request')]
    public function openModal(int $id): void
    {
        $fundRequest = FundRequest::findOrFail($id);

        // Check if can disburse
        if (! $fundRequest->canDisburse()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_disburse_fund_request'))->send();

            return;
        }

        // Check permission
        if (! auth()->user()->can('disburse fund requests')) {
            $this->toast()->error(__('common.error'), __('pages.unauthorized_disburse_fund_request'))->send();

            return;
        }

        $this->fundRequestId = $id;
        $this->reset('bankAccountId', 'disbursementDate', 'disbursementNotes');
        $this->disbursementDate = today()->format('Y-m-d');
        $this->modal = true;
    }

    #[Computed]
    public function fundRequest(): ?FundRequest
    {
        return $this->fundRequestId
            ? FundRequest::with(['user', 'items.category.parent'])->find($this->fundRequestId)
            : null;
    }

    #[Computed]
    public function bankAccounts()
    {
        return BankAccount::orderBy('account_name')->get();
    }

    public function disburse(): void
    {
        $this->validate();

        $fundRequest = $this->fundRequest;

        if (! $fundRequest || ! $fundRequest->canDisburse()) {
            $this->toast()->error(__('common.error'), __('pages.cannot_disburse_fund_request'))->send();
            $this->modal = false;

            return;
        }

        DB::transaction(function () use ($fundRequest) {
            $transactionIds = [];

            foreach ($fundRequest->items as $item) {
                $transaction = BankTransaction::create([
                    'bank_account_id' => $this->bankAccountId,
                    'amount' => $item->amount,
                    'transaction_date' => $this->disbursementDate,
                    'transaction_type' => 'debit',
                    'category_id' => $item->category_id,
                    'description' => "Fund Disbursement: {$fundRequest->title} - {$item->description}",
                    'reference_number' => $this->disbursementNotes,
                ]);

                $transactionIds[] = $transaction->id;
            }

            $mainTransactionId = $transactionIds[0];

            $fundRequest->disburse(
                $mainTransactionId,
                $this->disbursementDate,
                auth()->id(),
                $this->disbursementNotes
            );
        });

        $this->modal = false;
        $this->reset();

        $this->toast()->success(__('common.success'), __('pages.fund_request_disbursed'))->send();
        $this->dispatch('fund-request-disbursed');
    }

    public function render(): View
    {
        return view('livewire.fund-requests.disburse', [
            'fundRequest' => $this->fundRequest,
        ]);
    }
}
