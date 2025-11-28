<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-800 to-primary-800 dark:from-white dark:via-primary-200 dark:to-primary-200 bg-clip-text text-transparent">
                Reimbursements
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                Manage expense reimbursement requests
            </p>
        </div>

        <div class="flex space-x-5">
            <x-button wire:click="$toggle('workflowGuideModal')" icon="information-circle" color="orange" outline>
                Workflow Guide
            </x-button>

            @can('create reimbursements')
                <livewire:reimbursements.create @created="$refresh" />
            @endcan
        </div>
    </div>

    {{-- Tab Container --}}
    <x-tab selected="My Requests">
        {{-- My Requests Tab --}}
        <x-tab.items tab="My Requests">
            <x-slot:left>
                <x-icon name="user" class="w-5 h-5" />
            </x-slot:left>
            {{-- My Requests Component --}}
            <div class="mt-3">
                <livewire:reimbursements.my-requests />
            </div>
        </x-tab.items>

        {{-- All Requests Tab (Finance Only) --}}
        @can('approve reimbursements')
            <x-tab.items tab="All Requests">
                <x-slot:left>
                    <x-icon name="users" class="w-5 h-5" />
                </x-slot:left>
                {{-- All Requests Component --}}
                <div class="mt-3">
                    <livewire:reimbursements.all-requests />
                </div>
            </x-tab.items>
        @endcan
    </x-tab>

    {{-- Workflow Guide Modal --}}
    <x-modal wire="workflowGuideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="information-circle" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Reimbursement Workflow</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Understanding the reimbursement process</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-6">
            {{-- Step Component --}}
            <x-step selected="1" circles helpers navigate-previous>
                {{-- Step 1: Create/Submit --}}
                <x-step.items step="1" title="Create Request" description="Submit your expense">
                    <div class="space-y-4">
                        <div
                            class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-icon name="document-plus"
                                    class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                        Create Your Reimbursement
                                    </div>
                                    <ul
                                        class="text-sm text-blue-800 dark:text-blue-300 space-y-1 list-disc list-inside">
                                        <li>Fill in expense details (title, amount, date, category)</li>
                                        <li>Upload supporting documents (receipts, invoices)</li>
                                        <li>Add description for context</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <x-icon name="document" class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                                    <span class="text-sm font-semibold text-dark-900 dark:text-dark-50">Save as
                                        Draft</span>
                                </div>
                                <p class="text-xs text-dark-500 dark:text-dark-400">
                                    Save incomplete requests to finish later. You can edit or delete drafts anytime.
                                </p>
                            </div>

                            <div
                                class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <x-icon name="paper-airplane" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    <span class="text-sm font-semibold text-green-900 dark:text-green-200">Submit for
                                        Approval</span>
                                </div>
                                <p class="text-xs text-green-700 dark:text-green-300">
                                    Submit directly to finance team for review. Cannot be edited once submitted.
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <x-icon name="exclamation-triangle"
                                class="w-4 h-4 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                            <p class="text-xs text-yellow-800 dark:text-yellow-300">
                                <strong>Important:</strong> Ensure all information is accurate and complete before
                                submitting.
                            </p>
                        </div>
                    </div>
                </x-step.items>

                {{-- Step 2: Review --}}
                <x-step.items step="2" title="Finance Review" description="Approval process">
                    <div class="space-y-4">
                        <div
                            class="p-4 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-icon name="clipboard-document-check"
                                    class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-2">
                                        Finance Team Reviews Your Request
                                    </div>
                                    <ul
                                        class="text-sm text-purple-800 dark:text-purple-300 space-y-1 list-disc list-inside">
                                        <li>Finance manager verifies expense details and attachments</li>
                                        <li>Assigns transaction category for accounting</li>
                                        <li>May approve or reject with notes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    <span
                                        class="text-sm font-semibold text-green-900 dark:text-green-200">Approved</span>
                                </div>
                                <p class="text-xs text-green-700 dark:text-green-300">
                                    Request is approved and ready for payment processing. You'll be notified once paid.
                                </p>
                            </div>

                            <div
                                class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <x-icon name="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span class="text-sm font-semibold text-red-900 dark:text-red-200">Rejected</span>
                                </div>
                                <p class="text-xs text-red-700 dark:text-red-300">
                                    Review rejection reason and edit your request with corrections, then resubmit.
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-start gap-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <x-icon name="information-circle"
                                class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                            <p class="text-xs text-blue-800 dark:text-blue-300">
                                Review typically takes 1-3 business days. You'll receive email notification of the
                                decision.
                            </p>
                        </div>
                    </div>
                </x-step.items>

                {{-- Step 3: Payment --}}
                <x-step.items step="3" title="Payment Processing" description="Receive reimbursement" completed>
                    <div class="space-y-4">
                        <div
                            class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-icon name="banknotes"
                                    class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-green-900 dark:text-green-200 mb-2">
                                        Finance Processes Your Payment
                                    </div>
                                    <ul
                                        class="text-sm text-green-800 dark:text-green-300 space-y-1 list-disc list-inside">
                                        <li>Finance team selects bank account for payment</li>
                                        <li>Bank transaction is created automatically</li>
                                        <li>Payment details recorded with reference number</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div
                            class="p-4 bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-800 rounded-lg">
                            <div class="flex items-start gap-3">
                                <x-icon name="credit-card"
                                    class="w-5 h-5 text-cyan-600 dark:text-cyan-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-cyan-900 dark:text-cyan-200 mb-2">
                                        Payment Details Available
                                    </div>
                                    <p class="text-sm text-cyan-800 dark:text-cyan-300">
                                        View complete payment information including:
                                    </p>
                                    <ul
                                        class="text-xs text-cyan-700 dark:text-cyan-300 space-y-1 list-disc list-inside mt-2">
                                        <li>Bank account used for payment</li>
                                        <li>Payment date and time</li>
                                        <li>Transaction reference number</li>
                                        <li>Processor name</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center p-6 bg-gradient-to-r from-green-50 to-cyan-50 dark:from-green-900/20 dark:to-cyan-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <div class="text-center">
                                <div
                                    class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <x-icon name="check" class="w-8 h-8 text-white" />
                                </div>
                                <div class="text-lg font-bold text-green-900 dark:text-green-200">
                                    Reimbursement Complete!
                                </div>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                    Your reimbursement has been processed successfully
                                </p>
                            </div>
                        </div>
                    </div>
                </x-step.items>
            </x-step>

            {{-- Status Legend --}}
            <div class="space-y-3">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Status Reference</h4>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="flex items-center gap-2">
                        <x-badge text="Draft" color="gray" />
                        <span class="text-xs text-dark-500 dark:text-dark-400">Editable</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge text="Pending" color="yellow" />
                        <span class="text-xs text-dark-500 dark:text-dark-400">In Review</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge text="Approved" color="blue" />
                        <span class="text-xs text-dark-500 dark:text-dark-400">Awaiting Payment</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge text="Rejected" color="red" />
                        <span class="text-xs text-dark-500 dark:text-dark-400">Needs Revision</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-badge text="Paid" color="green" />
                        <span class="text-xs text-dark-500 dark:text-dark-400">Completed</span>
                    </div>
                </div>
            </div>

            {{-- Permissions Info --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg">
                <div class="flex items-start gap-3">
                    <x-icon name="shield-check"
                        class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">
                            Role-Based Access
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="font-medium text-dark-900 dark:text-dark-50">Staff:</span>
                                <span class="text-dark-500 dark:text-dark-400"> Create & manage own requests</span>
                            </div>
                            <div>
                                <span class="font-medium text-dark-900 dark:text-dark-50">Finance Manager:</span>
                                <span class="text-dark-500 dark:text-dark-400"> Review & approve requests</span>
                            </div>
                            <div>
                                <span class="font-medium text-dark-900 dark:text-dark-50">Admin:</span>
                                <span class="text-dark-500 dark:text-dark-400"> Full access to all features</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('workflowGuideModal')" color="primary">
                    Got it!
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Child Components (Shared) --}}
    <livewire:reimbursements.show />
    <livewire:reimbursements.update />

    @can('approve reimbursements')
        <livewire:reimbursements.review />
    @endcan

    @can('pay reimbursements')
        <livewire:reimbursements.payment />
    @endcan
</div>
