<div>
    {{-- Modal --}}
    <x-modal wire="modal" size="xl" center persistent>
        {{-- Custom Title --}}
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('common.edit') }} {{ __('common.reimbursements') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.submit_reimbursement') }}</p>

                    {{-- Show rejection note if status is rejected --}}
                    @if ($this->reimbursement && $this->reimbursement->isRejected())
                        <div
                            class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex items-start gap-2">
                                <x-icon name="exclamation-circle"
                                    class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                                <div>
                                    <div class="text-sm font-semibold text-red-900 dark:text-red-200">{{ __('common.rejected') }}
                                    </div>
                                    <div class="text-sm text-red-800 dark:text-red-300">
                                        {{ $this->reimbursement->review_notes ?? __('common.no_data') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-slot:title>

        {{-- Form --}}
        <form id="reimbursement-update" wire:submit="save" class="space-y-6">
            {{-- Section: Basic Information --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Expense Details</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Update information about your expense</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="lg:col-span-2">
                        <x-input wire:model="title" :label="__('common.title') . ' *'" placeholder="e.g., Taxi to client meeting" />
                    </div>

                    <x-wireui-currency wire:model="amount" :label="__('common.amount') . ' *'" prefix="Rp" thousands="." decimal=","
                        placeholder="0" />

                    <x-date wire:model="expense_date" label="Expense Date *" placeholder="Select date" />

                    <div class="lg:col-span-2">
                        <x-select.styled wire:model="category" :options="$this->categories" :label="__('common.category') . ' *'"
                            placeholder="Select category..." searchable />
                    </div>

                    <div class="lg:col-span-2">
                        <x-textarea wire:model="description" :label="__('common.description')" rows="3"
                            placeholder="Optional: Add more details about this expense" />
                    </div>
                </div>
            </div>

            {{-- Section: Attachment --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('common.attachment') }}</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">Update receipt or supporting document</p>
                </div>

                <div class="space-y-3">
                    {{-- Existing Attachment --}}
                    @if ($existingAttachment && !$removeAttachment)
                        <div
                            class="p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-icon name="paper-clip" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                    <div>
                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $existingAttachment }}
                                        </div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">Current attachment</div>
                                    </div>
                                </div>
                                <x-button.circle icon="trash" color="red" size="sm"
                                    wire:click="removeExistingAttachment" title="Remove" />
                            </div>
                        </div>
                    @endif

                    {{-- Upload New Attachment --}}
                    <div>
                        <x-upload wire:model="attachment" label="Upload New Receipt/Document"
                            tip="JPG, PNG, or PDF (Max 5MB)" accept="image/jpeg,image/png,application/pdf" />
                    </div>
                </div>
            </div>
        </form>

        {{-- Footer --}}
        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)" color="secondary" outline
                    class="w-full sm:w-auto order-3 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button wire:click="saveAsDraft" color="gray" icon="document" loading="saveAsDraft"
                    class="w-full sm:w-auto order-2 sm:order-2">
                    {{ __('common.save') }}
                </x-button>
                <x-button wire:click="submitForApproval" color="blue" icon="paper-airplane"
                    loading="submitForApproval" class="w-full sm:w-auto order-1 sm:order-3">
                    {{ __('common.submit') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
