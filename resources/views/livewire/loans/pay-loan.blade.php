{{-- resources/views/livewire/loans/pay-loan.blade.php --}}
<div>
    <x-modal wire size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Bayar Cicilan Pinjaman</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ $loan?->loan_number }} -
                        {{ $loan?->lender_name }}</p>
                </div>
            </div>
        </x-slot:title>

        <form id="pay-loan" wire:submit="save" class="space-y-6">
            @if ($loan)
                <div class="bg-secondary-50 dark:bg-dark-700 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Sisa Pokok</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingPrincipal, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                Terbayar: Rp {{ number_format($loan->payments()->sum('principal_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">Sisa Bunga</div>
                            <div class="text-lg font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->remainingInterest, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                Terbayar: Rp {{ number_format($loan->payments()->sum('interest_paid'), 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-select.styled wire:model="bank_account_id" :options="$this->bankAccounts" label="Bayar dari Rekening *"
                        placeholder="Pilih rekening..." searchable />

                    <x-date wire:model="payment_date" label="Tanggal Bayar *" />

                    <x-wireui-currency wire:model="principal_paid" label="Pembayaran Pokok"
                        hint="Kosongkan jika hanya bayar bunga" placeholder="0" prefix="Rp" thousands="."
                        decimal="," />

                    <x-wireui-currency wire:model="interest_paid" label="Pembayaran Bunga"
                        hint="Kosongkan jika hanya bayar pokok" placeholder="0" prefix="Rp" thousands="."
                        decimal="," />

                    <x-input wire:model="reference_number" label="No. Referensi"
                        placeholder="Transfer ID, check number, etc" />
                </div>

                <x-textarea wire:model="notes" label="Catatan" rows="2"
                    placeholder="Cicilan bulan ke-X, pembayaran dipercepat, dll" />
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="pay-loan" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Catat Pembayaran
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
