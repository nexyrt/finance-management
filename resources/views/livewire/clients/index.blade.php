{{-- resources/views/livewire/clients/index.blade.php --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                    Manajemen Klien
                </h1>
                <p class="text-gray-600 dark:text-zinc-400 text-lg">
                    Kelola klien Anda dan lacak hubungan bisnis mereka
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button wire:click="$dispatch('create-client')" icon="plus" color="primary">
                    Tambah Klien
                </x-button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Klien</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $rows->total() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="users" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Klien Aktif</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ $rows->where('status', 'Active')->count() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-green-500/10 dark:bg-green-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Perusahaan</p>
                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $rows->where('type', 'company')->count() ?? 0 }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-purple-500/10 dark:bg-purple-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="building-office" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Individu</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ $rows->where('type', 'individual')->count() ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="user" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="py-5 grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Type Filter --}}
        <x-select.styled label="Tipe Klien" wire:model.live="typeFilter" :options="[
            ['label' => 'Individu', 'value' => 'individual'],
            ['label' => 'Perusahaan', 'value' => 'company'],
        ]" placeholder="Pilih tipe..."
            searchable />

        {{-- Status Filter --}}
        <x-select.styled label="Status" wire:model.live="statusFilter" :options="[
            ['label' => 'Aktif', 'value' => 'Active'],
            ['label' => 'Tidak Aktif', 'value' => 'Inactive'],
        ]"
            placeholder="Pilih status..." />

        {{-- Clear Filters --}}
        <div class="flex items-end">
            <x-button wire:click="clearFilters" color="secondary" icon="x-mark" class="w-full">
                Hapus Semua Filter
            </x-button>
        </div>
    </div>

    {{-- Main Table Card --}}
    <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable wire:model.live="selected">
        {{-- Client Name with Enhanced Avatar --}}
        @interact('column_name', $row)
            <div class="flex items-center space-x-4">
                <div class="relative">
                    @if ($row->logo)
                        <img class="h-12 w-12 rounded-2xl object-cover shadow-md" src="{{ $row->logo }}"
                            alt="{{ $row->name }}">
                    @else
                        <div
                            class="h-12 w-12 rounded-2xl flex items-center justify-center shadow-md
                                        {{ $row->type === 'individual'
                                            ? 'bg-gradient-to-br from-blue-400 to-blue-600'
                                            : 'bg-gradient-to-br from-purple-400 to-purple-600' }}">
                            <x-icon name="{{ $row->type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-6 h-6 text-white" />
                        </div>
                    @endif

                    {{-- Status indicator --}}
                    <div
                        class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white dark:border-gray-800 
                                    {{ $row->status === 'Active' ? 'bg-green-400' : 'bg-gray-400' }}">
                    </div>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                        {{ $row->name }}
                    </p>
                    @if ($row->NPWP)
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 truncate font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-md inline-block mt-1">
                            {{ $row->NPWP }}
                        </p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Enhanced Type Column --}}
        @interact('column_type', $row)
            <x-badge text="{{ $row->type === 'individual' ? '👤 Individu' : '🏢 Perusahaan' }}"
                color="{{ $row->type === 'individual' ? 'blue' : 'purple' }}" class="shadow-sm" />
        @endinteract

        {{-- Enhanced Contact Info --}}
        @interact('column_person_in_charge', $row)
            <div class="space-y-2">
                @if ($row->email)
                    <a href="mailto:{{ $row->email }}"
                        class="group flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                        <x-icon name="envelope" class="w-4 h-4" />
                        <span class="truncate group-hover:underline">{{ $row->email }}</span>
                    </a>
                @endif

                @if ($row->ar_phone_number)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <x-icon name="phone" class="w-4 h-4" />
                        <span class="truncate">{{ $row->ar_phone_number }}</span>
                    </div>
                @endif

                @if (!$row->email && !$row->ar_phone_number)
                    <span class="text-gray-400 dark:text-gray-500 italic text-sm">Tidak ada info kontak</span>
                @endif
            </div>
        @endinteract

        {{-- Enhanced Status Column --}}
        @interact('column_status', $row)
            <div class="flex items-center gap-2">
                <div class="h-2 w-2 rounded-full {{ $row->status === 'Active' ? 'bg-green-400' : 'bg-red-400' }}">
                </div>
                <x-badge text="{{ $row->status }}" color="{{ $row->status === 'Active' ? 'green' : 'red' }}"
                    class="shadow-sm" />
            </div>
        @endinteract

        {{-- Enhanced Invoices Count --}}
        @interact('column_invoices_count', $row)
            <div class="text-center">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full 
                                {{ $row->invoices_count > 0
                                    ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                    : 'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                    <x-icon name="document-text" class="w-4 h-4" />
                    <span class="font-medium">{{ $row->invoices_count }}</span>
                </div>
            </div>
        @endinteract

        {{-- Enhanced Financial Summary --}}
        @interact('column_financial_summary', $row)
            <div class="text-right space-y-1">
                @php
                    $totalAmount = $row->invoices->sum('total_amount');
                    $paidAmount = $row->invoices->filter(fn($inv) => $inv->status === 'paid')->sum('total_amount');
                    $outstandingAmount = $totalAmount - $paidAmount;
                @endphp

                <div class="font-semibold text-gray-900 dark:text-white">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </div>

                @if ($outstandingAmount > 0)
                    <div
                        class="inline-flex items-center gap-1 text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-full">
                        <x-icon name="exclamation-triangle" class="w-3 h-3" />
                        <span>Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</span>
                    </div>
                @elseif($totalAmount > 0)
                    <div
                        class="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded-full">
                        <x-icon name="check-circle" class="w-3 h-3" />
                        <span>Lunas</span>
                    </div>
                @endif
            </div>
        @endinteract

        {{-- Enhanced Actions --}}
        @interact('column_actions', $row)
            <x-dropdown icon="ellipsis-vertical" class="shadow-lg">
                <x-dropdown.items text="Lihat Detail" icon="eye"
                    wire:click="$dispatch('show-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="Edit Klien" icon="pencil"
                    wire:click="$dispatch('edit-client', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="Hubungan" icon="users"
                    wire:click="$dispatch('manage-relationships', { clientId: {{ $row->id }} })" />
                <x-dropdown.items text="Hapus" icon="trash"
                    wire:click="$dispatch('delete-client', { clientId: {{ $row->id }} })" />
            </x-dropdown>
        @endinteract

    </x-table>

    {{-- Modal components --}}
    <livewire:clients.edit />
    <livewire:clients.delete />
    <livewire:clients.show />
    <livewire:clients.create />
    <livewire:clients.relationship />
</div>
