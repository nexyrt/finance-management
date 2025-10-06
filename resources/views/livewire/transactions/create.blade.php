<div>
    {{-- Modal (triggered by event) --}}
    <x-modal title="Tambah Transaksi" wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="plus" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-50">Tambah Transaksi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Catat transaksi masuk atau keluar</p>
                </div>
            </div>
        </x-slot:title>

        <form id="transaction-form" wire:submit="save" class="space-y-6">
            {{-- Transaction Type Selection --}}
            <div class="rounded-xl p-4">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-3">Jenis Transaksi</h4>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative">
                        <input type="radio" wire:model.live="transaction_type" value="credit" class="sr-only peer"
                            checked>
                        <div
                            class="p-4 rounded-lg border-2 border-zinc-200 dark:border-gray-600 cursor-pointer transition-all peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <x-icon name="arrow-down" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-50">Pemasukan</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Uang masuk ke rekening</p>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="relative">
                        <input type="radio" wire:model.live="transaction_type" value="debit" class="sr-only peer">
                        <div
                            class="p-4 rounded-lg border-2 border-zinc-200 dark:border-gray-600 cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20">
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-10 w-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                    <x-icon name="arrow-up" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-gray-50">Pengeluaran</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Uang keluar dari rekening</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Transaction Details --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <div class="border-b border-zinc-200 dark:border-gray-600 pb-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1">Detail Transaksi</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Informasi utama transaksi</p>
                    </div>

                    <x-select.styled wire:model.live="bank_account_id" :options="$this->accounts
                        ->map(
                            fn($account) => [
                                'label' => $account->account_name . ' (' . $account->bank_name . ')',
                                'value' => $account->id,
                            ],
                        )
                        ->toArray()" label="Rekening Bank"
                        placeholder="Pilih rekening..." searchable />

                    <x-select.styled wire:model.live="category_id" :options="$this->categoriesOptions" placeholder="Pilih kategori..."
                        searchable>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Kategori Transaksi</span>
                                <x-tooltip color="zinc" text="Kategori disesuaikan dengan jenis transaksi" position="top" />
                            </div>
                        </x-slot:label>
                    </x-select.styled>

                    <x-wireui-currency prefix="Rp " wire:model.blur="amount" placeholder="0" color="dark:dark">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Jumlah</span>
                                <x-tooltip color="zinc" text="Jumlah transaksi dalam Rupiah" position="top" />
                            </div>
                        </x-slot:label>
                    </x-wireui-currency>

                    <x-date wire:model.live="transaction_date" helpers>
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Tanggal Transaksi</span>
                                <x-tooltip color="zinc" text="Pilih tanggal saat transaksi terjadi" position="top" />
                            </div>
                        </x-slot:label>
                    </x-date>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4">
                    <div class="border-b border-zinc-200 dark:border-gray-600 pb-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-50 mb-1">Keterangan</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Deskripsi dan referensi</p>
                    </div>

                    <x-input wire:model.live="description" placeholder="Contoh: Pembayaran gaji karyawan...">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Deskripsi</span>
                                <x-tooltip color="zinc" text="Jelaskan tujuan transaksi" position="top" />
                            </div>
                        </x-slot:label>
                    </x-input>

                    <x-input wire:model.live="reference_number" placeholder="Contoh: TRX20240101001">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Nomor Referensi (Opsional)</span>
                                <x-tooltip color="zinc" text="Nomor referensi atau kode transaksi" position="top" />
                            </div>
                        </x-slot:label>
                    </x-input>

                    <x-upload wire:model="attachment" accept="application/pdf,image/jpeg,image/jpg,image/png">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Bukti Transaksi (Opsional)</span>
                                <x-tooltip color="zinc" text="Upload PDF, JPG, JPEG, atau PNG (max 2MB)" position="top" />
                            </div>
                        </x-slot:label>
                    </x-upload>
                </div>
            </div>

            {{-- Preview --}}
            @if ($bank_account_id && $amount && $description)
                <div
                    class="bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-50 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/20 rounded-xl p-4 border border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-200 dark:border-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="h-8 w-8 bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100 dark:bg-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900/40 rounded-lg flex items-center justify-center">
                            <x-icon name="eye"
                                class="w-4 h-4 text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-400" />
                        </div>
                        <div>
                            <h5
                                class="text-sm font-semibold text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-900 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-100">
                                Preview Transaksi</h5>
                            <p
                                class="text-xs text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-800 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-200">
                                {{ $transaction_type === 'credit' ? 'Pemasukan' : 'Pengeluaran' }}</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-50">{{ $description }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $this->accounts->find($bank_account_id)?->account_name }} •
                                    {{ \Carbon\Carbon::parse($transaction_date)->format('d M Y') }}
                                </p>
                            </div>
                            <p
                                class="text-lg font-bold text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-600 dark:text-{{ $transaction_type === 'credit' ? 'green' : 'red' }}-400">
                                {{ $transaction_type === 'credit' ? '+' : '-' }}Rp
                                {{ number_format($amount, 0, ',', '.') }}
                            </p>
                        </div>
                        @if ($reference_number)
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">Ref: {{ $reference_number }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>

                <x-button type="submit" form="transaction-form" :color="$transaction_type === 'credit' ? 'green' : 'red'" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    <span wire:loading.remove wire:target="save">Simpan Transaksi</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
