{{-- resources/views/livewire/accounts/index.blade.php --}}

<div wire:init="loadData">
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-600 to-primary-700 dark:from-white dark:via-primary-300 dark:to-primary-200 bg-clip-text text-transparent">
                    {{ __('common.bank_accounts') }}
                </h1>
                <p class="text-dark-600 dark:text-dark-400 text-base sm:text-lg">
                    {{ __('pages.manage_all_bank_accounts') }}
                </p>
            </div>

            {{-- Total Balance + Settings --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Workflow Guide Button --}}
                <button
                    wire:click="$toggle('guideModal')"
                    class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all self-start sm:self-auto"
                >
                    <x-icon name="information-circle" class="w-4 h-4" />
                    {{ __('pages.client_guide_btn') }}
                </button>
                @if ($ready)
                    {{-- Total Balance Card --}}
                    <div
                        class="px-6 py-4 bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-800 rounded-xl shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <x-icon name="currency-dollar" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <p class="text-xs text-white/70 font-medium">{{ __('common.total') }}</p>
                                <p class="text-2xl font-bold text-white">
                                    Rp {{ number_format($this->totalBalance, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-white/60">
                                    {{ $this->accountsData->count() }}
                                    {{ __('pages.accounts') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Account Settings --}}
                    @if ($selectedAccountId)
                        <x-dropdown icon="cog-6-tooth" position="bottom-end">
                            <x-slot:trigger>
                                <x-button color="secondary" outline icon="cog-6-tooth" class="w-full sm:w-auto">
                                    {{ __('common.settings') }}
                                </x-button>
                            </x-slot:trigger>
                            <x-dropdown.items text="{{ __('common.edit') }}" icon="pencil"
                                wire:click="editAccount({{ $selectedAccountId }})" />
                            <x-dropdown.items text="{{ __('common.delete') }}" icon="trash"
                                wire:click="deleteAccount({{ $selectedAccountId }})"
                                class="text-red-600 dark:text-red-400" />
                        </x-dropdown>
                    @endif
                @else
                    {{-- Balance Skeleton --}}
                    <div class="px-6 py-4 bg-gradient-to-br from-primary-500 to-primary-700 dark:from-primary-600 dark:to-primary-800 rounded-xl shadow-lg animate-pulse">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 bg-white/20 rounded-lg"></div>
                            <div class="space-y-2">
                                <div class="h-3 bg-white/30 rounded w-16"></div>
                                <div class="h-7 bg-white/30 rounded w-36"></div>
                                <div class="h-3 bg-white/20 rounded w-20"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if ($ready)
    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-[320px_1fr] 2xl:grid-cols-[384px_1fr] gap-6">
        {{-- Left Sidebar - Fixed Width --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('common.bank_accounts') }}</h2>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.select_account_to_view') }}</p>
                </div>
                <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus"
                    size="sm">
                    {{ __('common.create') }}
                </x-button>
            </div>

            {{-- Account Cards --}}
            @foreach ($this->accountsData as $account)
                <div wire:click="selectAccount({{ $account['id'] }})" wire:loading.class="opacity-60 scale-[0.98]"
                    wire:target="selectAccount({{ $account['id'] }})"
                    class="relative p-4 bg-white dark:bg-dark-700 border-2 border-zinc-200 dark:border-dark-600 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-md transform hover:scale-[1.02] {{ $selectedAccountId == $account['id'] ? 'border-primary-500 bg-primary-50 dark:bg-dark-700 ring-2 ring-primary-500/20' : '' }}">

                    {{-- Loading Overlay --}}
                    <div wire:loading wire:target="selectAccount({{ $account['id'] }})"
                        class="absolute inset-0 rounded-xl backdrop-blur-md bg-white/20 dark:bg-dark-900/20 z-10">
                        <div
                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 flex items-center gap-2 px-4 py-2 bg-white/80 dark:bg-dark-800/80 backdrop-blur-sm rounded-xl shadow-lg border border-white/30 dark:border-dark-600/30">
                            <x-icon name="arrow-path" class="w-4 h-4 text-primary-600 animate-spin" />
                            <span class="text-sm text-primary-600 dark:text-primary-400 font-medium">{{ __('common.loading') }}</span>
                        </div>
                    </div>

                    {{-- Card Content sama seperti sebelumnya --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-12 w-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shadow-sm">
                                <x-icon name="building-library" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-dark-900 dark:text-dark-50">{{ $account['name'] }}</h3>
                                <p class="text-sm text-dark-600 dark:text-dark-400">{{ $account['bank'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($selectedAccountId == $account['id'])
                                <div class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></div>
                            @endif
                            <x-icon
                                name="{{ $account['trend'] === 'up' ? 'arrow-trending-up' : 'arrow-trending-down' }}"
                                class="w-4 h-4 {{ $account['trend'] === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <p
                            class="text-2xl font-bold {{ $account['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($account['balance'], 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-dark-600 dark:text-dark-400">•••• •••• ••••
                            {{ substr($account['account_number'], -4) }}</p>
                    </div>

                    @if ($account['recent_transactions']->count() > 0)
                        <div class="space-y-2">
                            @foreach ($account['recent_transactions']->take(2) as $transaction)
                                <div class="flex items-center justify-between text-xs">
                                    <span
                                        class="text-dark-600 dark:text-dark-400 truncate flex-1">{{ Str::limit($transaction->description, 20) }}</span>
                                    <span
                                        class="{{ $transaction->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                                        {{ $transaction->transaction_type === 'credit' ? '+' : '-' }}{{ number_format($transaction->amount / 1000, 0) }}k
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if ($this->accountsData->count() === 0)
                <div class="text-center py-8">
                    <x-icon name="building-library" class="w-12 h-12 text-zinc-400 mx-auto mb-3" />
                    <p class="text-dark-600 dark:text-dark-400 mb-4">{{ __('pages.no_accounts_yet') }}</p>
                    <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus"
                        size="sm">
                        {{ __('common.create') }}
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Main Content - Constrained Width --}}
        <div class="min-w-0 space-y-6"> {{-- min-w-0 mencegah overflow --}}
            @if ($selectedAccountId)
                {{-- Quick Actions & Chart Component --}}
                <livewire:accounts.quick-actions-overview :selectedAccountId="$selectedAccountId" />

                {{-- Tab Navigation & Tables dengan Container --}}
                <div class="overflow-hidden"> {{-- Container untuk mencegah overflow --}}
                    <x-tab selected="transactions" scroll-on-mobile>
                        <x-tab.items tab="transactions" :title="__('common.transactions')">
                            <x-slot:left>
                                <x-icon name="arrows-right-left" class="w-4 h-4" />
                            </x-slot:left>

                            {{-- Transactions Table dengan overflow handling --}}
                            <div class="mt-3 overflow-x-auto">
                                <div class="min-w-full px-1">
                                    <livewire:transactions.listing :constrainedBankAccountId="$selectedAccountId" :key="'transactions-' . $selectedAccountId" />
                                </div>
                            </div>
                        </x-tab.items>

                        <x-tab.items tab="payments" :title="__('common.payments')">
                            <x-slot:left>
                                <x-icon name="banknotes" class="w-4 h-4" />
                            </x-slot:left>

                            {{-- Payments Table dengan overflow handling --}}
                            <div class="mt-3 overflow-x-auto">
                                <div class="min-w-full px-1">
                                    <livewire:payments.listing :constrainedBankAccountId="$selectedAccountId" :key="'payments-' . $selectedAccountId" />
                                </div>
                            </div>
                        </x-tab.items>
                    </x-tab>
                </div>
            @else
                {{-- No Account Selected --}}
                <div
                    class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-12 text-center">
                    <x-icon name="building-library" class="w-16 h-16 text-zinc-400 mx-auto mb-4" />
                    <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.select_account_to_view') }}</h3>
                    <p class="text-dark-600 dark:text-dark-400 mb-6">{{ __('pages.no_account_selected_message') }}</p>
                    <x-button wire:click="createAccount" loading="createAccount" color="primary" icon="plus">
                        {{ __('common.create') }}
                    </x-button>
                </div>
            @endif
        </div>
    </div>
    @else
    {{-- Loading Skeleton --}}
    <div class="grid grid-cols-1 xl:grid-cols-[320px_1fr] 2xl:grid-cols-[384px_1fr] gap-6 animate-pulse">
        {{-- Left Sidebar Skeleton --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <div class="h-6 bg-gray-200 dark:bg-dark-700 rounded w-36"></div>
                    <div class="h-4 bg-gray-200 dark:bg-dark-700 rounded w-48"></div>
                </div>
                <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-20"></div>
            </div>

            @foreach (range(1, 3) as $i)
                <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 bg-gray-200 dark:bg-dark-700 rounded-lg"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 dark:bg-dark-700 rounded w-28"></div>
                                <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-20"></div>
                            </div>
                        </div>
                        <div class="h-4 w-4 bg-gray-200 dark:bg-dark-700 rounded"></div>
                    </div>
                    <div class="mb-3 space-y-2">
                        <div class="h-7 bg-gray-200 dark:bg-dark-700 rounded w-40"></div>
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-32"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-24"></div>
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-12"></div>
                        </div>
                        <div class="flex justify-between">
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-20"></div>
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-14"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Right Content Skeleton --}}
        <div class="min-w-0 space-y-6">
            @include('livewire.placeholders.quick-actions-skeleton')
        </div>
    </div>
    @endif

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="map" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.account_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.account_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div x-data="{ tab: 'accounts' }" class="space-y-5">

            {{-- Tab Navigation --}}
            <div class="flex gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'accounts'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'accounts'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="building-library" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.account_guide_tab_accounts') }}</span>
                </button>
                <button
                    @click="tab = 'transactions'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'transactions'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrows-right-left" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.account_guide_tab_transactions') }}</span>
                </button>
                <button
                    @click="tab = 'analytics'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'analytics'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="chart-bar" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.account_guide_tab_analytics') }}</span>
                </button>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 1: REKENING --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'accounts'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">

                    {{-- Cara Kelola Rekening (timeline) --}}
                    <div class="relative">
                        <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>
                        <div class="space-y-4">
                            {{-- Step 1 --}}
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.account_guide_step1_title') }}</h4>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.account_guide_step1_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step1_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step1_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="cursor-arrow-rays" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.account_guide_step2_title') }}</h4>
                                            <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.account_guide_step2_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step2_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step2_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="chart-bar" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.account_guide_step3_title') }}</h4>
                                            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.account_guide_step3_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Cara Hitung Saldo --}}
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <x-icon name="calculator" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.account_guide_balance_title') }}</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mb-2">{{ __('pages.account_guide_balance_desc') }}</p>
                                <div class="bg-amber-100 dark:bg-amber-900/30 rounded-lg p-2.5 font-mono text-xs text-amber-800 dark:text-amber-200">
                                    {{ __('pages.account_guide_balance_formula') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tips --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="light-bulb" class="w-5 h-5 text-yellow-500 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.account_guide_tips_title') }}</h4>
                                <ul class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.account_guide_tip1') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.account_guide_tip2') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="exclamation-triangle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-red-500" />
                                        <span>{{ __('pages.account_guide_tip3') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 2: TRANSAKSI --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'transactions'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">

                    {{-- Tipe Transaksi --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.account_guide_txn_types_title') }}</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-4 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/40 rounded-xl">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="h-8 w-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <x-icon name="arrow-down-left" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    </div>
                                    <span class="text-sm font-semibold text-green-800 dark:text-green-200">{{ __('pages.account_guide_txn_credit') }}</span>
                                </div>
                                <p class="text-xs text-green-700 dark:text-green-300">{{ __('pages.account_guide_txn_credit_desc') }}</p>
                            </div>
                            <div class="p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/40 rounded-xl">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="h-8 w-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                        <x-icon name="arrow-up-right" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    </div>
                                    <span class="text-sm font-semibold text-red-800 dark:text-red-200">{{ __('pages.account_guide_txn_debit') }}</span>
                                </div>
                                <p class="text-xs text-red-700 dark:text-red-300">{{ __('pages.account_guide_txn_debit_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Cara Catat Transaksi --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="plus-circle" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.account_guide_add_txn_title') }}</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.account_guide_add_txn_desc') }}</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.account_guide_add_txn_tip1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                        <span>{{ __('pages.account_guide_add_txn_tip2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Transfer Antar Rekening --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="arrows-right-left" class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.account_guide_transfer_title') }}</h4>
                                <p class="text-xs text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.account_guide_transfer_desc') }}</p>
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg font-medium">{{ __('pages.account_guide_transfer_from') }}</span>
                                    <x-icon name="arrow-right" class="w-3.5 h-3.5 text-purple-400" />
                                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg font-medium">{{ __('pages.account_guide_transfer_to') }}</span>
                                    <span class="text-purple-500 dark:text-purple-400 ml-1">{{ __('pages.account_guide_transfer_auto') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pembayaran Invoice --}}
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="banknotes" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.account_guide_payments_title') }}</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ __('pages.account_guide_payments_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Kategori Transaksi --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="tag" class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.account_guide_category_title') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.account_guide_category_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- TAB 3: ANALITIK --}}
            {{-- ============================================ --}}
            <div x-show="tab === 'analytics'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">

                    {{-- Stats Cards Bulan Ini --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="calendar" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">{{ __('pages.account_guide_stats_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-green-700 dark:text-green-300">{{ __('pages.account_guide_stats_income') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.account_guide_stats_income_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-red-700 dark:text-red-300">{{ __('pages.account_guide_stats_expense') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.account_guide_stats_expense_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.account_guide_stats_net') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.account_guide_stats_net_desc') }}</p>
                                    </div>
                                    <div class="p-2.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                        <p class="text-xs font-semibold text-blue-800 dark:text-blue-200">{{ __('pages.account_guide_stats_count') }}</p>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ __('pages.account_guide_stats_count_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grafik 12 Bulan --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="chart-bar" class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.account_guide_chart_title') }}</h4>
                                <p class="text-xs text-purple-700 dark:text-purple-300">{{ __('pages.account_guide_chart_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Trend Indikator --}}
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                <x-icon name="arrow-trending-up" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-2">{{ __('pages.account_guide_trend_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-center gap-2 text-xs p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                                        <x-icon name="arrow-trending-up" class="w-3.5 h-3.5 flex-shrink-0" />
                                        {{ __('pages.account_guide_trend_up') }}
                                    </div>
                                    <div class="flex items-center gap-2 text-xs p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                                        <x-icon name="arrow-trending-down" class="w-3.5 h-3.5 flex-shrink-0" />
                                        {{ __('pages.account_guide_trend_down') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Ekspor Laporan --}}
                    <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                        <div class="flex items-start gap-3">
                            <x-icon name="document-arrow-down" class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.account_guide_export_title') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.account_guide_export_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Child Components --}}
    <livewire:accounts.create @account-created="refreshData" />
    <livewire:accounts.delete @account-deleted="refreshData" />
    <livewire:accounts.edit @account-updated="refreshData" />
    <livewire:transactions.create @transaction-created="refreshData" />
    <livewire:transactions.delete @transaction-deleted="refreshData" />
    <livewire:transactions.transfer @transfer-completed="refreshData" />
    <livewire:transactions.inline-category-create />
    <livewire:payments.delete @payment-deleted="refreshData" />
</div>
