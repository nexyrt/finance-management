<div class="space-y-6">
    {{-- Filters Section --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <x-date wire:model.live="dateRange" label="Periode" range placeholder="Pilih range tanggal..." />
            </div>

            <div class="lg:col-span-2">
                <x-select.styled wire:model.live="clientFilters" label="Klien" :options="$this->clients"
                    placeholder="Semua klien..." multiple searchable />
            </div>

            <div class="lg:col-span-1">
                <x-select.styled wire:model.live="categoryFilters" label="Kategori" :options="$this->incomeCategories"
                    placeholder="Semua kategori..." multiple searchable />
            </div>

            <div class="lg:col-span-1">
                <x-input wire:model.live.debounce.300ms="search" label="Cari" placeholder="Cari data..."
                    icon="magnifying-glass" />
            </div>
        </div>

        @php
            $activeFilters = collect([
                !empty($dateRange) && count($dateRange) >= 1,
                !empty($clientFilters),
                !empty($categoryFilters),
                $search,
            ])
                ->filter()
                ->count();
        @endphp

        @if ($activeFilters > 0)
            <div class="mt-4 pt-4 border-t border-secondary-200 dark:border-dark-600">
                <div class="flex items-center justify-between">
                    <x-badge text="{{ $activeFilters }} filter aktif" color="primary" size="sm" />
                    <div class="text-sm text-dark-500 dark:text-dark-400">
                        Menampilkan {{ $this->incomeData->count() }} dari {{ $this->incomeData->total() }} pemasukan
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Income Table --}}
    <div class="bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600 rounded-xl overflow-hidden">
        <div class="px-4 lg:px-6 py-4 border-b border-secondary-200 dark:border-dark-600">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50">Daftar Pemasukan</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Gabungan pembayaran invoice dan transaksi
                        langsung</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="export" color="green" icon="arrow-down-tray" size="sm" loading="export">
                        Export
                    </x-button>
                    <livewire:cash-flow.create-income @income-created="$refresh" />
                </div>
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div wire:loading.flex
            wire:target="dateRange,categoryFilters,clientFilters,search,sortBy,gotoPage,nextPage,previousPage,export"
            class="fixed inset-0 bg-white/75 dark:bg-dark-800/75 backdrop-blur-sm z-50 items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <x-icon name="arrow-path" class="w-8 h-8 text-primary-600 dark:text-primary-400 animate-spin" />
                <span class="text-sm font-medium text-dark-700 dark:text-dark-300">Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto relative">
            <table class="w-full" x-data="{
                selected: @entangle('selected').live,
                lastIndex: null,
                handleCheck(event, value, index) {
                    const checkbox = event.target;
            
                    if (event.shiftKey && this.lastIndex !== null) {
                        const start = Math.min(this.lastIndex, index);
                        const end = Math.max(this.lastIndex, index);
                        const checkboxes = document.querySelectorAll('[data-bulk-index]');
            
                        for (let i = start; i <= end; i++) {
                            const cb = checkboxes[i];
                            if (cb && checkbox.checked && !this.selected.includes(cb.value)) {
                                this.selected.push(cb.value);
                            }
                        }
                    }
            
                    this.lastIndex = index;
                }
            }">
                <thead class="bg-secondary-50 dark:bg-dark-700 border-b-2 border-secondary-200 dark:border-dark-600">
                    <tr>
                        <th class="px-4 py-3 text-left w-12"></th>
                        <th wire:click="sortBy('date')"
                            class="px-4 py-3 text-left text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 dark:hover:bg-dark-600 transition-colors">
                            <div class="flex items-center gap-2">
                                Tanggal
                                @if ($sort['column'] === 'date')
                                    <x-icon name="{{ $sort['direction'] === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="w-4 h-4 text-primary-600" />
                                @else
                                    <x-icon name="chevron-up-down" class="w-4 h-4 opacity-30" />
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider">
                            Sumber
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider">
                            Klien/Deskripsi
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th wire:click="sortBy('amount')"
                            class="px-4 py-3 text-right text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider cursor-pointer hover:bg-secondary-100 dark:hover:bg-dark-600 transition-colors">
                            <div class="flex items-center justify-end gap-2">
                                Jumlah
                                @if ($sort['column'] === 'amount')
                                    <x-icon name="{{ $sort['direction'] === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="w-4 h-4 text-primary-600" />
                                @else
                                    <x-icon name="chevron-up-down" class="w-4 h-4 opacity-30" />
                                @endif
                            </div>
                        </th>
                        <th
                            class="px-4 py-3 text-center text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-secondary-200 dark:divide-dark-600">
                    @forelse($this->incomeData as $index => $item)
                        <tr class="group hover:bg-gradient-to-r hover:from-secondary-50 hover:to-transparent dark:hover:from-dark-700 dark:hover:to-transparent transition-all duration-200 hover:shadow-sm"
                            wire:key="income-{{ $item->source_type }}-{{ $item->id }}">

                            <td class="px-4 py-5">
                                <input type="checkbox" value="{{ $item->source_type }}-{{ $item->id }}"
                                    wire:model.live="selected" data-bulk-index="{{ $index }}"
                                    @click="handleCheck($event, '{{ $item->source_type }}-{{ $item->id }}', {{ $index }})"
                                    class="rounded border-secondary-300 dark:border-dark-600 transition-all duration-200 hover:scale-110">
                            </td>

                            <td class="px-4 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-lg flex items-center justify-center">
                                        <x-icon name="calendar"
                                            class="w-5 h-5 text-secondary-600 dark:text-secondary-400" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                            {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-dark-500 dark:text-dark-400">
                                            {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-5 whitespace-nowrap">
                                @if ($item->source_type === 'payment')
                                    <div class="space-y-2">
                                        <x-badge text="Payment" color="primary" icon="document-text" size="sm" />
                                        @if ($item->invoice_number)
                                            <div class="flex items-center gap-1.5">
                                                <x-icon name="hashtag" class="w-3 h-3 text-primary-500" />
                                                <span
                                                    class="text-xs text-primary-600 dark:text-primary-400 font-mono font-semibold">
                                                    {{ $item->invoice_number }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        <x-badge text="Direct Income" color="green" icon="arrow-trending-up"
                                            size="sm" />
                                        <div class="flex items-center gap-1.5">
                                            <x-icon name="building-library" class="w-3 h-3 text-green-500" />
                                            <span class="text-xs text-green-600 dark:text-green-400 font-medium">
                                                {{ $item->bank_name }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-5">
                                <div class="max-w-xs">
                                    @if ($item->source_type === 'payment')
                                        <div class="flex items-center gap-2 mb-1">
                                            <div
                                                class="h-8 w-8 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/30 dark:to-primary-800/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <x-icon name="user"
                                                    class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                            </div>
                                            <div class="font-semibold text-dark-900 dark:text-dark-50 truncate">
                                                {{ $item->client_name }}
                                            </div>
                                        </div>
                                        @if ($item->reference_number)
                                            <div class="flex items-center gap-1.5 ml-10">
                                                <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                                                <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                                                    {{ $item->reference_number }}
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="space-y-1">
                                            <div
                                                class="text-sm font-medium text-dark-900 dark:text-dark-50 line-clamp-2">
                                                {{ $item->description ?? '-' }}
                                            </div>
                                            @if ($item->reference_number)
                                                <div class="flex items-center gap-1.5">
                                                    <x-icon name="document-duplicate" class="w-3 h-3 text-dark-400" />
                                                    <span class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                                                        {{ $item->reference_number }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-5 whitespace-nowrap">
                                @if ($item->category_label)
                                    <div
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                                        <x-icon name="tag"
                                            class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                                        <span class="text-xs font-medium text-purple-700 dark:text-purple-300">
                                            {{ $item->category_label }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-dark-400 dark:text-dark-500 italic">Tidak ada</span>
                                @endif
                            </td>

                            <td class="px-4 py-5 whitespace-nowrap text-right">
                                <div class="inline-flex flex-col items-end gap-0.5">
                                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($item->amount, 0, ',', '.') }}
                                    </div>
                                    @if ($item->attachment_path)
                                        <div
                                            class="flex items-center gap-1 text-xs text-primary-600 dark:text-primary-400">
                                            <x-icon name="paper-clip" class="w-3 h-3" />
                                            <span class="font-medium">Ada lampiran</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-5 whitespace-nowrap">
                                <div
                                    class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @if ($item->attachment_path)
                                        <x-button.circle icon="paper-clip" color="primary" size="sm"
                                            wire:click="viewAttachment('{{ $item->source_type }}', {{ $item->id }})"
                                            loading="viewAttachment('{{ $item->source_type }}', {{ $item->id }})"
                                            title="Lihat Lampiran" />
                                    @endif

                                    @if ($item->source_type === 'payment')
                                        <x-button.circle icon="pencil" color="green" size="sm"
                                            wire:click="editPayment({{ $item->id }})"
                                            loading="editPayment({{ $item->id }})" title="Edit Payment" />
                                    @endif

                                    <x-button.circle icon="trash" color="red" size="sm"
                                        wire:click="deleteItem('{{ $item->source_type }}', {{ $item->id }})"
                                        loading="deleteItem('{{ $item->source_type }}', {{ $item->id }})"
                                        title="Hapus" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="h-20 w-20 bg-gradient-to-br from-secondary-100 to-secondary-200 dark:from-dark-700 dark:to-dark-600 rounded-full flex items-center justify-center mb-4">
                                        <x-icon name="inbox"
                                            class="w-10 h-10 text-secondary-400 dark:text-secondary-500" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-2">
                                        Tidak ada data pemasukan
                                    </h3>
                                    <p class="text-sm text-dark-600 dark:text-dark-400">
                                        @if ($activeFilters > 0)
                                            Coba ubah filter untuk melihat data lain
                                        @else
                                            Belum ada pembayaran atau transaksi pemasukan
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->incomeData->hasPages())
            <div
                class="px-4 lg:px-6 py-4 border-t border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-700/50">
                {{ $this->incomeData->links() }}
            </div>
        @endif
    </div>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-secondary-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="h-10 w-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} item dipilih`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">Pilih aksi untuk item yang dipilih</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="exportSelected" size="sm" color="green" icon="arrow-down-tray"
                        loading="exportSelected" class="whitespace-nowrap">
                        Export
                    </x-button>
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash"
                        loading="executeBulkDelete" class="whitespace-nowrap">
                        Hapus
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="secondary" icon="x-mark"
                        class="whitespace-nowrap">
                        Batal
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Child Components --}}
    <livewire:payments.edit @payment-updated="$refresh" />
    <livewire:payments.delete @payment-deleted="$refresh" />
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>
