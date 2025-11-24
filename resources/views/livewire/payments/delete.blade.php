<div>
    <x-modal wire size="lg" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Hapus Pembayaran</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
        </x-slot:title>

        @if ($payment)
            <div class="space-y-6">
                {{-- Warning Banner --}}
                <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <x-icon name="exclamation-triangle"
                            class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-1">
                                Peringatan: Data Akan Dihapus Permanen
                            </p>
                            <p class="text-sm text-red-700 dark:text-red-300">
                                Pembayaran akan dihapus dan status invoice akan diperbarui. Lampiran yang terkait juga
                                akan dihapus.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">Detail Pembayaran</h4>

                        <div class="space-y-2">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Jumlah Pembayaran:</span>
                                <span class="text-sm font-bold text-dark-900 dark:text-dark-50">
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-start">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Tanggal Pembayaran:</span>
                                <span class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ $payment->payment_date->format('d M Y') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-start">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Metode Pembayaran:</span>
                                <x-badge :text="ucfirst(str_replace('_', ' ', $payment->payment_method))" color="blue" />
                            </div>

                            @if ($payment->reference_number)
                                <div class="flex justify-between items-start">
                                    <span class="text-sm text-dark-600 dark:text-dark-400">Reference Number:</span>
                                    <span class="text-sm font-mono text-dark-900 dark:text-dark-50">
                                        {{ $payment->reference_number }}
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-start">
                                <span class="text-sm text-dark-600 dark:text-dark-400">Bank Account:</span>
                                <span class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ $payment->bankAccount->account_name }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Invoice Info --}}
                    <div
                        class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                        <div class="flex items-start gap-2">
                            <x-icon name="document-text" class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                                    Invoice: {{ $invoice->invoice_number }}
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    Client: {{ $invoice->client->name }}
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    Total Invoice: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }} |
                                    Dibayar: Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Attachment Info --}}
                    @if ($payment->hasAttachment())
                        <div class="flex items-center gap-2 text-sm text-dark-600 dark:text-dark-400">
                            <x-icon name="paper-clip" class="w-4 h-4" />
                            <span>Lampiran: {{ $payment->attachment_name }}</span>
                        </div>
                    @endif
                </div>

                {{-- Status Impact Warning --}}
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <x-icon name="information-circle"
                            class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" />
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-yellow-900 dark:text-yellow-100 mb-2">
                                Status invoice akan berubah setelah pembayaran dihapus
                            </p>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-yellow-700 dark:text-yellow-300">Sisa pembayaran:</span>
                                    <span class="text-xs font-bold text-yellow-900 dark:text-yellow-100">
                                        Rp {{ number_format($remainingPaid, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-yellow-700 dark:text-yellow-300">Status akan
                                        menjadi:</span>
                                    <x-badge :text="$statusText" :color="$statusColor" xs />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Confirmation Text --}}
                <div class="bg-dark-50 dark:bg-dark-800 rounded-lg p-4">
                    <p class="text-sm text-dark-700 dark:text-dark-300 text-center font-medium">
                        Apakah Anda yakin ingin menghapus pembayaran ini?
                    </p>
                </div>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button wire:click="delete" color="red" icon="trash" loading="delete"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Ya, Hapus Pembayaran
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
