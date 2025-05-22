<section x-data="{ selectedWallet: null }" class="w-full bg-zinc-800 text-gray-200 p-6">
    <header class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">Bank Accounts</h1>
                <p class="mt-1 text-gray-400">Manage your company bank accounts and view transaction history</p>
            </div>
            <flux:modal.trigger name="add-wallet">
                <flux:button>Add Wallet</flux:button>
            </flux:modal.trigger>
        </div>
    </header>

    <!-- Bank Accounts Table -->
    <div class="mb-8">
        <div class="overflow-x-auto rounded-xl shadow-lg border border-zinc-700">
            <table class="min-w-full bg-zinc-900 divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Bank
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Account Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Account Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Last
                            Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    <!-- Bank Account -->
                    @foreach ($accounts as $account)
                        <tr class="hover:bg-zinc-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-600 h-10 w-10 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                        </svg>
                                    </div>
                                    <span class="text-white">
                                        <p class="font-medium">{{ $account->bank_name }}</p>
                                        <p class="text-sm">{{ $account->branch }}</p>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $account->account_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $account->account_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-400">
                                Rp {{ number_format((float) $account->current_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Today, 10:45</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                <div class="flex items-center gap-3">
                                    <button class="text-gray-400 hover:text-gray-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <flux:modal.trigger name="delete-wallet">
                                        <button @click="selectedWallet = {{ $account->id }}"
                                            class="text-gray-400 hover:text-red-400 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </flux:modal.trigger>
                                    <button
                                        class="px-3 py-1.5 bg-zinc-700 hover:bg-zinc-600 text-white text-xs rounded-lg transition-colors">
                                        View
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="mt-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-xl font-bold text-white mb-4 md:mb-0">Transaction History</h2>

            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <x-inputs.daterangepicker wire:model.live='date' placeholder="Select Date" />
                </div>
                <div class="flex-1 md:flex-none">
                    <x-inputs.select wire:model='transaction_type' placeholder="Choose a Transactions" selected=""
                        :options="[
                            ['value' => '', 'label' => 'All Transactions'],
                            ['value' => 'debit', 'label' => 'Debit'],
                            ['value' => 'credit', 'label' => 'Credit'],
                        ]" />
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl shadow-lg border border-zinc-700">
            <table class="min-w-full bg-zinc-900 divide-y divide-zinc-700">
                <thead class="bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                            Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-700">
                    <!-- Transaction 1 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-15</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Client Payment - PT Maju Jaya</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Bank Mandiri</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-900 text-emerald-300">Income</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-400">+ Rp 25,000,000
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 125,000,000</td>
                    </tr>

                    <!-- Transaction 2 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-12</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Office Rent Payment</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BCA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 15,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 78,500,000</td>
                    </tr>

                    <!-- Transaction 3 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-10</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Server Maintenance</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BNI</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 7,250,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 42,750,000</td>
                    </tr>

                    <!-- Transaction 4 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-08</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Client Payment - PT Sukses Makmur</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Bank Mandiri</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-emerald-900 text-emerald-300">Income</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-400">+ Rp 32,500,000
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 100,000,000</td>
                    </tr>

                    <!-- Transaction 5 -->
                    <tr class="hover:bg-zinc-800 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">2023-05-05</td>
                        <td class="px-6 py-4 text-sm text-gray-300">Employee Salaries</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">BCA</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-red-900 text-red-300">Expense</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-400">- Rp 45,000,000</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">Rp 93,500,000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-between items-center">
            <p class="text-sm text-gray-400">Showing 5 of 24 transactions</p>
            <div class="flex gap-2">
                <button
                    class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">Previous</button>
                <button
                    class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">Next</button>
            </div>
        </div>
    </div>

    <!-- Add Bank Account Modal -->
    <flux:modal name="add-wallet" class="md:w-96">
        <form class="space-y-6">
            <div>
                <flux:heading size="lg">Add Bank Account</flux:heading>
                <flux:text class="mt-2">Make sure all information is accurate before submitting.</flux:text>
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
                <x-inputs.select label="Currency" wire:model="form.currency" :options="[
                    ['value' => 'IDR', 'label' => 'IDR'],
                    ['value' => 'USD', 'label' => 'USD'],
                    ['value' => 'EUR', 'label' => 'EUR'],
                    ['value' => 'SGD', 'label' => 'SGD'],
                ]" selected="IDR" />

                <!-- Initial Balance -->
                <flux:input label="Initial Balance" wire:model="form.initial_balance" type="text"
                    inputmode="numeric" placeholder="100000" required />
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <flux.modal.close>
                    <flux:button type="button" variant="filled">
                        Cancel
                    </flux:button>
                </flux.modal.close>
                <flux:button wire:click='saveBankAccount' variant="primary">
                    Save Account
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Bank Account Modal -->
    <flux:modal name="delete-wallet" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Bank Account?</flux:heading>
                <flux:text class="mt-2">
                    <p>You're about to delete this Bank Account.</p>
                    <p>This action will delete the related <span class="font-bold">Payments and Transaction</span>.</p>
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button @click="$wire.deleteBankAccount(selectedWallet)" variant="danger">Delete project
                </flux:button>
            </div>
        </div>
    </flux:modal>
</section>
