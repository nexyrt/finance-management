<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="green" icon="plus">
        Pemasukan
    </x-button>

    {{-- Modal --}}
    <x-modal title="Tambah Pemasukan" wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-down-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Pemasukan</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Catat transaksi uang masuk ke rekening</p>
                </div>
            </div>
        </x-slot:title>

        <form id="income-create" wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Left Column: Detail Transaksi --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Transaksi</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Informasi utama pemasukan</p>
                    </div>

                    <x-select.styled
                        wire:model.live="bank_account_id"
                        :request="route('api.bank-accounts')"
                        label="Rekening Bank *"
                        placeholder="Pilih rekening..."
                        searchable
                    />

                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-select.styled
                                wire:model.live="category_id"
                                :request="[
                                    'url' => route('api.transaction-categories'),
                                    'method' => 'get',
                                    'params' => ['type' => 'credit'],
                                ]"
                                label="Kategori *"
                                placeholder="Pilih kategori..."
                                searchable
                            />
                        </div>
                        <div class="flex-shrink-0">
                            <livewire:transactions-categories.create />
                        </div>
                    </div>

                    <x-currency-input wire:model="amount" label="Jumlah *" prefix="Rp" placeholder="0" />

                    <x-date wire:model.live="transaction_date" label="Tanggal Transaksi *" placeholder="Pilih tanggal..." helpers />
                </div>

                {{-- Right Column: Keterangan --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Keterangan</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Deskripsi dan referensi transaksi</p>
                    </div>

                    <x-input wire:model="description" label="Deskripsi *" placeholder="Contoh: Pembayaran dari klien..." />

                    <x-input wire:model="reference_number" label="Nomor Referensi (Opsional)" placeholder="Contoh: TRX20240101001" />

                    <x-file-upload wire:model="attachment" label="Bukti Transaksi (Opsional)" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="income-create" color="green" icon="check" loading="save" class="w-full sm:w-auto order-1 sm:order-2">
                    Simpan Pemasukan
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
