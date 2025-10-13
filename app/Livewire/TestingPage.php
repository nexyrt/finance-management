<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\BankTransaction;
use App\Models\BankAccount;
use App\Models\TransactionCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TestingPage extends Component
{
    public ?string $search = null;
    public array $sort = ['column' => 'date', 'direction' => 'desc'];

    public $dateRange = null;
    
    // Filter properties
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public array $filterTypes = [];
    public array $filterCategories = [];
    public array $filterBankAccounts = [];
    
    public bool $modal = false;
    public ?array $selectedItem = null;

    public array $headers = [
        ['index' => 'date', 'label' => 'Tanggal'],
        ['index' => 'source_type', 'label' => 'Tipe'],
        ['index' => 'reference', 'label' => 'Referensi'],
        ['index' => 'description', 'label' => 'Deskripsi'],
        ['index' => 'client_name', 'label' => 'Klien'],
        ['index' => 'bank_account', 'label' => 'Rekening'],
        ['index' => 'category', 'label' => 'Kategori'],
        ['index' => 'amount', 'label' => 'Jumlah'],
        ['index' => 'action', 'label' => 'Aksi', 'sortable' => false],
    ];

    public function render(): View
    {
        return view('livewire.testing-page');
    }

    #[Computed]
    public function typeOptions(): array
    {
        return [
            ['label' => 'Payment', 'value' => 'payment'],
            ['label' => 'Transaction', 'value' => 'transaction'],
        ];
    }

    public function filter(): void
    {
        dd($this->dateRange);
    }

    #[Computed]
    public function categoryOptions(): array
    {
        $categories = collect([['label' => 'Payment', 'value' => 'Payment']]);
        
        $transactionCategories = TransactionCategory::where('type', 'income')
            ->orderBy('label')
            ->get()
            ->map(fn($cat) => [
                'label' => $cat->full_path,
                'value' => $cat->label
            ]);

        return $categories->concat($transactionCategories)->toArray();
    }

    #[Computed]
    public function bankAccountOptions(): array
    {
        return BankAccount::orderBy('account_name')
            ->get()
            ->map(fn($bank) => [
                'label' => $bank->account_name . ' - ' . $bank->bank_name,
                'value' => $bank->account_name
            ])
            ->toArray();
    }

    public function resetFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'filterTypes', 'filterCategories', 'filterBankAccounts', 'search']);
    }

    #[Computed]
    public function rows(): Collection
    {
        $payments = Payment::with('invoice.client', 'bankAccount')
            ->when($this->search, fn($q) => 
                $q->where('reference_number', 'like', "%{$this->search}%")
                  ->orWhereHas('invoice.client', fn($c) => 
                      $c->where('name', 'like', "%{$this->search}%")
                  )
            )
            ->when($this->dateFrom, fn($q) => 
                $q->whereDate('payment_date', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) => 
                $q->whereDate('payment_date', '<=', $this->dateTo)
            )
            ->when(!empty($this->filterBankAccounts), fn($q) =>
                $q->whereHas('bankAccount', fn($b) => 
                    $b->whereIn('account_name', $this->filterBankAccounts)
                )
            )
            ->get()
            ->map(fn($p) => [
                'original_id' => $p->id,
                'source_type' => 'payment',
                'amount' => $p->amount,
                'formatted_amount' => 'Rp ' . number_format($p->amount, 0, ',', '.'),
                'date' => $p->payment_date,
                'date_formatted' => $p->payment_date->format('d M Y'),
                'reference' => $p->reference_number ?? '-',
                'description' => "Payment - Invoice #{$p->invoice->invoice_number}",
                'client_name' => $p->invoice->client->name ?? '-',
                'bank_account' => $p->bankAccount->account_name ?? '-',
                'category' => 'Payment',
                'has_attachment' => $p->hasAttachment(),
            ]);

        $transactions = BankTransaction::with('category', 'bankAccount')
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->when($this->search, fn($q) =>
                $q->where('reference_number', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->when($this->dateFrom, fn($q) => 
                $q->whereDate('transaction_date', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) => 
                $q->whereDate('transaction_date', '<=', $this->dateTo)
            )
            ->when(!empty($this->filterCategories), fn($q) =>
                $q->whereHas('category', fn($c) => 
                    $c->whereIn('label', $this->filterCategories)
                )
            )
            ->when(!empty($this->filterBankAccounts), fn($q) =>
                $q->whereHas('bankAccount', fn($b) => 
                    $b->whereIn('account_name', $this->filterBankAccounts)
                )
            )
            ->get()
            ->map(fn($t) => [
                'original_id' => $t->id,
                'source_type' => 'transaction',
                'amount' => $t->amount,
                'formatted_amount' => 'Rp ' . number_format($t->amount, 0, ',', '.'),
                'date' => $t->transaction_date,
                'date_formatted' => $t->transaction_date->format('d M Y'),
                'reference' => $t->reference_number ?? '-',
                'description' => $t->description ?? '-',
                'client_name' => '-',
                'bank_account' => $t->bankAccount->account_name ?? '-',
                'category' => $t->category->label ?? 'Income',
                'has_attachment' => $t->hasAttachment(),
            ]);

        $combined = $payments->concat($transactions);

        // Filter by type
        if (!empty($this->filterTypes)) {
            $combined = $combined->whereIn('source_type', $this->filterTypes);
        }

        // Filter by category
        if (!empty($this->filterCategories)) {
            $combined = $combined->whereIn('category', $this->filterCategories);
        }

        // Manual sorting
        $sorted = $this->sort['direction'] === 'asc' 
            ? $combined->sortBy($this->sort['column'])
            : $combined->sortByDesc($this->sort['column']);

        return $sorted->values();
    }

    public function viewAttachment($id, $sourceType): void
    {
        if ($sourceType === 'payment') {
            $item = Payment::find($id);
            $this->selectedItem = [
                'type' => 'payment',
                'reference' => $item->reference_number ?? '-',
                'date' => $item->payment_date->format('d M Y'),
                'amount' => 'Rp ' . number_format($item->amount, 0, ',', '.'),
                'has_attachment' => $item->hasAttachment(),
                'attachment_url' => $item->attachment_url,
                'attachment_name' => $item->attachment_name,
                'is_image' => $item->isImageAttachment(),
                'is_pdf' => $item->isPdfAttachment(),
            ];
        } else {
            $item = BankTransaction::find($id);
            $this->selectedItem = [
                'type' => 'transaction',
                'reference' => $item->reference_number ?? '-',
                'date' => $item->transaction_date->format('d M Y'),
                'amount' => 'Rp ' . number_format($item->amount, 0, ',', '.'),
                'has_attachment' => $item->hasAttachment(),
                'attachment_url' => $item->attachment_url,
                'attachment_name' => $item->attachment_name,
                'is_image' => $item->isImageAttachment(),
                'is_pdf' => $item->isPdfAttachment(),
            ];
        }
        
        $this->modal = true;
    }
}