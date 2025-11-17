<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="blue" icon="plus" class="w-full sm:w-auto">
        New Request
    </x-button>

    {{-- Modal --}}
    <x-modal wire="modal" size="xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="document-plus" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">New Reimbursement</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Submit your expense reimbursement request</p>
                </div>
            </div>
        </x-slot:title>

        <form id="reimbursement-create" wire:submit="save" class="space-y-6">
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Expense Details</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Provide information about your expense</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="lg:col-span-2">
                        <x-input wire:model="title" label="Title *" placeholder="e.g., Taxi to client meeting" />
                    </div>

                    <x-wireui-currency wire:model="amount" label="Amount *" prefix="Rp" thousands="." decimal=","
                        placeholder="0" />

                    <x-date wire:model="expense_date" label="Expense Date *" placeholder="Select date" />

                    <div class="lg:col-span-2">
                        <x-select.styled wire:model="category" :options="$this->categories" label="Category (Referensi)"
                            placeholder="Pilih kategori..." searchable
                            hint="Finance akan menentukan kategori final saat review" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-textarea wire:model="description" label="Description" rows="3"
                            placeholder="Optional: Add more details about this expense" />
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Attachment</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Upload receipt or supporting document (optional)
                    </p>
                </div>

                <div>
                    <x-upload wire:model="attachment" label="Receipt/Document" tip="JPG, PNG, or PDF (Max 5MB)"
                        accept="image/jpeg,image/png,application/pdf" />
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-3 sm:order-1">
                    Cancel
                </x-button>
                <x-button wire:click="saveAsDraft" color="gray" icon="document" loading="saveAsDraft"
                    class="w-full sm:w-auto order-2 sm:order-2">
                    Save as Draft
                </x-button>
                <x-button wire:click="submitForApproval" color="blue" icon="paper-airplane"
                    loading="submitForApproval" class="w-full sm:w-auto order-1 sm:order-3">
                    Submit for Approval
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
