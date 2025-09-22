<div>
    <x-modal wire="showModal" title="Edit Pembayaran" size="2xl" center>
        @if ($payment)
            {{-- Invoice Info Header --}}
            <div
                class="bg-gradient-to-r from-blue-50 to-primary-50 dark:from-blue-900/20 dark:to-primary-900/20 -m-4 mb-6 p-4 border-b border-blue-200 dark:border-blue-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center">
                            <x-icon name="pencil" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900 dark:text-dark-50">
                                {{ $payment->invoice->invoice_number }}</h3>
                            <p class="text-sm text-secondary-600 dark:text-dark-400">
                                {{ $payment->invoice->client->name }}</p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-secondary-600 dark:text-dark-400">Total Invoice</p>
                        <p class="text-xl font-bold text-secondary-900 dark:text-dark-50">
                            Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="space-y-6">
                {{-- Amount & Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-wireui-currency wire:model.live="amount" label="Jumlah Pembayaran *" placeholder="0"
                            prefix="Rp" />
                        @php
                            $otherPayments = $payment->invoice
                                ->payments()
                                ->where('id', '!=', $payment->id)
                                ->sum('amount');
                            $maxAllowed = $payment->invoice->total_amount - $otherPayments;
                        @endphp
                        <p class="text-xs text-secondary-500 dark:text-dark-400 mt-1">
                            Maksimal: Rp {{ number_format($maxAllowed, 0, ',', '.') }}
                        </p>
                    </div>

                    <x-input wire:model="payment_date" label="Tanggal Pembayaran *" type="date" icon="calendar" />
                </div>

                {{-- Payment Method & Bank Account --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select.styled wire:model.live="payment_method" label="Metode Pembayaran *" :options="[
                        ['label' => '💳 Transfer Bank', 'value' => 'bank_transfer'],
                        ['label' => '💵 Tunai', 'value' => 'cash'],
                    ]" />

                    <x-select.styled wire:model="bank_account_id" label="Rekening Tujuan *" :options="$this->bankAccounts"
                        placeholder="Pilih rekening..." searchable />
                </div>

                {{-- Reference Number --}}
                <div>
                    <x-input wire:model="reference_number" label="Nomor Referensi"
                        placeholder="Nomor transaksi, slip, atau referensi lainnya" icon="hashtag"
                        hint="Opsional - untuk tracking pembayaran" />
                </div>

                {{-- Existing Attachment Display --}}
                @if ($payment->hasAttachment())
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-secondary-900 dark:text-dark-50">Bukti Pembayaran Saat
                            Ini</label>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex items-center justify-center">
                                        @if ($payment->isPdfAttachment())
                                            <x-icon name="document" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        @else
                                            <x-icon name="photo" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            {{ $payment->attachment_name }}
                                        </p>
                                        <p class="text-xs text-blue-700 dark:text-blue-300">
                                            Attachment saat ini
                                        </p>
                                    </div>
                                </div>
                                <x-button.circle wire:click="deleteExistingAttachment" color="red" icon="trash"
                                    size="sm" loading="deleteExistingAttachment" />
                            </div>
                        </div>
                    </div>
                @endif

                {{-- File Upload --}}
                <div>
                    <x-upload wire:model="attachment"
                        label="{{ $payment->hasAttachment() ? 'Ganti Bukti Pembayaran' : 'Bukti Pembayaran' }}"
                        hint="Upload screenshot atau dokumen bukti pembayaran (opsional)"
                        tip="Seret dan letakkan file di sini" accept="image/*,.pdf" delete
                        delete-method="deleteUpload" />
                </div>

                {{-- Payment Summary --}}
                @if ($amount)
                    @php
                        $amountInteger = (int) $amount;
                        $otherPayments = $payment->invoice->payments()->where('id', '!=', $payment->id)->sum('amount');
                        $totalPaidAfterEdit = $otherPayments + $amountInteger;
                        $remainingAfter = $payment->invoice->total_amount - $totalPaidAfterEdit;
                    @endphp

                    <div
                        class="bg-secondary-50 dark:bg-dark-800 rounded-xl p-4 border border-secondary-200 dark:border-dark-700">
                        <h4 class="font-medium text-secondary-900 dark:text-dark-50 mb-3 flex items-center gap-2">
                            <x-icon name="calculator" class="w-4 h-4" />
                            Ringkasan Setelah Edit
                        </h4>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">Total Invoice:</span>
                                <span class="font-medium">Rp
                                    {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">Pembayaran Lain:</span>
                                <span class="font-medium">Rp {{ number_format($otherPayments, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-secondary-600 dark:text-dark-400">Pembayaran Ini:</span>
                                <span class="font-medium text-blue-600">Rp
                                    {{ number_format($amountInteger, 0, ',', '.') }}</span>
                            </div>
                            <hr class="border-secondary-300 dark:border-dark-600">
                            <div class="flex justify-between font-bold">
                                <span class="text-secondary-900 dark:text-dark-50">Sisa Setelah Edit:</span>
                                <span class="{{ $remainingAfter <= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp {{ number_format(max(0, $remainingAfter), 0, ',', '.') }}
                                </span>
                            </div>
                            @if ($remainingAfter <= 0)
                                <div class="text-xs text-green-600 dark:text-green-400 italic text-center mt-2">
                                    ✅ Invoice akan lunas setelah edit ini
                                </div>
                            @endif
                            @if ($attachment || $payment->hasAttachment())
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-secondary-600 dark:text-dark-400">Bukti Pembayaran:</span>
                                    <div class="flex items-center gap-2">
                                        <x-icon name="check-circle"
                                            class="w-4 h-4 text-green-600 dark:text-green-400" />
                                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                            {{ $attachment ? 'File baru siap diupload' : 'File tersedia' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Footer Actions --}}
        <x-slot:footer>
            <div class="flex items-center justify-between w-full">
                {{-- Original Payment Info --}}
                @if ($payment)
                    <div class="text-sm text-secondary-600 dark:text-dark-400">
                        <div>Original: Rp {{ number_format($payment->getOriginal('amount'), 0, ',', '.') }}</div>
                        <div>{{ \Carbon\Carbon::parse($payment->getOriginal('payment_date'))->format('d M Y') }}</div>
                    </div>
                @else
                    <div></div>
                @endif

                {{-- Main Actions --}}
                <div class="flex items-center gap-3">
                    <x-button wire:click="$set('showModal', false)" color="secondary">
                        Batal
                    </x-button>
                    <x-button wire:click="save" color="primary" icon="check" loading="save">
                        Update Pembayaran
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
