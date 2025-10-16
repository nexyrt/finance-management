<div>
    <x-modal title="Tambah Transaksi" wire="modal" size="xl" center persistent>
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

        <form id="transaction-form" wire:submit="save" class="space-y-6">
            {{-- Transaction Type Selection --}}
            @if (count($allowedTypes) > 1)
                <div class="rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">Jenis Transaksi</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @if (in_array('credit', $allowedTypes))
                            <label class="relative">
                                <input type="radio" wire:model.live="transaction_type" value="credit"
                                    class="sr-only peer">
                                <div
                                    class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                            <x-icon name="arrow-down"
                                                class="w-5 h-5 text-green-600 dark:text-green-400" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark-900 dark:text-dark-50">Pemasukan</p>
                                            <p class="text-xs text-dark-500 dark:text-dark-400">Uang masuk ke rekening
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif

                        @if (in_array('debit', $allowedTypes))
                            <label class="relative">
                                <input type="radio" wire:model.live="transaction_type" value="debit"
                                    class="sr-only peer">
                                <div
                                    class="p-4 rounded-lg border-2 border-secondary-200 dark:border-dark-600 cursor-pointer transition-all peer-checked:border-red-600 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-10 w-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                            <x-icon name="arrow-up" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        </div>
                                        <div>
                                            <p class="font-semibold text-dark-900 dark:text-dark-50">Pengeluaran</p>
                                            <p class="text-xs text-dark-500 dark:text-dark-400">Uang keluar dari
                                                rekening</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif
                    </div>
                </div>
            @else
                <div
                    class="bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-50 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/20 rounded-xl p-4 border border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-200 dark:border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-800">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/40 rounded-lg flex items-center justify-center">
                            <x-icon name="arrow-{{ $transaction_type === 'credit' ? 'down' : 'up' }}"
                                class="w-5 h-5 text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-400" />
                        </div>
                        <div>
                            <p
                                class="font-semibold text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100">
                                {{ $transaction_type === 'credit' ? 'Transaksi Pemasukan' : 'Transaksi Pengeluaran' }}
                            </p>
                            <p
                                class="text-xs text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-700 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-300">
                                {{ $transaction_type === 'credit' ? 'Uang masuk ke rekening' : 'Uang keluar dari rekening' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Transaction Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Transaksi</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Informasi utama transaksi</p>
                    </div>

                    <x-select.styled wire:model.live="bank_account_id" :options="$this->accounts
                        ->map(
                            fn($account) => [
                                'label' => $account->account_name . ' (' . $account->bank_name . ')',
                                'value' => $account->id,
                            ],
                        )
                        ->toArray()" label="Rekening Bank *"
                        placeholder="Pilih rekening..." searchable />

                    <x-select.styled wire:model.live="category_id" :options="$this->categoriesOptions" placeholder="Pilih kategori..."
                        searchable>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Kategori Transaksi *</span>
                                <x-tooltip color="secondary" text="Kategori disesuaikan dengan jenis transaksi"
                                    position="top" />
                            </div>
                        </x-slot:label>
                    </x-select.styled>

                    <x-wireui-currency prefix="Rp " wire:model.blur="amount" placeholder="0" thousands="."
                        decimal=",">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Jumlah *</span>
                                <x-tooltip color="secondary" text="Jumlah transaksi dalam Rupiah" position="top" />
                            </div>
                        </x-slot:label>
                    </x-wireui-currency>

                    <x-date wire:model.live="transaction_date" helpers>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Tanggal Transaksi *</span>
                                <x-tooltip color="secondary" text="Pilih tanggal saat transaksi terjadi"
                                    position="top" />
                            </div>
                        </x-slot:label>
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

                    <x-upload wire:model="attachment" label="Bukti Transaksi (Opsional)"
                        tip="Upload PDF, JPG, JPEG, atau PNG (max 2MB)"
                        accept="application/pdf,image/jpeg,image/jpg,image/png" delete />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>

                <x-button type="submit" form="transaction-form" :color="$transaction_type === 'credit' ? 'green' : 'red'" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Simpan Transaksi
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
