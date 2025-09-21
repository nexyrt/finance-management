<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex flex-col sm:flex-row gap-4 lg:flex-1">
            <div class="w-full sm:w-48">
                <x-select.styled wire:model.live="account_id" label="Bank Account" :options="$this->accounts
                    ->map(
                        fn($account) => [
                            'label' => $account->account_name,
                            'value' => $account->id,
                        ],
                    )
                    ->prepend(['label' => 'Semua Rekening', 'value' => ''])
                    ->toArray()"
                    placeholder="Filter rekening..." />
            </div>

            <div class="w-full sm:w-48">
                <x-select.styled wire:model.live="transaction_type" label="Transaction Type" :options="[
                    ['label' => 'Semua Jenis', 'value' => ''],
                    ['label' => 'Pemasukan', 'value' => 'credit'],
                    ['label' => 'Pengeluaran', 'value' => 'debit'],
                ]"
                    placeholder="Filter jenis..." />
            </div>

            <div class="w-full sm:w-48">
                <x-date month-year-only wire:model.live="selected_month" label="Bulan" placeholder="Pilih bulan..." />
            </div>

            <div class="w-full sm:w-56">
                <x-date range wire:model.live="date_range" label="Range Tanggal" placeholder="Pilih range..." />
            </div>

            @if ($account_id || $transaction_type || $search || $selected_month || $date_range)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" size="sm"
                    class="h-[36px] whitespace-nowrap">
                    Clear
                </x-button>
            @endif
        </div>

        <div class="w-full lg:w-80">
            <x-input wire:model.live.debounce.300ms="search" label="Search" placeholder="Cari transaksi..."
                icon="magnifying-glass" />
        </div>
    </div>

    {{-- Table --}}
    <x-table :$headers :$sort :rows="$this->transactions" selectable wire:model="selected" paginate loading>

        {{-- Transaction Description with Icon --}}
        @interact('column_description', $row)
            <div class="flex items-center gap-3">
                <div
                    class="h-10 w-10 {{ $row->transaction_type === 'credit' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-lg flex items-center justify-center">
                    <x-icon name="{{ $row->transaction_type === 'credit' ? 'arrow-down' : 'arrow-up' }}"
                        class="w-5 h-5 {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}" />
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-gray-50">{{ $row->description ?: 'No description' }}</p>
                    @if ($row->reference_number)
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $row->reference_number }}</p>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Bank Account --}}
        @interact('column_bank_account_id', $row)
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-50">{{ $row->bankAccount->account_name }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $row->bankAccount->bank_name }}</p>
            </div>
        @endinteract

        {{-- Transaction Date --}}
        @interact('column_transaction_date', $row)
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-50">
                    {{ $row->transaction_date->format('d M Y') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $row->created_at->format('H:i') }}
                </p>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <p
                    class="font-bold {{ $row->transaction_type === 'credit' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    {{ $row->transaction_type === 'credit' ? '+' : '-' }}Rp {{ number_format($row->amount, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $row->transaction_type === 'credit' ? 'Pemasukan' : 'Pengeluaran' }}
                </p>
            </div>
        @endinteract

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex justify-center gap-1">
                @if ($row->attachment_path)
                    <x-button.circle wire:click="viewAttachment({{ $row->id }})" color="blue" icon="paper-clip"
                        size="sm" />
                @endif
                <x-button.circle wire:click="deleteTransaction({{ $row->id }})"
                    loading="deleteTransaction({{ $row->id }})" color="red" icon="trash" size="sm" />
            </div>
        @endinteract
    </x-table>

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
                            x-text="`${show.length} transaksi dipilih`"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pilih aksi untuk transaksi yang dipilih
                        </div>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red" icon="trash"
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

    {{-- Attachment Modal dengan Zoom --}}
    <x-modal title="Bukti Transaksi" wire="attachmentModal" center size="4xl">
        @if ($selectedTransaction && $selectedTransaction->attachment_path)
            <div class="text-center space-y-4">
                <h3 class="text-lg font-semibold">{{ $selectedTransaction->description }}</h3>
                <p class="text-sm text-gray-500">{{ $selectedTransaction->attachment_name ?? 'File attachment' }}</p>

                @php
                    $extension = pathinfo($selectedTransaction->attachment_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                    $isPdf = strtolower($extension) === 'pdf';
                @endphp

                @if ($isImage)
                    <div x-data="{ scale: 1, isDragging: false, startX: 0, startY: 0, translateX: 0, translateY: 0 }" class="relative overflow-hidden bg-gray-50 rounded-lg border"
                        style="height: 500px;">

                        {{-- Zoom Controls --}}
                        <div class="absolute top-2 right-2 z-10 flex gap-2">
                            <x-button.circle @click="scale = Math.min(scale + 0.2, 3)" icon="plus" size="sm"
                                color="white" />
                            <x-button.circle @click="scale = Math.max(scale - 0.2, 0.5)" icon="minus" size="sm"
                                color="white" />
                            <x-button.circle @click="scale = 1; translateX = 0; translateY = 0" icon="arrow-path"
                                size="sm" color="white" />
                        </div>

                        {{-- Image with Pan & Zoom --}}
                        <img src="{{ asset('storage/' . $selectedTransaction->attachment_path) }}"
                            alt="Bukti Transaksi"
                            class="absolute inset-0 w-full h-full object-contain cursor-move select-none"
                            :style="`transform: scale(${scale}) translate(${translateX}px, ${translateY}px); transition: ${isDragging ? 'none' : 'transform 0.2s'}`"
                            @mousedown="isDragging = true; startX = $event.clientX - translateX; startY = $event.clientY - translateY"
                            @mousemove="if (isDragging && scale > 1) { translateX = $event.clientX - startX; translateY = $event.clientY - startY }"
                            @mouseup="isDragging = false" @mouseleave="isDragging = false"
                            @wheel.prevent="
                            const delta = $event.deltaY > 0 ? -0.1 : 0.1;
                            scale = Math.min(Math.max(scale + delta, 0.5), 3);
                            if (scale === 1) { translateX = 0; translateY = 0; }
                         ">

                        {{-- Zoom Level Indicator --}}
                        <div
                            class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                            <span x-text="`${Math.round(scale * 100)}%`"></span>
                        </div>
                    </div>
                @elseif($isPdf)
                    <embed src="{{ asset('storage/' . $selectedTransaction->attachment_path) }}"
                        type="application/pdf" width="100%" height="600px" class="rounded-lg border">
                @else
                    <div class="text-center py-12">
                        <x-icon name="document" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                        <p class="text-gray-500">Preview tidak tersedia untuk file ini</p>
                        <a href="{{ asset('storage/' . $selectedTransaction->attachment_path) }}" target="_blank"
                            class="text-blue-600 hover:text-blue-800 underline">
                            Download file
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <x-slot:footer>
            <x-button wire:click="$set('attachmentModal', false)" color="gray">Tutup</x-button>
        </x-slot:footer>
    </x-modal>

    {{-- Components --}}
    <livewire:transactions.delete @transaction-deleted="$refresh" />
</div>