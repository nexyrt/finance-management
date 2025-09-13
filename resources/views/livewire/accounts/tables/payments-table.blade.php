{{-- resources/views/livewire/accounts/tables/payments-table.blade.php --}}

<div x-data="{}" @reinit-alpine.window="$nextTick(() => Alpine.initTree($el))" class="space-y-4">
    {{-- Internal Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex gap-3">
            <x-date wire:model.live="dateRange" range placeholder="Select date range..." class="w-64" />

            @if (!empty($dateRange) || $search)
                <x-button wire:click="clearFilters" loading="clearFilters" icon="x-mark" color="secondary" outline>
                    Clear
                </x-button>
            @endif
        </div>

        <x-input wire:model.live.debounce.300ms="search" placeholder="Search payments..." icon="magnifying-glass"
            class="flex-1" />
    </div>

    {{-- Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <x-button wire:click="addPayment" loading="addPayment" color="primary" icon="plus" size="sm">
                Add Payment
            </x-button>

            @if (!empty($selected))
                <x-badge color="blue" :text="count($selected) . ' selected'" />
            @endif
        </div>

        @if (!empty($selected))
            <div class="flex items-center gap-2">
                <x-button wire:click="exportSelected" loading="exportSelected" color="green" icon="document-arrow-down"
                    size="sm">
                    Export
                </x-button>

                <x-button wire:click="confirmBulkDelete" loading="confirmBulkDelete" color="red" icon="trash"
                    size="sm">
                    Delete Selected
                </x-button>
            </div>
        @endif
    </div>

    {{-- Table --}}
    <div wire:loading.class="opacity-50" wire:target="search,dateRange">
        <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate loading>

            {{-- Invoice Column --}}
            @interact('column_invoice', $row)
                <div class="max-w-xs">
                    <div class="font-medium text-dark-900 dark:text-white">
                        {{ $row->invoice->invoice_number }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        Due: {{ $row->invoice->due_date->format('d M Y') }}
                    </div>
                </div>
            @endinteract

            {{-- Client Column --}}
            @interact('column_client', $row)
                <div class="flex items-center space-x-3">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-semibold text-xs">
                            {{ strtoupper(substr($row->invoice->client->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <div class="font-medium text-dark-900 dark:text-white">
                            {{ $row->invoice->client->name }}
                        </div>
                        @if ($row->invoice->client->type)
                            <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">
                                {{ $row->invoice->client->type }}
                            </div>
                        @endif
                    </div>
                </div>
            @endinteract

            {{-- Date Column --}}
            @interact('column_payment_date', $row)
                <div class="text-sm">
                    <div class="text-dark-900 dark:text-white">
                        {{ $row->payment_date->format('d M Y') }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400">
                        {{ $row->payment_date->diffForHumans() }}
                    </div>
                </div>
            @endinteract

            {{-- Amount Column --}}
            @interact('column_amount', $row)
                <div class="text-right">
                    <div class="font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($row->amount, 0, ',', '.') }}
                    </div>
                    @if ($row->reference_number)
                        <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">
                            {{ $row->reference_number }}
                        </div>
                    @endif
                </div>
            @endinteract

            {{-- Payment Method Column --}}
            @interact('column_payment_method', $row)
                <x-badge :color="match ($row->payment_method) {
                    'cash' => 'green',
                    'bank_transfer' => 'blue',
                    default => 'gray',
                }" :text="match ($row->payment_method) {
                    'cash' => 'Cash',
                    'bank_transfer' => 'Bank Transfer',
                    default => ucfirst(str_replace('_', ' ', $row->payment_method)),
                }" />
            @endinteract

            {{-- Actions Column --}}
            @interact('column_action', $row)
                <div class="flex items-center gap-1">
                    <x-button.circle icon="eye" color="gray" size="sm"
                        wire:click="viewPayment({{ $row->id }})" title="View" />

                    <x-button.circle icon="pencil" color="blue" size="sm"
                        wire:click="editPayment({{ $row->id }})" title="Edit" />

                    <x-button.circle icon="trash" color="red" size="sm"
                        wire:click="deletePayment({{ $row->id }})" title="Delete" :key="uniqid()" />
                </div>
            @endinteract
        </x-table>
    </div>

    {{-- Empty State --}}
    @if ($this->rows->isEmpty() && $selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="banknotes" class="w-12 h-12 text-dark-400 mx-auto mb-3" />

            @if ($search || !empty($dateRange))
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No payments found</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Try adjusting your search criteria</p>
                <x-button wire:click="clearFilters" color="primary" outline>Clear Filters</x-button>
            @else
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No payments yet</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Start by recording your first payment</p>
                <x-button wire:click="addPayment" color="primary" icon="plus">Add Payment</x-button>
            @endif
        </div>
    @endif

    @if (!$selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="building-library" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">Select an Account</h3>
            <p class="text-dark-600 dark:text-dark-400">Choose an account to view its payments</p>
        </div>
    @endif
</div>
