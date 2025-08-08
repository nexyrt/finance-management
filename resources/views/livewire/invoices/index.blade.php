<section class="space-y-6">
    {{-- Header Section --}}
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-1">
                <h1
                    class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-dark-900 via-primary-600 to-primary-700 dark:from-white dark:via-primary-300 dark:to-primary-200 bg-clip-text text-transparent">
                    Manajemen Invoice
                </h1>
                <p class="text-dark-600 dark:text-dark-400 text-base sm:text-lg">
                    Kelola invoice, pembayaran, dan buat invoice baru
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <x-button wire:click="createInvoice" loading="createInvoice" class="w-full sm:w-auto">
                    Buat Invoice Baru
                </x-button>
            </div>
        </div>
    </div>

    {{-- Main Tabs Content --}}
    <x-tab selected="invoices">

        {{-- Tab 1: Invoice Management --}}
        <x-tab.items tab="invoices">
            <x-slot:left>
                <x-icon name="document-text" class="w-5 h-5" />
            </x-slot:left>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 mb-8">
                <!-- Total Invoice Card -->
                <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
                    x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-dark-900 dark:text-dark-50'>Total Invoice</h4><p class='text-xs text-dark-600 dark:text-dark-300 mb-3'>Jumlah semua invoice: draft, terkirim, dibayar, dan terlambat</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Draft: {{ $stats['draft_count'] ?? 0 }}</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Terkirim: {{ $stats['sent_count'] ?? 0 }}</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Dibayar: {{ $stats['paid_count'] ?? 0 }}</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Terlambat: {{ $stats['overdue_count'] ?? 0 }}</span></div></div></div>">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-dark-600 dark:text-dark-400">Total Invoice</p>
                            <p class="text-xl font-bold text-dark-900 dark:text-dark-50 break-all">
                                {{ number_format($stats['total_invoices']) }}
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 sm:h-12 sm:w-12 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <x-icon name="document-text"
                                class="w-5 h-5 sm:w-6 sm:h-6 text-zinc-600 dark:text-zinc-400" />
                        </div>
                    </div>
                </div>

                <!-- Outstanding Card -->
                <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
                    x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-dark-900 dark:text-dark-50'>Outstanding Amount</h4><p class='text-xs text-dark-600 dark:text-dark-300 mb-3'>Total nilai invoice yang belum dibayar penuh</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Terkirim: Rp {{ number_format($stats['sent_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Sebagian Dibayar: Rp {{ number_format($stats['partial_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-red-600'>Terlambat: Rp {{ number_format($stats['overdue_amount'] ?? 0, 0, ',', '.') }}</span></div></div></div>">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-dark-600 dark:text-dark-400">Outstanding</p>
                            <p class="text-xl font-bold text-red-600 dark:text-red-400 break-all">
                                Rp {{ number_format($stats['outstanding_amount'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 sm:h-12 sm:w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <x-icon name="exclamation-triangle"
                                class="w-5 h-5 sm:w-6 sm:h-6 text-red-600 dark:text-red-400" />
                        </div>
                    </div>
                </div>

                <!-- Paid This Month Card -->
                <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
                    x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-dark-900 dark:text-dark-50'>Pembayaran Bulan Ini</h4><p class='text-xs text-dark-600 dark:text-dark-300 mb-3'>Total pembayaran diterima bulan {{ now()->format('F Y') }}</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Jumlah Transaksi: {{ $stats['payments_count'] ?? 0 }}x</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Rata-rata: Rp {{ ($stats['payments_count'] ?? 0) > 0 ? number_format(($stats['paid_this_month'] ?? 0) / ($stats['payments_count'] ?? 1), 0, ',', '.') : '0' }}</span></div></div></div>">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-dark-600 dark:text-dark-400">Paid This Month</p>
                            <p class="text-xl font-bold text-green-600 dark:text-green-400 break-all">
                                Rp {{ number_format($stats['paid_this_month'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 sm:h-12 sm:w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <x-icon name="check-circle"
                                class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </div>

                <!-- Overdue Card -->
                <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow"
                    x-tooltip="<div class='p-3 max-w-xs'><h4 class='font-semibold text-sm mb-2 text-dark-900 dark:text-dark-50'>Invoice Terlambat</h4><p class='text-xs text-dark-600 dark:text-dark-300 mb-3'>Invoice yang melewati tanggal jatuh tempo</p><div class='space-y-1 text-xs'><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Total: {{ $stats['overdue_count'] }} invoice</span></div><div class='flex justify-between'><span class='text-red-600'>Nilai: Rp {{ number_format($stats['overdue_amount'] ?? 0, 0, ',', '.') }}</span></div><div class='flex justify-between'><span class='text-dark-600 dark:text-dark-400'>Rata-rata terlambat: {{ $stats['avg_overdue_days'] ?? 0 }} hari</span></div></div></div>">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-dark-600 dark:text-dark-400">Overdue</p>
                            <p class="text-xl font-bold text-orange-600 dark:text-orange-400 break-all">
                                {{ $stats['overdue_count'] }}
                            </p>
                        </div>
                        <div
                            class="h-10 w-10 sm:h-12 sm:w-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center flex-shrink-0 ml-3">
                            <x-icon name="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Enhanced Filters Section --}}
            <div
                class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl shadow-sm mb-8">
                <div
                    class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 sm:p-6 pb-4 border-b border-zinc-200 dark:border-dark-600">
                    <div class="flex items-center space-x-3 mb-4 sm:mb-0">
                        <div
                            class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="funnel" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Filter Invoice</h3>
                            <p class="text-sm text-dark-500 dark:text-dark-400">Gunakan filter untuk mempersempit
                                pencarian</p>
                        </div>
                    </div>

                    {{-- Active Filters Count --}}
                    @if ($statusFilter || $clientFilter || $dateRange)
                    <div class="flex items-center space-x-2">
                        <x-badge
                            text="{{ collect([$statusFilter, $clientFilter, $dateRange])->filter()->count() }} Filter Aktif"
                            color="zinc" />
                    </div>
                    @endif

                    <x-dropdown icon="document-arrow-down" outline color="zinc" class="w-full sm:w-auto">
                        <x-dropdown.items text="Export Excel" icon="document-text" wire:click="exportExcel" />
                        <x-dropdown.items text="Export PDF" icon="document" wire:click="exportPdf" />
                    </x-dropdown>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        {{-- Status Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Status</label>
                            <x-select.styled wire:model.live="statusFilter" :options="[
                                ['label' => '📄 Draft', 'value' => 'draft'],
                                ['label' => '📤 Terkirim', 'value' => 'sent'],
                                ['label' => '✅ Dibayar', 'value' => 'paid'],
                                ['label' => '💰 Sebagian', 'value' => 'partially_paid'],
                                ['label' => '⏰ Terlambat', 'value' => 'overdue'],
                            ]" placeholder="Semua status..." class="w-full" />
                        </div>

                        {{-- Client Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Klien</label>
                            <x-select.styled wire:model.live="clientFilter" :options="$clients
                                ->map(
                                    fn($client) => [
                                        'label' => $client->name,
                                        'value' => $client->id,
                                    ],
                                )
                                ->toArray()" placeholder="Semua klien..." searchable class="w-full" />
                        </div>

                        {{-- Date Range Filter --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Periode
                                Tanggal</label>
                            <x-date wire:model.live="dateRange" range placeholder="Pilih periode..." class="w-full" />
                        </div>

                        {{-- Clear Filters --}}
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-dark-700 dark:text-dark-300">Reset</label>
                            <x-button wire:click="clearFilters" color="zinc" icon="x-mark" outline class="w-full">
                                Hapus Filter
                            </x-button>
                        </div>
                    </div>

                    {{-- Active Filter Tags --}}
                    @if ($statusFilter || $clientFilter || $dateRange)
                    <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-dark-600">
                        <div class="flex items-center space-x-2 mb-3">
                            <x-icon name="tag" class="w-4 h-4 text-dark-500 dark:text-dark-400" />
                            <span class="text-sm font-medium text-dark-600 dark:text-dark-400">Filter
                                Aktif:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if ($statusFilter)
                            <div
                                class="inline-flex items-center gap-2 bg-zinc-50 dark:bg-zinc-900/20 text-zinc-700 dark:text-zinc-300 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-800 text-sm">
                                <span>{{ ucfirst($statusFilter) }}</span>
                                <button wire:click="$set('statusFilter', '')"
                                    class="hover:bg-zinc-200 dark:hover:bg-zinc-800 rounded-full p-0.5 transition-colors">
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

                            @if ($dateRange && is_array($dateRange) && count($dateRange) >= 2)
                            <div
                                class="inline-flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-3 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 text-sm">
                                <span>
                                    {{ \Carbon\Carbon::parse($dateRange[0])->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($dateRange[1])->format('d/m/Y') }}
                                </span>
                                <button wire:click="$set('dateRange', [])"
                                    class="hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-colors">
                                    <x-icon name="x-mark" class="w-3 h-3" />
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Invoice Table --}}
            <x-table :$headers :$rows :$sort filter :quantity="[10, 25, 50, 100]" paginate selectable
                wire:model="selected">

                {{-- Invoice Number Column --}}
                @interact('column_invoice_number', $row)
                <div class="group cursor-pointer">
                    <div
                        class="font-mono font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-300 transition-colors duration-200">
                        {{ $row->invoice_number }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        {{ $row->issue_date->format('d/m/Y') }}
                    </div>
                </div>
                @endinteract

                {{-- Client Column --}}
                @interact('column_client_name', $row)
                <div class="flex items-center space-x-4">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 {{ $row->client_type === 'individual'
                                    ? 'bg-gradient-to-br from-zinc-400 to-zinc-600'
                                    : 'bg-gradient-to-br from-purple-400 to-purple-600' }} 
                    rounded-2xl flex items-center justify-center shadow-lg ring-2 ring-white dark:ring-dark-800">
                            <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-5 h-5 text-white" />
                        </div>
                        {{-- Client Type Indicator --}}
                        <div
                            class="absolute -bottom-1 -right-1 w-4 h-4 {{ $row->client_type === 'individual' ? 'bg-zinc-500' : 'bg-purple-500' }} rounded-full border-2 border-white dark:border-dark-800 flex items-center justify-center">
                            <x-icon
                                name="{{ $row->client_type === 'individual' ? 'identification' : 'building-office-2' }}"
                                class="w-2 h-2 text-white" />
                        </div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-dark-900 dark:text-dark-50 truncate text-sm">
                            {{ $row->client_name }}
                        </p>
                        <div class="flex items-center gap-1 text-xs text-dark-500 dark:text-dark-400">
                            <x-icon name="{{ $row->client_type === 'individual' ? 'user' : 'building-office' }}"
                                class="w-3 h-3" />
                            <span class="capitalize">{{ $row->client_type === 'individual' ? 'Individu' : 'Perusahaan'
                                }}</span>
                        </div>
                    </div>
                </div>
                @endinteract

                {{-- Issue Date Column --}}
                @interact('column_issue_date', $row)
                <div class="space-y-1">
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $row->issue_date->format('d M Y') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 flex items-center gap-1">
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
                    $row->due_date->diffInDays(now()) <= 7 && !$row->due_date->isPast() &&
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
                                class="text-sm font-medium {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($isDueSoon ? 'text-yellow-600 dark:text-yellow-400' : 'text-dark-900 dark:text-dark-50') }}">
                                {{ $row->due_date->format('d M Y') }}
                            </span>
                        </div>

                        @if ($isOverdue)
                        <div
                            class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-md text-xs font-medium">
                            <x-icon name="exclamation-triangle" class="w-3 h-3" />
                            {{ (int) abs($row->due_date->diffInDays(now())) }} hari lewat
                        </div>
                        @elseif($isDueSoon)
                        <div
                            class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300 rounded-md text-xs font-medium">
                            <x-icon name="clock" class="w-3 h-3" />
                            {{ (int) $row->due_date->diffInDays(now()) }} hari lagi
                        </div>
                        @else
                        <div class="flex items-center gap-1 text-xs text-dark-500 dark:text-dark-400">
                            <x-icon name="check-circle" class="w-3 h-3" />
                            {{ $row->due_date->diffForHumans() }}
                        </div>
                        @endif
                </div>
                @endinteract

                {{-- Amount Column --}}
                @interact('column_total_amount', $row)
                <div class="text-right space-y-2">
                    <div class="font-bold text-lg text-dark-900 dark:text-dark-50">
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
                            <span class="text-dark-500 dark:text-dark-400">
                                Sisa: Rp {{ number_format($remainingAmount, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                        <div class="w-full bg-zinc-200 dark:bg-dark-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ min($paymentPercentage, 100) }}%"></div>
                        </div>
                    </div>
                    @else
                    <div class="text-xs text-dark-500 dark:text-dark-400 italic">
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
                    {{-- Main Actions Dropdown --}}
                    <x-dropdown icon="ellipsis-vertical" class="relative">
                        {{-- Header with Invoice Number --}}
                        <div
                            class="px-4 py-2 border-b border-zinc-100 dark:border-dark-700 bg-zinc-50 dark:bg-dark-800">
                            <div class="font-mono text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ $row->invoice_number }}
                            </div>
                            <div class="text-xs text-dark-500 dark:text-dark-400">
                                {{ $row->client_name }}
                            </div>
                        </div>

                        {{-- Primary Actions --}}
                        <div class="py-1">
                            <x-dropdown.items text="Lihat Detail" icon="eye"
                                wire:click="$dispatch('show-invoice', { invoiceId: {{ $row->id }} })"
                                loading="$dispatch('show-invoice')" />
                            <x-dropdown.items text="Edit Invoice" icon="pencil"
                                href="{{ route('invoices.edit', $row->id) }}"
                                class="text-zinc-600 dark:text-zinc-400" />
                            @if ($row->status === 'draft')
                            <x-dropdown.items wire:click='sendInvoice({{ $row->id }})' text="Kirim Invoice"
                                icon="paper-airplane" class="text-green-600 dark:text-green-400" />
                            @endif

                            @if (in_array($row->status, ['sent', 'overdue', 'partially_paid']))
                            <x-dropdown.items text="Catat Pembayaran" icon="currency-dollar"
                                wire:click="$dispatch('record-payment', { invoiceId: {{ $row->id }} })" />
                            @endif
                        </div>

                        {{-- Secondary Actions --}}
                        <div class="border-t border-zinc-100 dark:border-dark-700 py-1">
                            <x-dropdown.items text="Print PDF" icon="printer" wire:click="printInvoice({{ $row->id }})"
                                class=" dark:text-dark-100" />
                        </div>

                        {{-- Danger Actions --}}
                        <div class="border-t border-zinc-100 dark:border-dark-700 py-1">
                            <x-dropdown.items text="Hapus Invoice" icon="trash"
                                wire:click="$dispatch('delete-invoice', { invoiceId: {{ $row->id }} })"
                                class="text-red-600 dark:text-red-400" />
                        </div>
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

            <livewire:payments.listing />

        </x-tab.items>

        {{-- Tab 3: Create Invoice Form --}}
        <x-tab.items tab="create">
            <x-slot:left>
                <x-icon name="document-plus" class="w-5 h-5" />
            </x-slot:left>
            <x-slot:right>
                <x-badge text="New" color="purple" />
            </x-slot:right>

            <div class="text-center py-12">
                <div
                    class="h-20 w-20 sm:h-24 sm:w-24 bg-purple-50 dark:bg-purple-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-plus"
                        class="w-10 h-10 sm:w-12 sm:h-12 text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">Buat Invoice Baru</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-6">Klik tombol di bawah untuk membuat invoice dengan
                    multiple items</p>

                <x-button wire:click="createInvoice" color="purple" icon="plus" size="lg">
                    Buat Invoice Baru
                </x-button>
            </div>
        </x-tab.items>

    </x-tab>

    {{-- Enhanced Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">

        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-80">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                {{-- Selection Info --}}
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} invoice dipilih`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            Pilih aksi untuk invoice yang dipilih
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end">
                    {{-- Delete Selected --}}
                    <x-button wire:click="openBulkDeleteModal" size="sm" color="red" icon="trash"
                        class="whitespace-nowrap">
                        Hapus
                    </x-button>

                    {{-- Cancel Selection --}}
                    <x-button wire:click="$set('selected', [])" size="sm" color="zinc" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Confirmation Modal --}}
    <x-modal wire="showBulkDeleteModal" size="lg" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Konfirmasi Bulk Delete</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Hapus beberapa invoice sekaligus</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-6">
            {{-- Warning --}}
            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-3">
                    <div
                        class="h-8 w-8 bg-red-100 dark:bg-red-800/50 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h4 class="font-semibold text-red-900 dark:text-red-100 mb-1">Perhatian!</h4>
                        <p class="text-sm text-red-800 dark:text-red-200 mb-3">
                            Anda akan menghapus <strong>{{ count($selected) }}</strong> invoice secara permanen.
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3">
                            <div class="text-sm text-red-800 dark:text-red-200">
                                <div class="font-medium mb-1">Yang akan dihapus:</div>
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Invoice beserta semua item yang terkait</li>
                                    <li>Semua pembayaran yang sudah tercatat (jika ada)</li>
                                    <li>Riwayat transaksi terkait invoice</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Selected Invoice Count Info --}}
            <div class="bg-zinc-50 dark:bg-dark-700 rounded-xl p-4 border border-zinc-200 dark:border-dark-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 bg-zinc-50 dark:bg-zinc-900/20 rounded-xl flex items-center justify-center">
                            <x-icon name="document-text" class="w-5 h-5 text-zinc-600 dark:text-zinc-400" />
                        </div>
                        <div>
                            <div class="font-semibold text-dark-900 dark:text-dark-50">
                                {{ count($selected) }} Invoice Dipilih
                            </div>
                            <div class="text-sm text-dark-600 dark:text-dark-400">
                                Semua invoice yang dipilih akan dihapus
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button @click="$wire.set('showBulkDeleteModal', false)" color="zinc" class="w-full sm:w-auto">
                    Batal
                </x-button>

                <x-button wire:click="bulkDelete" color="red" icon="trash" wire:loading.attr="disabled"
                    wire:target="bulkDelete" class="w-full sm:w-auto">
                    <span wire:loading.remove wire:target="bulkDelete">Hapus Semua Invoice</span>
                    <span wire:loading wire:target="bulkDelete">Menghapus...</span>
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Livewire Components --}}
    <livewire:invoices.show />
    <livewire:invoices.create />
    <livewire:invoices.delete />
    <livewire:payments.create />
    <livewire:payments.edit />
</section>