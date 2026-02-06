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

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-200">Kategori Transaksi *</span>
                            <button type="button"
                                wire:click="$dispatch('open-inline-category-modal', { transactionType: '{{ $transaction_type }}' })"
                                class="flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20">
                                <x-icon name="plus" class="w-4 h-4" />
                                <span>Tambah</span>
                            </button>
                        </div>
                        <x-select.styled wire:model.live="category_id" :options="$this->categoriesOptions"
                            placeholder="Pilih kategori..." searchable />
                    </div>

                    <x-wireui-currency prefix="Rp " wire:model.blur="amount" placeholder="0" thousands="."
                        decimal=",">
                        <x-slot name="label">
                            Jumlah <span class="text-red-500">*</span>
                        </x-slot>
                        <x-slot name="hint">
                            Jumlah transaksi dalam Rupiah
                        </x-slot>
                    </x-wireui-currency>

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

    {{-- Inline Category Creation Component --}}
    <livewire:transactions.inline-category-create :transactionType="$transaction_type" />
</div>
