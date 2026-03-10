<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('common.feedbacks') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                @if ($this->canManageFeedbacks())
                    {{ __('feedback.page_subtitle_admin') }}
                @else
                    {{ __('feedback.page_subtitle_user') }}
                @endif
            </p>
        </div>

        @can('create feedbacks')
            <x-button @click="$dispatch('open-feedback-form', { pageUrl: window.location.href })" color="primary" size="sm">
                <x-slot:left>
                    <x-icon name="paper-airplane" class="w-4 h-4" />
                </x-slot:left>
                {{ __('feedback.send_new_feedback') }}
            </x-button>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-7 gap-4">

        <x-card class="hover:shadow-lg transition-shadow col-span-2 sm:col-span-2 xl:col-span-1">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="inbox-stack" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('common.total') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['total'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="clock" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Open</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['open'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="arrow-path" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.stat_in_progress') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['in_progress'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.stat_resolved') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['resolved'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="bug-ant" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.stat_bugs') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['bugs'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="light-bulb" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.stat_features') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['features'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="chat-bubble-left-right" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.stat_suggestions') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['feedbacks'] }}</p>
                </div>
            </div>
        </x-card>

    </div>

    {{-- Custom Tab Navigation --}}
    <div x-data="{ activeTab: $persist('my_feedbacks').as('feedbacks_active_tab') }">

        {{-- Tab Bar (pill/segment style) --}}
        <div class="inline-flex items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
            <button
                @click="activeTab = 'my_feedbacks'"
                class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="activeTab === 'my_feedbacks'
                    ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'">
                <x-icon name="user" class="w-4 h-4 shrink-0" />
                <span>{{ __('feedback.tab_my_feedbacks') }}</span>
            </button>

            @can('manage feedbacks')
                <button
                    @click="activeTab = 'all_feedbacks'"
                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                    :class="activeTab === 'all_feedbacks'
                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600'">
                    <x-icon name="users" class="w-4 h-4 shrink-0" />
                    <span>{{ __('feedback.tab_all_feedbacks') }}</span>
                    @if ($this->stats['open'] > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full">{{ $this->stats['open'] }}</span>
                    @endif
                </button>
            @endcan
        </div>

        {{-- Tab Panels --}}
        <div class="mt-4">
            <div
                x-show="activeTab === 'my_feedbacks'"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0">
                <livewire:feedbacks.my-feedbacks />
            </div>

            @can('manage feedbacks')
                <div
                    x-show="activeTab === 'all_feedbacks'"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <livewire:feedbacks.all-feedbacks />
                </div>
            @endcan
        </div>

    </div>

    {{-- Modals --}}
    <livewire:feedbacks.create />
    <livewire:feedbacks.show />
    <livewire:feedbacks.update />
    <livewire:feedbacks.delete />
    @can('respond feedbacks')
        <livewire:feedbacks.respond />
    @endcan

</div>
