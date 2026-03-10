{{-- resources/views/livewire/accounts/index.blade.php --}}
<div wire:init="loadData" class="space-y-6">

    {{-- ============================================================ --}}
    {{-- HEADER                                                       --}}
    {{-- ============================================================ --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('common.bank_accounts') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_all_bank_accounts') }}
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Guide Button --}}
            <button wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#1e1e1e] text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all">
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.client_guide_btn') }}
            </button>

            {{-- Add Account Button --}}
            <x-button wire:click="createAccount" loading="createAccount" color="primary" size="sm" icon="plus">
                {{ __('common.create') }}
            </x-button>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT                                                 --}}
    {{-- ============================================================ --}}
    @if ($ready)
        {{-- Mobile: Horizontal scroll account switcher --}}
        <div class="lg:hidden">
            @if ($this->accountsData->count() > 0)
                <div class="overflow-x-auto pb-2 -mx-1 px-1">
                    <div class="flex gap-3 min-w-max">
                        @foreach ($this->accountsData as $account)
                            <button wire:click="selectAccount({{ $account['id'] }})"
                                wire:loading.attr="disabled"
                                class="shrink-0 w-52 p-3 rounded-xl border-2 transition-all text-left
                                    {{ $selectedAccountId == $account['id']
                                        ? 'border-primary-400 dark:border-primary-600 bg-primary-50 dark:bg-primary-900/20'
                                        : 'border-secondary-200 dark:border-white/10 bg-white dark:bg-[#1e1e1e] hover:border-primary-300 dark:hover:border-primary-700' }}">
                                <div class="flex items-center gap-2.5 mb-2">
                                    <div class="h-8 w-8 bg-linear-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center shrink-0">
                                        <x-icon name="building-library" class="w-4 h-4 text-white" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-sm text-dark-900 dark:text-dark-50 truncate">{{ $account['name'] }}</div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ $account['bank'] }}</div>
                                    </div>
                                </div>
                                <div class="text-sm font-bold text-dark-900 dark:text-dark-50">
                                    Rp {{ number_format($account['balance'], 0, ',', '.') }}
                                </div>
                            </button>
                        @endforeach

                        {{-- Add New (mobile) --}}
                        <button wire:click="createAccount"
                            class="shrink-0 w-32 p-3 rounded-xl border-2 border-dashed border-zinc-300 dark:border-white/10 hover:border-primary-400 dark:hover:border-primary-500 flex flex-col items-center justify-center gap-2 transition-colors">
                            <x-icon name="plus" class="w-5 h-5 text-dark-400 dark:text-dark-500" />
                            <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.add_new_account') }}</span>
                        </button>
                    </div>
                </div>

                {{-- Mobile: Summary stats compact --}}
                <div class="grid grid-cols-3 gap-2 mt-3">
                    <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-secondary-200 dark:border-white/10 p-3 text-center">
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.total_balance') }}</p>
                        <p class="text-sm font-bold text-dark-900 dark:text-dark-50">Rp {{ number_format($this->totalBalance, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-secondary-200 dark:border-white/10 p-3 text-center">
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.income') }}</p>
                        <p class="text-sm font-bold text-green-600 dark:text-green-400">Rp {{ number_format($this->monthlySummary['income'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-secondary-200 dark:border-white/10 p-3 text-center">
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.expense') }}</p>
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($this->monthlySummary['expense'], 0, ',', '.') }}</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Master-Detail Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ======================================================== --}}
            {{-- LEFT SIDEBAR (Desktop only — sticky)                     --}}
            {{-- ======================================================== --}}
            <div class="hidden lg:block lg:col-span-3">
                <div class="lg:sticky lg:top-6 space-y-4">

                    {{-- Account List Card --}}
                    <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-secondary-200 dark:border-white/10 overflow-hidden">
                        <div class="px-4 py-3 border-b border-secondary-200 dark:border-white/10">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50 flex items-center gap-2">
                                    <x-icon name="building-library" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                    {{ __('pages.accounts') }}
                                </h3>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ $this->accountsData->count() }}</span>
                            </div>
                        </div>

                        <div class="p-2 space-y-1 max-h-[calc(100vh-28rem)] overflow-y-auto">
                            @foreach ($this->accountsData as $account)
                                <button wire:click="selectAccount({{ $account['id'] }})"
                                    wire:loading.attr="disabled"
                                    class="w-full text-left px-3 py-3 rounded-xl transition-all duration-150
                                        {{ $selectedAccountId == $account['id']
                                            ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-300 dark:border-primary-700'
                                            : 'hover:bg-gray-50 dark:hover:bg-dark-700 border border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 bg-linear-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shrink-0">
                                            <x-icon name="building-library" class="w-4 h-4 text-white" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="font-semibold text-sm text-dark-900 dark:text-dark-50 truncate">
                                                {{ $account['name'] }}
                                            </div>
                                            <div class="text-xs text-dark-500 dark:text-dark-400">
                                                {{ $account['bank'] }}
                                            </div>
                                        </div>
                                        {{-- Trend indicator --}}
                                        <div class="shrink-0">
                                            @if ($account['trend'] === 'up')
                                                <x-icon name="arrow-trending-up" class="w-4 h-4 text-green-500" />
                                            @else
                                                <x-icon name="arrow-trending-down" class="w-4 h-4 text-red-500" />
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-1.5 pl-12">
                                        <span class="text-sm font-bold {{ $account['balance'] >= 0 ? 'text-dark-900 dark:text-dark-50' : 'text-red-600 dark:text-red-400' }}">
                                            Rp {{ number_format($account['balance'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                </button>
                            @endforeach

                            {{-- Add New Account --}}
                            <button wire:click="createAccount"
                                class="w-full flex items-center justify-center gap-2 px-3 py-3 rounded-xl border-2 border-dashed border-zinc-300 dark:border-white/10 hover:border-primary-400 dark:hover:border-primary-500 text-dark-400 dark:text-dark-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                <x-icon name="plus" class="w-4 h-4" />
                                <span class="text-xs font-medium">{{ __('pages.add_new_account') }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Monthly Summary Stats --}}
                    <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-secondary-200 dark:border-white/10 p-4 space-y-3">
                        <h4 class="text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wider">
                            {{ __('pages.monthly_summary') }}
                        </h4>

                        {{-- Total Balance --}}
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_balance') }}</span>
                            <span class="text-sm font-bold text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($this->totalBalance, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="h-px bg-secondary-200 dark:bg-[#161618]"></div>

                        {{-- Monthly Income --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.income') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                Rp {{ number_format($this->monthlySummary['income'], 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Monthly Expense --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                <span class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.expense') }}</span>
                            </div>
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                Rp {{ number_format($this->monthlySummary['expense'], 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Net --}}
                        @php $netTotal = $this->monthlySummary['income'] - $this->monthlySummary['expense']; @endphp
                        <div class="h-px bg-secondary-200 dark:bg-[#161618]"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-dark-600 dark:text-dark-400">{{ __('pages.net_flow') }}</span>
                            <span class="text-sm font-bold {{ $netTotal >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-orange-600 dark:text-orange-400' }}">
                                {{ $netTotal >= 0 ? '+' : '' }}Rp {{ number_format($netTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- RIGHT PANEL                                              --}}
            {{-- ======================================================== --}}
            <div class="lg:col-span-9 space-y-6">
                @if ($selectedAccountId)
                    @php $selectedAccount = $this->accountsData->firstWhere('id', $selectedAccountId); @endphp

                    {{-- Selected Account Header + Actions --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 bg-linear-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="building-library" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-dark-900 dark:text-dark-50">
                                    {{ $selectedAccount['name'] }}
                                </h2>
                                <p class="text-sm text-dark-500 dark:text-dark-400">
                                    {{ $selectedAccount['bank'] }} &middot; {{ $selectedAccount['account_number'] }}
                                </p>
                            </div>
                            <div class="ml-2">
                                @if ($selectedAccount['trend'] === 'up')
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-lg">
                                        <x-icon name="arrow-trending-up" class="w-3.5 h-3.5" />
                                        {{ __('pages.trending_up') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-lg">
                                        <x-icon name="arrow-trending-down" class="w-3.5 h-3.5" />
                                        {{ __('pages.trending_down') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <livewire:transactions.create-expense :selectedAccountId="$selectedAccountId" @transaction-created="refreshData" />
                            <livewire:transactions.create-income :selectedAccountId="$selectedAccountId" @transaction-created="refreshData" />

                            <x-dropdown position="bottom-end">
                                <x-slot:trigger>
                                    <x-button color="secondary" outline icon="ellipsis-vertical" size="sm" />
                                </x-slot:trigger>
                                <x-dropdown.items text="{{ __('common.edit') }}" icon="pencil"
                                    wire:click="editAccount({{ $selectedAccountId }})" />
                                <x-dropdown.items text="{{ __('pages.export_report') }}" icon="document-arrow-down"
                                    wire:click="exportReport" />
                                <x-dropdown.items text="{{ __('common.delete') }}" icon="trash"
                                    wire:click="deleteAccount({{ $selectedAccountId }})"
                                    separator class="text-red-600 dark:text-red-400" />
                            </x-dropdown>
                        </div>
                    </div>

                    {{-- Charts Section (QuickActionsOverview) --}}
                    <livewire:accounts.quick-actions-overview
                        :selectedAccountId="$selectedAccountId" />

                    {{-- Custom Tabs: Transactions | Payments --}}
                    <div x-data="{ activeTab: $persist('transactions').as('ba_active_tab') }">
                        {{-- Tab Bar --}}
                        <div class="flex items-center gap-4 mb-4">
                            <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-[#27272a] rounded-xl border border-zinc-200 dark:border-white/10">
                                <button @click="activeTab = 'transactions'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    :class="activeTab === 'transactions'
                                        ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'">
                                    <x-icon name="arrows-right-left" class="w-4 h-4 shrink-0" />
                                    <span>{{ __('common.transactions') }}</span>
                                </button>
                                <button @click="activeTab = 'payments'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                                    :class="activeTab === 'payments'
                                        ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'">
                                    <x-icon name="banknotes" class="w-4 h-4 shrink-0" />
                                    <span>{{ __('common.payments') }}</span>
                                </button>
                            </div>

                            {{-- Context subtitle --}}
                            <div class="hidden sm:flex items-center gap-3 flex-1 min-w-0">
                                <div class="h-px flex-1 bg-linear-to-r from-zinc-200 dark:from-dark-600 to-transparent"></div>
                                <p x-show="activeTab === 'transactions'" x-transition.opacity
                                    class="text-xs text-dark-400 dark:text-dark-500 whitespace-nowrap">
                                    {{ __('pages.ba_tab_transactions_hint') }}
                                </p>
                                <p x-show="activeTab === 'payments'" x-transition.opacity
                                    class="text-xs text-dark-400 dark:text-dark-500 whitespace-nowrap">
                                    {{ __('pages.ba_tab_payments_hint') }}
                                </p>
                            </div>
                        </div>

                        {{-- Tab Panels --}}
                        <div x-show="activeTab === 'transactions'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <livewire:accounts.transaction-list
                                :selectedAccountId="$selectedAccountId" />
                        </div>

                        <div x-show="activeTab === 'payments'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <livewire:accounts.payment-list
                                :selectedAccountId="$selectedAccountId" />
                        </div>
                    </div>

                @elseif ($this->accountsData->count() > 0)
                    {{-- No Account Selected --}}
                    <div class="bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl flex items-center justify-center min-h-[400px]">
                        <div class="text-center p-8">
                            <div class="h-16 w-16 bg-gray-100 dark:bg-[#27272a] rounded-full flex items-center justify-center mx-auto mb-4">
                                <x-icon name="cursor-arrow-rays" class="w-8 h-8 text-gray-400 dark:text-dark-500" />
                            </div>
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                                {{ __('pages.no_account_selected') }}
                            </h3>
                            <p class="text-dark-500 dark:text-dark-400">
                                {{ __('pages.no_account_selected_message') }}
                            </p>
                        </div>
                    </div>

                @else
                    {{-- Empty State: No Accounts --}}
                    <div class="bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-12 text-center">
                        <x-icon name="building-library" class="w-16 h-16 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
                        <h3 class="text-xl font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.no_accounts_yet') }}</h3>
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
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-pulse">
            {{-- Sidebar Skeleton --}}
            <div class="hidden lg:block lg:col-span-3 space-y-4">
                <div class="bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-4 space-y-3">
                    @foreach (range(1, 3) as $i)
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 bg-gray-200 dark:bg-[#27272a] rounded-xl shrink-0"></div>
                            <div class="flex-1 space-y-1.5">
                                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-24"></div>
                                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-16"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-4 space-y-3">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-28"></div>
                    <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded w-36"></div>
                    <div class="h-4 bg-gray-200 dark:bg-[#27272a] rounded w-32"></div>
                    <div class="h-4 bg-gray-200 dark:bg-[#27272a] rounded w-32"></div>
                </div>
            </div>

            {{-- Main Panel Skeleton --}}
            <div class="lg:col-span-9 space-y-6">
                {{-- Header skeleton --}}
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
                    <div class="space-y-2">
                        <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded w-40"></div>
                        <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-56"></div>
                    </div>
                </div>

                {{-- Stats skeleton --}}
                <div class="grid grid-cols-3 gap-3">
                    @foreach (range(1, 3) as $i)
                        <div class="h-16 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
                    @endforeach
                </div>

                {{-- Chart skeleton --}}
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    <div class="lg:col-span-3 bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-5">
                        <div class="h-4 bg-gray-200 dark:bg-[#27272a] rounded w-36 mb-4"></div>
                        <div class="h-[260px] bg-gray-100 dark:bg-[#27272a] rounded-xl"></div>
                    </div>
                    <div class="lg:col-span-2 bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-5">
                        <div class="h-4 bg-gray-200 dark:bg-[#27272a] rounded w-28 mb-4"></div>
                        <div class="h-[160px] bg-gray-100 dark:bg-[#27272a] rounded-xl"></div>
                    </div>
                </div>

                {{-- Table skeleton --}}
                <div class="bg-white dark:bg-[#1e1e1e] border border-secondary-200 dark:border-white/10 rounded-xl p-4 space-y-3">
                    @foreach (range(1, 5) as $i)
                        <div class="h-10 bg-gray-100 dark:bg-[#27272a] rounded-lg"></div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- WORKFLOW GUIDE MODAL                                         --}}
    {{-- ============================================================ --}}
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
            <div class="flex gap-1 p-1 bg-zinc-100 dark:bg-[#27272a] rounded-xl border border-zinc-200 dark:border-white/10">
                <button
                    @click="tab = 'accounts'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'accounts'
                        ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="building-library" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.account_guide_tab_accounts') }}</span>
                </button>
                <button
                    @click="tab = 'transactions'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'transactions'
                        ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrows-right-left" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.account_guide_tab_transactions') }}</span>
                </button>
                <button
                    @click="tab = 'analytics'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'analytics'
                        ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="chart-bar" class="w-3.5 h-3.5 shrink-0" />
                    <span>{{ __('pages.account_guide_tab_analytics') }}</span>
                </button>
            </div>

            {{-- TAB 1: REKENING --}}
            <div x-show="tab === 'accounts'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
                    <div class="relative">
                        <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>
                        <div class="space-y-4">
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.account_guide_step1_title') }}</h4>
                                            <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.account_guide_step1_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step1_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step1_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="cursor-arrow-rays" class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.account_guide_step2_title') }}</h4>
                                            <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.account_guide_step2_desc') }}</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step2_tip1') }}</span>
                                                </div>
                                                <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                    <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                                    <span>{{ __('pages.account_guide_step2_tip2') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <x-icon name="chart-bar" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.account_guide_step3_title') }}</h4>
                                            <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.account_guide_step3_desc') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <x-icon name="calculator" class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.account_guide_balance_title') }}</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-300 mb-2">{{ __('pages.account_guide_balance_desc') }}</p>
                                <div class="bg-amber-100 dark:bg-amber-900/30 rounded-lg p-2.5 font-mono text-xs text-amber-800 dark:text-amber-200">
                                    {{ __('pages.account_guide_balance_formula') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-[#27272a] rounded-xl border border-gray-200 dark:border-white/10">
                        <div class="flex items-start gap-3">
                            <x-icon name="light-bulb" class="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0 mt-0.5" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.account_guide_tips_title') }}</h4>
                                <ul class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.account_guide_tip1') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-green-500" />
                                        <span>{{ __('pages.account_guide_tip2') }}</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <x-icon name="exclamation-triangle" class="w-3.5 h-3.5 shrink-0 mt-0.5 text-red-500" />
                                        <span>{{ __('pages.account_guide_tip3') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: TRANSAKSI --}}
            <div x-show="tab === 'transactions'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
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
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="plus-circle" class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.account_guide_add_txn_title') }}</h4>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.account_guide_add_txn_desc') }}</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                        <span>{{ __('pages.account_guide_add_txn_tip1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                        <span>{{ __('pages.account_guide_add_txn_tip2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
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
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="banknotes" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.account_guide_payments_title') }}</h4>
                                <p class="text-xs text-emerald-700 dark:text-emerald-300">{{ __('pages.account_guide_payments_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-[#27272a] rounded-xl border border-gray-200 dark:border-white/10">
                        <div class="flex items-start gap-3">
                            <x-icon name="tag" class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
                            <div>
                                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.account_guide_category_title') }}</h4>
                                <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.account_guide_category_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 3: ANALITIK --}}
            <div x-show="tab === 'analytics'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-4">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
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
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="chart-bar" class="w-4.5 h-4.5 text-purple-600 dark:text-purple-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.account_guide_chart_title') }}</h4>
                                <p class="text-xs text-purple-700 dark:text-purple-300">{{ __('pages.account_guide_chart_desc') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                        <div class="flex items-start gap-3">
                            <div class="h-9 w-9 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center shrink-0">
                                <x-icon name="arrow-trending-up" class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-emerald-900 dark:text-emerald-200 mb-2">{{ __('pages.account_guide_trend_title') }}</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="flex items-center gap-2 text-xs p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                                        <x-icon name="arrow-trending-up" class="w-3.5 h-3.5 shrink-0" />
                                        {{ __('pages.account_guide_trend_up') }}
                                    </div>
                                    <div class="flex items-center gap-2 text-xs p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                                        <x-icon name="arrow-trending-down" class="w-3.5 h-3.5 shrink-0" />
                                        {{ __('pages.account_guide_trend_down') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-[#27272a] rounded-xl border border-gray-200 dark:border-white/10">
                        <div class="flex items-start gap-3">
                            <x-icon name="document-arrow-down" class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
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

    {{-- ============================================================ --}}
    {{-- CHILD COMPONENTS                                             --}}
    {{-- ============================================================ --}}
    <livewire:accounts.create @account-created="refreshData" />
    <livewire:accounts.delete @account-deleted="refreshData" />
    <livewire:accounts.edit @account-updated="refreshData" />
    <livewire:transactions.create @transaction-created="refreshData" />
    <livewire:transactions.delete @transaction-deleted="refreshData" />
    <livewire:transactions.transfer @transfer-completed="refreshData" />
    <livewire:transactions.inline-category-create />
    <livewire:payments.delete @payment-deleted="refreshData" />
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    function registerBankAccountCharts() {
        if (typeof Alpine === 'undefined') return;

        // Always re-register on each call (handles SPA navigated)
        Alpine.data('bankAccountCharts', (chartType, initialData) => ({
            chart: null,
            data: initialData,

            isDark() { return document.documentElement.classList.contains('dark'); },
            textColor() { return this.isDark() ? '#9ca3af' : '#6b7280'; },
            gridColor() { return this.isDark() ? '#374151' : '#f3f4f6'; },

            formatRp(value) {
                if (Math.abs(value) >= 1000000000) return 'Rp ' + (value / 1000000000).toFixed(1) + 'B';
                if (Math.abs(value) >= 1000000) return 'Rp ' + (value / 1000000).toFixed(0) + 'Jt';
                if (Math.abs(value) >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            },

            render() {
                if (typeof Chart === 'undefined') return;
                if (chartType === 'incomeExpense') this.renderBarChart();
                if (chartType === 'categoryBreakdown') this.renderDonutChart();
            },

            init() {
                const self = this;

                // Chart.js is loaded via script tag above, just render
                this.$nextTick(() => self.render());

                // Listen for data updates from Livewire
                Livewire.on('account-charts-updated', (payload) => {
                    const newData = payload[0];
                    if (chartType === 'incomeExpense' && newData.incomeExpense) {
                        self.data = newData.incomeExpense;
                        self.renderBarChart();
                    }
                    if (chartType === 'categoryBreakdown' && newData.categoryBreakdown) {
                        self.data = newData.categoryBreakdown;
                        self.renderDonutChart();
                    }
                });

                // Listen for PDF download
                Livewire.on('download-pdf', (event) => {
                    window.open(event.url, '_blank');
                });

                // Dark mode observer — re-render chart on theme change
                this._themeObserver = new MutationObserver(() => {
                    if (self.chart) {
                        setTimeout(() => self.render(), 50);
                    }
                });
                this._themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            },

            destroyChart() {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
            },

            renderBarChart() {
                this.destroyChart();
                if (!this.data || this.data.length === 0 || !this.$refs.canvas) return;

                const isDark = this.isDark();

                this.chart = new Chart(this.$refs.canvas, {
                    type: 'bar',
                    data: {
                        labels: this.data.map(item => item.month),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: this.data.map(item => item.income),
                                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                borderColor: 'rgba(34, 197, 94, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: this.data.map(item => item.expense),
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderColor: 'rgba(239, 68, 68, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            tooltip: {
                                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                titleColor: isDark ? '#f3f4f6' : '#111827',
                                bodyColor: isDark ? '#d1d5db' : '#374151',
                                borderColor: isDark ? '#374151' : '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (ctx) => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                                }
                            },
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { color: this.gridColor(), drawBorder: false },
                                ticks: { color: this.textColor(), font: { size: 10 } }
                            },
                            y: {
                                grid: { color: this.gridColor(), drawBorder: false },
                                ticks: {
                                    color: this.textColor(),
                                    font: { size: 10 },
                                    callback: (value) => this.formatRp(value)
                                }
                            }
                        }
                    }
                });
            },

            renderDonutChart() {
                this.destroyChart();
                if (!this.data || this.data.length === 0 || !this.$refs.canvas) return;

                const isDark = this.isDark();
                const colors = ['#8b5cf6', '#06b6d4', '#f59e0b', '#ef4444', '#10b981', '#6366f1'];

                this.chart = new Chart(this.$refs.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: this.data.map(item => item.name),
                        datasets: [{
                            data: this.data.map(item => item.total),
                            backgroundColor: colors.slice(0, this.data.length),
                            borderColor: isDark ? '#27272a' : '#ffffff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: isDark ? '#1f2937' : '#ffffff',
                                titleColor: isDark ? '#f3f4f6' : '#111827',
                                bodyColor: isDark ? '#d1d5db' : '#374151',
                                borderColor: isDark ? '#374151' : '#e5e7eb',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: (ctx) => {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                        return ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed) + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            },

            destroy() {
                this.destroyChart();
                if (this._themeObserver) {
                    this._themeObserver.disconnect();
                }
            }
        }));
    }

    // Register immediately since Alpine is already loaded at this point
    registerBankAccountCharts();

    // Re-register after Livewire SPA navigation (wire:navigate)
    document.addEventListener('livewire:navigated', () => {
        registerBankAccountCharts();
    });
})();
</script>
@endpush
