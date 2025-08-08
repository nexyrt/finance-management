{{-- resources/views/livewire/bank-accounts/index.blade.php --}}

<section class="space-y-8">
    {{-- Header Section --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-1">
            <h1
                class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-500 to-primary-700 dark:from-white dark:via-primary-200 dark:to-primary-100 bg-clip-text text-transparent">
                Manajemen Rekening Bank
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-base sm:text-lg">
                Kelola rekening bank dan transaksi keuangan Anda
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            {{-- <x-button wire:click="$dispatch('open-transaction-modal')" color="secondary" outline icon="plus"
                class="w-full sm:w-auto">
                Tambah Transaksi
            </x-button> --}}
            {{-- <x-button wire:click="$dispatch('open-create-modal')" color="primary" icon="building-library"
                class="w-full sm:w-auto">
                Tambah Rekening
            </x-button> --}}
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Balance Card --}}
        <div
            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Total Saldo</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">Dari semua rekening</p>
                </div>
                <div
                    class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        {{-- Total Accounts Card --}}
        <div
            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Total Rekening</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $totalAccounts }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">Rekening terdaftar</p>
                </div>
                <div
                    class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="building-library" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        {{-- Active Accounts Card --}}
        <div
            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-dark-600 dark:text-dark-400 mb-1">Rekening Aktif</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $activeAccounts }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">Siap digunakan</p>
                </div>
                <div
                    class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filter Section --}}
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl shadow-sm">
        <div class="p-6 border-b border-zinc-200 dark:border-dark-600">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="magnifying-glass" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Pencarian & Filter</h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400">Temukan rekening yang Anda cari</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Search Input --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Pencarian</label>
                    <x-input wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama rekening, bank, atau nomor..." icon="magnifying-glass" />
                </div>

                {{-- Bank Filter --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Bank</label>
                    <x-select.styled wire:model.live="bankFilter"
                        :options="$bankNames->map(fn($bank) => ['label' => $bank, 'value' => $bank])->toArray()"
                        placeholder="Semua bank..." searchable />
                </div>

                {{-- Clear Filters --}}
                <div class="space-y-2">
                    <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Reset</label>
                    <x-button wire:click="clearFilters" color="zinc" icon="x-mark" outline class="w-full">
                        Hapus Filter
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Accounts Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($accounts as $account)
        <div
            class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 group">
            {{-- Card Header --}}
            <div class="p-6 border-b border-zinc-100 dark:border-dark-700">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div
                                class="h-10 w-10 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center">
                                <x-icon name="building-library" class="w-5 h-5 text-white" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-semibold text-dark-900 dark:text-dark-50 truncate">
                                    {{ $account->account_name }}
                                </h4>
                                <p class="text-sm text-dark-500 dark:text-dark-400">
                                    {{ $account->bank_name }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-zinc-50 dark:bg-dark-700 rounded-lg px-3 py-2">
                            <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">Nomor Rekening</p>
                            <p class="font-mono text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ $account->account_number }}
                            </p>
                        </div>
                    </div>

                    {{-- Action Dropdown --}}
                    <x-dropdown icon="ellipsis-vertical" position="bottom-end">
                        {{--
                        <x-dropdown.items text="Lihat Detail" icon="eye"
                            wire:click="$dispatch('show-account', { accountId: {{ $account->id }} })" />
                        <x-dropdown.items text="Edit Rekening" icon="pencil"
                            wire:click="$dispatch('edit-account', { accountId: {{ $account->id }} })" />
                        <x-dropdown.items text="Tambah Transaksi" icon="plus"
                            wire:click="$dispatch('create-transaction', { accountId: {{ $account->id }} })" />
                        <div class="border-t border-zinc-100 dark:border-dark-700 my-1"></div>
                        <x-dropdown.items text="Hapus Rekening" icon="trash"
                            wire:click="$dispatch('delete-account', { accountId: {{ $account->id }} })"
                            class="text-red-600 dark:text-red-400" /> --}}
                    </x-dropdown>
                </div>
            </div>

            {{-- Card Content --}}
            <div class="p-6">
                @if($account->branch)
                <div class="mb-4">
                    <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">Cabang</p>
                    <p class="text-sm text-dark-700 dark:text-dark-300">{{ $account->branch }}</p>
                </div>
                @endif

                {{-- Balance Display --}}
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">Saldo Saat Ini</p>
                        <p
                            class="text-xl font-bold {{ $account->current_balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($account->current_balance, 0, ',', '.') }}
                        </p>
                    </div>

                    @if($account->initial_balance !== $account->current_balance)
                    <div class="bg-zinc-50 dark:bg-dark-700 rounded-lg p-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-dark-500 dark:text-dark-400">Saldo Awal:</span>
                            <span class="font-medium text-dark-700 dark:text-dark-300">
                                Rp {{ number_format($account->initial_balance, 0, ',', '.') }}
                            </span>
                        </div>
                        @php
                        $difference = $account->current_balance - $account->initial_balance;
                        @endphp
                        <div class="flex justify-between items-center text-xs mt-1">
                            <span class="text-dark-500 dark:text-dark-400">Perubahan:</span>
                            <span
                                class="font-medium {{ $difference >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $difference >= 0 ? '+' : '' }}Rp {{ number_format($difference, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Card Footer --}}
            <div
                class="px-6 py-4 bg-zinc-50 dark:bg-dark-700 rounded-b-xl border-t border-zinc-100 dark:border-dark-700">
                <div class="flex items-center justify-between">
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        Terakhir diperbarui {{ $account->updated_at->diffForHumans() }}
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- <x-button size="sm" color="zinc" outline icon="eye"
                            class="opacity-0 group-hover:opacity-100 transition-opacity">
                            Detail
                        </x-button> --}}
                    </div>
                </div>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="col-span-full">
            <div class="text-center py-12">
                <div
                    class="h-20 w-20 bg-zinc-50 dark:bg-zinc-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="building-library" class="w-10 h-10 text-zinc-400 dark:text-zinc-500" />
                </div>
                <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">Belum Ada Rekening Bank</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-6 max-w-sm mx-auto">
                    Tambahkan rekening bank pertama Anda untuk mulai mengelola keuangan
                </p>
                {{-- <x-button wire:click="$dispatch('open-create-modal')" color="primary" icon="plus">
                    Tambah Rekening Pertama
                </x-button> --}}
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($accounts->hasPages())
    <div class="flex justify-center">
        {{ $accounts->links() }}
    </div>
    @endif

    {{-- Recent Transactions Section --}}
    @if($recentTransactions->count() > 0)
    <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl shadow-sm">
        <div class="p-6 border-b border-zinc-200 dark:border-dark-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="clock" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Transaksi Terbaru</h3>
                        <p class="text-sm text-dark-500 dark:text-dark-400">5 transaksi terakhir</p>
                    </div>
                </div>
                <x-button color="zinc" outline size="sm" {{-- href="{{ route('bank-transactions') }}" --}}>
                    Lihat Semua
                </x-button>
            </div>
        </div>

        <div class="divide-y divide-zinc-100 dark:divide-dark-700">
            @foreach($recentTransactions as $transaction)
            <div class="p-6 hover:bg-zinc-50 dark:hover:bg-dark-700 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div
                            class="h-10 w-10 {{ $transaction->transaction_type === 'credit' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-lg flex items-center justify-center">
                            <x-icon name="{{ $transaction->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                                class="w-5 h-5 {{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                        </div>
                        <div>
                            <p class="font-medium text-dark-900 dark:text-dark-50">{{ $transaction->description }}</p>
                            <p class="text-sm text-dark-500 dark:text-dark-400">
                                {{ $transaction->bankAccount->account_name }} • {{
                                $transaction->transaction_date->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p
                            class="font-semibold {{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}Rp {{
                            number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                        @if($transaction->reference_number)
                        <p class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                            {{ $transaction->reference_number }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Modals akan dihandle oleh komponen terpisah --}}
    {{--
    <livewire:bank-accounts.create />
    <livewire:bank-accounts.edit />
    <livewire:bank-accounts.show />
    <livewire:bank-accounts.delete />
    <livewire:bank-transactions.create /> --}}
</section>