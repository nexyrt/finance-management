<section class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                    Manajemen Invoice
                </h1>
                <p class="text-gray-600 dark:text-zinc-400 text-lg">
                    Kelola invoice, pembayaran, dan buat invoice baru
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button color="secondary" icon="document-arrow-down" outline>
                    Export
                </x-button>
                <x-button color="primary" icon="plus">
                    Buat Invoice Baru
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Invoice</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ number_format($stats['total_invoices']) }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500/10 dark:bg-blue-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Outstanding</p>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">Rp
                        {{ number_format($stats['outstanding_amount'], 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 bg-red-500/10 dark:bg-red-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
            </div>
        </div>

        <div
            class="bg-white/80 dark:bg-white/5 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-white/10 shadow-lg shadow-gray-500/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Paid This Month</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">Rp
                        {{ number_format($stats['paid_this_month'], 0, ',', '.') }}</p>
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
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Overdue</p>
                    <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['overdue_count'] }}</p>
                </div>
                <div
                    class="h-12 w-12 bg-orange-500/10 dark:bg-orange-400/10 rounded-xl flex items-center justify-center">
                    <x-icon name="clock" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabs Content --}}
    <x-tab wire:model.live="activeTab">

        {{-- Tab 1: Invoice Management --}}
        <x-tab.items tab="invoices">
            <x-slot:left>
                <x-icon name="document-text" class="w-5 h-5" />
            </x-slot:left>
            <x-slot:right>
                <x-badge text="{{ $stats['total_invoices'] }}" color="blue" />
            </x-slot:right>

            {{-- Filters Section --}}
            <div
                class="bg-gradient-to-r from-white/90 via-white/95 to-white/90 dark:from-zinc-800/90 dark:via-zinc-800/95 dark:to-zinc-800/90 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-lg shadow-zinc-500/5 mb-8">
                <div
                    class="flex items-center justify-between p-6 pb-4 border-b border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="flex items-center space-x-3">
                        <div
                            class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <x-icon name="funnel" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Filter Invoice</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Gunakan filter untuk mempersempit
                                pencarian</p>
                        </div>
                    </div>

                    {{-- Active Filters Count --}}
                    @if ($statusFilter || $clientFilter)
                        <div class="flex items-center space-x-2">
                            <div
                                class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full text-sm font-medium">
                                {{ collect([$statusFilter, $clientFilter])->filter()->count() }} Filter Aktif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        {{-- Status Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                            <x-select.styled wire:model.live="statusFilter" :options="[
                                ['label' => '📄 Draft', 'value' => 'draft'],
                                ['label' => '📤 Terkirim', 'value' => 'sent'],
                                ['label' => '✅ Dibayar', 'value' => 'paid'],
                                ['label' => '💰 Sebagian', 'value' => 'partially_paid'],
                                ['label' => '⏰ Terlambat', 'value' => 'overdue'],
                            ]"
                                placeholder="Semua status..." class="w-full" />
                        </div>

                        {{-- Client Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Klien</label>
                            <x-select.styled wire:model.live="clientFilter" :options="$clients
                                ->map(
                                    fn($client) => [
                                        'label' => $client->name,
                                        'value' => $client->id,
                                    ],
                                )
                                ->toArray()"
                                placeholder="Semua klien..." searchable class="w-full" />
                        </div>

                        {{-- Clear Filters --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Reset</label>
                            <x-button wire:click="clearFilters" color="secondary" icon="x-mark" class="w-full">
                                Hapus Filter
                            </x-button>
                        </div>
                    </div>

                    {{-- Active Filter Tags --}}
                    @if ($statusFilter || $clientFilter)
                        <div class="mt-6 pt-4 border-t border-zinc-200/50 dark:border-zinc-700/50">
                            <div class="flex items-center space-x-2 mb-3">
                                <x-icon name="tag" class="w-4 h-4 text-zinc-500 dark:text-zinc-400" />
                                <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Filter Aktif:</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @if ($statusFilter)
                                    <div
                                        class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                                        <span>{{ ucfirst($statusFilter) }}</span>
                                        <button wire:click="$set('statusFilter', '')"
                                            class="hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-colors">
                                            <x-icon name="x-mark" class="w-3 h-3" />
                                        </button>
                                    </div>
                                @endif
                                @if ($clientFilter)
                                    @php $selectedClient = $clients->find($clientFilter); @endphp
                                    @if ($selectedClient)
                                        <div
                                            class="inline-flex items-center gap-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 px-3 py-1.5 rounded-lg border border-green-200 dark:border-green-800 text-sm">
                                            <span>{{ $selectedClient->name }}</span>
                                            <button wire:click="$set('clientFilter', '')"
                                                class="hover:bg-green-200 dark:hover:bg-green-800 rounded-full p-0.5 transition-colors">
                                                <x-icon name="x-mark" class="w-3 h-3" />
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Invoice Table --}}
            <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable wire:model="selected">

                {{-- Invoice Number Column --}}
                @interact('column_invoice_number', $row)
                    <div class="group cursor-pointer">
                        <div
                            class="font-mono font-bold text-blue-600 dark:text-blue-400 group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-200">
                            {{ $row->invoice_number }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $row->issue_date->format('d/m/Y') }}
                        </div>
                    </div>
                @endinteract

                {{-- Client Column --}}
                @interact('column_client_name', $row)
                    <div class="flex items-center space-x-4">
                        <div class="relative flex-shrink-0">
                            <div
                                class="w-10 h-10 {{ $row->client_type === 'individual'
                                    ? 'bg-gradient-to-br from-blue-400 to-blue-600'
                                    : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                    rounded-2xl flex items-center justify-center shadow-lg ring-2 ring-white dark:ring-gray-800">
                                <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-5 h-5 text-white" />
                            </div>
                            {{-- Client Type Indicator --}}
                            <div
                                class="absolute -bottom-1 -right-1 w-4 h-4 {{ $row->client_type === 'individual' ? 'bg-blue-500' : 'bg-purple-500' }} rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center">
                                <x-icon
                                    name="{{ $row->client_type === 'individual' ? 'identification' : 'building-office-2' }}"
                                    class="w-2 h-2 text-white" />
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 dark:text-white truncate text-sm">
                                {{ $row->client_name }}
                            </p>
                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                                    class="w-3 h-3" />
                                <span
                                    class="capitalize">{{ $row->client_type === 'individual' ? 'Individu' : 'Perusahaan' }}</span>
                            </div>
                        </div>
                    </div>
                @endinteract

                {{-- Issue Date Column --}}
                @interact('column_issue_date', $row)
                    <div class="space-y-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $row->issue_date->format('d M Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <x-icon name="calendar" class="w-3 h-3" />
                            {{ $row->issue_date->diffForHumans() }}
                        </div>
                    </div>
                @endinteract

                {{-- Due Date Column --}}
                @interact('column_due_date', $row)
                    <div class="space-y-1">
                        @php
                            $isOverdue = $row->due_date->isPast() && !in_array($row->status, ['paid']);
                            $isDueSoon =
                                $row->due_date->diffInDays(now()) <= 7 &&
                                !$row->due_date->isPast() &&
                                !in_array($row->status, ['paid']);
                        @endphp

                        <div class="flex items-center gap-2">
                            @if ($isOverdue)
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            @elseif($isDueSoon)
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                            @else
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            @endif

                            <span
                                class="text-sm font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isDueSoon ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white') }}">
                                {{ $row->due_date->format('d M Y') }}
                            </span>
                        </div>

                        @if ($isOverdue)
                            <div
                                class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-md text-xs font-medium">
                                <x-icon name="exclamation-triangle" class="w-3 h-3" />
                                {{ $row->due_date->diffInDays(now()) }} hari lewat
                            </div>
                        @elseif($isDueSoon)
                            <div
                                class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 rounded-md text-xs font-medium">
                                <x-icon name="clock" class="w-3 h-3" />
                                {{ $row->due_date->diffInDays(now()) }} hari lagi
                            </div>
                        @else
                            <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                <x-icon name="check-circle" class="w-3 h-3" />
                                {{ $row->due_date->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                @endinteract

                {{-- Amount Column --}}
                @interact('column_total_amount', $row)
                    <div class="text-right space-y-2">
                        <div class="font-bold text-lg text-gray-900 dark:text-white">
                            Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                        </div>

                        @if ($row->amount_paid > 0)
                            @php
                                $paymentPercentage = ($row->amount_paid / $row->total_amount) * 100;
                                $remainingAmount = $row->total_amount - $row->amount_paid;
                            @endphp

                            {{-- Payment Progress Bar --}}
                            <div class="space-y-1">
                                <div class="flex justify-between text-xs">
                                    <span class="text-green-600 dark:text-green-400 font-medium">
                                        {{ number_format($paymentPercentage, 1) }}% Dibayar
                                    </span>
                                    @if ($remainingAmount > 0)
                                        <span class="text-gray-500 dark:text-gray-400">
                                            Sisa: Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ min($paymentPercentage, 100) }}%"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-gray-500 dark:text-gray-400 italic">
                                Belum ada pembayaran
                            </div>
                        @endif
                    </div>
                @endinteract

                {{-- Status Column --}}
                @interact('column_status', $row)
                    @php
                        $statusConfig = [
                            'draft' => [
                                'color' => 'gray',
                                'text' => 'Draft',
                                'icon' => 'document',
                                'bg' => 'bg-gray-50 dark:bg-gray-800',
                                'ring' => 'ring-gray-200 dark:ring-gray-700',
                            ],
                            'sent' => [
                                'color' => 'blue',
                                'text' => 'Terkirim',
                                'icon' => 'paper-airplane',
                                'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                                'ring' => 'ring-blue-200 dark:ring-blue-800',
                            ],
                            'paid' => [
                                'color' => 'green',
                                'text' => 'Dibayar',
                                'icon' => 'check-circle',
                                'bg' => 'bg-green-50 dark:bg-green-900/20',
                                'ring' => 'ring-green-200 dark:ring-green-800',
                            ],
                            'partially_paid' => [
                                'color' => 'yellow',
                                'text' => 'Sebagian',
                                'icon' => 'currency-dollar',
                                'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                                'ring' => 'ring-yellow-200 dark:ring-yellow-800',
                            ],
                            'overdue' => [
                                'color' => 'red',
                                'text' => 'Terlambat',
                                'icon' => 'exclamation-triangle',
                                'bg' => 'bg-red-50 dark:bg-red-900/20',
                                'ring' => 'ring-red-200 dark:ring-red-800',
                            ],
                        ];
                        $config = $statusConfig[$row->status] ?? $statusConfig['draft'];
                    @endphp

                    <div
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl {{ $config['bg'] }} ring-1 {{ $config['ring'] }} transition-all duration-200 hover:shadow-md">
                        <div
                            class="w-2 h-2 bg-{{ $config['color'] }}-500 rounded-full {{ $row->status === 'overdue' ? 'animate-pulse' : '' }}">
                        </div>
                        <x-icon name="{{ $config['icon'] }}"
                            class="w-4 h-4 text-{{ $config['color'] }}-600 dark:text-{{ $config['color'] }}-400" />
                        <span
                            class="text-sm font-medium text-{{ $config['color'] }}-700 dark:text-{{ $config['color'] }}-300">
                            {{ $config['text'] }}
                        </span>
                    </div>
                @endinteract

                {{-- Actions Column --}}
                @interact('column_actions', $row)
                    <div class="flex items-center gap-2">
                        {{-- Quick View Button --}}
                        <x-button size="sm" color="secondary" outline icon="eye"
                            class="opacity-70 hover:opacity-100 transition-opacity">
                        </x-button>

                        {{-- Main Actions Dropdown --}}
                        <x-dropdown icon="ellipsis-vertical" class="relative">
                            {{-- Header with Invoice Number --}}
                            <div
                                class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                                <div class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $row->invoice_number }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $row->client_name }}
                                </div>
                            </div>

                            {{-- Primary Actions --}}
                            <div class="py-1">
                                <x-dropdown.items text="Lihat Detail" icon="eye"
                                    wire:click="$dispatch('show-invoice', { invoiceId: {{ $row->id }} })" />

                                @if ($row->status === 'draft')
                                    <x-dropdown.items text="Edit Invoice" icon="pencil"
                                        class="text-blue-600 dark:text-blue-400" />
                                    <x-dropdown.items text="Kirim Invoice" icon="paper-airplane"
                                        class="text-green-600 dark:text-green-400" />
                                @endif

                                @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                                    <x-dropdown.items text="Catat Pembayaran" icon="currency-dollar"
                                        class="text-green-600 dark:text-green-400" />
                                @endif
                            </div>

                            {{-- Secondary Actions --}}
                            <div class="border-t border-gray-100 dark:border-gray-700 py-1">
                                <x-dropdown.items text="Print PDF" icon="printer"
                                    class="text-gray-600 dark:text-gray-400" />
                                <x-dropdown.items text="Duplikasi" icon="document-duplicate"
                                    class="text-gray-600 dark:text-gray-400" />
                                <x-dropdown.items text="Export" icon="arrow-down-tray"
                                    class="text-gray-600 dark:text-gray-400" />
                            </div>

                            {{-- Danger Actions --}}
                            @if ($row->status === 'draft')
                                <div class="border-t border-gray-100 dark:border-gray-700 py-1">
                                    <x-dropdown.items text="Hapus Invoice" icon="trash"
                                        class="text-red-600 dark:text-red-400" />
                                </div>
                            @endif
                        </x-dropdown>
                    </div>
                @endinteract

            </x-table>

        </x-tab.items>

        {{-- Tab 2: Payment Tracking --}}
        <x-tab.items tab="payments">
            <x-slot:left>
                <x-icon name="currency-dollar" class="w-5 h-5" />
            </x-slot:left>
            Payment Tracking
            <x-slot:right>
                <x-badge text="84" color="green" />
            </x-slot:right>

            <div class="text-center py-12">
                <div
                    class="h-24 w-24 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="currency-dollar" class="w-12 h-12 text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Payment Tracking</h3>
                <p class="text-gray-600 dark:text-gray-400">Kelola dan lacak semua pembayaran invoice</p>
            </div>

        </x-tab.items>

        {{-- Tab 3: Create Invoice Form --}}
        <x-tab.items tab="create">
            <x-slot:left>
                <x-icon name="document-plus" class="w-5 h-5" />
            </x-slot:left>
            Buat Invoice
            <x-slot:right>
                <x-badge text="New" color="purple" />
            </x-slot:right>

            <div class="text-center py-12">
                <div
                    class="h-24 w-24 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-plus" class="w-12 h-12 text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Buat Invoice Baru</h3>
                <p class="text-gray-600 dark:text-gray-400">Form untuk membuat invoice baru dengan multiple items</p>
            </div>

        </x-tab.items>

    </x-tab>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 left-1/2 transform -translate-x-1/2 z-50 mb-6">

        <div
            class="bg-white/95 dark:bg-zinc-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-zinc-200/50 dark:border-zinc-700/50 px-6 py-4 min-w-80">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white"
                            x-text="`${show.length} invoice dipilih`"></p>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Pilih aksi untuk semua invoice terpilih</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <x-button wire:click="$set('selected', [])" color="secondary" size="sm" icon="x-mark">
                        Batal
                    </x-button>
                    <x-button color="blue" size="sm" icon="paper-airplane">
                        <span x-text="`Kirim ${show.length} Invoice`"></span>
                    </x-button>
                    <x-button color="red" size="sm" icon="trash">
                        <span x-text="`Hapus ${show.length} Invoice`"></span>
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    <livewire:invoices.show />
</section>
