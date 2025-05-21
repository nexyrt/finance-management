<section class="w-full bg-zinc-800 text-gray-200 p-6">
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

    <!-- Bank Accounts Carousel using Flowbite -->
    <div id="bank-accounts-carousel" class="relative w-full mb-8" data-carousel="static">
        <!-- Carousel wrapper -->
        <div class="relative overflow-hidden rounded-lg h-auto">
            @php
                // Calculate how many items per slide (2 for md screens and above, 1 for smaller)
                $itemsPerSlide = 2;
                $totalSlides = ceil(count($accounts) / $itemsPerSlide);
                
                // Group accounts into slides
                $slides = [];
                for ($i = 0; $i < $totalSlides; $i++) {
                    $slideAccounts = array_slice($accounts->toArray(), $i * $itemsPerSlide, $itemsPerSlide);
                    $slides[] = $slideAccounts;
                }
            @endphp

            @foreach ($slides as $index => $slideAccounts)
                <!-- Slide {{ $index + 1 }} -->
                <div class="hidden duration-700 ease-in-out" data-carousel-item="{{ $index === 0 ? 'active' : '' }}">
                    <div class="flex flex-col md:flex-row gap-4 p-4">
                        @foreach ($slideAccounts as $item)
                            <!-- Bank Account Card -->
                            <div class="w-full md:w-1/2">
                                <div class="bg-zinc-900 rounded-xl p-5 border border-zinc-700 shadow-lg h-full">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                // Generate dynamic bank icon color based on bank name
                                                $colors = ['blue', 'red', 'purple', 'green', 'yellow', 'indigo', 'pink'];
                                                $colorIndex = crc32($item['bank_name'] ?? 'default') % count($colors);
                                                $color = $colors[$colorIndex];
                                            @endphp
                                            <div class="bg-{{ $color }}-600 h-10 w-10 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-white">{{ $item['bank_name'] ?? 'Bank' }}</h3>
                                                <p class="text-sm text-gray-400">{{ $item['account_name'] ?? 'Account' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button class="text-gray-400 hover:text-gray-200 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8-2.83-2.828z" />
                                                </svg>
                                            </button>
                                            <button class="text-gray-400 hover:text-red-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-400">Account Number</p>
                                            <p class="text-lg font-medium text-white">
                                                @php
                                                    // Format account number with spaces for readability
                                                    $acc = $item['account_number'] ?? '0000000000';
                                                    echo implode(' ', str_split($acc, 4));
                                                @endphp
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-400">Last Updated</p>
                                            <p class="text-white">
                                                @if (isset($item['updated_at']))
                                                    @php
                                                        // Format the date in a more readable way
                                                        $date = is_string($item['updated_at'])
                                                            ? new DateTime($item['updated_at'])
                                                            : $item['updated_at'];
                                                        
                                                        $now = new DateTime();
                                                        $interval = $date->diff($now);
                                                        
                                                        if ($interval->days == 0) {
                                                            echo 'Today, ' . $date->format('H:i');
                                                        } elseif ($interval->days == 1) {
                                                            echo 'Yesterday, ' . $date->format('H:i');
                                                        } elseif ($interval->days < 7) {
                                                            echo $interval->days . ' days ago';
                                                        } else {
                                                            echo $date->format('M d, Y');
                                                        }
                                                    @endphp
                                                @else
                                                    Not available
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-400">Balance</p>
                                        <p class="text-xl font-bold text-emerald-400">
                                            @php
                                                // Format currency with Rp prefix and thousand separators
                                                $balance = $item['current_balance'] ?? 0;
                                                if (is_string($balance)) {
                                                    $balance = (float) preg_replace('/[^0-9.]/', '', $balance);
                                                }
                                                // Format with thousand separator and remove decimal if it's zero
                                                $formatted = number_format($balance, 0, ',', '.');
                                                echo "Rp {$formatted}";
                                            @endphp
                                        </p>
                                    </div>

                                    <button
                                        class="w-full bg-zinc-700 hover:bg-zinc-600 text-white py-2 rounded-lg transition-colors">
                                        View Transactions
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Slider indicators -->
        <div class="absolute z-30 flex -translate-x-1/2 space-x-3 rtl:space-x-reverse bottom-5 left-1/2">
            @for ($i = 0; $i < $totalSlides; $i++)
                <button type="button" class="w-8 h-2 rounded-full bg-{{ $i === 0 ? 'blue-500' : 'zinc-600' }}" 
                    aria-current="{{ $i === 0 ? 'true' : 'false' }}" 
                    aria-label="Slide {{ $i + 1 }}" 
                    data-carousel-slide-to="{{ $i }}"></button>
            @endfor
        </div>

        <!-- Slider controls -->
        <button type="button"
            class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
            data-carousel-prev>
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-zinc-700/80 hover:bg-zinc-600 group-focus:ring-4 group-focus:ring-zinc-500 group-focus:outline-none">
                <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 1 1 5l4 4" />
                </svg>
                <span class="sr-only">Previous</span>
            </span>
        </button>
        <button type="button"
            class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
            data-carousel-next>
            <span
                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-zinc-700/80 hover:bg-zinc-600 group-focus:ring-4 group-focus:ring-zinc-500 group-focus:outline-none">
                <svg class="w-4 h-4 text-white rtl:rotate-180" aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 9 4-4-4-4" />
                </svg>
                <span class="sr-only">Next</span>
            </span>
        </button>
    </div>

    <!-- Transaction History Section -->
    <div class="mt-10">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-xl font-bold text-white mb-4 md:mb-0">Transaction History</h2>

            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <div class="flex-1 md:flex-none">
                    <x-inputs.select selected="" placeholder="Choose a Wallet" :options="[
                        ['value' => '', 'label' => 'All Wallet'],
                        ['value' => 'BNI', 'label' => 'BNI'],
                        ['value' => 'BCA', 'label' => 'BCA'],
                    ]" />
                </div>

                <div class="flex-1 md:flex-none">
                    <x-inputs.select wire:model='transaction_type' placeholder="Choose a Transactions" selected=""
                        :options="[
                            ['value' => '', 'label' => 'All Transactions'],
                            ['value' => 'income', 'label' => 'Income'],
                            ['value' => 'expense', 'label' => 'Expense'],
                        ]" />
                </div>

                <div class="flex-1 md:flex-none">
                    <flux:input type="date" max="2999-12-31" />
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
</section>
