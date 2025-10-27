<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" class="w-full sm:w-auto">
        Buat Pengajuan
    </x-button>

    {{-- Modal --}}
    <x-modal title="Buat Pengajuan Reimbursement" wire size="2xl">
        <form id="reimbursement-create" wire:submit="save" class="space-y-4">
            {{-- Title --}}
            <div>
                <x-input label="Judul Pengajuan *" wire:model="title" placeholder="Contoh: Transportasi ke klien"
                    required />
            </div>

            {{-- Description --}}
            <div>
                <x-textarea label="Deskripsi" wire:model="description" placeholder="Detail pengeluaran..."
                    rows="3" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Amount --}}
                <div>
                    <x-wireui-currency label="Jumlah *" wire:model="amount" prefix="Rp" thousands="." decimal=","
                        placeholder="0" required />
                </div>

                {{-- Expense Date --}}
                <div>
                    <x-date label="Tanggal Pengeluaran *" wire:model="expense_date" :max-date="now()" required />
                </div>
            </div>

            {{-- Category --}}
            <div>
                <x-select.styled label="Kategori *" wire:model="category" :options="$this->categories" placeholder="Pilih kategori"
                    required />
            </div>

            {{-- Attachment Upload --}}
            <div>
                <x-upload wire:model="attachment" label="Bukti Pengeluaran"
                    hint="Upload struk/nota (JPG, PNG, PDF - Max 2MB)" tip="Drag & drop file atau klik untuk upload"
                    accept="image/jpeg,image/png,application/pdf" delete delete-method="deleteUpload" />
            </div>

            {{-- Info Box --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium mb-1">Informasi:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Simpan sebagai draft untuk melanjutkan nanti</li>
                            <li>Submit untuk langsung mengirim ke finance</li>
                            <li>Draft dapat diedit dan dihapus kapan saja</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-between w-full gap-3">
                {{-- Cancel --}}
                <x-button color="gray" wire:click="$set('modal', false)">
                    Batal
                </x-button>

                <div class="flex gap-2">
                    {{-- Save as Draft --}}
                    <x-button type="submit" form="reimbursement-create" wire:click="$set('submitOnSave', false)"
                        color="secondary" loading="save" icon="document">
                        Simpan Draft
                    </x-button>

                    {{-- Submit --}}
                    <x-button type="submit" form="reimbursement-create" wire:click="$set('submitOnSave', true)"
                        color="blue" loading="save" icon="paper-airplane">
                        Submit
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
