<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.fund_request_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_fund_requests_description') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            {{-- Guide Button --}}
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-[#1e1e1e] text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.fund_guide_btn') }}
            </button>

            @can('create fund requests')
                <x-button color="primary" size="sm" wire:click="$dispatch('create::fund-request')">
                    <x-slot:left>
                        <x-icon name="plus" class="w-4 h-4" />
                    </x-slot:left>
                    {{ __('pages.create_fund_request') }}
                </x-button>
            @endcan
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total Requested --}}
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
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
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center shrink-0">
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
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center shrink-0">
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
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center shrink-0">
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
                <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-[#27272a] rounded-xl border border-zinc-200 dark:border-white/10">
                    {{-- My Requests Tab --}}
                    <button
                        @click="activeTab = 'my_requests'"
                        :class="activeTab === 'my_requests'
                            ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    >
                        <x-icon name="user" class="w-4 h-4 shrink-0" />
                        <span>{{ __('pages.my_fund_requests') }}</span>
                        <span
                            :class="activeTab === 'my_requests'
                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300'
                                : 'bg-zinc-200 dark:bg-[#161618] text-dark-500 dark:text-dark-400'"
                            class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold transition-colors duration-200"
                        >{{ $this->stats['my_total'] }}</span>
                    </button>

                    {{-- All Requests Tab (Manager/Admin only) --}}
                    @can('approve fund requests')
                        <button
                            @click="activeTab = 'all_requests'"
                            :class="activeTab === 'all_requests'
                                ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10'
                                : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                        >
                            <x-icon name="users" class="w-4 h-4 shrink-0" />
                            <span>{{ __('pages.all_fund_requests') }}</span>
                            <span
                                :class="activeTab === 'all_requests'
                                    ? 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300'
                                    : 'bg-zinc-200 dark:bg-[#161618] text-dark-500 dark:text-dark-400'"
                                class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full text-xs font-semibold transition-colors duration-200"
                            >{{ $this->stats['all_total'] }}</span>
                        </button>
                    @endcan
                </div>

                {{-- Divider gradient line --}}
                <div class="w-48 h-px bg-linear-to-r from-transparent via-zinc-300 dark:via-dark-600 to-transparent"></div>
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

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="information-circle" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.fund_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        {{-- Tab Navigation --}}
        <div x-data="{ tab: 'flow' }" class="space-y-5">
            <div class="flex gap-1 p-1 bg-zinc-100 dark:bg-[#27272a] rounded-xl border border-zinc-200 dark:border-white/10">
                <button @click="tab = 'flow'" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center" :class="tab === 'flow' ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10' : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'">
                    <x-icon name="arrow-right-circle" class="w-3.5 h-3.5" />
                    {{ __('pages.fund_guide_tab_flow') }}
                </button>
                <button @click="tab = 'status'" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center" :class="tab === 'status' ? 'bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-white/10' : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'">
                    <x-icon name="tag" class="w-3.5 h-3.5" />
                    {{ __('pages.fund_guide_tab_status') }}
                </button>
            </div>

            {{-- Tab 1: Alur Pengajuan --}}
            <div x-show="tab === 'flow'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative">
                    <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block"></div>
                    <div class="space-y-4">

                        {{-- Step 1 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                <span class="text-white font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="document-plus" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_step1_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-3">{{ __('pages.fund_guide_step1_desc') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex items-start gap-1.5 text-xs text-blue-700 dark:text-blue-300">
                                        <span class="mt-0.5">•</span><span>{{ __('pages.fund_guide_step1_tip1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-xs text-blue-700 dark:text-blue-300">
                                        <span class="mt-0.5">•</span><span>{{ __('pages.fund_guide_step1_tip2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                                <span class="text-white font-bold text-sm">2</span>
                            </div>
                            <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="paper-airplane" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_step2_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-3">{{ __('pages.fund_guide_step2_desc') }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex items-start gap-1.5 text-xs text-purple-700 dark:text-purple-300">
                                        <span class="mt-0.5">•</span><span>{{ __('pages.fund_guide_step2_tip1') }}</span>
                                    </div>
                                    <div class="flex items-start gap-1.5 text-xs text-purple-700 dark:text-purple-300">
                                        <span class="mt-0.5">•</span><span>{{ __('pages.fund_guide_step2_tip2') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-amber-900/40 z-10">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="clipboard-document-check" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_step3_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-3">{{ __('pages.fund_guide_step3_desc') }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">{{ __('pages.fund_guide_step3_approved') }}</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">{{ __('pages.fund_guide_step3_rejected') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 z-10">
                                <span class="text-white font-bold text-sm">4</span>
                            </div>
                            <div class="flex-1 bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-200 dark:border-indigo-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="banknotes" class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_step4_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400 mb-2">{{ __('pages.fund_guide_step4_desc') }}</p>
                                <div class="flex items-start gap-1.5 text-xs text-indigo-700 dark:text-indigo-300">
                                    <span class="mt-0.5">•</span><span>{{ __('pages.fund_guide_step4_tip') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="flex gap-4">
                            <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                <span class="text-white font-bold text-sm">5</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <x-icon name="check-badge" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_guide_step5_title') }}</h4>
                                </div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.fund_guide_step5_desc') }}</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Tab 2: Status & Peran --}}
            <div x-show="tab === 'status'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-5">

                    {{-- Status Cards --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.fund_guide_status_title') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">Draft</span>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_status_draft') }}</span>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">Pending</span>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_status_pending') }}</span>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">Approved</span>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_status_approved') }}</span>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Rejected</span>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_status_rejected') }}</span>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">Disbursed</span>
                                <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_status_disbursed') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Role Access --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.fund_guide_role_title') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                                <div class="text-xs font-semibold text-blue-700 dark:text-blue-300 mb-1">Staff</div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.fund_guide_role_staff') }}</p>
                            </div>
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                                <div class="text-xs font-semibold text-purple-700 dark:text-purple-300 mb-1">Finance Manager</div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.fund_guide_role_finance') }}</p>
                            </div>
                            <div class="p-3 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl">
                                <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-1">Admin</div>
                                <p class="text-xs text-dark-600 dark:text-dark-400">{{ __('pages.fund_guide_role_admin') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.fund_guide_priority_title') }}</h4>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300">Urgent</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300">High</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">Medium</span>
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">Low</span>
                        </div>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_guide_priority_note') }}</p>
                    </div>

                    {{-- Number Format --}}
                    <div class="p-3 bg-zinc-50 dark:bg-[#27272a] border border-zinc-200 dark:border-white/10 rounded-xl">
                        <h4 class="text-xs font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.fund_guide_number_title') }}</h4>
                        <code class="text-xs text-indigo-600 dark:text-indigo-400 font-mono">001/KSN/I/2026</code>
                        <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">{{ __('pages.fund_guide_number_desc') }}</p>
                    </div>

                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.fund_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
