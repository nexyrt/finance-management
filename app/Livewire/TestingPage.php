<?php

namespace App\Livewire;

use App\Livewire\Traits\Alert;
use Livewire\Component;

class TestingPage extends Component
{
    use Alert;

    public bool $modal = false;

    // Form properties
    public string $transaction_type = 'credit';
    public ?string $bank_account_id = null;
    public ?string $category_id = null;
    public ?string $amount = null;
    public ?string $transaction_date = null;
    public ?string $description = null;
    public ?string $reference_number = null;

    public function mount()
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    public function save()
    {
        $this->validate([
            'bank_account_id' => 'required',
            'category_id' => 'required',
            'amount' => 'required',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        $this->modal = false;
        $this->success('Transaksi berhasil disimpan (testing)');
    }

    public function accountOptions(): array
    {
        return [
            ['label' => 'BCA - 1234567890', 'value' => '1'],
            ['label' => 'BNI - 0987654321', 'value' => '2'],
            ['label' => 'Mandiri - 1122334455', 'value' => '3'],
        ];
    }

    public function categoryOptions(): array
    {
        return match ($this->transaction_type) {
            'credit' => [
                ['label' => 'Pendapatan Usaha', 'value' => 'parent-1', 'disabled' => true],
                ['label' => '  ↳ Pembayaran Client', 'value' => '1'],
                ['label' => '  ↳ Jasa Konsultasi', 'value' => '2'],
                ['label' => 'Pendapatan Lainnya', 'value' => 'parent-2', 'disabled' => true],
                ['label' => '  ↳ Bunga Bank', 'value' => '3'],
            ],
            'debit' => [
                ['label' => 'Biaya Operasional', 'value' => 'parent-3', 'disabled' => true],
                ['label' => '  ↳ Gaji Karyawan', 'value' => '4'],
                ['label' => '  ↳ Sewa Kantor', 'value' => '5'],
                ['label' => 'Biaya Lainnya', 'value' => 'parent-4', 'disabled' => true],
                ['label' => '  ↳ Alat Tulis Kantor', 'value' => '6'],
            ],
            default => [],
        };
    }

    public function updatedTransactionType()
    {
        $this->category_id = null;
    }

    public function render()
    {
        return view('livewire.testing-page')->layout('components.layouts.new-layout');
    }
}
