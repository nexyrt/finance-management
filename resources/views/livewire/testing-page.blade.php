<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50">Testing Page</h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg">Eksperimen komponen</p>
    </div>

    {{-- Tombol buka modal --}}
    <div class="flex gap-3">
        <x-button wire:click="$toggle('modal')" text="Modal Custom" />
        <livewire:transactions.create-income />
        <livewire:transactions.create-expense />
    </div>

    {{-- ================================================================ --}}
    {{-- MODAL PEMASUKAN (Custom) --}}
    {{-- ================================================================ --}}

    <x-modal wire size="md" center scrollable x-on:keydown.enter.window="$wire.save()">
        <x-slot:title>
            Modal Custom
        </x-slot:title>

        <div class="p-4">
            <x-file-upload wire:model="attachment" label="Lampiran" multiple/>

            <x-select.styled wire:model="bank_account_id" :request="route('api.bank-accounts')" label="Rekening Bank"
                placeholder="Pilih rekening..." searchable />
            <x-currency-input wire:model="amount" label="Jumlah" prefix="Rp" placeholder="0" />
            <x-date wire:model="transaction_date" label="Tanggal Transaksi" placeholder="Pilih tanggal..." />

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-select.styled wire:model="category_id" :request="[
                        'url' => route('api.transaction-categories'),
                        'method' => 'get',
                        'params' => ['type' => 'debit'],
                    ]" placeholder="Pilih kategori..."
                        searchable label="Kategori *" />
                </div>
                <div class="flex-shrink-0">
                    <livewire:transactions-categories.create button-label="+" />
                </div>
            </div>

            <x-input wire:model="description" label="Deskripsi" placeholder="Masukkan deskripsi..." />
            <x-input wire:model="reference_number" label="Nomor Referensi" placeholder="Masukkan nomor referensi..." />
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-3">
                <x-button wire:click="$toggle('modal')" color="zinc">
                    Tutup
                </x-button>

                <x-button wire:click="save" loading="save" color="blue" icon="check">
                    Simpan
                </x-button>

            </div>
        </x-slot:footer>
    </x-modal>

</div>
