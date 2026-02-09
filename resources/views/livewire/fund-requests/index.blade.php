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
    @php
        // Map tab identifiers to translated names
        $tabMap = [
            'my_requests' => __('pages.my_fund_requests'),
            'all_requests' => __('pages.all_fund_requests'),
        ];
        $currentSelectedTab = $tabMap[$activeTab] ?? $activeTab;
    @endphp

    <x-tab :selected="$currentSelectedTab">
        {{-- My Requests Tab (Everyone) --}}
        <x-tab.items :tab="__('pages.my_fund_requests')">
            <x-slot:right>
                <x-badge text="{{ $this->stats['my_total'] }}" color="primary" size="sm" />
            </x-slot:right>
            <livewire:fund-requests.my-requests :key="'my-requests-tab'" />
        </x-tab.items>

        {{-- All Requests Tab (Manager/Admin only) --}}
        @can('approve fund requests')
            <x-tab.items :tab="__('pages.all_fund_requests')">
                <x-slot:right>
                    <x-badge text="{{ $this->stats['all_total'] }}" color="secondary" size="sm" />
                </x-slot:right>
                <livewire:fund-requests.all-requests :key="'all-requests-tab'" />
            </x-tab.items>
        @endcan
    </x-tab>

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
