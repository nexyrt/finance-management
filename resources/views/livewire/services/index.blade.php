{{-- resources/views/livewire/services/index.blade.php --}}

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Manajemen Layanan
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Kelola dan atur semua layanan yang ditawarkan.
            </p>
        </div>
        <livewire:services.create @service-created="$refresh" :key="'create-service'" />
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="squares-2x2" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Layanan</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $stats['total_services'] }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Rata-rata Harga</p>
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">
                        @if ($stats['avg_price'])
                            Rp {{ number_format($stats['avg_price'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="star" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Harga Tertinggi</p>
                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">
                        @if ($stats['highest_price'])
                            Rp {{ number_format($stats['highest_price'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="rectangle-group" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Kategori Terbanyak</p>
                    <p class="text-sm font-bold text-orange-600 dark:text-orange-400">
                        @if ($stats['by_type']->isNotEmpty())
                            {{ $stats['by_type']->keys()->first() }}
                            <span class="text-xs">({{ $stats['by_type']->first() }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                <x-select.styled wire:model.live="typeFilter" :options="[
                    ['label' => 'Perizinan', 'value' => 'Perizinan'],
                    ['label' => 'Administrasi Perpajakan', 'value' => 'Administrasi Perpajakan'],
                    ['label' => 'Digital Marketing', 'value' => 'Digital Marketing'],
                    ['label' => 'Sistem Digital', 'value' => 'Sistem Digital'],
                ]" placeholder="Semua kategori..." />
            </div>
        </div>

        <div class="flex gap-2">
            @if ($typeFilter)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    Clear
                </x-button>
            @endif
        </div>
    </div>

    {{-- Services Table --}}
    <x-table :$headers :$sort :rows="$this->services" selectable wire:model="selected" paginate filter loading>

        {{-- Service Name Column --}}
        @interact('column_name', $row)
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-xl flex items-center justify-center">
                    <x-icon name="cog-6-tooth" class="w-5 h-5 text-white" />
                </div>
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-zinc-50">{{ $row->name }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">ID: {{ $row->id }}</p>
                </div>
            </div>
        @endinteract

        {{-- Type Column --}}
        @interact('column_type', $row)
            <x-badge :text="$row->type" :color="match ($row->type) {
                'Perizinan' => 'blue',
                'Administrasi Perpajakan' => 'green',
                'Digital Marketing' => 'purple',
                'Sistem Digital' => 'orange',
                default => 'gray',
            }" />
        @endinteract

        {{-- Price Column --}}
        @interact('column_price', $row)
            <div class="text-right">
                <p class="font-bold text-lg text-zinc-900 dark:text-zinc-50">
                    Rp {{ number_format($row->price, 0, ',', '.') }}
                </p>
            </div>
        @endinteract

        {{-- Created At Column --}}
        @interact('column_created_at', $row)
            <div>
                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-50">
                    {{ $row->created_at->format('d M Y') }}
                </p>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $row->created_at->diffForHumans() }}
                </p>
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="pencil" color="green" size="sm" wire:click="edit({{ $row->id }})"
                    title="Edit" />

                <livewire:services.delete :service="$row" :key="'delete-service-' . $row->id" @service-deleted="$refresh" />
            </div>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} layanan dipilih`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            Pilih aksi untuk layanan yang dipilih
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash" loading="bulkDelete"
                        class="whitespace-nowrap">
                        Hapus
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Child Components --}}
    <livewire:services.edit @service-updated="$refresh" />
</div>
