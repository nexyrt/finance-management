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

        <div class="flex items-center gap-2">
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.fr_guide_btn') }}
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

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="4xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="banknotes" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.fr_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.fr_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div x-data="{ tab: 'workflow' }" class="space-y-5">
            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <button
                    @click="tab = 'workflow'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'workflow'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="arrow-path" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.fr_guide_tab_workflow') }}</span>
                </button>
                <button
                    @click="tab = 'status'"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 flex-1 justify-center"
                    :class="tab === 'status'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200'"
                >
                    <x-icon name="tag" class="w-3.5 h-3.5 flex-shrink-0" />
                    <span>{{ __('pages.fr_guide_tab_status') }}</span>
                </button>
            </div>

            {{-- TAB 1: ALUR KERJA --}}
            <div x-show="tab === 'workflow'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="relative">
                    <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 via-amber-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:via-amber-700 dark:to-emerald-700 hidden sm:block"></div>
                    <div class="space-y-4">
                        {{-- Step 1 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                                <span class="text-white font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="document-plus" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.fr_guide_step1_title') }}</h4>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">{{ __('pages.fr_guide_step1_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step1_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step1_tip2') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step1_tip3') }}</span>
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
                                    <x-icon name="paper-airplane" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.fr_guide_step2_title') }}</h4>
                                        <p class="text-sm text-purple-700 dark:text-purple-300 mb-2">{{ __('pages.fr_guide_step2_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step2_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step2_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center shadow-lg shadow-amber-200 dark:shadow-amber-900/40 z-10">
                                <span class="text-white font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="shield-check" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-amber-900 dark:text-amber-200 mb-1">{{ __('pages.fr_guide_step3_title') }}</h4>
                                        <p class="text-sm text-amber-700 dark:text-amber-300 mb-2">{{ __('pages.fr_guide_step3_desc') }}</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div class="flex items-start gap-2 text-xs text-amber-600 dark:text-amber-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step3_tip1') }}</span>
                                            </div>
                                            <div class="flex items-start gap-2 text-xs text-amber-600 dark:text-amber-400">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                                <span>{{ __('pages.fr_guide_step3_tip2') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                                <span class="text-white font-bold text-sm">4</span>
                            </div>
                            <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                                <div class="flex items-start gap-3">
                                    <x-icon name="banknotes" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.fr_guide_step4_title') }}</h4>
                                        <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.fr_guide_step4_desc') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Number Format Info --}}
                <div class="mt-4 p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                    <div class="flex items-start gap-3">
                        <x-icon name="hashtag" class="w-5 h-5 text-gray-500 dark:text-gray-400 flex-shrink-0 mt-0.5" />
                        <div>
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.fr_guide_number_title') }}</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mb-2">{{ __('pages.fr_guide_number_desc') }}</p>
                            <code class="text-xs bg-gray-200 dark:bg-dark-600 text-dark-700 dark:text-dark-200 px-2 py-1 rounded-lg font-mono">001/KSN/I/2026</code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB 2: STATUS & FITUR --}}
            <div x-show="tab === 'status'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="space-y-5">
                    {{-- Status Legend --}}
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.fr_guide_status_title') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach ([
                                ['status' => 'Draft', 'color' => 'gray', 'icon' => 'pencil', 'title' => __('pages.fr_guide_status_draft_title'), 'desc' => __('pages.fr_guide_status_draft_desc')],
                                ['status' => 'Pending', 'color' => 'yellow', 'icon' => 'clock', 'title' => __('pages.fr_guide_status_pending_title'), 'desc' => __('pages.fr_guide_status_pending_desc')],
                                ['status' => 'Approved', 'color' => 'blue', 'icon' => 'check', 'title' => __('pages.fr_guide_status_approved_title'), 'desc' => __('pages.fr_guide_status_approved_desc')],
                                ['status' => 'Rejected', 'color' => 'red', 'icon' => 'x-mark', 'title' => __('pages.fr_guide_status_rejected_title'), 'desc' => __('pages.fr_guide_status_rejected_desc')],
                                ['status' => 'Disbursed', 'color' => 'green', 'icon' => 'check-badge', 'title' => __('pages.fr_guide_status_disbursed_title'), 'desc' => __('pages.fr_guide_status_disbursed_desc')],
                            ] as $s)
                                <div class="flex items-start gap-3 p-3 rounded-xl border
                                    @if($s['color'] === 'gray') bg-gray-50 dark:bg-gray-900/10 border-gray-200 dark:border-gray-900/40
                                    @elseif($s['color'] === 'yellow') bg-yellow-50 dark:bg-yellow-900/10 border-yellow-200 dark:border-yellow-900/40
                                    @elseif($s['color'] === 'blue') bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-900/40
                                    @elseif($s['color'] === 'red') bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-900/40
                                    @elseif($s['color'] === 'green') bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-900/40
                                    @endif">
                                    <x-badge :text="$s['status']" :color="$s['color']" />
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ $s['title'] }}</p>
                                        <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $s['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Role Access --}}
                    <div class="border-t border-secondary-200 dark:border-dark-600 pt-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.fr_guide_role_title') }}</h4>
                        <div class="space-y-2">
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl">
                                <div class="h-7 w-7 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-icon name="user" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fr_guide_role_staff') }}</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.fr_guide_role_staff_desc') }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl">
                                <div class="h-7 w-7 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <x-icon name="users" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fr_guide_role_finance') }}</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('pages.fr_guide_role_finance_desc') }}</p>
                                </div>
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
