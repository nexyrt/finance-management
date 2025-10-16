<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="primary" icon="plus" size="sm">
        Tambah Pemasukan
    </x-button>

    {{-- Modal --}}
    <x-modal wire title="Tambah Pemasukan" size="2xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 p-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Pemasukan</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">
                        Catat pemasukan dari invoice atau transaksi langsung
                    </p>
                </div>
            </div>
        </x-slot:title>

        <form id="income-create-form" wire:submit="save" class="space-y-6">
            {{-- Source Type Selection --}}
            <div
                class="bg-secondary-50 dark:bg-dark-800 rounded-xl p-4 border border-secondary-200 dark:border-dark-700">
                <label class="block text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">
                    Tipe Pemasukan *
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label
                        class="relative flex flex-col items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                  {{ $source_type === 'transaction'
                                      ? 'border-green-600 bg-green-50 dark:bg-green-900/20 shadow-sm'
                                      : 'border-secondary-300 dark:border-dark-600 hover:border-secondary-400 dark:hover:border-dark-500' }}">
                        <input type="radio" wire:model.live="source_type" value="transaction" class="sr-only">
                        <x-icon name="arrow-trending-up"
                            class="w-8 h-8 mb-2 {{ $source_type === 'transaction' ? 'text-green-600 dark:text-green-400' : 'text-dark-500 dark:text-dark-400' }}" />
                        <div
                            class="font-semibold text-sm {{ $source_type === 'transaction' ? 'text-green-700 dark:text-green-300' : 'text-dark-700 dark:text-dark-300' }}">
                            Transaksi Langsung
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400 mt-1 text-center">
                            Pemasukan non-invoice
                        </div>
                    </label>

                    <label
                        class="relative flex flex-col items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                  {{ $source_type === 'payment'
                                      ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20 shadow-sm'
                                      : 'border-secondary-300 dark:border-dark-600 hover:border-secondary-400 dark:hover:border-dark-500' }}">
                        <input type="radio" wire:model.live="source_type" value="payment" class="sr-only">
                        <x-icon name="document-text"
                            class="w-8 h-8 mb-2 {{ $source_type === 'payment' ? 'text-primary-600 dark:text-primary-400' : 'text-dark-500 dark:text-dark-400' }}" />
                        <div
                            class="font-semibold text-sm {{ $source_type === 'payment' ? 'text-primary-700 dark:text-primary-300' : 'text-dark-700 dark:text-dark-300' }}">
                            Pembayaran Invoice
                        </div>
                        <div class="text-xs text-dark-500 dark:text-dark-400 mt-1 text-center">
                            Dari invoice klien
                        </div>
                    </label>
                </div>
            </div>

            {{-- Conditional Fields with Better Loading State --}}
            <div class="relative">
                {{-- Centered Loading Overlay --}}
                <div wire:loading wire:target="source_type"
                    class="absolute inset-0 bg-white/90 dark:bg-dark-900/90 backdrop-blur-sm z-10 rounded-xl">
                    <div class="h-full flex items-center justify-center">
                        <div class="flex flex-col items-center gap-3">
                            <x-icon name="arrow-path"
                                class="w-8 h-8 text-primary-600 dark:text-primary-400 animate-spin" />
                            <span class="text-sm font-medium text-dark-700 dark:text-dark-300">Memuat form...</span>
                        </div>
                    </div>
                </div>

                @if ($source_type === 'transaction')
                    {{-- Transaction Fields --}}
                    <div class="space-y-4" wire:key="transaction-form">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-select.styled wire:model.live="bank_account_id" label="Rekening Bank *" :options="$this->bankAccounts"
                                placeholder="Pilih rekening..." searchable />

                            <x-select.styled wire:model.live="category_id" label="Kategori Pemasukan *"
                                :options="$this->incomeCategories" placeholder="Pilih kategori..." searchable />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-wireui-currency wire:model.live="amount" prefix="Rp " label="Jumlah *" placeholder="0"
                                thousands="." decimal="," />

                            <x-date wire:model.live="transaction_date" label="Tanggal Transaksi *"
                                placeholder="Pilih tanggal..." />
                        </div>

                        <x-textarea wire:model.live="description" label="Deskripsi *"
                            placeholder="Contoh: Pendapatan jasa konsultasi" rows="3" />

                        <x-input wire:model.live="reference_number" label="Nomor Referensi (Opsional)"
                            placeholder="Contoh: REF-2025-001" />

                        <x-upload wire:model="attachment" label="Lampiran (Opsional)"
                            tip="Upload bukti transfer (JPG, PNG, PDF max 2MB)"
                            accept="image/jpeg,image/png,application/pdf" delete />
                    </div>
                @else
                    {{-- Payment Fields --}}
                    <div class="space-y-4" wire:key="payment-form">
                        <x-select.styled wire:model.live="invoice_id" label="Invoice *" :options="$this->invoices"
                            placeholder="Pilih invoice yang belum lunas..." searchable />

                        @if ($this->selectedInvoice)
                            <div
                                class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 border border-primary-200 dark:border-primary-800">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-10 w-10 bg-primary-100 dark:bg-primary-800 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <x-icon name="information-circle"
                                            class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">
                                            Detail Invoice
                                        </h5>
                                        <dl class="space-y-1.5">
                                            <div class="flex justify-between text-sm">
                                                <dt class="text-dark-600 dark:text-dark-400">Total Tagihan:</dt>
                                                <dd class="font-medium text-dark-900 dark:text-dark-50">
                                                    Rp
                                                    {{ number_format($this->selectedInvoice->total_amount, 0, ',', '.') }}
                                                </dd>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <dt class="text-dark-600 dark:text-dark-400">Sudah Dibayar:</dt>
                                                <dd class="font-medium text-dark-900 dark:text-dark-50">
                                                    Rp
                                                    {{ number_format($this->selectedInvoice->amount_paid, 0, ',', '.') }}
                                                </dd>
                                            </div>
                                            <div
                                                class="flex justify-between text-sm pt-1.5 border-t border-primary-200 dark:border-primary-800">
                                                <dt class="font-semibold text-primary-700 dark:text-primary-300">Sisa
                                                    Tagihan:</dt>
                                                <dd class="font-bold text-primary-700 dark:text-primary-300">
                                                    Rp
                                                    {{ number_format($this->selectedInvoice->amount_remaining, 0, ',', '.') }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-select.styled wire:model.live="bank_account_id" label="Rekening Bank *" :options="$this->bankAccounts"
                                placeholder="Pilih rekening..." searchable />

                            <x-wireui-currency wire:model.live="amount" prefix="Rp " label="Jumlah Pembayaran *"
                                placeholder="0" thousands="." decimal=","
                                hint="{{ $this->selectedInvoice ? 'Maksimal: Rp ' . number_format($this->selectedInvoice->amount_remaining, 0, ',', '.') : '' }}" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-date wire:model.live="transaction_date" label="Tanggal Pembayaran *"
                                placeholder="Pilih tanggal..." />

                            <x-input wire:model.live="reference_number" label="Nomor Referensi (Opsional)"
                                placeholder="Contoh: TRF-2025-001" />
                        </div>

                        <x-upload wire:model="attachment" label="Lampiran (Opsional)"
                            tip="Upload bukti transfer (JPG, PNG, PDF max 2MB)"
                            accept="image/jpeg,image/png,application/pdf" delete />
                    </div>
                @endif
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3 w-full">
                <x-button wire:click="$toggle('modal')" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>

                <x-button type="submit" form="income-create-form"
                    color="{{ $source_type === 'payment' ? 'primary' : 'green' }}" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Simpan {{ $source_type === 'payment' ? 'Pembayaran' : 'Pemasukan' }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
