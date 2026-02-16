<div class="space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-dark-900 dark:text-dark-50">Testing Page</h1>
        <p class="text-gray-600 dark:text-gray-400 text-lg">Eksperimen komponen</p>
    </div>

    {{-- Content --}}
    <x-button text="Tambah Transaksi" icon="plus" wire:click="$toggle('modal')" />

    {{-- Modal --}}
    <x-modal title="Tambah Transaksi" size="xl" center persistent wire>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Transaksi</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Catat transaksi masuk atau keluar</p>
                </div>
            </div>
        </x-slot:title>

        <form id="test-transaction-form" wire:submit="save" class="space-y-6">
            {{-- Transaction Type Selection --}}
            <div class="rounded-xl p-4">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">Jenis Transaksi</h4>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" wire:model.live="transaction_type" value="credit" class="sr-only peer">
                        <div class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <x-icon name="arrow-down" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-dark-900 dark:text-dark-50">Pemasukan</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400">Uang masuk ke rekening</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" wire:model.live="transaction_type" value="debit" class="sr-only peer">
                        <div class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                    <x-icon name="arrow-up" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-dark-900 dark:text-dark-50">Pengeluaran</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400">Uang keluar dari rekening</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Form Fields --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Transaksi</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Informasi utama transaksi</p>
                    </div>

                    <x-select.styled wire:model.live="bank_account_id"
                        :options="$this->accountOptions()"
                        label="Rekening Bank *"
                        placeholder="Pilih rekening..."
                        searchable />

                    <x-select.styled wire:model.live="category_id"
                        :options="$this->categoryOptions()"
                        label="Kategori Transaksi *"
                        placeholder="Pilih kategori..."
                        searchable />

                    <x-currency-input wire:model="amount" label="Jumlah *" prefix="Rp"
                        placeholder="0" hint="Jumlah transaksi dalam Rupiah" />

                    <x-date wire:model.live="transaction_date" helpers label="Tanggal Transaksi *">
                        <x-slot name="hint">
                            Pilih tanggal saat transaksi terjadi
                        </x-slot>
                    </x-date>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Keterangan</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Deskripsi dan referensi</p>
                    </div>

                    <x-input wire:model.live="description" label="Deskripsi *"
                        placeholder="Contoh: Pembayaran gaji karyawan..." />

                    <x-input wire:model.live="reference_number" label="Nomor Referensi (Opsional)"
                        placeholder="Contoh: TRX20240101001" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="test-transaction-form"
                    :color="$transaction_type === 'credit' ? 'green' : 'red'"
                    icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Simpan Transaksi
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
