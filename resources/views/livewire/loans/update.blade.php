{{-- resources/views/livewire/loans/update.blade.php --}}
<div>
    <x-modal wire size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Edit Pinjaman</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Perbarui informasi pinjaman</p>
                </div>
            </div>
        </x-slot:title>

        <form id="loan-update" wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Pinjaman</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Detail dasar pinjaman</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-input wire:model="loan_number" label="Nomor Pinjaman *" readonly />

                    <x-input wire:model="lender_name" label="Nama Pemberi Pinjaman *"
                        placeholder="Bank BCA, Bank Mandiri, dll" />

                    <x-input wire:model="principal_amount" label="Jumlah Pokok *" placeholder="0" prefix="Rp"
                        x-mask:dynamic="$money($input, ',')" />

                    <x-select.native wire:model.live="interest_type" label="Tipe Bunga *" :options="[
                        ['label' => 'Jumlah Tetap', 'value' => 'fixed'],
                        ['label' => 'Persentase', 'value' => 'percentage'],
                    ]" />

                    @if ($interest_type === 'fixed')
                        <x-input wire:model="interest_amount" label="Jumlah Bunga *" placeholder="0" prefix="Rp"
                            x-mask:dynamic="$money($input, ',')" />
                    @else
                        <x-input wire:model="interest_rate" type="number" step="0.01"
                            label="Rate Bunga (% per tahun) *" placeholder="12.50" suffix="%" />
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
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Dokumen Kontrak</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Upload dokumen perjanjian pinjaman</p>
                </div>

                @if ($currentAttachment)
                    <div class="flex items-center gap-2 p-3 bg-secondary-50 dark:bg-dark-700 rounded-lg">
                        <x-icon name="paper-clip" class="w-5 h-5 text-dark-500" />
                        <span class="text-sm text-dark-900 dark:text-dark-50 flex-1">Dokumen saat ini terlampir</span>
                        <a href="{{ \Storage::url($currentAttachment) }}" target="_blank"
                            class="text-primary-600 hover:text-primary-700 text-sm">
                            Lihat
                        </a>
                    </div>
                @endif

                <x-upload wire:model="contract_attachment" label="Ganti Dokumen Kontrak"
                    tip="Upload perjanjian pinjaman baru (PDF, JPG, PNG)" accept="application/pdf,image/*" />
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="loan-update" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Perbarui
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
