<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.reimbursements') }}
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                {{ __('pages.manage_expense_reimbursement_requests') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Workflow Guide Button --}}
            <button
                wire:click="$toggle('workflowGuideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.workflow_guide') }}
            </button>

            @can('create reimbursements')
                <livewire:reimbursements.create @created="$refresh" />
            @endcan
        </div>
    </div>

    {{-- Custom Tab Navigation --}}
    <div x-data="{ activeTab: $persist('my_requests').as('reimb_active_tab') }">

        {{-- Tab Bar --}}
        <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
            {{-- My Requests --}}
            <button
                @click="activeTab = 'my_requests'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="activeTab === 'my_requests'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
            >
                <x-icon name="user" class="w-4 h-4 shrink-0" />
                <span>{{ __('pages.my_requests') }}</span>
            </button>

            {{-- All Requests (Finance Only) --}}
            @can('approve reimbursements')
                <button
                    @click="activeTab = 'all_requests'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="activeTab === 'all_requests'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'"
                >
                    <x-icon name="users" class="w-4 h-4 shrink-0" />
                    <span>{{ __('pages.all_reimbursements') }}</span>
                </button>
            @endcan
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4">
            {{-- My Requests Panel --}}
            <div
                x-show="activeTab === 'my_requests'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
            >
                <livewire:reimbursements.my-requests />
            </div>

            {{-- All Requests Panel --}}
            @can('approve reimbursements')
                <div
                    x-show="activeTab === 'all_requests'"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                >
                    <livewire:reimbursements.all-requests />
                </div>
            @endcan
        </div>
    </div>

    {{-- Workflow Guide Modal --}}
    <x-modal wire="workflowGuideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="map" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.reimb_workflow_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.reimb_workflow_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-6">
            {{-- Workflow Steps Timeline --}}
            <div class="relative">
                {{-- Connecting line --}}
                <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 to-green-300 dark:from-blue-700 dark:via-purple-700 dark:to-green-700 hidden sm:block"></div>

                <div class="space-y-4">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="document-plus" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.reimb_step1_title') }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">{{ __('pages.reimb_step1_desc') }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                            <span>{{ __('pages.reimb_step1_tip1') }}</span>
                                        </div>
                                        <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                            <span>{{ __('pages.reimb_step1_tip2') }}</span>
                                        </div>
                                    </div>
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
                            <div class="flex items-start gap-3">
                                <x-icon name="clipboard-document-check" class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.reimb_step2_title') }}</h4>
                                    <p class="text-sm text-purple-700 dark:text-purple-300 mb-3">{{ __('pages.reimb_step2_desc') }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-center gap-2 text-xs px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 shrink-0" />
                                            {{ __('pages.reimb_step2_approved_title') }}
                                        </div>
                                        <div class="flex items-center gap-2 text-xs px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                                            <x-icon name="x-circle" class="w-3.5 h-3.5 shrink-0" />
                                            {{ __('pages.reimb_step2_rejected_title') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                            <span class="text-white font-bold text-sm">3</span>
                        </div>
                        <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="banknotes" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.reimb_step3_title') }}</h4>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.reimb_step3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Status Legend --}}
            <div class="border-t border-secondary-200 dark:border-dark-600 pt-5">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.reimb_status_legend_title') }}</h4>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                    @foreach ([
                        ['label' => 'Draft', 'color' => 'gray', 'desc' => __('pages.reimb_status_editable')],
                        ['label' => 'Pending', 'color' => 'yellow', 'desc' => __('pages.reimb_status_in_review')],
                        ['label' => 'Approved', 'color' => 'blue', 'desc' => __('pages.reimb_status_awaiting_payment')],
                        ['label' => 'Rejected', 'color' => 'red', 'desc' => __('pages.reimb_status_needs_revision')],
                        ['label' => 'Paid', 'color' => 'green', 'desc' => __('pages.reimb_status_completed')],
                    ] as $status)
                        <div class="flex flex-col gap-1 p-2 rounded-lg bg-gray-50 dark:bg-dark-700">
                            <x-badge :text="$status['label']" :color="$status['color']" />
                            <span class="text-xs text-dark-500 dark:text-dark-400">{{ $status['desc'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Role Access --}}
            <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                <div class="flex items-start gap-3">
                    <x-icon name="shield-check" class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.reimb_role_access_title') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-dark-900 dark:text-dark-50">Staff:</span>
                                <span class="text-dark-500 dark:text-dark-400">{{ __('pages.reimb_role_staff') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-dark-900 dark:text-dark-50">Finance:</span>
                                <span class="text-dark-500 dark:text-dark-400">{{ __('pages.reimb_role_finance') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-dark-900 dark:text-dark-50">Admin:</span>
                                <span class="text-dark-500 dark:text-dark-400">{{ __('pages.reimb_role_admin') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('workflowGuideModal')" color="primary" icon="check">
                    {{ __('pages.reimb_got_it_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Child Components --}}
    <livewire:reimbursements.show />
    <livewire:reimbursements.update />

    @can('approve reimbursements')
        <livewire:reimbursements.review />
    @endcan

    @can('pay reimbursements')
        <livewire:reimbursements.payment />
    @endcan
</div>
