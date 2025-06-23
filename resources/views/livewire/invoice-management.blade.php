<!-- Invoice Management View -->
<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Manajemen Invoice</h1>
            <p class="text-gray-500 dark:text-zinc-400">Kelola invoice dan pembayaran klien</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            @if (!empty($selectedInvoices))
                <button wire:click="openBulkActionModal"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg border transition-colors duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aksi Massal ({{ count($selectedInvoices) }})
                </button>
            @endif

            <button
                class="bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 px-4 py-2 rounded-lg border border-gray-200 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors duration-200 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>
            <button wire:click="openAddInvoiceModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Invoice
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Invoices -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Invoice</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalInvoices }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        Semua status
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $this->formatCurrency($this->totalRevenue) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                clip-rule="evenodd" />
                        </svg>
                        Invoice lunas
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Amount -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Pending</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $this->formatCurrency($this->pendingAmount) }}</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                        Belum dibayar
                    </p>
                </div>
                <div class="bg-amber-100 dark:bg-amber-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-600 dark:text-amber-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Overdue</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->overdueInvoices }}</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-2">
                        Lewat jatuh tempo
                    </p>
                </div>
                <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Invoices List -->
        <div class="xl:col-span-2">
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Invoice</h2>
                        <div class="flex items-center space-x-3">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" wire:model.live="search" placeholder="Cari invoice..."
                                    class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg px-4 py-2 pl-10 text-sm text-gray-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-4">
                        <flux:select wire:model.live="filterStatus" placeholder="Semua Status" size="sm"
                            clearable>
                            <flux:select.option value="">Semua Status</flux:select.option>
                            <flux:select.option value="draft">Draft</flux:select.option>
                            <flux:select.option value="sent">Terkirim</flux:select.option>
                            <flux:select.option value="paid">Lunas</flux:select.option>
                            <flux:select.option value="partially_paid">Dibayar Sebagian</flux:select.option>
                            <flux:select.option value="overdue">Overdue</flux:select.option>
                        </flux:select>

                        <flux:select wire:model.live="filterClient" placeholder="Semua Klien" size="sm"
                            clearable>
                            <flux:select.option value="">Semua Klien</flux:select.option>
                            @foreach ($this->availableClients as $client)
                                <flux:select.option value="{{ $client->id }}">{{ $client->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <x-inputs.datepicker wire:model.live="filterDateRange" name="filterDateRange"
                            placeholder="Filter tanggal" mode="range" class="text-sm" />

                        <!-- Select All -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:click="toggleSelectAll"
                                {{ count($selectedInvoices) === $this->invoices->count() && $this->invoices->count() > 0 ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                            <label class="ml-2 text-sm text-gray-600 dark:text-zinc-400">Pilih Semua</label>
                        </div>
                    </div>
                </div>

                <!-- Custom Invoices Table -->
                <div class="overflow-hidden">
                    @if ($this->invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                                <thead class="bg-gray-50 dark:bg-zinc-800">
                                    <tr>
                                        <th scope="col" class="w-12 px-6 py-3">
                                            <input type="checkbox" wire:click="toggleSelectAll"
                                                {{ count($selectedInvoices) === $this->invoices->count() && $this->invoices->count() > 0 ? 'checked' : '' }}
                                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                        </th>

                                        <!-- Sortable Headers -->
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                            wire:click="sortBy('invoice_number')">
                                            <div class="flex items-center space-x-1">
                                                <span>Invoice</span>
                                                @if ($sortBy === 'invoice_number')
                                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                            wire:click="sortBy('billed_to_id')">
                                            <div class="flex items-center space-x-1">
                                                <span>Klien</span>
                                                @if ($sortBy === 'billed_to_id')
                                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                            wire:click="sortBy('issue_date')">
                                            <div class="flex items-center space-x-1">
                                                <span>Tanggal</span>
                                                @if ($sortBy === 'issue_date')
                                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                            wire:click="sortBy('status')">
                                            <div class="flex items-center space-x-1">
                                                <span>Status</span>
                                                @if ($sortBy === 'status')
                                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>

                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors"
                                            wire:click="sortBy('total_amount')">
                                            <div class="flex items-center justify-end space-x-1">
                                                <span>Amount</span>
                                                @if ($sortBy === 'total_amount')
                                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 text-gray-300" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </div>
                                        </th>

                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                                    @foreach ($this->invoices as $invoice)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors">
                                            <!-- Checkbox -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" wire:model.live="selectedInvoices"
                                                    value="{{ $invoice->id }}"
                                                    class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                            </td>

                                            <!-- Invoice Number -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <!-- Avatar placeholder (like in reference) -->
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center">
                                                            <svg class="h-4 w-4 text-gray-500 dark:text-zinc-400"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div
                                                            class="text-sm font-medium text-gray-900 dark:text-white font-mono">
                                                            {{ $invoice->invoice_number }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                            {{ Carbon\Carbon::parse($invoice->created_at)->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Client -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $invoice->client->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-zinc-400">
                                                    {{ $invoice->client->type === 'individual' ? 'Individual' : 'Perusahaan' }}
                                                </div>
                                            </td>

                                            <!-- Date -->
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                <div>
                                                    {{ Carbon\Carbon::parse($invoice->issue_date)->format('M d, g:i A') }}
                                                </div>
                                                @if ($invoice->due_date < now() && $invoice->status !== 'paid')
                                                    <div class="text-xs text-red-600 dark:text-red-400 font-medium">
                                                        Due
                                                        {{ Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- Status -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    @php
                                                        $statusColors = [
                                                            'paid' =>
                                                                'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
                                                            'sent' =>
                                                                'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
                                                            'draft' =>
                                                                'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
                                                            'partially_paid' =>
                                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                                                            'overdue' =>
                                                                'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$invoice->status] ?? $statusColors['draft'] }}">
                                                        {{ $this->getStatusLabel($invoice->status) }}
                                                    </span>
                                                    @if ($invoice->due_date < now() && $invoice->status !== 'paid')
                                                        <span
                                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100">
                                                            Overdue
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Amount -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $this->formatCurrency($invoice->total_amount) }}
                                                </div>
                                                @if ($invoice->status === 'partially_paid' || $invoice->status === 'paid')
                                                    <div class="text-xs text-green-600 dark:text-green-400">
                                                        Paid: {{ $this->formatCurrency($invoice->amount_paid) }}
                                                    </div>
                                                    @if ($invoice->status === 'partially_paid')
                                                        <div class="text-xs text-amber-600 dark:text-amber-400">
                                                            Remaining:
                                                            {{ $this->formatCurrency($invoice->amount_remaining) }}
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <!-- Quick Actions -->
                                                    @if ($invoice->status === 'draft')
                                                        <button wire:click="sendInvoice({{ $invoice->id }})"
                                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-xs px-2 py-1 bg-blue-50 dark:bg-blue-900/30 rounded">
                                                            Send
                                                        </button>
                                                    @endif
                                                    @if ($invoice->status !== 'paid')
                                                        <button wire:click="openPaymentModal({{ $invoice->id }})"
                                                            class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 text-xs px-2 py-1 bg-green-50 dark:bg-green-900/30 rounded">
                                                            Pay
                                                        </button>
                                                    @endif

                                                    <!-- Actions Dropdown using Flux -->
                                                    <flux:dropdown position="bottom" align="end">
                                                        <flux:button variant="ghost" size="sm"
                                                            icon="ellipsis-horizontal" inset="top bottom" />
                                                        <flux:menu>
                                                            <flux:menu.item
                                                                wire:click="openDetailModal({{ $invoice->id }})"
                                                                icon="eye">
                                                                Lihat Detail
                                                            </flux:menu.item>
                                                            <flux:menu.item
                                                                wire:click="openPreviewModal({{ $invoice->id }})"
                                                                icon="document-text">
                                                                Preview
                                                            </flux:menu.item>
                                                            @if ($invoice->status !== 'paid')
                                                                <flux:menu.item
                                                                    wire:click="openPaymentModal({{ $invoice->id }})"
                                                                    icon="credit-card">
                                                                    Catat Pembayaran
                                                                </flux:menu.item>
                                                            @endif
                                                            <flux:menu.separator />
                                                            <flux:menu.item
                                                                wire:click="openEditInvoiceModal({{ $invoice->id }})"
                                                                icon="pencil">
                                                                Edit Invoice
                                                            </flux:menu.item>
                                                            @if ($invoice->status === 'draft')
                                                                <flux:menu.item
                                                                    wire:click="sendInvoice({{ $invoice->id }})"
                                                                    icon="paper-airplane">
                                                                    Kirim Invoice
                                                                </flux:menu.item>
                                                            @endif
                                                            <flux:menu.separator />
                                                            <flux:menu.item
                                                                wire:click="confirmDeleteInvoice({{ $invoice->id }})"
                                                                icon="trash" variant="danger">
                                                                Hapus Invoice
                                                            </flux:menu.item>
                                                        </flux:menu>
                                                    </flux:dropdown>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($this->invoices->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
                                {{ $this->invoices->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-16 w-16 text-gray-400 dark:text-zinc-500 mx-auto mb-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Belum ada invoice</h3>
                            <p class="text-gray-500 dark:text-zinc-400 mb-4">Mulai dengan membuat invoice pertama Anda
                            </p>
                            <button wire:click="openAddInvoiceModal"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Buat Invoice
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Recent Invoices -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Invoice Terbaru</h3>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($this->recentInvoices as $invoice)
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                            wire:click="openDetailModal({{ $invoice['id'] }})">
                            <div
                                class="p-2 rounded-lg 
                                {{ $invoice['status'] === 'paid'
                                    ? 'bg-green-100 dark:bg-green-900/30'
                                    : ($invoice['is_overdue']
                                        ? 'bg-red-100 dark:bg-red-900/30'
                                        : 'bg-blue-100 dark:bg-blue-900/30') }}">
                                @if ($invoice['status'] === 'paid')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @elseif ($invoice['is_overdue'])
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-red-600 dark:text-red-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $invoice['invoice_number'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $invoice['client_name'] }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $this->formatCurrency($invoice['total_amount']) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $invoice['formatted_date'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-zinc-400 text-sm">Belum ada invoice baru</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Aksi Cepat</h3>
                </div>

                <div class="p-6 space-y-3">
                    <button wire:click="openAddInvoiceModal"
                        class="w-full bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 p-4 rounded-lg transition-colors duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="font-medium">Buat Invoice Baru</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <a href="#"
                        class="w-full bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 text-green-700 dark:text-green-400 p-4 rounded-lg transition-colors duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">Catat Pembayaran</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Invoice Statistics -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Statistik Invoice</h3>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Status Distribution -->
                    @php
                        $totalInvoiceCount = collect($this->invoiceStats)->sum('count');
                    @endphp

                    @foreach ($this->invoiceStats as $status => $stat)
                        @if ($stat['count'] > 0)
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStatusClass($status) }}">
                                            {{ $this->getStatusLabel($status) }}
                                        </span>
                                        <span
                                            class="text-sm text-gray-600 dark:text-zinc-300">{{ $stat['count'] }}</span>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-zinc-400">
                                        {{ $totalInvoiceCount > 0 ? round(($stat['count'] / $totalInvoiceCount) * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                    <div class="h-2 rounded-full
                                        {{ $status === 'paid'
                                            ? 'bg-green-500 dark:bg-green-400'
                                            : ($status === 'overdue'
                                                ? 'bg-red-500 dark:bg-red-400'
                                                : ($status === 'partially_paid'
                                                    ? 'bg-yellow-500 dark:bg-yellow-400'
                                                    : ($status === 'sent'
                                                        ? 'bg-blue-500 dark:bg-blue-400'
                                                        : 'bg-gray-500 dark:bg-gray-400'))) }}"
                                        style="width: {{ $totalInvoiceCount > 0 ? ($stat['count'] / $totalInvoiceCount) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-zinc-400">
                                    Total: {{ $this->formatCurrency($stat['total']) }}
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Summary -->
                    <div class="pt-4 border-t border-gray-100 dark:border-zinc-800">
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">Total Invoice</span>
                                <span
                                    class="text-sm font-semibold text-gray-800 dark:text-white">{{ $this->totalInvoices }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-zinc-300">Total Revenue</span>
                                <span class="text-sm text-green-600 dark:text-green-400 font-medium">
                                    {{ $this->formatCurrency($this->totalRevenue) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-zinc-300">Pending</span>
                                <span class="text-sm text-amber-600 dark:text-amber-400 font-medium">
                                    {{ $this->formatCurrency($this->pendingAmount) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods Stats -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Metode Pembayaran</h3>
                </div>

                <div class="p-6 space-y-4">
                    @php
                        $paymentMethods = \App\Models\Payment::selectRaw(
                            'payment_method, COUNT(*) as count, SUM(amount) as total',
                        )
                            ->groupBy('payment_method')
                            ->get();
                        $totalPayments = $paymentMethods->sum('count');
                    @endphp

                    @forelse($paymentMethods as $method)
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    @if ($method->payment_method === 'bank_transfer')
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2z" />
                                        </svg>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">
                                        {{ $method->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash' }}
                                    </span>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-zinc-400">
                                    {{ $totalPayments > 0 ? round(($method->count / $totalPayments) * 100, 1) : 0 }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                <div class="h-2 rounded-full {{ $method->payment_method === 'bank_transfer' ? 'bg-blue-500 dark:bg-blue-400' : 'bg-green-500 dark:bg-green-400' }}"
                                    style="width: {{ $totalPayments > 0 ? ($method->count / $totalPayments) * 100 : 0 }}%">
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-zinc-400">
                                <span>{{ $method->count }} transaksi</span>
                                <span>{{ $this->formatCurrency($method->total) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-zinc-400 text-sm">Belum ada pembayaran</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Add Invoice Modal -->
    <flux:modal wire:model.self="showAddInvoiceModal" name="add-invoice" class="max-w-4xl" :dismissible="false">
        <form wire:submit="saveInvoice" class="space-y-6">
            <div>
                <flux:heading size="lg">Buat Invoice Baru</flux:heading>
                <flux:text class="mt-2">Masukkan detail invoice baru</flux:text>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column - Invoice Details -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Detail Invoice</h4>

                    <flux:input wire:model="invoice_number" label="Nomor Invoice" placeholder="INV-2024-0001"
                        required />

                    <flux:select wire:model.live="billed_to_id" label="Klien" placeholder="Pilih Klien" required>
                        @foreach ($this->availableClients as $client)
                            <flux:select.option value="{{ $client->id }}">
                                {{ $client->name }}
                                ({{ $client->type === 'individual' ? 'Individual' : 'Perusahaan' }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="issue_date" label="Tanggal Invoice" type="date" required />
                        <flux:input wire:model="due_date" label="Jatuh Tempo" type="date" required />
                    </div>

                    <flux:select wire:model="status" label="Status">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="sent">Terkirim</flux:select.option>
                        <flux:select.option value="paid">Lunas</flux:select.option>
                        <flux:select.option value="partially_paid">Dibayar Sebagian</flux:select.option>
                        <flux:select.option value="overdue">Overdue</flux:select.option>
                    </flux:select>
                </div>

                <!-- Right Column - Total -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Total</h4>

                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-zinc-300">Auto Calculate</span>
                            <input type="checkbox" wire:model.live="autoCalculateTotal"
                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                        </div>

                        <flux:input wire:model="total_amount" label="Total Amount" type="number" step="0.01"
                            min="0" placeholder="0.00" :disabled="$autoCalculateTotal" />

                        <div class="mt-4 text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $this->formatCurrency($total_amount) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Total Invoice</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Item Invoice</h4>
                    <button type="button" wire:click="addInvoiceItem"
                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                        + Tambah Item
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach ($invoiceItems as $index => $item)
                        <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-4">
                                <h5 class="font-medium text-gray-800 dark:text-white">Item {{ $index + 1 }}</h5>
                                @if (count($invoiceItems) > 1)
                                    <button type="button" wire:click="removeInvoiceItem({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <flux:select wire:model.live="invoiceItems.{{ $index }}.client_id"
                                    label="Klien" required>
                                    @foreach ($this->availableClients as $client)
                                        <flux:select.option value="{{ $client->id }}">{{ $client->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                <div class="space-y-2">
                                    <flux:input wire:model.live="invoiceItems.{{ $index }}.service_name"
                                        label="Nama Layanan" placeholder="Nama layanan..." required />

                                    <!-- Quick Service Selection -->
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($this->availableServices->take(3) as $service)
                                            <button type="button"
                                                wire:click="selectService({{ $index }}, {{ $service->id }})"
                                                class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-1 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50">
                                                {{ $service->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <flux:input wire:model.live="invoiceItems.{{ $index }}.amount" label="Jumlah"
                                    type="number" step="0.01" min="0.01" placeholder="0.00" required />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showAddInvoiceModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Buat Invoice
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Invoice Modal -->
    <flux:modal wire:model.self="showEditInvoiceModal" name="edit-invoice" class="max-w-4xl">
        <form wire:submit="updateInvoice" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Invoice</flux:heading>
                <flux:text class="mt-2">Perbarui detail invoice</flux:text>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column - Invoice Details -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Detail Invoice</h4>

                    <flux:input wire:model="invoice_number" label="Nomor Invoice" placeholder="INV-2024-0001"
                        required />

                    <flux:select wire:model.live="billed_to_id" label="Klien" placeholder="Pilih Klien" required>
                        @foreach ($this->availableClients as $client)
                            <flux:select.option value="{{ $client->id }}">
                                {{ $client->name }}
                                ({{ $client->type === 'individual' ? 'Individual' : 'Perusahaan' }})
                            </flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="issue_date" label="Tanggal Invoice" type="date" required />
                        <flux:input wire:model="due_date" label="Jatuh Tempo" type="date" required />
                    </div>

                    <flux:select wire:model="status" label="Status">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="sent">Terkirim</flux:select.option>
                        <flux:select.option value="paid">Lunas</flux:select.option>
                        <flux:select.option value="partially_paid">Dibayar Sebagian</flux:select.option>
                        <flux:select.option value="overdue">Overdue</flux:select.option>
                    </flux:select>
                </div>

                <!-- Right Column - Total -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Total</h4>

                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-zinc-300">Auto Calculate</span>
                            <input type="checkbox" wire:model.live="autoCalculateTotal"
                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                        </div>

                        <flux:input wire:model="total_amount" label="Total Amount" type="number" step="0.01"
                            min="0" placeholder="0.00" :disabled="$autoCalculateTotal" />

                        <div class="mt-4 text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $this->formatCurrency($total_amount) }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Total Invoice</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Item Invoice</h4>
                    <button type="button" wire:click="addInvoiceItem"
                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                        + Tambah Item
                    </button>
                </div>

                <div class="space-y-4">
                    @foreach ($invoiceItems as $index => $item)
                        <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-4">
                                <h5 class="font-medium text-gray-800 dark:text-white">Item {{ $index + 1 }}</h5>
                                @if (count($invoiceItems) > 1)
                                    <button type="button" wire:click="removeInvoiceItem({{ $index }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <flux:select wire:model.live="invoiceItems.{{ $index }}.client_id"
                                    label="Klien" required>
                                    @foreach ($this->availableClients as $client)
                                        <flux:select.option value="{{ $client->id }}">{{ $client->name }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>

                                <div class="space-y-2">
                                    <flux:input wire:model.live="invoiceItems.{{ $index }}.service_name"
                                        label="Nama Layanan" placeholder="Nama layanan..." required />

                                    <!-- Quick Service Selection -->
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($this->availableServices->take(3) as $service)
                                            <button type="button"
                                                wire:click="selectService({{ $index }}, {{ $service->id }})"
                                                class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-1 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50">
                                                {{ $service->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <flux:input wire:model.live="invoiceItems.{{ $index }}.amount" label="Jumlah"
                                    type="number" step="0.01" min="0.01" placeholder="0.00" required />
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditInvoiceModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Perbarui Invoice
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Payment Modal -->
    <flux:modal wire:model.self="showPaymentModal" name="payment" class="md:w-2xl">
        <form wire:submit="savePayment" class="space-y-6">
            <div>
                <flux:heading size="lg">Catat Pembayaran</flux:heading>
                @if ($paymentInvoice)
                    <flux:text class="mt-2">
                        Invoice: {{ $paymentInvoice->invoice_number }} - {{ $paymentInvoice->client->name }}
                    </flux:text>
                @endif
            </div>

            @if ($paymentInvoice)
                <!-- Invoice Summary -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-zinc-400">Total Invoice:</p>
                            <p class="font-bold text-gray-800 dark:text-white text-lg">
                                {{ $this->formatCurrency($paymentInvoice->total_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-zinc-400">Sudah Dibayar:</p>
                            <p class="font-medium text-green-600 dark:text-green-400">
                                {{ $this->formatCurrency($paymentInvoice->amount_paid) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-zinc-400">Sisa Tagihan:</p>
                            <p class="font-bold text-red-600 dark:text-red-400 text-lg">
                                {{ $this->formatCurrency($paymentInvoice->amount_remaining) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-zinc-400">Status:</p>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStatusClass($paymentInvoice->status) }}">
                                {{ $this->getStatusLabel($paymentInvoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Details -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Detail Pembayaran</h4>

                    <flux:input wire:model="payment_amount" label="Jumlah Pembayaran" type="number" step="0.01"
                        min="0.01" placeholder="0.00" required />

                    <flux:input wire:model="payment_date" label="Tanggal Pembayaran" type="date" required />

                    <flux:select wire:model.live="payment_method" label="Metode Pembayaran" required>
                        <flux:select.option value="cash">Cash</flux:select.option>
                        <flux:select.option value="bank_transfer">Transfer Bank</flux:select.option>
                    </flux:select>

                    @if ($payment_method === 'bank_transfer')
                        <flux:select wire:model="bank_account_id" label="Akun Bank" placeholder="Pilih Akun Bank"
                            required>
                            @foreach ($this->availableBankAccounts as $account)
                                <flux:select.option value="{{ $account->id }}">
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    <flux:input wire:model="reference_number" label="Nomor Referensi (Opsional)"
                        placeholder="Contoh: TRF001234" />
                </div>

                <!-- Payment Summary -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Ringkasan</h4>

                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-600 dark:text-blue-400">Jumlah Pembayaran:</span>
                                <span
                                    class="font-bold text-blue-700 dark:text-blue-300">{{ $this->formatCurrency($payment_amount) }}</span>
                            </div>
                            @if ($paymentInvoice)
                                <div class="flex justify-between">
                                    <span class="text-blue-600 dark:text-blue-400">Sisa Setelah Bayar:</span>
                                    <span class="font-medium text-blue-700 dark:text-blue-300">
                                        {{ $this->formatCurrency(max(0, $paymentInvoice->amount_remaining - $payment_amount)) }}
                                    </span>
                                </div>
                                @if ($payment_amount >= $paymentInvoice->amount_remaining)
                                    <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded text-center">
                                        <span class="text-green-700 dark:text-green-400 font-medium">✓ Invoice akan
                                            lunas</span>
                                    </div>
                                @elseif ($payment_amount > 0)
                                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-2 rounded text-center">
                                        <span class="text-yellow-700 dark:text-yellow-400 font-medium">⚠ Pembayaran
                                            sebagian</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showPaymentModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Catat Pembayaran
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Invoice Detail Modal -->
    <flux:modal wire:model.self="showDetailModal" name="invoice-detail" class="max-w-4xl">
        @if ($invoiceDetail)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">{{ $invoiceDetail->invoice_number }}</flux:heading>
                        <flux:text class="mt-2">Detail invoice untuk {{ $invoiceDetail->client->name }}</flux:text>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->getStatusClass($invoiceDetail->status) }}">
                            {{ $this->getStatusLabel($invoiceDetail->status) }}
                        </span>

                        <button wire:click="$set('showDetailModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300">
                            
                        </button>
                    </div>
                </div>

                <!-- Invoice Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Informasi Invoice</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Nomor Invoice:</span>
                                <span
                                    class="text-gray-800 dark:text-white font-mono">{{ $invoiceDetail->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Tanggal:</span>
                                <span
                                    class="text-gray-800 dark:text-white">{{ Carbon\Carbon::parse($invoiceDetail->issue_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Jatuh Tempo:</span>
                                <span
                                    class="text-gray-800 dark:text-white">{{ Carbon\Carbon::parse($invoiceDetail->due_date)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Klien:</span>
                                <span class="text-gray-800 dark:text-white">{{ $invoiceDetail->client->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Informasi Keuangan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Total:</span>
                                <span
                                    class="text-gray-800 dark:text-white font-bold">{{ $this->formatCurrency($invoiceDetail->total_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Dibayar:</span>
                                <span
                                    class="text-green-600 dark:text-green-400 font-medium">{{ $this->formatCurrency($invoiceDetail->amount_paid) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-zinc-400">Sisa:</span>
                                <span
                                    class="text-red-600 dark:text-red-400 font-medium">{{ $this->formatCurrency($invoiceDetail->amount_remaining) }}</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-zinc-700">
                                <span class="text-gray-500 dark:text-zinc-400">Status:</span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getStatusClass($invoiceDetail->status) }}">
                                    {{ $this->getStatusLabel($invoiceDetail->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-3">Item Invoice</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 dark:bg-zinc-700">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-zinc-300">Klien
                                    </th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-zinc-300">
                                        Layanan</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-700 dark:text-zinc-300">
                                        Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-600">
                                @foreach ($invoiceDetail->items as $item)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $item->client->name }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-800 dark:text-white">{{ $item->service_name }}
                                        </td>
                                        <td class="px-4 py-2 text-right text-gray-800 dark:text-white font-medium">
                                            {{ $this->formatCurrency($item->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-100 dark:bg-zinc-700">
                                <tr>
                                    <td colspan="2"
                                        class="px-4 py-2 text-right font-bold text-gray-800 dark:text-white">Total:
                                    </td>
                                    <td class="px-4 py-2 text-right font-bold text-gray-800 dark:text-white">
                                        {{ $this->formatCurrency($invoiceDetail->total_amount) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment History -->
                @if ($invoiceDetail->payments->count() > 0)
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Riwayat Pembayaran</h4>
                        <div class="space-y-2">
                            @foreach ($invoiceDetail->payments as $payment)
                                <div
                                    class="flex items-center justify-between p-3 bg-white dark:bg-zinc-900 rounded border">
                                    <div class="flex items-center space-x-3">
                                        <div class="p-2 rounded-lg bg-green-100 dark:bg-green-900/30">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white">
                                                {{ $this->formatCurrency($payment->amount) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-zinc-400">
                                                {{ $payment->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash' }}
                                                @if ($payment->bankAccount)
                                                    - {{ $payment->bankAccount->bank_name }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-800 dark:text-white">
                                            {{ Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</p>
                                        @if ($payment->reference_number)
                                            <p class="text-xs text-gray-500 dark:text-zinc-400">Ref:
                                                {{ $payment->reference_number }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    @if ($invoiceDetail->status !== 'paid')
                        <flux:button type="button" variant="outline"
                            wire:click="openPaymentModal({{ $invoiceDetail->id }})">
                            Catat Pembayaran
                        </flux:button>
                    @endif
                    <flux:button type="button" variant="outline"
                        wire:click="openEditInvoiceModal({{ $invoiceDetail->id }})">
                        Edit Invoice
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="$set('showDetailModal', false)">
                        Tutup
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Invoice Preview Modal -->
    <flux:modal wire:model.self="showPreviewModal" name="invoice-preview" class="max-w-4xl">
        @if ($invoiceDetail)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">Preview Invoice</flux:heading>
                    <div class="flex items-center space-x-2">
                        <button
                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-2 rounded-lg text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                        <button
                            class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg text-sm font-medium hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </button>
                        <button wire:click="$set('showPreviewModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300">
                            
                        </button>
                    </div>
                </div>

                <!-- Invoice Preview Content -->
                <div class="bg-white dark:bg-zinc-900 p-8 rounded-lg border">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">INVOICE</h1>
                            <p class="text-gray-600 dark:text-zinc-400 mt-2"># {{ $invoiceDetail->invoice_number }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600 dark:text-zinc-400">Tanggal:
                                {{ Carbon\Carbon::parse($invoiceDetail->issue_date)->format('d/m/Y') }}</p>
                            <p class="text-gray-600 dark:text-zinc-400">Jatuh Tempo:
                                {{ Carbon\Carbon::parse($invoiceDetail->due_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Ditagih Kepada:</h3>
                        <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded">
                            <p class="font-medium text-gray-800 dark:text-white">{{ $invoiceDetail->client->name }}
                            </p>
                            @if ($invoiceDetail->client->email)
                                <p class="text-gray-600 dark:text-zinc-400">{{ $invoiceDetail->client->email }}</p>
                            @endif
                            @if ($invoiceDetail->client->address)
                                <p class="text-gray-600 dark:text-zinc-400">{{ $invoiceDetail->client->address }}</p>
                            @endif
                            @if ($invoiceDetail->client->NPWP)
                                <p class="text-gray-600 dark:text-zinc-400">NPWP: {{ $invoiceDetail->client->NPWP }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="mb-8">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-zinc-800">
                                    <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">
                                        Deskripsi</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-800 dark:text-white">Klien
                                    </th>
                                    <th class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">Jumlah
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                @foreach ($invoiceDetail->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-800 dark:text-white">{{ $item->service_name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-zinc-400">
                                            {{ $item->client->name }}</td>
                                        <td class="px-4 py-3 text-right text-gray-800 dark:text-white">
                                            {{ $this->formatCurrency($item->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-end">
                        <div class="w-64">
                            <div class="bg-gray-50 dark:bg-zinc-800 p-4 rounded">
                                <div
                                    class="flex justify-between items-center text-xl font-bold text-gray-800 dark:text-white">
                                    <span>TOTAL:</span>
                                    <span>{{ $this->formatCurrency($invoiceDetail->total_amount) }}</span>
                                </div>
                                @if ($invoiceDetail->amount_paid > 0)
                                    <div
                                        class="flex justify-between items-center text-sm text-green-600 dark:text-green-400 mt-2">
                                        <span>Dibayar:</span>
                                        <span>{{ $this->formatCurrency($invoiceDetail->amount_paid) }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center text-sm text-red-600 dark:text-red-400">
                                        <span>Sisa:</span>
                                        <span>{{ $this->formatCurrency($invoiceDetail->amount_remaining) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="mt-8 pt-8 border-t border-gray-200 dark:border-zinc-700 text-center text-gray-600 dark:text-zinc-400">
                        <p>Terima kasih atas kepercayaan Anda.</p>
                    </div>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Bulk Action Modal -->
    <flux:modal wire:model.self="showBulkActionModal" name="bulk-action" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Aksi Massal</flux:heading>
                <flux:text class="mt-2">Pilih aksi untuk {{ count($selectedInvoices) }} invoice yang dipilih
                </flux:text>
            </div>

            <flux:select wire:model="bulkAction" label="Pilih Aksi" placeholder="Pilih aksi...">
                <flux:select.option value="mark_sent">Tandai Sebagai Terkirim</flux:select.option>
                <flux:select.option value="mark_overdue">Tandai Sebagai Overdue</flux:select.option>
                <flux:select.option value="delete">Hapus Invoice</flux:select.option>
            </flux:select>

            @if ($bulkAction === 'delete')
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Peringatan!</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                <p>Aksi ini akan menghapus semua invoice yang dipilih beserta data terkait (pembayaran,
                                    item) dan tidak dapat dikembalikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showBulkActionModal', false)">
                    Batal
                </flux:button>
                <flux:button type="button" variant="{{ $bulkAction === 'delete' ? 'danger' : 'primary' }}"
                    wire:click="processBulkAction">
                    {{ $bulkAction === 'mark_sent' ? 'Tandai Terkirim' : ($bulkAction === 'mark_overdue' ? 'Tandai Overdue' : 'Hapus') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.self="showDeleteModal" name="delete-invoice" class="md:w-96">
        <div class="space-y-6">
            <div class="text-center">
                <div
                    class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 dark:text-red-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <flux:heading size="lg">Hapus Invoice</flux:heading>
                <flux:text class="mt-2">
                    Apakah Anda yakin ingin menghapus invoice
                    @if ($invoiceToDelete)
                        <strong>{{ $invoiceToDelete->invoice_number }}</strong>
                    @endif
                    ? Semua data terkait (pembayaran, item) akan ikut terhapus dan tidak dapat dikembalikan.
                </flux:text>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">
                    Batal
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteInvoice">
                    Ya, Hapus Invoice
                </flux:button>
            </div>
        </div>
    </flux:modal>

</section>
