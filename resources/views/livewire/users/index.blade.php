<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.user_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.user_management_desc') }}
            </p>
        </div>
        @can('manage users')
            <livewire:users.create @created="$refresh" />
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.user_stat_total') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['total'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.user_stat_active') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['active'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="shield-exclamation" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.user_stat_admins') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['admins'] }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="briefcase" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.user_stat_finance_managers') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">{{ $this->stats['finance_managers'] }}</p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filter & Search --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="w-full sm:w-64">
                <x-input wire:model.live.debounce.300ms="search"
                         :placeholder="__('common.search_placeholder')"
                         icon="magnifying-glass"
                         class="h-8" />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span class="hidden sm:inline">{{ __('common.showing') }} </span>{{ $this->rows->count() }}
                <span class="hidden sm:inline">{{ __('common.of') }} {{ $this->rows->total() }}</span>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate loading>

        {{-- Name with avatar --}}
        @interact('column_name', $row)
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-sm">
                        {{ $row->initials() }}
                    </span>
                </div>
                <div>
                    <div class="font-medium text-dark-900 dark:text-white">{{ $row->name }}</div>
                    @if ($row->phone_number)
                        <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->phone_number }}</div>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Role badge --}}
        @interact('column_role', $row)
            @php
                $role = $row->roles->first();
            @endphp
            @if ($role)
                <x-badge :color="match ($role->name) {
                    'admin' => 'red',
                    'finance manager' => 'blue',
                    'staff' => 'green',
                    default => 'gray',
                }" :text="ucfirst($role->name)" />
            @else
                <span class="text-dark-400 dark:text-dark-500 italic">{{ __('pages.user_no_role') }}</span>
            @endif
        @endinteract

        {{-- Status --}}
        @interact('column_status', $row)
            <x-badge
                :color="$row->status === 'active' ? 'green' : 'red'"
                :text="$row->status === 'active' ? __('pages.user_status_active') : __('pages.user_status_inactive')" />
        @endinteract

        {{-- Date --}}
        @interact('column_created_at', $row)
            <div class="text-sm">
                <div class="text-dark-900 dark:text-white">{{ $row->created_at?->format('d M Y') ?? '-' }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->created_at?->diffForHumans() ?? '-' }}</div>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                @can('manage users')
                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="$dispatch('load::user', { user: '{{ $row->id }}' })"
                        :title="__('common.edit')" />
                    <livewire:users.delete :user="$row" :key="uniqid()" @deleted="$refresh" />
                @endcan
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions --}}
    @can('manage users')
        <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
            class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-4 min-w-80">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50"
                                x-text="`${show.length} {{ __('common.selected') }}`"></div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.user_select_actions_hint') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash">
                            {{ __('common.delete') }}
                        </x-button>
                        <x-button wire:click="$set('selected', [])" size="sm" color="zinc" icon="x-mark">
                            {{ __('common.cancel') }}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Edit Modal --}}
    <livewire:users.edit @updated="$refresh" />
</div>
