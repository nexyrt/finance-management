<div>
    {{-- Modal --}}
    <x-modal wire="modal" size="2xl" center>
        @if ($this->reimbursement)
            {{-- Custom Title --}}
            <x-slot:title>
                <div class="flex items-center justify-between my-3">
                    <div class="flex items-center gap-4">
                        <div
                            class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="document-text" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Reimbursement Details</h3>
                            <p class="text-sm text-dark-600 dark:text-dark-400">View reimbursement request information
                            </p>
                        </div>
                    </div>
                    <x-badge :text="$this->reimbursement->status_label"
                        :color="$this->reimbursement->status_badge_color" size="lg" />
                </div>
            </x-slot:title>

            {{-- Content --}}
            <div class="space-y-6">
                {{-- Basic Information --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Expense Information</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Title --}}
                        <div class="md:col-span-2">
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Title</label>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->title }}
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Amount</label>
                            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                                {{ $this->reimbursement->formatted_amount }}
                            </div>
                        </div>

                        {{-- Expense Date --}}
                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Expense Date</label>
                            <div class="text-sm font-medium text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->expense_date->format('d M Y') }}
                                <span class="text-xs text-dark-500 dark:text-dark-400">
                                    ({{ $this->reimbursement->expense_date->diffForHumans() }})
                                </span>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Category</label>
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

                        {{-- Requestor --}}
                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Requested By</label>
                            <div class="flex items-center gap-2 mt-1">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">
                                        {{ $this->reimbursement->user->initials() }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                        {{ $this->reimbursement->user->name }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $this->reimbursement->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        @if ($this->reimbursement->description)
                            <div class="md:col-span-2">
                                <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Description</label>
                                <div class="text-sm text-dark-900 dark:text-dark-50 mt-1 whitespace-pre-wrap">
                                    {{ $this->reimbursement->description }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Attachment --}}
                @if ($this->reimbursement->hasAttachment())
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Attachment</h4>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            @if ($this->reimbursement->isImageAttachment())
                                {{-- Image Preview --}}
                                <img src="{{ $this->reimbursement->attachment_url }}"
                                    alt="{{ $this->reimbursement->attachment_name }}"
                                    class="w-full h-auto max-h-96 object-contain">
                            @elseif ($this->reimbursement->isPdfAttachment())
                                {{-- PDF Preview --}}
                                <div class="p-6 text-center">
                                    <x-icon name="document" class="w-16 h-16 text-gray-400 mx-auto mb-3" />
                                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                        {{ $this->reimbursement->attachment_name }}
                                    </div>
                                    <div class="text-xs text-dark-500 dark:text-dark-400 mb-4">PDF Document</div>
                                    <a href="{{ $this->reimbursement->attachment_url }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                        <x-icon name="arrow-down-tray" class="w-4 h-4" />
                                        Download PDF
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Workflow History --}}
                @if ($this->reimbursement->status !== 'draft')
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Workflow History</h4>
                        </div>

                        <div class="space-y-3">
                            {{-- Review Information --}}
                            @if ($this->reimbursement->reviewed_at)
                                <div
                                    class="p-4 bg-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-50 dark:bg-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-900/20 border border-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-200 dark:border-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-800 rounded-lg">
                                    <div class="flex items-start gap-3">
                                        <x-icon
                                            name="{{ $this->reimbursement->isApproved() ? 'check-circle' : 'x-circle' }}"
                                            class="w-5 h-5 text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-600 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <div
                                                class="text-sm font-semibold text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-900 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-200">
                                                {{ $this->reimbursement->isApproved() ? 'Approved' : 'Rejected' }} by
                                                {{ $this->reimbursement->reviewer->name }}
                                            </div>
                                            <div
                                                class="text-xs text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-700 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-300">
                                                {{ $this->reimbursement->reviewed_at->format('d M Y H:i') }}
                                            </div>
                                            @if ($this->reimbursement->review_notes)
                                                <div
                                                    class="text-sm text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-800 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-300 mt-2">
                                                    {{ $this->reimbursement->review_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Payment Information --}}
                            @if ($this->reimbursement->paid_at)
                                <div
                                    class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="banknotes"
                                            class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-green-900 dark:text-green-200">
                                                Payment Processed by {{ $this->reimbursement->payer->name }}
                                            </div>
                                            <div class="text-xs text-green-700 dark:text-green-300">
                                                {{ $this->reimbursement->paid_at->format('d M Y H:i') }}
                                            </div>
                                            @if ($this->reimbursement->bankTransaction)
                                                <div class="text-sm text-green-800 dark:text-green-300 mt-2">
                                                    <div class="flex items-center gap-2">
                                                        <span>Bank Account:</span>
                                                        <span class="font-medium">
                                                            {{ $this->reimbursement->bankTransaction->bankAccount->account_name }}
                                                        </span>
                                                    </div>
                                                    @if ($this->reimbursement->bankTransaction->reference_number)
                                                        <div class="text-xs text-green-700 dark:text-green-400 mt-1">
                                                            Ref: {{ $this->reimbursement->bankTransaction->reference_number }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Footer with Actions --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-between gap-3">
                    <div class="flex gap-2">
                        {{-- Edit (Draft/Rejected only) --}}
                        @if ($this->reimbursement->canEdit() && $this->reimbursement->user_id === auth()->id())
                            <x-button wire:click="editReimbursement" color="green" icon="pencil" size="sm">
                                Edit
                            </x-button>
                        @endif

                        {{-- Review (Finance - Pending only) --}}
                        @if ($this->reimbursement->canReview() && auth()->user()->can('approve reimbursements'))
                            <x-button wire:click="reviewReimbursement" color="yellow" icon="clipboard-document-check"
                                size="sm">
                                Review
                            </x-button>
                        @endif

                        {{-- Pay (Finance - Approved only) --}}
                        @if ($this->reimbursement->canPay() && auth()->user()->can('pay reimbursements'))
                            <x-button wire:click="payReimbursement" color="green" icon="banknotes" size="sm">
                                Process Payment
                            </x-button>
                        @endif
                    </div>

                    <x-button wire:click="$toggle('modal')" color="secondary" outline>
                        Close
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>