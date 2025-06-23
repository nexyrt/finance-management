<!-- Bank Accounts Management View with Modals -->
<section class="w-full p-6 bg-white dark:bg-zinc-800">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Akun Bank</h1>
            <p class="text-gray-500 dark:text-zinc-400">Kelola rekening bank dan transaksi keuangan</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button
                class="bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 px-4 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>
            <button wire:click="openAddAccountModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Akun Bank
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Balance -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Saldo</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalBalance }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                clip-rule="evenodd" />
                        </svg>
                        +12.3% dari bulan lalu
                    </p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 dark:text-emerald-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Accounts -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Akun</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalAccounts }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        Aktif semua
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->todayTransactions }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                        Total transaksi
                    </p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Bank Accounts List -->
        <div class="xl:col-span-2">
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Akun Bank</h2>
                        <div class="flex items-center space-x-3">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" wire:model.live="search" placeholder="Cari akun..."
                                    class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg px-4 py-2 pl-10 text-sm text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <!-- Filter -->
                            <flux:select wire:model.live="filterBank" placeholder="Filter Bank" size="sm"
                                clearable>
                                <flux:select.option value="">Semua Bank</flux:select.option>
                                <flux:select.option value="Mandiri">Bank Mandiri</flux:select.option>
                                <flux:select.option value="BCA">Bank BCA</flux:select.option>
                                <flux:select.option value="BRI">Bank BRI</flux:select.option>
                                <flux:select.option value="BNI">Bank BNI</flux:select.option>
                            </flux:select>
                        </div>
                    </div>
                </div>

                <!-- Bank Accounts Cards -->
                <div class="p-6 space-y-4">
                    @forelse($this->bankAccounts as $account)
                        <div
                            class="group relative bg-gray-50 dark:bg-zinc-800 rounded-xl p-6 border border-gray-100 dark:border-zinc-700 hover:shadow-md dark:hover:shadow-zinc-950/25 transition-all duration-300 hover:scale-[1.01]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Bank Logo/Icon -->
                                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-xl">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="font-semibold text-gray-800 dark:text-white text-lg">
                                            {{ $account->bank_name }}</h3>
                                        <p class="text-gray-500 dark:text-zinc-400 text-sm">
                                            {{ $account->account_name }}</p>
                                        <p class="text-gray-600 dark:text-zinc-300 text-sm font-mono">
                                            {{ $account->account_number }}</p>
                                        @if ($account->branch)
                                            <p class="text-gray-500 dark:text-zinc-400 text-xs">Cabang:
                                                {{ $account->branch }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                        {{ $this->formatAccountBalance($account) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                                        Saldo saat ini
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div
                                class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                                <div class="flex space-x-3">
                                    <button wire:click="openTransferModal"
                                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-lg text-xs font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                        Transfer
                                    </button>
                                    <button wire:click="openAddTransactionModal({{ $account->id }})"
                                        class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1 rounded-lg text-xs font-medium hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                        Transaksi
                                    </button>
                                    <button
                                        class="bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 px-3 py-1 rounded-lg text-xs font-medium hover:bg-gray-200 dark:hover:bg-zinc-600 transition-colors">
                                        Riwayat
                                    </button>
                                </div>

                                <!-- More Actions -->
                                <div class="relative">
                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item wire:click="openEditAccountModal({{ $account->id }})"
                                                icon="pencil">
                                                Edit Akun
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="confirmDeleteAccount({{ $account->id }})"
                                                icon="trash" variant="danger">
                                                Hapus Akun
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-16 w-16 text-gray-400 dark:text-zinc-500 mx-auto mb-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Belum ada akun bank</h3>
                            <p class="text-gray-500 dark:text-zinc-400 mb-4">Mulai dengan menambahkan akun bank pertama
                                Anda</p>
                            <button wire:click="openAddAccountModal"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Tambah Akun Bank
                            </button>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($this->bankAccounts->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
                        {{ $this->bankAccounts->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Recent Transactions -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Transaksi Terbaru</h3>
                        <button wire:click="openAllTransactionsModal"
                            class="text-blue-600 dark:text-blue-400 text-sm font-medium hover:text-blue-700 dark:hover:text-blue-300">
                            Lihat Semua
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($this->recentTransactions as $transaction)
                        <div
                            class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                            <div
                                class="p-2 rounded-lg 
                                @if ($transaction['type'] === 'credit') bg-green-100 dark:bg-green-900/30
                                @else
                                    bg-red-100 dark:bg-red-900/30 @endif
                            ">
                                @if ($transaction['type'] === 'credit')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-red-600 dark:text-red-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 12H6" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $transaction['description'] ?: 'Transaksi ' . ucfirst($transaction['type']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $transaction['bank_name'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p
                                    class="text-sm font-semibold 
                                    @if ($transaction['type'] === 'credit') text-green-600 dark:text-green-400
                                    @else
                                        text-red-600 dark:text-red-400 @endif
                                ">
                                    {{ $transaction['type'] === 'credit' ? '+' : '-' }}{{ $this->formatCurrency($transaction['amount']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $transaction['formatted_date'] }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-zinc-400 text-sm">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aksi Cepat</h3>
                </div>

                <div class="p-6 space-y-3">
                    <button wire:click="openAddTransactionModal"
                        class="w-full bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 p-4 rounded-lg transition-colors duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="font-medium">Tambah Transaksi</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <button wire:click="openTransferModal"
                        class="w-full bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 text-green-700 dark:text-green-400 p-4 rounded-lg transition-colors duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="font-medium">Transfer Bank</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Bank Balance Distribution -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Distribusi Saldo</h3>
                </div>

                <div class="p-6 space-y-4">
                    @foreach ($this->balanceDistribution as $bank)
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ $bank['bank_name'] }}</span>
                                <span
                                    class="text-sm text-gray-500 dark:text-zinc-400">{{ $bank['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                <div class="bg-blue-500 dark:bg-blue-400 h-2 rounded-full"
                                    style="width: {{ $bank['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach

                    @if ($this->balanceDistribution->isNotEmpty())
                        <!-- Summary -->
                        <div class="pt-4 border-t border-gray-100 dark:border-zinc-800">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">Total Saldo</span>
                                <span
                                    class="text-sm font-semibold text-gray-800 dark:text-white">{{ $this->totalBalance }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bank Account Modal -->
    <flux:modal wire:model.self="showAddAccountModal" name="add-account" class="md:w-96">
        <form wire:submit="saveAccount" class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Akun Bank</flux:heading>
                <flux:text class="mt-2">Masukkan detail akun bank baru</flux:text>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="account_name" label="Nama Akun" placeholder="Contoh: Rekening Operasional" />
                @error('account_name')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="account_number" label="Nomor Rekening" placeholder="1234567890" />
                @error('account_number')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:select wire:model="bank_name" label="Bank" placeholder="Pilih Bank">
                    <flux:select.option value="Bank Mandiri">Bank Mandiri</flux:select.option>
                    <flux:select.option value="Bank BCA">Bank BCA</flux:select.option>
                    <flux:select.option value="Bank BRI">Bank BRI</flux:select.option>
                    <flux:select.option value="Bank BNI">Bank BNI</flux:select.option>
                    <flux:select.option value="Bank BTN">Bank BTN</flux:select.option>
                    <flux:select.option value="Bank CIMB Niaga">Bank CIMB Niaga</flux:select.option>
                    <flux:select.option value="Bank Danamon">Bank Danamon</flux:select.option>
                    <flux:select.option value="Bank Permata">Bank Permata</flux:select.option>
                    <flux:select.option value="Lainnya">Lainnya</flux:select.option>
                </flux:select>
                @error('bank_name')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="branch" label="Cabang (Opsional)" placeholder="Contoh: Jakarta Pusat" />
                @error('branch')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                {{-- Currency Input untuk Saldo Awal --}}
                <div x-data="currencyInput({
                    name: 'current_balance',
                    value: {{ $current_balance ?? 0 }},
                    placeholder: '50.000.000',
                    wireModel: 'current_balance'
                })">
                    <flux:label>Saldo Awal</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>Rp</flux:input.group.prefix>
                        <flux:input x-ref="input" placeholder="50.000.000" required x-on:input="handleInput($event)"
                            x-on:keydown="restrictInput($event)" x-on:paste="handlePaste($event)" />
                    </flux:input.group>
                    <input type="hidden" name="current_balance" x-ref="hiddenInput" :value="rawValue">
                    @error('current_balance')
                        <flux:error class="mt-1">{{ $message }}</flux:error>
                    @enderror
                    <flux:description>
                        Maksimum: <span x-text="getMaxValueFormatted()"></span>
                    </flux:description>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showAddAccountModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Simpan Akun
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Bank Account Modal -->
    <flux:modal wire:model.self="showEditAccountModal" name="edit-account" class="md:w-96">
        <form wire:submit="updateAccount" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Akun Bank</flux:heading>
                <flux:text class="mt-2">Perbarui detail akun bank</flux:text>
            </div>

            <div class="space-y-4">
                <flux:input wire:model="account_name" label="Nama Akun" placeholder="Contoh: Rekening Operasional" />
                @error('account_name')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="account_number" label="Nomor Rekening" placeholder="1234567890" />
                @error('account_number')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:select wire:model="bank_name" label="Bank" placeholder="Pilih Bank">
                    <flux:select.option value="Bank Mandiri">Bank Mandiri</flux:select.option>
                    <flux:select.option value="Bank BCA">Bank BCA</flux:select.option>
                    <flux:select.option value="Bank BRI">Bank BRI</flux:select.option>
                    <flux:select.option value="Bank BNI">Bank BNI</flux:select.option>
                    <flux:select.option value="Bank BTN">Bank BTN</flux:select.option>
                    <flux:select.option value="Bank CIMB Niaga">Bank CIMB Niaga</flux:select.option>
                    <flux:select.option value="Bank Danamon">Bank Danamon</flux:select.option>
                    <flux:select.option value="Bank Permata">Bank Permata</flux:select.option>
                    <flux:select.option value="Lainnya">Lainnya</flux:select.option>
                </flux:select>
                @error('bank_name')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="branch" label="Cabang (Opsional)" placeholder="Contoh: Jakarta Pusat" />
                @error('branch')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                {{-- Currency Input untuk Edit dengan wire:key untuk force re-render --}}
                <div wire:key="current_balance_edit_{{ $editingAccount?->id ?? 'new' }}" x-data="currencyInput({
                    name: 'current_balance_edit',
                    value: {{ $current_balance ?? 0 }},
                    placeholder: '50.000.000',
                    wireModel: 'current_balance'
                })">
                    <flux:label>Saldo Saat Ini</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>Rp</flux:input.group.prefix>
                        <flux:input x-ref="input" placeholder="50.000.000" required x-on:input="handleInput($event)"
                            x-on:keydown="restrictInput($event)" x-on:paste="handlePaste($event)" />
                    </flux:input.group>
                    <input type="hidden" name="current_balance" x-ref="hiddenInput" :value="rawValue">
                    @error('current_balance')
                        <flux:error class="mt-1">{{ $message }}</flux:error>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditAccountModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Perbarui Akun
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Add Transaction Modal -->
    <flux:modal wire:model.self="showAddTransactionModal" name="add-transaction" class="md:w-96">
        <form wire:submit="saveTransaction" class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Transaksi</flux:heading>
                <flux:text class="mt-2">Catat transaksi masuk atau keluar</flux:text>
            </div>

            <div class="space-y-4">
                <flux:select wire:model="selected_bank_account_id" label="Akun Bank" placeholder="Pilih Akun Bank">
                    @foreach ($this->availableAccounts as $account)
                        <flux:select.option value="{{ $account->id }}">
                            {{ $account->bank_name }} - {{ $account->account_number }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('selected_bank_account_id')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:select wire:model="transaction_type" label="Jenis Transaksi">
                    <flux:select.option value="credit">Kredit (Masuk)</flux:select.option>
                    <flux:select.option value="debit">Debit (Keluar)</flux:select.option>
                </flux:select>
                @error('transaction_type')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                {{-- Currency Input untuk Transaction Amount --}}
                <div x-data="currencyInput({
                    name: 'transaction_amount',
                    value: {{ $transaction_amount ?? 0 }},
                    placeholder: '1.000.000',
                    wireModel: 'transaction_amount'
                })">
                    <flux:label>Jumlah</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>Rp</flux:input.group.prefix>
                        <flux:input x-ref="input" placeholder="1.000.000" required x-on:input="handleInput($event)"
                            x-on:keydown="restrictInput($event)" x-on:paste="handlePaste($event)" />
                    </flux:input.group>
                    <input type="hidden" name="transaction_amount" x-ref="hiddenInput" :value="rawValue">
                    @error('transaction_amount')
                        <flux:error class="mt-1">{{ $message }}</flux:error>
                    @enderror
                </div>

                <flux:input wire:model="transaction_date" label="Tanggal Transaksi" type="date" />
                @error('transaction_date')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:textarea wire:model="transaction_description" label="Deskripsi (Opsional)"
                    placeholder="Keterangan transaksi..." rows="3" />
                @error('transaction_description')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="reference_number" label="Nomor Referensi (Opsional)"
                    placeholder="Contoh: TF001234" />
                @error('reference_number')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showAddTransactionModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Simpan Transaksi
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Transfer Modal -->
    <flux:modal wire:model.self="showTransferModal" name="transfer" class="md:w-96">
        <form wire:submit="processTransfer" class="space-y-6">
            <div>
                <flux:heading size="lg">Transfer Antar Bank</flux:heading>
                <flux:text class="mt-2">Transfer dana antar akun bank</flux:text>
            </div>

            <div class="space-y-4">
                <flux:select wire:model="transfer_from_account" label="Dari Akun" placeholder="Pilih akun sumber">
                    @foreach ($this->availableAccounts as $account)
                        <flux:select.option value="{{ $account->id }}">
                            {{ $account->bank_name }} - {{ $account->account_number }}
                            ({{ $account->formatted_balance }})
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('transfer_from_account')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:select wire:model="transfer_to_account" label="Ke Akun" placeholder="Pilih akun tujuan">
                    @foreach ($this->availableAccounts as $account)
                        <flux:select.option value="{{ $account->id }}">
                            {{ $account->bank_name }} - {{ $account->account_number }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                @error('transfer_to_account')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                {{-- Currency Input untuk Transfer Amount --}}
                <div x-data="currencyInput({
                    name: 'transfer_amount',
                    value: {{ $transfer_amount ?? 0 }},
                    placeholder: '1.000.000',
                    wireModel: 'transfer_amount'
                })">
                    <flux:label>Jumlah Transfer</flux:label>
                    <flux:input.group>
                        <flux:input.group.prefix>Rp</flux:input.group.prefix>
                        <flux:input x-ref="input" placeholder="1.000.000" required x-on:input="handleInput($event)"
                            x-on:keydown="restrictInput($event)" x-on:paste="handlePaste($event)" />
                    </flux:input.group>
                    <input type="hidden" name="transfer_amount" x-ref="hiddenInput" :value="rawValue">
                    @error('transfer_amount')
                        <flux:error class="mt-1">{{ $message }}</flux:error>
                    @enderror
                    <flux:description>
                        Maksimum: <span x-text="getMaxValueFormatted()"></span>
                    </flux:description>
                </div>

                <flux:input wire:model="transfer_date" label="Tanggal Transfer" type="date" />
                @error('transfer_date')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:textarea wire:model="transfer_description" label="Keterangan (Opsional)"
                    placeholder="Keterangan transfer..." rows="2" />
                @error('transfer_description')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror

                <flux:input wire:model="transfer_reference" label="Nomor Referensi (Opsional)"
                    placeholder="Contoh: TRF001234" />
                @error('transfer_reference')
                    <flux:error class="mt-1">{{ $message }}</flux:error>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showTransferModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Proses Transfer
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.self="showDeleteModal" name="delete-account" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <div
                    class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <flux:heading size="lg">Hapus Akun Bank</flux:heading>
                <flux:text class="mt-2">
                    Apakah Anda yakin ingin menghapus akun bank
                    @if ($accountToDelete)
                        <strong>{{ $accountToDelete->bank_name }} - {{ $accountToDelete->account_number }}</strong>
                    @endif
                    ?
                    <br><br>
                    <span class="text-red-600 dark:text-red-400 font-medium">
                        ⚠️ PERINGATAN: Semua transaksi dan data terkait akan ikut terhapus permanen dan tidak dapat
                        dikembalikan.
                    </span>
                </flux:text>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">
                    Batal
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteAccount">
                    Ya, Hapus Akun
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- All Transactions Modal -->
    <flux:modal wire:model.self="showAllTransactionsModal" name="all-transactions" class="max-w-5xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Semua Transaksi</flux:heading>
                <flux:text class="mt-2">Riwayat transaksi dari semua akun bank</flux:text>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Bank Filter -->
                    <flux:select wire:model.live="transactionFilterBank" placeholder="Semua Bank" size="sm"
                        clearable>
                        <flux:select.option value="">Semua Bank</flux:select.option>
                        @foreach ($this->availableAccounts as $account)
                            <flux:select.option value="{{ $account->id }}">{{ $account->bank_name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <!-- Transaction Type Filter -->
                    <flux:select wire:model.live="transactionFilterType" placeholder="Semua Jenis" size="sm"
                        clearable>
                        <flux:select.option value="">Semua Jenis</flux:select.option>
                        <flux:select.option value="credit">Kredit (Masuk)</flux:select.option>
                        <flux:select.option value="debit">Debit (Keluar)</flux:select.option>
                    </flux:select>

                    <!-- Date Range Picker -->
                    <x-inputs.datepicker wire:model.live="transactionDateRange" name="transactionDateRange"
                        placeholder="Pilih rentang tanggal" mode="range" class="text-sm" />

                    <!-- Clear Filter Button -->
                    <flux:button wire:click="resetTransactionFilters" variant="outline" size="sm"
                        class="whitespace-nowrap" icon="arrow-path">
                        Reset Filter
                    </flux:button>
                </div>

                <!-- Active Filters Display -->
                @if ($transactionFilterBank || $transactionFilterType || $transactionDateRange)
                    <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <span class="text-sm text-gray-600 dark:text-zinc-400 font-medium">Filter aktif:</span>

                        @if ($transactionFilterBank)
                            @php
                                $selectedBank = $this->availableAccounts->where('id', $transactionFilterBank)->first();
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                Bank: {{ $selectedBank->bank_name ?? 'N/A' }}
                                <button wire:click="$set('transactionFilterBank', '')"
                                    class="ml-1 hover:text-blue-600 dark:hover:text-blue-200">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if ($transactionFilterType)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                Jenis: {{ $transactionFilterType === 'credit' ? 'Kredit (Masuk)' : 'Debit (Keluar)' }}
                                <button wire:click="$set('transactionFilterType', '')"
                                    class="ml-1 hover:text-green-600 dark:hover:text-green-200">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif

                        @if ($transactionDateRange)
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                Tanggal: {{ $transactionDateRange }}
                                <button wire:click="$set('transactionDateRange', '')"
                                    class="ml-1 hover:text-purple-600 dark:hover:text-purple-200">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Summary Stats -->
            @if ($this->allTransactions->count() > 0)
                <div
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white dark:bg-zinc-900 rounded-lg border border-gray-200 dark:border-zinc-700">
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">Total Transaksi</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-white">
                            {{ $this->allTransactions->total() }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">Total Masuk</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">
                            {{ $this->formatCurrency($this->allTransactions->where('transaction_type', 'credit')->sum('amount')) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">Total Keluar</p>
                        <p class="text-xl font-bold text-red-600 dark:text-red-400">
                            {{ $this->formatCurrency($this->allTransactions->where('transaction_type', 'debit')->sum('amount')) }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-500 dark:text-zinc-400">Saldo Bersih</p>
                        @php
                            $netBalance =
                                $this->allTransactions->where('transaction_type', 'credit')->sum('amount') -
                                $this->allTransactions->where('transaction_type', 'debit')->sum('amount');
                        @endphp
                        <p
                            class="text-xl font-bold {{ $netBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $this->formatCurrency(abs($netBalance)) }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Transactions Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-800">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-zinc-300">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-zinc-300">Bank</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-zinc-300">Deskripsi</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-zinc-300">Jenis</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-zinc-300">Jumlah</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-zinc-300">Referensi</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-700 dark:text-zinc-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                        @forelse($this->allTransactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                                <td class="px-4 py-3 text-gray-800 dark:text-white">
                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">
                                    <div>
                                        <p class="font-medium">{{ $transaction->bankAccount->bank_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ $transaction->bankAccount->account_number }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">
                                    {{ $transaction->description ?: 'Transaksi ' . ucfirst($transaction->transaction_type) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if ($transaction->transaction_type === 'credit') bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400
                                        @else
                                            bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 @endif
                                    ">
                                        {{ $transaction->transaction_type === 'credit' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 text-right font-semibold 
                                    @if ($transaction->transaction_type === 'credit') text-green-600 dark:text-green-400
                                    @else
                                        text-red-600 dark:text-red-400 @endif
                                ">
                                    {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}{{ $this->formatCurrency($transaction->amount) }}
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-zinc-400 text-xs">
                                    {{ $transaction->reference_number ?: '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <!-- Edit Button (optional for future enhancement) -->
                                        {{-- <button 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
                                            title="Edit Transaksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button> --}}

                                        <!-- Delete Button -->
                                        <button wire:click="confirmDeleteTransaction({{ $transaction->id }})"
                                            class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors"
                                            title="Hapus Transaksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-zinc-400">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-12 w-12 text-gray-400 dark:text-zinc-500 mb-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        @if ($transactionFilterBank || $transactionFilterType || $transactionDateRange)
                                            <p class="font-medium">Tidak ada transaksi yang sesuai dengan filter</p>
                                            <p class="text-xs mt-1">Coba ubah atau reset filter untuk melihat data
                                                lainnya</p>
                                        @else
                                            <p>Belum ada transaksi</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for All Transactions -->
            @if ($this->allTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator && $this->allTransactions->hasPages())
                <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                    {{ $this->allTransactions->links() }}
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Delete Transaction Confirmation Modal -->
    <flux:modal wire:model.self="showDeleteTransactionModal" name="delete-transaction" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <div
                    class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <flux:heading size="lg">Hapus Transaksi</flux:heading>
                <flux:text class="mt-2">
                    Apakah Anda yakin ingin menghapus transaksi ini?
                    @if ($transactionToDelete)
                        <br><br>
                        <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded-lg mt-4 text-left">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-zinc-400">Bank:</span>
                                    <span
                                        class="font-medium">{{ $transactionToDelete->bankAccount->bank_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-zinc-400">Tanggal:</span>
                                    <span
                                        class="font-medium">{{ \Carbon\Carbon::parse($transactionToDelete->transaction_date)->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-zinc-400">Jenis:</span>
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full 
                                        @if ($transactionToDelete->transaction_type === 'credit') bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $transactionToDelete->transaction_type === 'credit' ? 'Kredit (Masuk)' : 'Debit (Keluar)' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-zinc-400">Jumlah:</span>
                                    <span
                                        class="font-semibold {{ $transactionToDelete->transaction_type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transactionToDelete->transaction_type === 'credit' ? '+' : '-' }}{{ $this->formatCurrency($transactionToDelete->amount) }}
                                    </span>
                                </div>
                                @if ($transactionToDelete->description)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-zinc-400">Deskripsi:</span>
                                        <span class="font-medium">{{ $transactionToDelete->description }}</span>
                                    </div>
                                @endif
                                @if ($transactionToDelete->reference_number)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-zinc-400">Referensi:</span>
                                        <span class="font-medium">{{ $transactionToDelete->reference_number }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <br>
                        <span class="text-amber-600 dark:text-amber-400 font-medium">
                            ⚠️ PERHATIAN: Saldo bank akan disesuaikan secara otomatis setelah transaksi dihapus.
                        </span>
                    @endif
                </flux:text>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteTransactionModal', false)">
                    Batal
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteTransaction">
                    Ya, Hapus Transaksi
                </flux:button>
            </div>
        </div>
    </flux:modal>

</section>

{{-- JavaScript untuk notifikasi (opsional) --}}
@script
    <script>
        $wire.on('notify', (event) => {
            const {
                type,
                message
            } = event;

            // Membuat elemen notifikasi
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full opacity-0 ${
            type === 'success' 
                ? 'bg-green-100 border border-green-400 text-green-700' 
                : 'bg-red-100 border border-red-400 text-red-700'
        }`;

            notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' 
                        ? `<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                   </svg>`
                        : `<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                   </svg>`
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" 
                                class="inline-flex rounded-md p-1.5 hover:bg-opacity-20 hover:bg-gray-600 focus:outline-none">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;

            // Menambahkan notifikasi ke body
            document.body.appendChild(notification);

            // Animasi masuk
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
                notification.classList.add('translate-x-0', 'opacity-100');
            }, 100);

            // Auto remove setelah 5 detik
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        });

        // Debug: Log currency input values
        $wire.on('debug-currency', (event) => {
            console.log('Currency Debug:', event);
        });
    </script>
@endscript
