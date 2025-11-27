{{-- resources/views/livewire/loans/create.blade.php --}}
<div>
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" class="w-full sm:w-auto">
        Add Loan
    </x-button>

    <x-modal wire size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="banknotes" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Add New Loan</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Record new loan from bank or lender</p>
                </div>
            </div>
        </x-slot:title>

        <form id="loan-create" wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Pinjaman</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Detail dasar pinjaman</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-input wire:model="loan_number" label="Nomor Pinjaman *" readonly />

                    <x-input wire:model="lender_name" label="Nama Pemberi Pinjaman *"
                        placeholder="Bank BCA, Bank Mandiri, dll" />

                    <x-wireui-currency wire:model="principal_amount" label="Jumlah Pokok *" placeholder="0"
                        prefix="Rp" thousands="." decimal="," />

                    <x-select.native wire:model.live="interest_type" label="Tipe Bunga *" :options="[
                        ['label' => 'Jumlah Tetap', 'value' => 'fixed'],
                        ['label' => 'Persentase', 'value' => 'percentage'],
                    ]" />

                    @if ($interest_type === 'fixed')
                        <x-wireui-currency wire:model="interest_amount" label="Jumlah Bunga (opsional)" placeholder="0"
                            prefix="Rp" thousands="." decimal="," />
                    @else
                        <x-input wire:model="interest_rate" type="number" step="0.01"
                            label="Rate Bunga (% per tahun, opsional)" placeholder="0" suffix="%" />
                    @endif

                    <x-input wire:model.live="term_months" type="number" label="Tenor (Bulan) *" placeholder="24" />

                    <x-date wire:model.live="start_date" label="Tanggal Mulai *" />

                    <x-date wire:model="maturity_date" label="Tanggal Jatuh Tempo *" />
                </div>

                <x-textarea wire:model="purpose" label="Tujuan" rows="2"
                    placeholder="Refund client, modal kerja, dll" />
            </div>

            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Tambahan</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Rekening bank dan kontrak</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <x-select.styled wire:model="bank_account_id" :options="$this->bankAccounts" label="Terima ke Rekening *"
                        placeholder="Pilih rekening bank..." searchable />

                    <x-upload wire:model="contract_attachment" label="Dokumen Kontrak"
                        tip="Upload perjanjian pinjaman (PDF, JPG, PNG)" accept="application/pdf,image/*" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="loan-create" color="blue" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Buat Pinjaman
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
