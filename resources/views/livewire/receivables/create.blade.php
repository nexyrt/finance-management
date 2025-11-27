<div>
    <x-button wire:click="$toggle('modal')" color="green" icon="plus" class="w-full sm:w-auto">
        Tambah Piutang
    </x-button>

    <x-modal wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Tambah Piutang</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Buat piutang baru untuk karyawan atau perusahaan
                    </p>
                </div>
            </div>
        </x-slot:title>

        <form id="receivable-create" wire:submit="save" class="space-y-6">
            {{-- Section: Jenis Piutang --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Jenis Piutang</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Pilih jenis piutang</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-select.native wire:model.live="type" label="Jenis *" :options="[
                        ['label' => 'Pinjaman Karyawan', 'value' => 'employee_loan'],
                        ['label' => 'Pinjaman Perusahaan', 'value' => 'company_loan'],
                    ]" />

                    @if ($type === 'employee_loan')
                        <x-select.styled wire:model="debtor_id" :options="$this->employees" label="Karyawan *"
                            placeholder="Pilih karyawan..." searchable />
                    @else
                        <x-select.styled wire:model="debtor_id" :options="$this->companies" label="Perusahaan *"
                            placeholder="Pilih perusahaan..." searchable />
                    @endif
                </div>
            </div>

            {{-- Section: Detail Pinjaman --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Detail Pinjaman</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Informasi jumlah dan tenor</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <x-wireui-currency wire:model="principal_amount" placeholder="0" prefix="Rp ">
                        <x-slot:label>
                            <div class="flex items-center gap-2">
                                <span>Jumlah Pokok *</span>
                                @if ($type === 'employee_loan')
                                    <x-tooltip color="secondary" text="Maksimal Rp 10.000.000" position="top" />
                                @endif
                            </div>
                        </x-slot:label>
                    </x-wireui-currency>

                    {{-- Interest Type Selection --}}
                    <x-select.native wire:model.live="interest_type" label="Tipe Bunga *" :options="[
                        ['label' => 'Persentase (% per tahun)', 'value' => 'percentage'],
                        ['label' => 'Jumlah Tetap (Rp)', 'value' => 'fixed'],
                    ]" />

                    {{-- Interest Input (conditional based on type) --}}
                    @if ($interest_type === 'fixed')
                        <x-wireui-currency wire:model="interest_amount" label="Jumlah Bunga" placeholder="0"
                            hint="Kosongkan jika 0" />
                    @else
                        <x-input wire:model="interest_rate" type="number" step="0.01"
                            label="Rate Bunga (% per tahun)" placeholder="0" suffix="%" hint="Kosongkan jika 0%" />
                    @endif

                    <x-input wire:model="installment_months" type="number" label="Tenor (bulan)" placeholder="1" />

                    <x-date wire:model="loan_date" label="Tanggal Pinjaman *" />
                </div>
            </div>

            {{-- Section: Informasi Tambahan --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Informasi Tambahan</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Tujuan dan pencairan</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <x-input wire:model="purpose" label="Tujuan Pinjaman *" placeholder="Contoh: Modal usaha" />

                    <x-input wire:model="disbursement_account" label="Akun Tujuan Pencairan *"
                        placeholder="Nomor rekening atau tulis CASH" hint="Contoh: 1234567890 (BCA) atau CASH" />

                    <x-textarea wire:model="notes" label="Catatan" placeholder="Catatan tambahan..." rows="3" />

                    <x-upload wire:model="contract_attachment" label="Dokumen Kontrak"
                        hint="{{ $type === 'company_loan' ? 'Wajib untuk pinjaman perusahaan' : 'Opsional' }}"
                        accept="application/pdf,image/jpeg,image/jpg,image/png" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-2 sm:order-1">
                    Batal
                </x-button>
                <x-button type="submit" form="receivable-create" color="green" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    Buat Piutang
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
