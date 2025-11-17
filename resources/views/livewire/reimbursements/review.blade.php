<div>
    <x-modal wire="modal" size="xl" center persistent>
        @if ($this->reimbursement)
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div
                        class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="clipboard-document-check" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Review Reimbursement</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">Approve atau tolak pengajuan</p>
                    </div>
                </div>
            </x-slot:title>

            <div class="space-y-6">
                {{-- Request Summary --}}
                <div
                    class="p-6 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border border-primary-200 dark:border-primary-800 rounded-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Requestor</label>
                            <div class="flex items-center gap-2 mt-1">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">
                                        {{ strtoupper(substr($this->reimbursement->user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                        {{ $this->reimbursement->user->name }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $this->reimbursement->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Amount</label>
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                                {{ $this->reimbursement->formatted_amount }}
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Title</label>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->title }}
                            </div>
                        </div>
                        @if ($this->reimbursement->description)
                            <div class="md:col-span-2">
                                <label
                                    class="text-xs font-medium text-primary-700 dark:text-primary-300">Description</label>
                                <div class="text-sm text-dark-900 dark:text-dark-50 mt-1 whitespace-pre-line">
                                    {{ $this->reimbursement->description }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Review Form --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Review Details</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">Assign kategori dan berikan notes (opsional)
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Category Selection --}}
                        <x-select.styled wire:model="categoryId" :options="$this->expenseCategories" label="Kategori Transaksi *"
                            placeholder="Pilih kategori..." searchable
                            hint="Kategori ini akan digunakan untuk pencatatan transaksi" />

                        {{-- Review Notes --}}
                        <x-textarea wire:model="reviewNotes" label="Catatan Review"
                            placeholder="Opsional: Tambahkan catatan untuk requestor" rows="3" />
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
                                <li>Approve: Kategori wajib dipilih untuk pencatatan transaksi</li>
                                <li>Reject: Requestor dapat edit dan submit ulang</li>
                                <li>Aksi ini tidak dapat dibatalkan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)" color="secondary" outline
                        class="w-full sm:w-auto order-3 sm:order-1">
                        Cancel
                    </x-button>
                    <x-button wire:click="rejectReimbursement" color="red" icon="x-circle"
                        loading="rejectReimbursement" class="w-full sm:w-auto order-2">
                        Reject
                    </x-button>
                    <x-button wire:click="approveReimbursement" color="green" icon="check-circle"
                        loading="approveReimbursement" class="w-full sm:w-auto order-1 sm:order-3">
                        Approve
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
