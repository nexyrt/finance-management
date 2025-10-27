<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Reimbursement</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Kelola pengajuan reimbursement karyawan</p>
        </div>
        @can('create reimbursements')
            <livewire:reimbursements.create @created="$refresh" />
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Pengajuan --}}
        <x-card
            class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border-blue-200 dark:border-blue-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">Total Pengajuan</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $this->stats['total'] }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-500 rounded-xl flex items-center justify-center">
                    <x-icon name="document-text" class="w-6 h-6 text-white" />
                </div>
            </div>
        </x-card>

        {{-- Pending Review --}}
        <x-card
            class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-yellow-200 dark:border-yellow-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 dark:text-yellow-400">Pending Review</p>
                    <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100">{{ $this->stats['pending'] }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-500 rounded-xl flex items-center justify-center">
                    <x-icon name="clock" class="w-6 h-6 text-white" />
                </div>
            </div>
        </x-card>

        {{-- Approved --}}
        <x-card
            class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border-green-200 dark:border-green-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 dark:text-green-400">Disetujui</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">{{ $this->stats['approved'] }}</p>
                </div>
                <div class="h-12 w-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <x-icon name="check-circle" class="w-6 h-6 text-white" />
                </div>
            </div>
        </x-card>

        {{-- Total Amount --}}
        <x-card
            class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border-purple-200 dark:border-purple-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 dark:text-purple-400">Total Nilai</p>
                    <p class="text-lg font-bold text-purple-900 dark:text-purple-100">
                        Rp {{ number_format($this->stats['total_amount'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="h-12 w-12 bg-purple-500 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-white" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- View Mode Tabs --}}
    <x-card class="p-0">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-2 px-4" aria-label="Tabs">
                <button wire:click="setViewMode('all')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'all'
                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Semua
                </button>
                <button wire:click="setViewMode('my_requests')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'my_requests'
                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Pengajuan Saya
                </button>
                @can('approve reimbursements')
                    <button wire:click="setViewMode('pending')"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'pending'
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Perlu Review
                        @if ($this->stats['pending'] > 0)
                            <span
                                class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                {{ $this->stats['pending'] }}
                            </span>
                        @endif
                    </button>
                    <button wire:click="setViewMode('approved')"
                        class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'approved'
                            ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300' }}">
                        Menunggu Pembayaran
                        @if ($this->stats['approved'] > 0)
                            <span
                                class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                {{ $this->stats['approved'] }}
                            </span>
                        @endif
                    </button>
                @endcan
            </nav>
        </div>

        {{-- Additional Filters --}}
        <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <x-select.styled wire:model.live="statusFilter" :options="[
                    ['label' => 'Semua Status', 'value' => ''],
                    ['label' => 'Draft', 'value' => 'draft'],
                    ['label' => 'Pending', 'value' => 'pending'],
                    ['label' => 'Approved', 'value' => 'approved'],
                    ['label' => 'Rejected', 'value' => 'rejected'],
                    ['label' => 'Paid', 'value' => 'paid'],
                ]" placeholder="Filter Status" />

                <x-select.styled wire:model.live="categoryFilter" :options="array_merge(
                    [['label' => 'Semua Kategori', 'value' => '']],
                    \App\Models\Reimbursement::categories(),
                )" placeholder="Filter Kategori" />

                <x-date wire:model.live="dateFrom" label="Dari Tanggal" placeholder="Pilih tanggal awal" />

                <x-date wire:model.live="dateTo" label="Sampai Tanggal" placeholder="Pilih tanggal akhir" />
            </div>

            @if ($statusFilter || $categoryFilter || $dateFrom || $dateTo)
                <div class="mt-3">
                    <x-button wire:click="clearFilters" size="sm" color="gray" icon="x-mark">
                        Clear Filters
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Table --}}
        <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate filter loading>
            {{-- Title with Description --}}
            @interact('column_title', $row)
                <div>
                    <div class="font-medium text-gray-900 dark:text-white">{{ $row->title }}</div>
                    @if ($row->description)
                        <div class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $row->description }}</div>
                    @endif
                </div>
            @endinteract

            {{-- User with Avatar --}}
            @interact('column_user', $row)
                <div class="flex items-center space-x-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-xs">
                            {{ $row->user->initials() }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-900 dark:text-white">{{ $row->user->name }}</div>
                </div>
            @endinteract

            {{-- Amount --}}
            @interact('column_amount', $row)
                <div class="font-semibold text-gray-900 dark:text-white">
                    {{ $row->formatted_amount }}
                </div>
            @endinteract

            {{-- Category Badge --}}
            @interact('column_category', $row)
                <x-badge :color="match ($row->category) {
                    'transport' => 'blue',
                    'meals' => 'green',
                    'office_supplies' => 'purple',
                    'communication' => 'yellow',
                    'accommodation' => 'pink',
                    'medical' => 'red',
                    default => 'gray',
                }" :text="$row->category_label" />
            @endinteract

            {{-- Date --}}
            @interact('column_expense_date', $row)
                <div class="text-sm">
                    <div class="text-gray-900 dark:text-white">{{ $row->expense_date->format('d M Y') }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row->expense_date->diffForHumans() }}</div>
                </div>
            @endinteract

            {{-- Status Badge --}}
            @interact('column_status', $row)
                <x-badge :color="$row->status_badge_color" :text="$row->status_label" />
            @endinteract

            {{-- Actions --}}
            @interact('column_action', $row)
                <div class="flex items-center gap-1">
                    {{-- View Detail --}}
                    <x-button.circle icon="eye" color="gray" size="sm"
                        wire:click="$dispatch('show::reimbursement', { reimbursement: '{{ $row->id }}' })"
                        title="Detail" />

                    {{-- Edit (draft/rejected, own request) --}}
                    @if ($row->canEdit() && $row->user_id === auth()->id())
                        <x-button.circle icon="pencil" color="blue" size="sm"
                            wire:click="$dispatch('load::reimbursement', { reimbursement: '{{ $row->id }}' })"
                            title="Edit" />
                    @endif

                    {{-- Review (pending, finance) --}}
                    @can('approve reimbursements')
                        @if ($row->canReview())
                            <x-button.circle icon="clipboard-document-check" color="yellow" size="sm"
                                wire:click="$dispatch('review::reimbursement', { reimbursement: '{{ $row->id }}' })"
                                title="Review" />
                        @endif
                    @endcan

                    {{-- Payment (approved, finance) --}}
                    @can('pay reimbursements')
                        @if ($row->canPay())
                            <x-button.circle icon="banknotes" color="green" size="sm"
                                wire:click="$dispatch('pay::reimbursement', { reimbursement: '{{ $row->id }}' })"
                                title="Bayar" />
                        @endif
                    @endcan

                    {{-- Delete (draft, own request) --}}
                    @if ($row->canDelete() && $row->user_id === auth()->id())
                        <livewire:reimbursements.delete :reimbursement="$row" :key="uniqid()" @deleted="$refresh" />
                    @endif
                </div>
            @endinteract
        </x-table>
    </x-card>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                {{-- Selection Info --}}
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-50"
                            x-text="`${show.length} pengajuan dipilih`"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pilih aksi untuk pengajuan yang dipilih
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end flex-wrap">
                    {{-- Finance Actions --}}
                    @can('approve reimbursements')
                        @if ($viewMode === 'pending')
                            <x-button wire:click="confirmBulkApprove" size="sm" color="green" icon="check"
                                class="whitespace-nowrap">
                                Setujui
                            </x-button>
                            <x-button wire:click="confirmBulkReject" size="sm" color="red" icon="x-mark"
                                class="whitespace-nowrap">
                                Tolak
                            </x-button>
                        @endif
                    @endcan

                    {{-- Staff Actions --}}
                    @if ($viewMode === 'my_requests')
                        <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash"
                            class="whitespace-nowrap">
                            Hapus
                        </x-button>
                    @endif

                    {{-- Common Actions --}}
                    <x-button wire:click="exportSelected" size="sm" color="blue" icon="document-arrow-down"
                        loading="exportSelected" class="whitespace-nowrap">
                        Export
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
    <livewire:reimbursements.update @updated="$refresh" />
    <livewire:reimbursements.show />

    @can('approve reimbursements')
        <livewire:reimbursements.review @reviewed="$refresh" />
    @endcan

    @can('pay reimbursements')
        <livewire:reimbursements.payment @paid="$refresh" />
    @endcan
</div>
