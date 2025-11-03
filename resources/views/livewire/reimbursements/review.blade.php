<div>
    {{-- Modal --}}
    <x-modal wire="modal" size="xl" center persistent>
        @if ($this->reimbursement)
            {{-- Custom Title --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div
                        class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="clipboard-document-check" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Review Reimbursement</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">Approve or reject this expense request</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Content --}}
            <div class="space-y-6">
                {{-- Request Summary --}}
                <div
                    class="p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Title</label>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->title }}
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Amount</label>
                            <div class="text-xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                                {{ $this->reimbursement->formatted_amount }}
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Requested
                                By</label>
                            <div class="flex items-center gap-2 mt-1">
                                <div
                                    class="w-6 h-6 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">
                                        {{ $this->reimbursement->user->initials() }}
                                    </span>
                                </div>
                                <span class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ $this->reimbursement->user->name }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Category</label>
                            <div class="mt-1">
                                <x-badge :text="$this->reimbursement->category_label" :color="match ($this->reimbursement->category) {
                                    'transport' => 'blue',
                                    'meals' => 'orange',
                                    'office_supplies' => 'green',
                                    'communication' => 'purple',
                                    'accommodation' => 'pink',
                                    'medical' => 'red',
                                    default => 'gray',
                                }" />
                            </div>
                        </div>
                    </div>

                    @if ($this->reimbursement->description)
                        <div class="mt-4 pt-4 border-t border-primary-200 dark:border-primary-800">
                            <label
                                class="text-xs font-medium text-primary-700 dark:text-primary-300">Description</label>
                            <div class="text-sm text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->description }}
                            </div>
                        </div>
                    @endif

                    @if ($this->reimbursement->hasAttachment())
                        <div class="mt-4 pt-4 border-t border-primary-200 dark:border-primary-800">
                            <label
                                class="text-xs font-medium text-primary-700 dark:text-primary-300 block mb-2">Attachment</label>
                            <div
                                class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                @if ($this->reimbursement->isImageAttachment())
                                    <img src="{{ $this->reimbursement->attachment_url }}"
                                        alt="{{ $this->reimbursement->attachment_name }}"
                                        class="w-full h-auto max-h-64 object-contain">
                                @elseif ($this->reimbursement->isPdfAttachment())
                                    <div class="p-4 text-center">
                                        <x-icon name="document" class="w-12 h-12 text-gray-400 mx-auto mb-2" />
                                        <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                            {{ $this->reimbursement->attachment_name }}
                                        </div>
                                        <a href="{{ $this->reimbursement->attachment_url }}" target="_blank"
                                            class="inline-flex items-center gap-2 mt-2 px-3 py-1.5 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                            <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                            View PDF
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Decision Form --}}
                <form wire:submit="submitReview" class="space-y-6">
                    {{-- Decision Selection --}}
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Decision</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">Choose whether to approve or reject
                                this request</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <button type="button" wire:click="approveQuick" loading="approveQuick"
                                class="p-4 border-2 rounded-xl transition-all cursor-pointer {{ $this->decision === 'approve' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-blue-300' }}">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 {{ $this->decision === 'approve' ? 'bg-blue-500' : 'bg-gray-200 dark:bg-gray-700' }} rounded-xl flex items-center justify-center transition-colors">
                                        <x-icon name="check-circle"
                                            class="w-6 h-6 {{ $this->decision === 'approve' ? 'text-white' : 'text-gray-500' }}" />
                                    </div>
                                    <div class="text-left">
                                        <div
                                            class="font-semibold {{ $this->decision === 'approve' ? 'text-blue-900 dark:text-blue-100' : 'text-dark-900 dark:text-dark-50' }}">
                                            Approve
                                        </div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">Accept this request</div>
                                    </div>
                                </div>
                            </button>

                            <button type="button" wire:click="rejectQuick" loading="rejectQuick"
                                class="p-4 border-2 rounded-xl transition-all cursor-pointer {{ $this->decision === 'reject' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-red-300' }}">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 {{ $this->decision === 'reject' ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-700' }} rounded-xl flex items-center justify-center transition-colors">
                                        <x-icon name="x-circle"
                                            class="w-6 h-6 {{ $this->decision === 'reject' ? 'text-white' : 'text-gray-500' }}" />
                                    </div>
                                    <div class="text-left">
                                        <div
                                            class="font-semibold {{ $this->decision === 'reject' ? 'text-red-900 dark:text-red-100' : 'text-dark-900 dark:text-dark-50' }}">
                                            Reject
                                        </div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">Decline this request</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- Transaction Category (Only show if approving) --}}
                    @if ($decision === 'approve')
                        <div class="space-y-4">
                            <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Transaction
                                    Category *</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400">Select expense category for
                                    accounting</p>
                            </div>

                            <x-select.styled wire:model="categoryId" :options="$this->transactionCategories"
                                placeholder="Select transaction category..." searchable />
                        </div>
                    @endif

                    {{-- Notes --}}
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">
                                Notes {{ $decision === 'reject' ? '*' : '' }}
                            </h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">
                                @if ($decision === 'reject')
                                    Provide reason for rejection (required)
                                @else
                                    Add optional notes or comments
                                @endif
                            </p>
                        </div>

                        <x-textarea wire:model="notes" rows="3"
                            placeholder="{{ $decision === 'reject' ? 'Please provide a clear reason for rejection...' : 'Optional: Add any additional comments...' }}" />
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)" color="secondary" outline
                        class="w-full sm:w-auto order-2 sm:order-1">
                        Cancel
                    </x-button>
                    <x-button wire:click="submitReview" :color="$decision === 'approve' ? 'blue' : 'red'" :icon="$decision === 'approve' ? 'check-circle' : 'x-circle'" loading="submitReview"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        {{ $decision === 'approve' ? 'Approve Request' : 'Reject Request' }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
