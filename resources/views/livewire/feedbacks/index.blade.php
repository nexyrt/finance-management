<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-dark-900 dark:text-white">Feedback & Bug Reports</h1>
            <p class="text-dark-500 dark:text-dark-400 mt-1">
                @if ($this->canManageFeedbacks())
                    Kelola feedback dari semua pengguna
                @else
                    Lihat dan kelola feedback Anda
                @endif
            </p>
        </div>
        @can('create feedbacks')
            <x-button wire:click="$dispatch('open-feedback-form')" color="primary" icon="plus">
                Kirim Feedback Baru
            </x-button>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
        {{-- Total --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-gray-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                    <x-icon name="inbox-stack" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-dark-900 dark:text-white">{{ $this->stats['total'] }}</p>
                    <p class="text-xs text-dark-500">Total</p>
                </div>
            </div>
        </div>

        {{-- Open --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->stats['open'] }}</p>
                    <p class="text-xs text-dark-500">Open</p>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="arrow-path" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->stats['in_progress'] }}</p>
                    <p class="text-xs text-dark-500">In Progress</p>
                </div>
            </div>
        </div>

        {{-- Resolved --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $this->stats['resolved'] }}</p>
                    <p class="text-xs text-dark-500">Resolved</p>
                </div>
            </div>
        </div>

        {{-- Bugs --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="bug-ant" class="w-5 h-5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $this->stats['bugs'] }}</p>
                    <p class="text-xs text-dark-500">Bugs</p>
                </div>
            </div>
        </div>

        {{-- Features --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <x-icon name="light-bulb" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $this->stats['features'] }}</p>
                    <p class="text-xs text-dark-500">Features</p>
                </div>
            </div>
        </div>

        {{-- Feedbacks --}}
        <div class="bg-white dark:bg-dark-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-dark-700">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-gray-100 dark:bg-dark-700 rounded-lg flex items-center justify-center">
                    <x-icon name="chat-bubble-left-right" class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $this->stats['feedbacks'] }}</p>
                    <p class="text-xs text-dark-500">Saran</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <x-tab selected="my-feedbacks">
        <x-tab.items tab="my-feedbacks">
            <x-slot:left>
                <x-icon name="user" class="w-4 h-4" />
            </x-slot:left>
            <livewire:feedbacks.my-feedbacks />
        </x-tab.items>

        @can('manage feedbacks')
            <x-tab.items tab="all-feedbacks">
                <x-slot:left>
                    <x-icon name="users" class="w-4 h-4" />
                </x-slot:left>
                @if ($this->stats['open'] > 0)
                    <x-slot:right>
                        <x-badge :text="$this->stats['open']" color="yellow" />
                    </x-slot:right>
                @endif
                <livewire:feedbacks.all-feedbacks />
            </x-tab.items>
        @endcan
    </x-tab>

    {{-- Modals --}}
    <livewire:feedbacks.create />
    <livewire:feedbacks.show />
    <livewire:feedbacks.update />
    <livewire:feedbacks.delete />
    @can('respond feedbacks')
        <livewire:feedbacks.respond />
    @endcan
</div>
