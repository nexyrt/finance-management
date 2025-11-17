<div>
    <x-modal wire="modal" size="2xl" center persistent>
        @if ($this->reimbursement)
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Proses Pembayaran</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">Bayar reimbursement karyawan</p>
                    </div>
                </div>
            </x-slot:title>

            <div class="space-y-6">
                {{-- Payment Summary --}}
                <div
                    class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <div class="text-center">
                        <div class="text-sm font-medium text-green-700 dark:text-green-300 mb-2">Total Reimbursement
                        </div>
                        <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                            {{ $this->reimbursement->formatted_amount }}
                        </div>

                        @if ($this->reimbursement->hasPartialPayment())
                            <div class="space-y-1 mb-3">
                                <div class="flex justify-center items-center gap-2 text-sm">
                                    <span class="text-blue-600 dark:text-blue-400">Dibayar:</span>
                                    <span
                                        class="font-bold text-blue-700 dark:text-blue-300">{{ $this->reimbursement->formatted_amount_paid }}</span>
                                </div>
                                <div class="flex justify-center items-center gap-2 text-sm">
                                    <span class="text-amber-600 dark:text-amber-400">Sisa:</span>
                                    <span
                                        class="font-bold text-amber-700 dark:text-amber-300">{{ $this->reimbursement->formatted_amount_remaining }}</span>
                                </div>
                            </div>
                        @endif

                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-800 border border-green-200 dark:border-green-700 rounded-lg">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-xs">
                                    {{ strtoupper(substr($this->reimbursement->user->name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ $this->reimbursement->user->name }}
                                </div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">
                                    {{ $this->reimbursement->title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment History --}}
                @if ($this->reimbursement->payments->count() > 0)
                    <div
                        class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Riwayat Pembayaran</h5>
                            <x-badge text="{{ $this->reimbursement->payments->count() }} pembayaran" color="blue" />
                        </div>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach ($this->reimbursement->payments as $payment)
                                <div
                                    class="flex items-center justify-between p-2 bg-white dark:bg-dark-800 rounded-lg text-sm">
                                    <div class="flex items-center gap-2">
                                        <x-icon name="check-circle"
                                            class="w-4 h-4 text-green-600 dark:text-green-400" />
                                        <div>
                                            <div class="font-medium text-dark-900 dark:text-dark-50">
                                                {{ $payment->formatted_amount }}</div>
                                            <div class="text-xs text-dark-500 dark:text-dark-400">
                                                {{ $payment->payment_date->format('d M Y') }} •
                                                {{ $payment->payer->name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Details --}}
                <div class="p-4 bg-gray-50 dark:bg-dark-700 border border-gray-200 dark:border-dark-600 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Kategori:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50 ml-2">
                                {{ $this->reimbursement->category_label }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Tanggal:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50 ml-2">
                                {{ $this->reimbursement->expense_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Pembayaran</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Pilih metode pembayaran dan jumlah</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- Payment Type Toggle --}}
                        <div
                            class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <div class="text-sm font-medium text-amber-900 dark:text-amber-100">Jenis Pembayaran
                                    </div>
                                    <div class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                                        @if ($isPartialPayment)
                                            Bayar sebagian (cicilan)
                                        @else
                                            Bayar penuh (lunas)
                                        @endif
                                    </div>
                                </div>
                                <x-toggle wire:model.live="isPartialPayment" label="" />
                            </div>

                            @if ($isPartialPayment)
                                <x-input wire:model="paymentAmount" label="Jumlah Bayar *" prefix="Rp"
                                    x-mask:dynamic="$money($input, ',')"
                                    hint="Maksimal: {{ $this->reimbursement->formatted_amount_remaining }}" />
                            @else
                                <div
                                    class="p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-green-900 dark:text-green-100">Jumlah
                                            Pelunasan</span>
                                        <span class="text-lg font-bold text-green-700 dark:text-green-300">
                                            {{ $this->reimbursement->formatted_amount_remaining }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <x-select.styled wire:model.live="bankAccountId" :options="$this->bankAccounts" label="Rekening Bank *"
                            placeholder="Pilih rekening..." searchable />

                        <x-date wire:model="paymentDate" label="Tanggal Pembayaran *" />

                        <x-input wire:model="referenceNotes" label="Catatan/Referensi"
                            placeholder="Opsional: Tambahkan catatan pembayaran" />
                    </div>
                </div>

                {{-- Warning --}}
                <div
                    class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex items-start gap-3">
                        <x-icon name="exclamation-triangle"
                            class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                        <div class="text-sm text-yellow-900 dark:text-yellow-200">
                            <div class="font-semibold mb-1">Penting:</div>
                            <ul class="list-disc list-inside space-y-1 text-yellow-800 dark:text-yellow-300">
                                <li>Akan membuat transaksi debit di rekening terpilih</li>
                                @if ($isPartialPayment)
                                    <li>Status pembayaran akan menjadi "Cicilan"</li>
                                @else
                                    <li>Status akan menjadi "Lunas" setelah pembayaran</li>
                                @endif
                                <li>Aksi ini tidak dapat dibatalkan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Loading State --}}
                <div wire:loading wire:target="processPayment"
                    class="text-center py-4 border-t border-dark-200 dark:border-dark-600">
                    <div class="flex items-center justify-center gap-3">
                        <x-icon name="arrow-path" class="w-5 h-5 animate-spin text-primary-600 dark:text-primary-400" />
                        <span class="text-sm font-medium text-dark-900 dark:text-dark-50">Memproses pembayaran...</span>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)" color="secondary" outline
                        class="w-full sm:w-auto order-2 sm:order-1">
                        Batal
                    </x-button>
                    <x-button wire:click="processPayment" color="green" icon="banknotes" loading="processPayment"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        @if ($isPartialPayment)
                            Bayar Cicilan
                        @else
                            Bayar Lunas
                        @endif
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
