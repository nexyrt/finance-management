<div>
    <x-modal :title="$reimbursement ? 'Edit: ' . $reimbursement->title : 'Edit Pengajuan'" wire size="2xl">
        @if ($reimbursement)
            <form id="reimbursement-update" wire:submit="save" class="space-y-4">
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
                        <x-wireui-currency label="Jumlah *" wire:model="amount" prefix="Rp" thousands="."
                            decimal="," placeholder="0" required />
                    </div>

                    {{-- Expense Date --}}
                    <div>
                        <x-date label="Tanggal Pengeluaran *" wire:model="expense_date" :max-date="now()" required />
                    </div>
                </div>

                {{-- Category --}}
                <div>
                    <x-select.styled label="Kategori *" wire:model="category" :options="$this->categories"
                        placeholder="Pilih kategori" required />
                </div>

                {{-- Existing Attachment --}}
                @if ($reimbursement->hasAttachment() && !$removeExistingAttachment)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if ($reimbursement->isImageAttachment())
                                    <img src="{{ $reimbursement->attachment_url }}" alt="Attachment"
                                        class="w-16 h-16 rounded object-cover">
                                @else
                                    <div
                                        class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded flex items-center justify-center">
                                        <x-icon name="document" class="w-8 h-8 text-red-600 dark:text-red-400" />
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $reimbursement->attachment_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">File saat ini</p>
                                </div>
                            </div>
                            <x-button.circle wire:click="removeAttachment" color="red" size="sm"
                                icon="trash" />
                        </div>
                    </div>
                @endif

                {{-- New Attachment Upload --}}
                <div>
                    <x-upload wire:model="attachment" label="Bukti Pengeluaran"
                        hint="Upload struk/nota baru (JPG, PNG, PDF - Max 2MB)"
                        tip="Drag & drop file atau klik untuk upload" accept="image/jpeg,image/png,application/pdf"
                        delete delete-method="deleteUpload" />
                </div>

                {{-- Status Info --}}
                @if ($reimbursement->isRejected())
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <x-icon name="exclamation-circle" class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" />
                            <div class="text-sm text-red-800 dark:text-red-200">
                                <p class="font-medium mb-1">Pengajuan Ditolak</p>
                                @if ($reimbursement->review_notes)
                                    <p class="text-xs">{{ $reimbursement->review_notes }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </form>

            <x-slot:footer>
                <div class="flex justify-between w-full">
                    <x-button color="gray" wire:click="$set('modal', false)">
                        Batal
                    </x-button>
                    <x-button type="submit" form="reimbursement-update" color="green" loading="save" icon="check">
                        Update Pengajuan
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
