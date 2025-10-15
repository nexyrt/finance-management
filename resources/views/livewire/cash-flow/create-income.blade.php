<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" size="sm">
        Tambah Pemasukan
    </x-button>

    {{-- Modal --}}
    <x-modal wire title="Tambah Pemasukan Langsung" size="2xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Pemasukan Langsung</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Catat pemasukan yang tidak berasal dari invoice
                    </p>
                </div>
            </div>
        </x-slot:title>

        <form id="income-create-form" wire:submit="save" class="space-y-4">
            {{-- Source Type Selection --}}
            <div class="bg-zinc-50 dark:bg-dark-700 rounded-lg p-4 border border-zinc-200 dark:border-dark-600">
                <label class="block text-sm font-medium text-dark-900 dark:text-dark-50 mb-3">
                    Tipe Pemasukan *
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label
                        class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                  {{ $source_type === 'transaction'
                                      ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                      : 'border-zinc-300 dark:border-dark-600 hover:border-zinc-400' }}">
                        <input type="radio" wire:model.live="source_type" value="transaction" class="sr-only">
                        <div class="text-center">
                            <x-icon name="arrow-trending-up"
                                class="w-6 h-6 mx-auto mb-2 {{ $source_type === 'transaction' ? 'text-green-600' : 'text-dark-500' }}" />
                            <div
                                class="font-semibold text-sm {{ $source_type === 'transaction' ? 'text-green-700 dark:text-green-300' : 'text-dark-700 dark:text-dark-300' }}">
                                Transaksi Langsung
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                                Pemasukan non-invoice
                            </div>
                        </div>
                    </label>

                    <label
                        class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all
                                  {{ $source_type === 'payment'
                                      ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                      : 'border-zinc-300 dark:border-dark-600 hover:border-zinc-400' }}">
                        <input type="radio" wire:model.live="source_type" value="payment" class="sr-only">
                        <div class="text-center">
                            <x-icon name="document-text"
                                class="w-6 h-6 mx-auto mb-2 {{ $source_type === 'payment' ? 'text-blue-600' : 'text-dark-500' }}" />
                            <div
                                class="font-semibold text-sm {{ $source_type === 'payment' ? 'text-blue-700 dark:text-blue-300' : 'text-dark-700 dark:text-dark-300' }}">
                                Pembayaran Invoice
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                                Dari invoice
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Conditional Fields Based on Source Type --}}
            <div class="relative">
                {{-- Loading Overlay --}}
                <div wire:loading wire:target="source_type"
                    class="absolute inset-0 bg-white/75 dark:bg-dark-800/75 backdrop-blur-sm z-10 flex items-center justify-center rounded-lg">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="arrow-path" class="w-6 h-6 text-primary-600 dark:text-primary-400 animate-spin" />
                        <span class="text-sm font-medium text-dark-700 dark:text-dark-300">Memuat form...</span>
                    </div>
                </div>

                @if ($source_type === 'transaction')
                    {{-- Transaction Fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" wire:key="transaction-fields">
                        <div>
                            <x-select.styled wire:model.live="bank_account_id" label="Rekening Bank *" :options="$this->bankAccounts"
                                placeholder="Pilih rekening..." searchable />
                        </div>

                        <div>
                            <x-select.styled wire:model.live="category_id" label="Kategori Pemasukan *"
                                :options="$this->incomeCategories" placeholder="Pilih kategori..." searchable />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" wire:key="transaction-amount-date">
                        <div>
                            <x-wireui-currency wire:model.live="amount" prefix="Rp " label="Jumlah *" placeholder="0"
                                color="dark:dark" />
                        </div>

                        <div>
                            <x-date wire:model.live="transaction_date" label="Tanggal Transaksi *"
                                placeholder="Pilih tanggal..." />
                        </div>
                    </div>

                    <div wire:key="transaction-description">
                        <x-textarea wire:model.live="description" label="Deskripsi *"
                            placeholder="Contoh: Pendapatan jasa konsultasi" rows="3" />
                    </div>

                    <div wire:key="transaction-reference">
                        <x-input wire:model.live="reference_number" label="Nomor Referensi (Opsional)"
                            placeholder="Contoh: REF-2025-001" />
                    </div>
                @else
                    {{-- Payment Fields --}}
                    <div wire:key="payment-invoice">
                        <x-select.styled wire:model.live="invoice_id" label="Invoice *" :options="$this->invoices"
                            placeholder="Pilih invoice yang belum lunas..." searchable />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" wire:key="payment-bank-amount">
                        <div>
                            <x-select.styled wire:model.live="bank_account_id" label="Rekening Bank *" :options="$this->bankAccounts"
                                placeholder="Pilih rekening..." searchable />
                        </div>

                        <div>
                            <x-wireui-currency wire:model.live="amount" prefix="Rp " label="Jumlah Pembayaran *"
                                placeholder="0" color="dark:dark" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" wire:key="payment-date-reference">
                        <div>
                            <x-date wire:model.live="transaction_date" label="Tanggal Pembayaran *"
                                placeholder="Pilih tanggal..." />
                        </div>

                        <div>
                            <x-input wire:model.live="reference_number" label="Nomor Referensi (Opsional)"
                                placeholder="Contoh: TRF-2025-001" />
                        </div>
                    </div>
                @endif
            </div>

            {{-- Attachment (Common) --}}
            <div>
                <x-upload wire:model="attachment" label="Lampiran (Opsional)"
                    tip="Upload bukti transfer atau dokumen pendukung" accept="image/jpeg,image/png,application/pdf"
                    delete />
            </div>

            {{-- Preview Section --}}
            @if ($bank_account_id && $amount)
                <div
                    class="bg-{{ $source_type === 'payment' ? 'blue' : 'green' }}-50 dark:bg-{{ $source_type === 'payment' ? 'blue' : 'green' }}-900/20 rounded-xl p-4 border border-{{ $source_type === 'payment' ? 'blue' : 'green' }}-200 dark:border-{{ $source_type === 'payment' ? 'blue' : 'green' }}-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="h-8 w-8 bg-{{ $source_type === 'payment' ? 'blue' : 'green' }}-100 dark:bg-{{ $source_type === 'payment' ? 'blue' : 'green' }}-800 rounded-lg flex items-center justify-center">
                            <x-icon name="eye"
                                class="w-4 h-4 text-{{ $source_type === 'payment' ? 'blue' : 'green' }}-600 dark:text-{{ $source_type === 'payment' ? 'blue' : 'green' }}-400" />
                        </div>
                        <div>
                            <h5 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Preview</h5>
                            <p class="text-xs text-dark-500 dark:text-dark-400">
                                {{ $source_type === 'payment' ? 'Pembayaran' : 'Transaksi' }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-600 dark:text-dark-400">
                                {{ $source_type === 'payment' ? 'Jumlah Pembayaran' : 'Jumlah Pemasukan' }}
                            </span>
                            <span
                                class="text-lg font-bold text-{{ $source_type === 'payment' ? 'blue' : 'green' }}-600 dark:text-{{ $source_type === 'payment' ? 'blue' : 'green' }}-400">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </span>
                        </div>

                        @if ($transaction_date)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Tanggal</span>
                                <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ \Carbon\Carbon::parse($transaction_date)->format('d M Y') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$toggle('modal')" color="zinc" outline class="w-full sm:w-auto">
                    Batal
                </x-button>

                <x-button type="submit" form="income-create-form"
                    color="{{ $source_type === 'payment' ? 'blue' : 'green' }}" icon="check" loading="save"
                    class="w-full sm:w-auto">
                    Simpan {{ $source_type === 'payment' ? 'Pembayaran' : 'Pemasukan' }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
