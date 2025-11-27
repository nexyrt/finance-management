<div>
    <x-modal wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Catat Pembayaran Piutang</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Pencatatan pembayaran dari peminjam</p>
                </div>
            </div>
        </x-slot:title>

        @if ($receivable)
            <form id="pay-receivable" wire:submit="save" class="space-y-6">
                {{-- Receivable Summary --}}
                <div class="bg-secondary-50 dark:bg-dark-700 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Sisa Pokok</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingPrincipal, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                Terbayar: Rp
                                {{ number_format($receivable->payments()->sum('principal_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Sisa Bunga</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingInterest, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                Terbayar: Rp
                                {{ number_format($receivable->payments()->sum('interest_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Pembayaran</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Informasi pembayaran yang diterima</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <x-date wire:model="payment_date" label="Tanggal Pembayaran *" />

                        <x-select.native wire:model.live="payment_method" label="Metode Pembayaran *"
                            :options="[
                                ['label' => 'Transfer Bank', 'value' => 'bank_transfer'],
                                ['label' => 'Potong Gaji', 'value' => 'payroll_deduction'],
                                ['label' => 'Tunai', 'value' => 'cash'],
                            ]" />

                        {{-- Bank Account (only show for bank_transfer) --}}
                        @if ($payment_method === 'bank_transfer')
                            <div class="lg:col-span-2">
                                <div
                                    class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                                    <div class="flex items-start gap-3 mb-3">
                                        <x-icon name="information-circle"
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                                        <div>
                                            <div class="font-semibold text-blue-900 dark:text-blue-100 text-sm">
                                                Penerimaan Dana</div>
                                            <div class="text-xs text-blue-700 dark:text-blue-300">
                                                Pilih rekening bank yang menerima pembayaran
                                            </div>
                                        </div>
                                    </div>
                                    <x-select.styled wire:model="bank_account_id" :options="$this->bankAccounts"
                                        label="Rekening Bank *" placeholder="Pilih rekening..." searchable />
                                </div>
                            </div>
                        @endif

                        <x-wireui-currency wire:model="principal_paid" label="Pembayaran Pokok"
                            hint="Kosongkan jika hanya bayar bunga" prefix="Rp" thousands="." decimal="," />

                        <x-wireui-currency wire:model="interest_paid" label="Pembayaran Bunga"
                            hint="Kosongkan jika hanya bayar pokok" prefix="Rp" thousands="." decimal="," />

                        <x-input wire:model="reference_number" label="Nomor Referensi"
                            placeholder="Nomor transaksi / referensi" />

                        <x-textarea wire:model="notes" label="Catatan" placeholder="Catatan tambahan..."
                            rows="3" />
                    </div>
                </div>
            </form>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="pay-receivable" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Catat Pembayaran
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
