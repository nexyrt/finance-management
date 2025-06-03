<section x-data="{ selectedWallet: null }" class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="bg-gradient-to-r from-zinc-600 to-zinc-700 dark:from-zinc-700 dark:to-zinc-800 p-3 rounded-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Bank Accounts</h1>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1">Manage your company bank accounts and
                                monitor transactions</p>
                        </div>
                    </div>
                    <flux:modal.trigger name="add-wallet">
                        <flux:button wire:click="resetForm" variant="primary"
                            class="px-6 py-3 rounded-xl shadow-lg transition-all duration-200 hover:shadow-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Bank Account
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </div>
        </div>

        <!-- Bank Accounts Grid -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-zinc-800 dark:text-white mb-4">Your Bank Accounts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($accounts as $account)
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-zinc-200 dark:border-zinc-700 hover:shadow-xl transition-all duration-200 overflow-hidden group">
                        <!-- Card Header with Bank Info -->
                        <div class="bg-zinc-50 dark:bg-zinc-800 p-6 border-b border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-center gap-4">
                                <div
                                    class="bg-gradient-to-r from-zinc-600 to-zinc-700 h-12 w-12 rounded-xl flex items-center justify-center shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-zinc-900 dark:text-white text-lg">
                                        {{ $account->bank_name }}</h3>
                                    <p class="text-zinc-600 dark:text-zinc-400 text-sm">
                                        {{ $account->branch ?? 'Main Branch' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Account Name</p>
                                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $account->account_name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Account Number</p>
                                    <p class="font-mono text-zinc-700 dark:text-zinc-300">{{ $account->account_number }}
                                    </p>
                                </div>
                                <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Current Balance</p>
                                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $account->currency }}
                                        {{ number_format((float) $account->current_balance, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="px-6 pb-6">
                            <div class="flex items-center gap-2">
                                <button wire:click="loadBankTransactions({{ $account->id }})"
                                    class="flex-1 bg-zinc-600 hover:bg-zinc-700 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 text-center">
                                    View Transactions
                                </button>
                                <button wire:click="editBankAccount({{ $account->id }})"
                                    class="p-2.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <flux:modal.trigger name="delete-wallet">
                                    <button @click="selectedWallet = {{ $account->id }}"
                                        class="p-2.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </flux:modal.trigger>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-12 text-center">
                        <div
                            class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-zinc-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2">No Bank Accounts</h3>
                        <p class="text-zinc-600 dark:text-zinc-400">Get started by adding your first bank account.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Transaction History Section -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-700">
            <!-- Section Header -->
            <div class="border-b border-zinc-200 dark:border-zinc-700 p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white flex items-center gap-3">
                            <div class="bg-gradient-to-r from-zinc-600 to-zinc-700 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            Transaction History
                        </h2>
                        @if ($selectedBankId && $accounts->find($selectedBankId))
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1">
                                {{ $accounts->find($selectedBankId)->bank_name }} -
                                {{ $accounts->find($selectedBankId)->account_name }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        @if ($selectedBankId)
                            <flux:button variant="ghost" wire:click='clearSelectedBank' class="w-full sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Selection
                            </flux:button>
                        @endif
                        <div class="min-w-[200px]">
                            <flux:select wire:model.live="transactionType" placeholder="Choose Transaction...">
                                <flux:select.option value="">All Transaction</flux:select.option>
                                <flux:select.option>Debit</flux:select.option>
                                <flux:select.option>Credit</flux:select.option>
                            </flux:select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                                Account</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">
                                Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($transactions as $transaction)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium">{{ $transaction->created_at ? $transaction->created_at->format('d M Y') : 'N/A' }}</span>
                                        <span
                                            class="text-xs text-zinc-500">{{ $transaction->created_at ? $transaction->created_at->format('H:i') : '' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-900 dark:text-white">
                                    <div class="max-w-xs">
                                        <p class="font-medium truncate">
                                            {{ $transaction->description ?? 'No description' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $transaction->bankAccount ? $transaction->bankAccount->bank_name . ' - ' . $transaction->bankAccount->account_name : 'Unknown Account' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($transaction->transaction_type === 'credit')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Credit
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Debit
                                        </span>
                                    @endif
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold {{ $transaction->transaction_type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    <div class="flex items-center">
                                        {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}
                                        <span class="ml-1">Rp
                                            {{ number_format((float) abs($transaction->amount), 0, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                                            @if ($selectedBankId)
                                                No transactions found
                                            @else
                                                No transactions to display
                                            @endif
                                        </h3>
                                        <p class="text-zinc-500 dark:text-zinc-400">
                                            @if ($selectedBankId)
                                                This account doesn't have any transactions yet.
                                            @else
                                                Select a bank account to view its transaction history.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($transactions->hasPages())
                <div class="border-t border-zinc-200 dark:border-zinc-700 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of
                            {{ $transactions->total() }} results
                        </p>
                        <div class="flex gap-2">
                            {{ $transactions->links(data: ['scrollTo' => false]) }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Bank Account Modal -->
    <flux:modal name="add-wallet" class="md:w-96">
        <form wire:submit.prevent="saveOrUpdateBankAccount" class="space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                    {{ $editMode ? 'Edit Bank Account' : 'Add Bank Account' }}
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ $editMode ? 'Update bank account information below.' : 'Make sure all information is accurate before submitting.' }}
                </p>
            </div>

            <!-- Account Name -->
            <flux:input label="Account Name" wire:model="form.account_name" placeholder="Rekening Gaji" required />

            <!-- Account Number -->
            <flux:input label="Account Number" wire:model="form.account_number" type="text" inputmode="numeric"
                pattern="[0-9]*" placeholder="0272828901" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                required />

            <!-- Bank Name -->
            <flux:input label="Bank Name" wire:model="form.bank_name" placeholder="Bank Central Asia (BCA)"
                required />

            <!-- Branch -->
            <flux:input label="Branch" wire:model="form.branch" placeholder="KCP Sudirman" />

            <!-- Currency Selection -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-inputs.select name="form.currency" label="Currency" :options="[
                        ['value' => 'IDR', 'label' => 'IDR'],
                        ['value' => 'USD', 'label' => 'USD'],
                        ['value' => 'EUR', 'label' => 'EUR'],
                        ['value' => 'SGD', 'label' => 'SGD'],
                    ]" :selected="$form['currency']"
                        :modal-mode="true" />
                </div>

                <!-- Initial Balance -->
                <flux:input label="Initial Balance" wire:model="form.initial_balance" type="text"
                    inputmode="numeric" placeholder="100000" required />
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <flux:modal.close>
                    <flux:button variant="ghost">
                        Cancel
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ $editMode ? 'Update Account' : 'Save Account' }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Bank Account Modal -->
    <flux:modal name="delete-wallet" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Delete Bank Account?</h2>
                <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    <p>You're about to delete this Bank Account.</p>
                    <p>This action will delete the related <span class="font-bold">Payments and Transaction</span>.</p>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button @click="$wire.deleteBankAccount(selectedWallet)" variant="danger">Delete Account
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
