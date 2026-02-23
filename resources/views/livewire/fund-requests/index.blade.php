<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.fund_request_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_fund_requests_description') }}
            </p>
        </div>

        @can('create fund requests')
            <x-button color="primary" size="sm" wire:click="$dispatch('create::fund-request')">
                <x-slot:left>
                    <x-icon name="plus" class="w-4 h-4" />
                </x-slot:left>
                {{ __('pages.create_fund_request') }}
            </x-button>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Requested --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_requested_amount') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($this->stats['total_requested'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Pending Amount --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.pending_approval_amount') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($this->stats['pending_amount'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Total Disbursed --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_disbursed_amount') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        Rp {{ number_format($this->stats['total_disbursed'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>

        {{-- Urgent Requests --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.urgent_requests') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $this->stats['all_urgent'] }}
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Tab Navigation --}}
    <div x-data="{ activeTab: @entangle('activeTab').live }">
        {{-- Centered Tab Bar --}}
        <div class="flex justify-center mb-6">
            <div class="flex flex-col items-center gap-3">
                <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                    {{-- My Requests Tab --}}
                    <button
                        @click="activeTab = 'my_requests'"
                        :class="activeTab === 'my_requests'
                            ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    >
                        <x-icon name="user" class="w-4 h-4 flex-shrink-0" />
                        <span>{{ __('pages.my_fund_requests') }}</span>
                        <span
                            :class="activeTab === 'my_requests'
                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300'
                                : 'bg-zinc-200 dark:bg-dark-600 text-dark-500 dark:text-dark-400'"
                            class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold transition-colors duration-200"
                        >{{ $this->stats['my_total'] }}</span>
                    </button>

                    {{-- All Requests Tab (Manager/Admin only) --}}
                    @can('approve fund requests')
                        <button
                            @click="activeTab = 'all_requests'"
                            :class="activeTab === 'all_requests'
                                ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                                : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                        >
                            <x-icon name="users" class="w-4 h-4 flex-shrink-0" />
                            <span>{{ __('pages.all_fund_requests') }}</span>
                            <span
                                :class="activeTab === 'all_requests'
                                    ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300'
                                    : 'bg-zinc-200 dark:bg-dark-600 text-dark-500 dark:text-dark-400'"
                                class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold transition-colors duration-200"
                            >{{ $this->stats['all_total'] }}</span>
                        </button>
                    @endcan
                </div>

                {{-- Divider gradient line --}}
                <div class="w-48 h-px bg-gradient-to-r from-transparent via-zinc-300 dark:via-dark-600 to-transparent"></div>
            </div>
        </div>

        {{-- Tab Panels --}}
        <div
            x-show="activeTab === 'my_requests'"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
        >
            <livewire:fund-requests.my-requests :key="'my-requests-tab'" />
        </div>

        @can('approve fund requests')
            <div
                x-show="activeTab === 'all_requests'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
            >
                <livewire:fund-requests.all-requests :key="'all-requests-tab'" />
            </div>
        @endcan
    </div>

    {{-- Modals --}}
    <livewire:fund-requests.create />
    <livewire:fund-requests.edit />
    <livewire:fund-requests.delete />
    <livewire:fund-requests.show />

    @can('approve fund requests')
        <livewire:fund-requests.review />
    @endcan

    @can('disburse fund requests')
        <livewire:fund-requests.disburse />
    @endcan
</div>
