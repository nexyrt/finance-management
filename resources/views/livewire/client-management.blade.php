<!-- Client Management View -->
<section class="w-full p-6 bg-white dark:bg-zinc-800">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Manajemen Klien</h1>
            <p class="text-gray-500 dark:text-zinc-400">Kelola data klien individual dan perusahaan</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            @if (!empty($selectedClients))
                <button wire:click="openBulkActionModal"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg border transition-colors duration-200 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aksi Massal ({{ count($selectedClients) }})
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
            <button wire:click="openAddClientModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors duration-200 flex items-center font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Klien
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Clients -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Klien</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalClients }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                        {{ $this->activeClients }} aktif
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 dark:text-blue-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Individual Clients -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Individual</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalIndividuals }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                        Klien perorangan
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600 dark:text-green-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Company Clients -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Perusahaan</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $this->totalCompanies }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-2">
                        Klien korporat
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 dark:text-purple-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 p-6 border border-gray-100 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-zinc-400 font-medium">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">
                        {{ $this->formatCurrency($this->clientStats['total_revenue']) }}
                    </p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-2">
                        {{ $this->clientStats['total_invoices'] }} invoice
                    </p>
                </div>
                <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600 dark:text-emerald-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Clients List -->
        <div class="xl:col-span-2">
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Daftar Klien</h2>
                        <div class="flex items-center space-x-3">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" wire:model.live="search" placeholder="Cari klien..."
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
                        <flux:select wire:model.live="filterType" placeholder="Semua Tipe" size="sm" clearable>
                            <flux:select.option value="">Semua Tipe</flux:select.option>
                            <flux:select.option value="individual">Individual</flux:select.option>
                            <flux:select.option value="company">Perusahaan</flux:select.option>
                        </flux:select>

                        <flux:select wire:model.live="filterStatus" placeholder="Semua Status" size="sm"
                            clearable>
                            <flux:select.option value="">Semua Status</flux:select.option>
                            <flux:select.option value="Active">Aktif</flux:select.option>
                            <flux:select.option value="Inactive">Tidak Aktif</flux:select.option>
                        </flux:select>

                        <!-- Select All -->
                        <div class="flex items-center">
                            <input type="checkbox" wire:click="toggleSelectAll"
                                {{ count($selectedClients) === $this->clients->count() && $this->clients->count() > 0 ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                            <label class="ml-2 text-sm text-gray-600 dark:text-zinc-400">Pilih Semua</label>
                        </div>
                    </div>
                </div>

                <!-- Clients Grid -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($this->clients as $client)
                            <div
                                class="group relative bg-gray-50 dark:bg-zinc-800 rounded-xl p-6 border border-gray-100 dark:border-zinc-700 hover:shadow-md dark:hover:shadow-zinc-950/25 transition-all duration-300 hover:scale-[1.02]">

                                <!-- Selection Checkbox -->
                                <div class="absolute top-4 left-4">
                                    <input type="checkbox" wire:model.live="selectedClients"
                                        value="{{ $client->id }}"
                                        class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                </div>

                                <div class="ml-8">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- Client Logo/Avatar -->
                                            <div class="relative">
                                                @if ($client->logo)
                                                    <img src="{{ Storage::url($client->logo) }}"
                                                        alt="{{ $client->name }}"
                                                        class="h-12 w-12 rounded-lg object-cover border border-gray-200 dark:border-zinc-700">
                                                @else
                                                    <div
                                                        class="h-12 w-12 rounded-lg flex items-center justify-center
                                                        {{ $client->type === 'individual' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }}">
                                                        @if ($client->type === 'individual')
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-6 w-6 text-green-600 dark:text-green-400"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-6 w-6 text-purple-600 dark:text-purple-400"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Status Badge -->
                                                <span
                                                    class="absolute -top-1 -right-1 h-4 w-4 rounded-full border-2 border-white dark:border-zinc-800
                                                    {{ $client->status === 'Active' ? 'bg-green-500' : 'bg-red-500' }}">
                                                </span>
                                            </div>

                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-800 dark:text-white text-lg">
                                                    {{ $client->name }}
                                                </h3>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $client->type === 'individual'
                                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                            : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                                        {{ $client->type === 'individual' ? 'Individual' : 'Perusahaan' }}
                                                    </span>
                                                    <span
                                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $client->status === 'Active'
                                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                                            : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                                        {{ $client->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}
                                                    </span>
                                                </div>

                                                @if ($client->email)
                                                    <p class="text-sm text-gray-500 dark:text-zinc-400 mt-1">
                                                        {{ $client->email }}
                                                    </p>
                                                @endif

                                                @if ($client->NPWP)
                                                    <p class="text-xs text-gray-500 dark:text-zinc-400 font-mono">
                                                        NPWP: {{ $client->NPWP }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions Dropdown -->
                                        <div class="relative">
                                            <flux:dropdown position="bottom" align="end">
                                                <flux:button variant="ghost" size="sm"
                                                    icon="ellipsis-horizontal" />
                                                <flux:menu>
                                                    <flux:menu.item wire:click="openDetailModal({{ $client->id }})"
                                                        icon="eye">
                                                        Lihat Detail
                                                    </flux:menu.item>
                                                    <flux:menu.item
                                                        wire:click="openEditClientModal({{ $client->id }})"
                                                        icon="pencil">
                                                        Edit Klien
                                                    </flux:menu.item>
                                                    <flux:menu.item
                                                        wire:click="openRelationshipModal({{ $client->id }})"
                                                        icon="link">
                                                        Kelola Hubungan
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item
                                                        wire:click="confirmDeleteClient({{ $client->id }})"
                                                        icon="trash" variant="danger">
                                                        Hapus Klien
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                @if ($client->account_representative)
                                                    <p class="text-gray-600 dark:text-zinc-300">
                                                        AR: {{ $client->account_representative }}
                                                    </p>
                                                @endif
                                                @if ($client->person_in_charge)
                                                    <p class="text-gray-500 dark:text-zinc-400">
                                                        PIC: {{ $client->person_in_charge }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-gray-500 dark:text-zinc-400 text-xs">
                                                    {{ Carbon\Carbon::parse($client->created_at)->format('d/m/Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-16 w-16 text-gray-400 dark:text-zinc-500 mx-auto mb-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-2">Belum ada klien</h3>
                                <p class="text-gray-500 dark:text-zinc-400 mb-4">Mulai dengan menambahkan klien pertama
                                    Anda</p>
                                <button wire:click="openAddClientModal"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                    Tambah Klien
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if ($this->clients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
                        {{ $this->clients->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Recent Clients -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Klien Terbaru</h3>
                </div>

                <div class="p-6 space-y-4">
                    @forelse($this->recentClients as $client)
                        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                            wire:click="openDetailModal({{ $client['id'] }})">
                            <div
                                class="p-2 rounded-lg 
                                {{ $client['type'] === 'individual'
                                    ? 'bg-green-100 dark:bg-green-900/30'
                                    : 'bg-purple-100 dark:bg-purple-900/30' }}">
                                @if ($client['type'] === 'individual')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-purple-600 dark:text-purple-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $client['name'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $client['type'] === 'individual' ? 'Individual' : 'Perusahaan' }}
                                    @if ($client['email'])
                                        • {{ $client['email'] }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $client['status'] === 'Active'
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                    {{ $client['status'] === 'Active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                                    {{ $client['formatted_date'] }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-zinc-400 text-sm">Belum ada klien baru</p>
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
                    <button wire:click="openAddClientModal"
                        class="w-full bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-400 p-4 rounded-lg transition-colors duration-200 flex items-center justify-between group">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="font-medium">Tambah Klien Baru</span>
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
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="font-medium">Buat Invoice</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Client Statistics -->
            <div
                class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm dark:shadow-zinc-950/25 border border-gray-100 dark:border-zinc-800">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Statistik</h3>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Client Type Distribution -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Individual</span>
                            <span class="text-sm text-gray-500 dark:text-zinc-400">
                                {{ $this->totalClients > 0 ? round(($this->totalIndividuals / $this->totalClients) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-green-500 dark:bg-green-400 h-2 rounded-full"
                                style="width: {{ $this->totalClients > 0 ? ($this->totalIndividuals / $this->totalClients) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Perusahaan</span>
                            <span class="text-sm text-gray-500 dark:text-zinc-400">
                                {{ $this->totalClients > 0 ? round(($this->totalCompanies / $this->totalClients) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                            <div class="bg-purple-500 dark:bg-purple-400 h-2 rounded-full"
                                style="width: {{ $this->totalClients > 0 ? ($this->totalCompanies / $this->totalClients) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="pt-4 border-t border-gray-100 dark:border-zinc-800">
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-800 dark:text-white">Total Klien</span>
                                <span
                                    class="text-sm font-semibold text-gray-800 dark:text-white">{{ $this->totalClients }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-zinc-300">Rata-rata Revenue</span>
                                <span class="text-sm text-gray-600 dark:text-zinc-300">
                                    {{ $this->formatCurrency($this->clientStats['average_per_client']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->

    <!-- Add Client Modal -->
    <flux:modal wire:model.self="showAddClientModal" name="add-client" class="md:w-2xl">
        <form wire:submit="saveClient" class="space-y-6">
            <div>
                <flux:heading size="lg">Tambah Klien Baru</flux:heading>
                <flux:text class="mt-2">Masukkan detail klien baru</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Informasi Dasar</h4>

                    <flux:input wire:model="name" label="Nama Klien" placeholder="Nama lengkap/perusahaan"
                        required />

                    <flux:select wire:model.live="type" label="Tipe Klien" required>
                        <flux:select.option value="individual">Individual</flux:select.option>
                        <flux:select.option value="company">Perusahaan</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="email" label="Email" type="email" placeholder="email@contoh.com" />

                    <flux:select wire:model="status" label="Status">
                        <flux:select.option value="Active">Aktif</flux:select.option>
                        <flux:select.option value="Inactive">Tidak Aktif</flux:select.option>
                    </flux:select>

                    <!-- Logo Upload -->
                    <div>
                        <flux:label>Logo/Foto</flux:label>
                        <input type="file" wire:model="uploadedLogo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-medium
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/20 dark:file:text-blue-400" />
                        @if ($uploadedLogo)
                            <div class="mt-2">
                                <img src="{{ $uploadedLogo->temporaryUrl() }}"
                                    class="h-16 w-16 object-cover rounded-lg">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Detailed Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Detail Informasi</h4>

                    <flux:input wire:model="NPWP" label="NPWP" placeholder="Nomor Pokok Wajib Pajak" />
                    <flux:input wire:model="KPP" label="KPP" placeholder="Kantor Pelayanan Pajak" />
                    <flux:input wire:model="EFIN" label="EFIN"
                        placeholder="Electronic Filing Identification Number" />

                    <flux:input wire:model="account_representative" label="Account Representative"
                        placeholder="Nama petugas pajak" />
                    <flux:input wire:model="ar_phone_number" label="No. Telp AR" placeholder="Nomor telepon AR" />

                    <flux:input wire:model="person_in_charge" label="Person in Charge" placeholder="Nama PIC" />

                    <flux:textarea wire:model="address" label="Alamat" placeholder="Alamat lengkap..."
                        rows="3" />
                </div>
            </div>

            <!-- Relationships -->
            @if ($type === 'individual')
                <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-4">Perusahaan yang Dimiliki</h4>
                    <div class="space-y-2">
                        @foreach ($availableCompanies as $company)
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="selectedCompanies" value="{{ $company['id'] }}"
                                    class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                <span class="text-sm text-gray-700 dark:text-zinc-300">{{ $company['name'] }}</span>
                            </label>
                        @endforeach

                        @if (empty($availableCompanies))
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Belum ada perusahaan yang tersedia</p>
                        @endif
                    </div>
                </div>
            @elseif ($type === 'company')
                <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-4">Pemilik Perusahaan</h4>
                    <div class="space-y-2">
                        @foreach ($availableOwners as $owner)
                            <label class="flex items-center space-x-3">
                                <input type="checkbox" wire:model="selectedOwners" value="{{ $owner['id'] }}"
                                    class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                <span class="text-sm text-gray-700 dark:text-zinc-300">{{ $owner['name'] }}</span>
                            </label>
                        @endforeach

                        @if (empty($availableOwners))
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Belum ada individual yang tersedia</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showAddClientModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Simpan Klien
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Edit Client Modal -->
    <flux:modal wire:model.self="showEditClientModal" name="edit-client" class="md:w-2xl">
        <form wire:submit="updateClient" class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Klien</flux:heading>
                <flux:text class="mt-2">Perbarui detail klien</flux:text>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Informasi Dasar</h4>

                    <flux:input wire:model="name" label="Nama Klien" placeholder="Nama lengkap/perusahaan"
                        required />

                    <flux:select wire:model.live="type" label="Tipe Klien" required>
                        <flux:select.option value="individual">Individual</flux:select.option>
                        <flux:select.option value="company">Perusahaan</flux:select.option>
                    </flux:select>

                    <flux:input wire:model="email" label="Email" type="email" placeholder="email@contoh.com" />

                    <flux:select wire:model="status" label="Status">
                        <flux:select.option value="Active">Aktif</flux:select.option>
                        <flux:select.option value="Inactive">Tidak Aktif</flux:select.option>
                    </flux:select>

                    <!-- Logo Upload -->
                    <div>
                        <flux:label>Logo/Foto</flux:label>
                        <input type="file" wire:model="uploadedLogo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-medium
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100
                                dark:file:bg-blue-900/20 dark:file:text-blue-400" />

                        @if ($uploadedLogo)
                            <div class="mt-2">
                                <img src="{{ $uploadedLogo->temporaryUrl() }}"
                                    class="h-16 w-16 object-cover rounded-lg">
                            </div>
                        @elseif ($logo)
                            <div class="mt-2">
                                <img src="{{ Storage::url($logo) }}" class="h-16 w-16 object-cover rounded-lg">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Detailed Information -->
                <div class="space-y-4">
                    <h4 class="font-medium text-gray-800 dark:text-white">Detail Informasi</h4>

                    <flux:input wire:model="NPWP" label="NPWP" placeholder="Nomor Pokok Wajib Pajak" />
                    <flux:input wire:model="KPP" label="KPP" placeholder="Kantor Pelayanan Pajak" />
                    <flux:input wire:model="EFIN" label="EFIN"
                        placeholder="Electronic Filing Identification Number" />

                    <flux:input wire:model="account_representative" label="Account Representative"
                        placeholder="Nama petugas pajak" />
                    <flux:input wire:model="ar_phone_number" label="No. Telp AR" placeholder="Nomor telepon AR" />

                    <flux:input wire:model="person_in_charge" label="Person in Charge" placeholder="Nama PIC" />

                    <flux:textarea wire:model="address" label="Alamat" placeholder="Alamat lengkap..."
                        rows="3" />
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showEditClientModal', false)">
                    Batal
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Perbarui Klien
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Client Detail Modal -->
    <flux:modal wire:model.self="showDetailModal" name="client-detail" class="max-w-4xl">
        @if ($clientDetail)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Client Logo/Avatar -->
                        @if ($clientDetail->logo)
                            <img src="{{ Storage::url($clientDetail->logo) }}" alt="{{ $clientDetail->name }}"
                                class="h-16 w-16 rounded-lg object-cover border border-gray-200 dark:border-zinc-700">
                        @else
                            <div
                                class="h-16 w-16 rounded-lg flex items-center justify-center
                                {{ $clientDetail->type === 'individual' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }}">
                                @if ($clientDetail->type === 'individual')
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 text-green-600 dark:text-green-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 text-purple-600 dark:text-purple-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                @endif
                            </div>
                        @endif

                        <div>
                            <flux:heading size="lg">{{ $clientDetail->name }}</flux:heading>
                            <div class="flex items-center space-x-2 mt-1">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $clientDetail->type === 'individual'
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                        : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                    {{ $clientDetail->type === 'individual' ? 'Individual' : 'Perusahaan' }}
                                </span>
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $clientDetail->status === 'Active'
                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                        : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                    {{ $clientDetail->status === 'Active' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Information Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contact Information -->
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Informasi Kontak</h4>
                        <div class="space-y-2 text-sm">
                            @if ($clientDetail->email)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">Email:</span>
                                    <span class="text-gray-800 dark:text-white">{{ $clientDetail->email }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->person_in_charge)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">PIC:</span>
                                    <span
                                        class="text-gray-800 dark:text-white">{{ $clientDetail->person_in_charge }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->address)
                                <div class="flex flex-col">
                                    <span class="text-gray-500 dark:text-zinc-400">Alamat:</span>
                                    <span
                                        class="text-gray-800 dark:text-white mt-1">{{ $clientDetail->address }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Tax Information -->
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Informasi Pajak</h4>
                        <div class="space-y-2 text-sm">
                            @if ($clientDetail->NPWP)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">NPWP:</span>
                                    <span
                                        class="text-gray-800 dark:text-white font-mono">{{ $clientDetail->NPWP }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->KPP)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">KPP:</span>
                                    <span class="text-gray-800 dark:text-white">{{ $clientDetail->KPP }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->EFIN)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">EFIN:</span>
                                    <span
                                        class="text-gray-800 dark:text-white font-mono">{{ $clientDetail->EFIN }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->account_representative)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">AR:</span>
                                    <span
                                        class="text-gray-800 dark:text-white">{{ $clientDetail->account_representative }}</span>
                                </div>
                            @endif
                            @if ($clientDetail->ar_phone_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-zinc-400">No. Telp AR:</span>
                                    <span
                                        class="text-gray-800 dark:text-white">{{ $clientDetail->ar_phone_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Relationships -->
                @if ($clientDetail->type === 'individual' && $clientDetail->ownedCompanies->count() > 0)
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Perusahaan yang Dimiliki</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($clientDetail->ownedCompanies as $company)
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-purple-600 dark:text-purple-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-gray-800 dark:text-white">{{ $company->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif ($clientDetail->type === 'company' && $clientDetail->owners->count() > 0)
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Pemilik Perusahaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach ($clientDetail->owners as $owner)
                                <div class="flex items-center space-x-2 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-green-600 dark:text-green-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-gray-800 dark:text-white">{{ $owner->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Invoice Summary -->
                @if ($clientDetail->invoices->count() > 0)
                    <div class="bg-gray-50 dark:bg-zinc-800 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Ringkasan Invoice</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div class="text-center">
                                <p class="text-gray-500 dark:text-zinc-400">Total Invoice</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                    {{ $clientDetail->invoices->count() }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500 dark:text-zinc-400">Total Amount</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                    {{ $this->formatCurrency($clientDetail->invoices->sum('total_amount')) }}
                                </p>
                            </div>
                            <div class="text-center">
                                <p class="text-gray-500 dark:text-zinc-400">Paid Amount</p>
                                <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                                    {{ $this->formatCurrency($clientDetail->invoices->where('status', 'paid')->sum('total_amount')) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost"
                        wire:click="openEditClientModal({{ $clientDetail->id }})">
                        Edit Klien
                    </flux:button>
                    <flux:button type="button" variant="outline"
                        wire:click="openRelationshipModal({{ $clientDetail->id }})">
                        Kelola Hubungan
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="$set('showDetailModal', false)">
                        Tutup
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Relationship Management Modal -->
    <flux:modal wire:model.self="showRelationshipModal" name="relationship" class="md:w-2xl">
        @if ($editingClient)
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Kelola Hubungan</flux:heading>
                    <flux:text class="mt-2">Atur hubungan kepemilikan untuk {{ $editingClient->name }}</flux:text>
                </div>

                @if ($editingClient->type === 'individual')
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-white mb-4">Perusahaan yang Dimiliki</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse ($availableCompanies as $company)
                                <label
                                    class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800">
                                    <input type="checkbox" wire:model="selectedCompanies"
                                        value="{{ $company['id'] }}"
                                        class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                    <div class="flex-1">
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ $company['name'] }}</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-zinc-400 text-center py-8">
                                    Belum ada perusahaan yang tersedia
                                </p>
                            @endforelse
                        </div>
                    </div>
                @else
                    <div>
                        <h4 class="font-medium text-gray-800 dark:text-white mb-4">Pemilik Perusahaan</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse ($availableOwners as $owner)
                                <label
                                    class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-800">
                                    <input type="checkbox" wire:model="selectedOwners" value="{{ $owner['id'] }}"
                                        class="rounded border-gray-300 dark:border-zinc-600 text-blue-600 focus:ring-blue-500 dark:bg-zinc-800">
                                    <div class="flex-1">
                                        <span
                                            class="text-sm font-medium text-gray-700 dark:text-zinc-300">{{ $owner['name'] }}</span>
                                    </div>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-zinc-400 text-center py-8">
                                    Belum ada individual yang tersedia
                                </p>
                            @endforelse
                        </div>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <flux:button type="button" variant="ghost" wire:click="$set('showRelationshipModal', false)">
                        Batal
                    </flux:button>
                    <flux:button type="button" variant="primary" wire:click="updateRelationships">
                        Simpan Hubungan
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>

    <!-- Bulk Action Modal -->
    <flux:modal wire:model.self="showBulkActionModal" name="bulk-action" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Aksi Massal</flux:heading>
                <flux:text class="mt-2">Pilih aksi untuk {{ count($selectedClients) }} klien yang dipilih
                </flux:text>
            </div>

            <flux:select wire:model="bulkAction" label="Pilih Aksi" placeholder="Pilih aksi...">
                <flux:select.option value="activate">Aktifkan Klien</flux:select.option>
                <flux:select.option value="deactivate">Nonaktifkan Klien</flux:select.option>
                <flux:select.option value="delete">Hapus Klien</flux:select.option>
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
                                <p>Aksi ini akan menghapus semua klien yang dipilih beserta data terkait (invoice,
                                    hubungan, dll) dan tidak dapat dikembalikan.</p>
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
                    {{ $bulkAction === 'activate' ? 'Aktifkan' : ($bulkAction === 'deactivate' ? 'Nonaktifkan' : 'Hapus') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model.self="showDeleteModal" name="delete-client" class="md:w-96">
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
                <flux:heading size="lg">Hapus Klien</flux:heading>
                <flux:text class="mt-2">
                    Apakah Anda yakin ingin menghapus klien
                    @if ($clientToDelete)
                        <strong>{{ $clientToDelete->name }}</strong>
                    @endif
                    ? Semua data terkait (invoice, hubungan, dll) akan ikut terhapus dan tidak dapat dikembalikan.
                </flux:text>
            </div>

            <div class="flex justify-end space-x-3">
                <flux:button type="button" variant="ghost" wire:click="$set('showDeleteModal', false)">
                    Batal
                </flux:button>
                <flux:button type="button" variant="danger" wire:click="deleteClient">
                    Ya, Hapus Klien
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Additional CSS for enhanced styling -->
    <style>
        /* Custom hover effects for client cards */
        .group:hover .group-hover\:scale-105 {
            transform: scale(1.05);
        }

        /* Custom scrollbar for relationship lists */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: rgb(243 244 246);
        }

        .dark .overflow-y-auto::-webkit-scrollbar-track {
            background: rgb(63 63 70);
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgb(156 163 175);
            border-radius: 3px;
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgb(113 113 122);
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgb(107 114 128);
        }

        .dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgb(161 161 170);
        }

        /* Enhanced file input styling */
        input[type="file"]::-webkit-file-upload-button {
            transition: all 0.2s ease-in-out;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            transform: translateY(-1px);
        }

        /* Custom animation for status badges */
        @keyframes pulse-green {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        @keyframes pulse-red {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .status-active {
            animation: pulse-green 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-inactive {
            animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Enhanced modal transitions */
        .modal-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Custom loading states */
        .loading-overlay {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
        }

        .dark .loading-overlay {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Enhanced grid hover effects */
        .client-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .client-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .dark .client-card:hover {
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        }

        /* Custom checkbox styling */
        input[type="checkbox"]:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7 7-.793-.793-3.5 3.5a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708L6.5 8.5l6.646-6.646a.5.5 0 0 0-.708-.708z'/%3e%3c/svg%3e");
        }

        /* Enhanced dropdown animations */
        .dropdown-content {
            animation: dropdown-appear 0.2s ease-out;
        }

        @keyframes dropdown-appear {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Custom progress indicators */
        .progress-ring {
            transition: stroke-dasharray 0.5s ease-in-out;
        }

        /* Enhanced button hover effects */
        .btn-hover-lift {
            transition: all 0.2s ease-in-out;
        }

        .btn-hover-lift:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .dark .btn-hover-lift:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Custom focus states */
        .focus-ring:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        .dark .focus-ring:focus {
            box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.5);
        }

        /* Enhanced table styling */
        .table-row {
            transition: background-color 0.2s ease-in-out;
        }

        .table-row:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        .dark .table-row:hover {
            background-color: rgba(96, 165, 250, 0.1);
        }

        /* Custom skeleton loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        .dark .skeleton {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 200% 100%;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Enhanced modal backdrop */
        .modal-backdrop {
            backdrop-filter: blur(8px);
            background: rgba(0, 0, 0, 0.5);
        }

        /* Custom tooltip styling */
        .tooltip {
            position: relative;
        }

        .tooltip:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 1000;
        }

        .tooltip:hover:before {
            opacity: 1;
        }

        /* Enhanced search input */
        .search-input {
            transition: all 0.3s ease-in-out;
        }

        .search-input:focus {
            transform: scale(1.02);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Custom notification styles */
        .notification-enter {
            animation: slide-in-right 0.3s ease-out;
        }

        .notification-exit {
            animation: slide-out-right 0.3s ease-in;
        }

        @keyframes slide-in-right {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out-right {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <!-- JavaScript for enhanced interactions -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced keyboard navigation
            document.addEventListener('keydown', function(e) {
                // Escape key to close modals
                if (e.key === 'Escape') {
                    // Close any open modals
                    const modals = document.querySelectorAll('[wire\\:model\\.self*="show"]');
                    modals.forEach(modal => {
                        if (modal.style.display !== 'none') {
                            // Trigger Livewire to close modal
                            const modalName = modal.getAttribute('wire:model.self');
                            if (modalName) {
                                window.Livewire.find(modal.closest('[wire\\:id]').getAttribute(
                                    'wire:id')).set(modalName, false);
                            }
                        }
                    });
                }

                // Ctrl/Cmd + K to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('input[wire\\:model\\.live="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }
            });

            // Enhanced file upload preview
            document.addEventListener('change', function(e) {
                if (e.target.type === 'file' && e.target.accept === 'image/*') {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('Ukuran file terlalu besar. Maksimal 2MB.');
                            e.target.value = '';
                            return;
                        }

                        // Validate file type
                        if (!file.type.startsWith('image/')) {
                            alert('File harus berupa gambar.');
                            e.target.value = '';
                            return;
                        }
                    }
                }
            });

            // Auto-save draft functionality (optional)
            let draftTimeout;
            const draftInputs = document.querySelectorAll(
                'input[wire\\:model], textarea[wire\\:model], select[wire\\:model]');

            draftInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(draftTimeout);
                    draftTimeout = setTimeout(() => {
                        // Save draft to localStorage (optional feature)
                        const formData = {};
                        draftInputs.forEach(inp => {
                            const model = inp.getAttribute('wire:model') || inp
                                .getAttribute('wire:model.live');
                            if (model && inp.value) {
                                formData[model] = inp.value;
                            }
                        });
                        localStorage.setItem('client_form_draft', JSON.stringify(formData));
                    }, 1000);
                });
            });

            // Enhanced loading states
            document.addEventListener('livewire:loading', function() {
                document.body.style.cursor = 'wait';
            });

            document.addEventListener('livewire:loaded', function() {
                document.body.style.cursor = 'default';
            });

            // Smooth scroll to validation errors
            document.addEventListener('livewire:updated', function() {
                const firstError = document.querySelector('.error, [class*="text-red"]');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            });

            // Enhanced tooltips
            function initTooltips() {
                const tooltipElements = document.querySelectorAll('[data-tooltip]');
                tooltipElements.forEach(el => {
                    el.classList.add('tooltip');
                });
            }

            // Initialize tooltips on page load and after Livewire updates
            initTooltips();
            document.addEventListener('livewire:updated', initTooltips);

            // Auto-refresh data every 5 minutes (optional)
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    // Refresh data only if page is visible
                    window.Livewire.emit('refreshData');
                }
            }, 5 * 60 * 1000);

            // Enhanced form validation feedback
            function validateForm(formElement) {
                const requiredFields = formElement.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500', 'shake');
                        isValid = false;

                        // Remove shake animation after it completes
                        setTimeout(() => {
                            field.classList.remove('shake');
                        }, 500);
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                return isValid;
            }

            // Add shake animation CSS
            const style = document.createElement('style');
            style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            .shake {
                animation: shake 0.5s ease-in-out;
            }
        `;
            document.head.appendChild(style);
        });
    </script>
</section>
