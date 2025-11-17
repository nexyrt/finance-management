<div>
    <x-modal wire="modal" size="2xl" center>
        @if ($this->reimbursement)
            <x-slot:title>
                <div class="flex items-center justify-between my-3">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="document-text" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Reimbursement Details</h3>
                            <p class="text-sm text-dark-600 dark:text-dark-400">View reimbursement information</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-badge :text="$this->reimbursement->status_label" :color="$this->reimbursement->status_badge_color" size="lg" />
                        @if($this->reimbursement->payment_status !== 'unpaid')
                            <x-badge :text="$this->reimbursement->payment_status_label" :color="$this->reimbursement->payment_status_badge_color" size="lg" />
                        @endif
                    </div>
                </div>
            </x-slot:title>

            <div class="space-y-6">
                {{-- Basic Information --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Expense Information</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Title</label>
                            <div class="text-sm font-semibold text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->title }}
                            </div>
                        </div>

                        {{-- Amount Summary --}}
                        <div class="md:col-span-2 p-4 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border border-primary-200 dark:border-primary-800 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="text-xs font-medium text-primary-700 dark:text-primary-300">Total Amount</label>
                                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-1">
                                        {{ $this->reimbursement->formatted_amount }}
                                    </div>
                                </div>
                                
                                @if($this->reimbursement->amount_paid > 0)
                                    <div>
                                        <label class="text-xs font-medium text-green-700 dark:text-green-300">Amount Paid</label>
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                                            {{ $this->reimbursement->formatted_amount_paid }}
                                        </div>
                                    </div>
                                    
                                    @if($this->reimbursement->amount_remaining > 0)
                                        <div>
                                            <label class="text-xs font-medium text-amber-700 dark:text-amber-300">Remaining</label>
                                            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">
                                                {{ $this->reimbursement->formatted_amount_remaining }}
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Expense Date</label>
                            <div class="text-sm font-medium text-dark-900 dark:text-dark-50 mt-1">
                                {{ $this->reimbursement->expense_date->format('d M Y') }}
                                <span class="text-xs text-dark-500 dark:text-dark-400">
                                    ({{ $this->reimbursement->expense_date->diffForHumans() }})
                                </span>
                            </div>
                        </div>

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

                        <div>
                            <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Requested By</label>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white font-semibold text-xs">
                                        {{ strtoupper(substr($this->reimbursement->user->name, 0, 2)) }}
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

                        @if ($this->reimbursement->description)
                            <div class="md:col-span-2">
                                <label class="text-xs font-medium text-dark-500 dark:text-dark-400">Description</label>
                                <div class="text-sm text-dark-900 dark:text-dark-50 mt-1 whitespace-pre-line">
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
                                <a href="{{ $this->reimbursement->attachment_url }}" target="_blank"
                                    class="block hover:opacity-90 transition-opacity">
                                    <img src="{{ $this->reimbursement->attachment_url }}"
                                        alt="{{ $this->reimbursement->attachment_name }}"
                                        class="w-full h-auto max-h-96 object-contain cursor-pointer">
                                </a>
                            @else
                                <div class="p-4">
                                    <a href="{{ $this->reimbursement->attachment_url }}" target="_blank"
                                        class="flex items-center gap-3 hover:bg-gray-100 dark:hover:bg-gray-800 p-3 rounded-lg transition-colors">
                                        <x-icon name="document" class="w-8 h-8 text-gray-600 dark:text-gray-400" />
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                                {{ $this->reimbursement->attachment_name }}
                                            </div>
                                            <div class="text-xs text-dark-500 dark:text-dark-400">
                                                Click to download
                                            </div>
                                        </div>
                                        <x-icon name="arrow-down-tray" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Payment History --}}
                @if($this->reimbursement->payments->count() > 0)
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Payment History</h4>
                                <x-badge text="{{ $this->reimbursement->payments->count() }} payment(s)" color="blue" />
                            </div>
                        </div>

                        <div class="space-y-3">
                            @foreach($this->reimbursement->payments as $payment)
                                <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <x-icon name="banknotes" class="w-5 h-5 text-green-600 dark:text-green-400" />
                                            </div>
                                            <div class="flex-1">
                                                <div class="text-lg font-bold text-green-700 dark:text-green-300">
                                                    {{ $payment->formatted_amount }}
                                                </div>
                                                <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                    {{ $payment->payment_date->format('d M Y, H:i') }}
                                                </div>
                                                <div class="flex items-center gap-2 mt-2 text-sm text-green-700 dark:text-green-300">
                                                    <span>Paid by:</span>
                                                    <span class="font-medium">{{ $payment->payer->name }}</span>
                                                </div>
                                                @if($payment->bankTransaction)
                                                    <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                                        <div class="flex items-center gap-2">
                                                            <x-icon name="building-library" class="w-4 h-4" />
                                                            <span>{{ $payment->bankTransaction->bankAccount->account_name }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($payment->notes)
                                                    <div class="mt-2 text-xs text-green-600 dark:text-green-400">
                                                        <span class="font-medium">Note:</span> {{ $payment->notes }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0" />
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Workflow History --}}
                @if ($this->reimbursement->status !== 'draft')
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Workflow History</h4>
                        </div>

                        @if ($this->reimbursement->reviewed_at)
                            <div class="space-y-3">
                                {{-- Review --}}
                                <div class="p-4 bg-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-50 dark:bg-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-900/20 border border-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-200 dark:border-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-800 rounded-lg">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="{{ $this->reimbursement->isApproved() ? 'check-circle' : 'x-circle' }}"
                                            class="w-5 h-5 text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-600 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <div class="text-sm font-semibold text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-900 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-200">
                                                {{ $this->reimbursement->isApproved() ? 'Approved' : 'Rejected' }} by {{ $this->reimbursement->reviewer->name }}
                                            </div>
                                            <div class="text-xs text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-700 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-300">
                                                {{ $this->reimbursement->reviewed_at->format('d M Y H:i') }}
                                            </div>
                                            @if ($this->reimbursement->review_notes)
                                                <div class="text-sm text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-800 dark:text-{{ $this->reimbursement->isApproved() ? 'blue' : 'red' }}-300 mt-2">
                                                    {{ $this->reimbursement->review_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <x-icon name="clock" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                                <div class="text-sm font-medium text-dark-900 dark:text-dark-50 mb-1">Awaiting Review</div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">Pending approval from finance team</div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-between gap-3">
                    <div class="flex gap-2">
                        @if ($this->reimbursement->canEdit() && $this->reimbursement->user_id === auth()->id())
                            <x-button wire:click="editReimbursement" color="green" icon="pencil" size="sm">
                                Edit
                            </x-button>
                        @endif

                        @if ($this->reimbursement->canReview() && auth()->user()->can('approve reimbursements'))
                            <x-button wire:click="reviewReimbursement" color="yellow" icon="clipboard-document-check" size="sm">
                                Review
                            </x-button>
                        @endif

                        @if ($this->reimbursement->canPay() && auth()->user()->can('pay reimbursements'))
                            <x-button wire:click="payReimbursement" color="green" icon="banknotes" size="sm">
                                @if($this->reimbursement->hasPartialPayment())
                                    Lanjut Bayar
                                @else
                                    Process Payment
                                @endif
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