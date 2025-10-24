<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                User Management
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Manage system users and their roles
            </p>
        </div>
        @can('manage users')
            <livewire:users.create @created="$refresh" />
        @endcan
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>

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
                    <div class="font-medium text-zinc-900 dark:text-white">{{ $row->name }}</div>
                    @if ($row->phone_number)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row->phone_number }}</div>
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
                <span class="text-zinc-400 italic">No role</span>
            @endif
        @endinteract

        {{-- Status --}}
        @interact('column_status', $row)
            <x-badge :color="$row->status === 'active' ? 'green' : 'red'" :text="ucfirst($row->status)" />
        @endinteract

        {{-- Date --}}
        @interact('column_created_at', $row)
            <div class="text-sm">
                <div class="text-zinc-900 dark:text-white">{{ $row->created_at?->format('d M Y') ?? '-' }}</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row->created_at?->diffForHumans() ?? '-' }}
                </div>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                @can('manage users')
                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="$dispatch('load::user', { user: '{{ $row->id }}' })" title="Edit" />
                    <livewire:users.delete :user="$row" :key="uniqid()" @deleted="$refresh" />
                @endcan
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions --}}
    @can('manage users')
        <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
            class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
            <div
                class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-600 px-4 sm:px-6 py-4 sm:min-w-96">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <div class="font-semibold text-zinc-900 dark:text-zinc-50"
                                x-text="`${show.length} users selected`"></div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Select actions for selected users</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <x-button wire:click="confirmBulkDelete" size="sm" color="red"
                            icon="trash">Delete</x-button>
                        <x-button wire:click="$set('selected', [])" size="sm" color="gray"
                            icon="x-mark">Cancel</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Edit Modal --}}
    <livewire:users.edit @updated="$refresh" />
</div>
