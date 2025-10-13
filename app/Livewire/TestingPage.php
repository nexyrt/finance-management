<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\BankTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TestingPage extends Component
{
    public ?string $search = null;
    public array $sort = ['column' => 'date', 'direction' => 'desc'];

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

    public bool $modal = false;
    public ?array $selectedItem = null;

    // Method baru
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

    public function render(): View
    {
        return view('livewire.testing-page');
    }

    #[Computed]
    public function rows(): Collection
    {
        $payments = Payment::with('invoice.client', 'bankAccount')
            ->when(
                $this->search,
                fn($q) =>
                $q->where('reference_number', 'like', "%{$this->search}%")
                    ->orWhereHas(
                        'invoice.client',
                        fn($c) =>
                        $c->where('name', 'like', "%{$this->search}%")
                    )
            )
            ->get()
            ->map(fn($p) => [
                'original_id' => $p->id, // Keep original ID
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
                'has_attachment' => $p->hasAttachment(), // Pre-check
            ]);

        $transactions = BankTransaction::with('category', 'bankAccount')
            ->whereHas('category', fn($q) => $q->where('type', 'income'))
            ->when(
                $this->search,
                fn($q) =>
                $q->where('reference_number', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->get()
            ->map(fn($t) => [
                'original_id' => $t->id, // Keep original ID
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
                'has_attachment' => $t->hasAttachment(), // Pre-check
            ]);

        $combined = $payments->concat($transactions);

        $sorted = $this->sort['direction'] === 'asc'
            ? $combined->sortBy($this->sort['column'])
            : $combined->sortByDesc($this->sort['column']);

        return $sorted->values();
    }
}