<div>
    {{-- Modal --}}
    <x-modal wire="modal" size="xl" center persistent>
        @if ($this->reimbursement)
            {{-- Custom Title --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Process Payment</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">Complete reimbursement payment</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Content --}}
            <div class="space-y-6">
                {{-- Payment Summary --}}
                <div
                    class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <div class="text-center">
                        <div class="text-sm font-medium text-green-700 dark:text-green-300 mb-2">Payment Amount</div>
                        <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-4">
                            {{ $this->reimbursement->formatted_amount }}
                        </div>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-800 border border-green-200 dark:border-green-700 rounded-lg">
                            <div
                                class="w-8 h-8 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-xs">
                                    {{ $this->reimbursement->user->initials() }}
                                </span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                    {{ $this->reimbursement->user->name }}
                                </div>
                                <div class="text-xs text-dark-500 dark:text-dark-400">
                                    {{ $this->reimbursement->title }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Details --}}
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Category:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50 ml-2">
                                {{ $this->reimbursement->category_label }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Expense Date:</span>
                            <span class="font-medium text-dark-900 dark:text-dark-50 ml-2">
                                {{ $this->reimbursement->expense_date->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Payment Form --}}
                <form wire:submit="processPayment" class="space-y-6">
                    {{-- Section: Payment Information --}}
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">Payment Information
                            </h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">Select bank account and payment date</p>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="lg:col-span-2">
                                <x-select.styled wire:model="bankAccountId" :options="$this->bankAccounts" label="Bank Account *"
                                    placeholder="Select bank account..." searchable />
                            </div>

                            <x-date wire:model="paymentDate" label="Payment Date *" placeholder="Select date" />

                            <div class="lg:col-span-2">
                                <x-input wire:model="referenceNotes" label="Reference/Notes"
                                    placeholder="Optional: Add payment reference or notes" />
                            </div>
                        </div>
                    </div>

                    {{-- Warning Note --}}
                    <div
                        class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                        <div class="flex items-start gap-3">
                            <x-icon name="exclamation-triangle"
                                class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-yellow-900 dark:text-yellow-200">
                                <div class="font-semibold mb-1">Important:</div>
                                <ul class="list-disc list-inside space-y-1 text-yellow-800 dark:text-yellow-300">
                                    <li>This action will create a debit transaction in the selected bank account</li>
                                    <li>The reimbursement status will be marked as "Paid"</li>
                                    <li>This action cannot be undone</li>
                                </ul>
                            </div>
                        </div>
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
                    <x-button wire:click="processPayment" color="green" icon="banknotes" loading="processPayment"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        Process Payment
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
