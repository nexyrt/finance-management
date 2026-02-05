<div>
    {{-- Modal --}}
    <x-modal title="Tambah Kategori Cepat" center wire="modal" size="lg">
        <form id="inline-category-create" wire:submit="save" class="space-y-4">

            {{-- Type Info --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium">
                            Membuat kategori untuk:
                            <span class="font-bold">
                                {{ $transactionType === 'credit' ? '📈 Pemasukan (Income)' : '📉 Pengeluaran (Expense)' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Label Input --}}
            <div>
                <x-input label="Nama Kategori *" wire:model="label"
                    hint="Contoh: Pembayaran Client, Pembelian Alat Tulis"
                    placeholder="Masukkan nama kategori..."
                    required />
            </div>

            {{-- Parent Selector (conditional) --}}
            @if (count($this->parentOptions) > 0)
                <div>
                    <x-select.styled label="Parent Kategori (Opsional)" wire:model="parent_id"
                        :options="$this->parentOptions"
                        placeholder="Pilih parent atau kosongkan untuk top-level"
                        searchable />
                    <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                        <x-icon name="information-circle" class="w-4 h-4 inline" />
                        Kosongkan untuk membuat parent kategori
                    </p>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex gap-3">
                        <x-icon name="information-circle" class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0" />
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <p>Belum ada parent kategori untuk {{ $transactionType === 'credit' ? 'Income' : 'Expense' }}</p>
                            <p class="mt-1 text-xs">Kategori ini akan menjadi parent kategori</p>
                        </div>
                    </div>
                </div>
            @endif

        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button color="gray" wire:click="$set('modal', false)">
                    Batal
                </x-button>
                <x-button type="submit" form="inline-category-create" color="blue" loading="save" icon="check">
                    Simpan Kategori
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
