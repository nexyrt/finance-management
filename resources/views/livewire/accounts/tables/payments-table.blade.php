{{-- resources/views/livewire/accounts/tables/payments-table.blade.php --}}

<div class="space-y-6">
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row mt-2 gap-4">
        <div class="flex gap-3">
            <div wire:ignore>
                <x-date wire:model.live="dateRange" range placeholder="Select date range..." class="w-64" />
            </div>

            @if (!empty($dateRange) || $search)
                <x-button wire:click="clearFilters" color="secondary" outline icon="x-mark">
                    Clear
                </x-button>
            @endif
        </div>

        <x-input wire:model.live.debounce.300ms="search" placeholder="Search payments..." icon="magnifying-glass"
            class="flex-1" />
    </div>

    {{-- Table with Bulk Actions --}}
    <x-table :$headers :$sort :rows="$this->rows" selectable wire:model="selected" paginate loading>

        {{-- Invoice --}}
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

        {{-- Client --}}
        @interact('column_client', $row)
            <div class="flex items-center space-x-3">
                <div
                    class="w-8 h-8 bg-gradient-to-r from-primary-400 to-primary-600 rounded-full flex items-center justify-center">
                    <span class="text-white font-semibold text-xs">
                        {{ strtoupper(substr($row->invoice->client->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <div class="font-medium text-dark-900 dark:text-white">{{ $row->invoice->client->name }}</div>
                    @if ($row->invoice->client->type)
                        <div class="text-xs text-dark-500 dark:text-dark-400 capitalize">{{ $row->invoice->client->type }}
                        </div>
                    @endif
                </div>
            </div>
        @endinteract

        {{-- Date --}}
        @interact('column_payment_date', $row)
            <div class="text-sm">
                <div class="text-dark-900 dark:text-white">{{ $row->payment_date->format('d M Y') }}</div>
                <div class="text-xs text-dark-500 dark:text-dark-400">{{ $row->payment_date->diffForHumans() }}</div>
            </div>
        @endinteract

        {{-- Amount --}}
        @interact('column_amount', $row)
            <div class="text-right">
                <div class="font-semibold text-green-600 dark:text-green-400">
                    Rp {{ number_format($row->amount, 0, ',', '.') }}
                </div>
                @if ($row->reference_number)
                    <div class="text-xs text-dark-500 dark:text-dark-400 font-mono">{{ $row->reference_number }}</div>
                @endif
            </div>
        @endinteract

        {{-- Payment Method --}}
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

        {{-- Actions --}}
        @interact('column_action', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="trash" color="red" size="sm"
                    wire:click="deletePayment({{ $row->id }})" title="Delete" />
            </div>
        @endinteract
    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 px-6 py-4 min-w-96">
            <div class="flex items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-gray-50" x-text="`${show.length} selected`">
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Choose action</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-button wire:click="exportSelected" size="sm" color="green"
                        icon="document-arrow-down">Export</x-button>
                    <x-button wire:click="confirmBulkDelete" size="sm" color="red"
                        icon="trash">Delete</x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray"
                        icon="x-mark">Clear</x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Empty State --}}
    @if ($this->rows->isEmpty() && $selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="banknotes" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            @if ($search || !empty($dateRange))
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No payments found</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Try adjusting your filters</p>
                <x-button wire:click="clearFilters" color="primary" outline>Clear Filters</x-button>
            @else
                <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">No payments yet</h3>
                <p class="text-dark-600 dark:text-dark-400 mb-4">Record your first payment</p>
                <x-button wire:click="addPayment" color="primary" icon="plus">Add Payment</x-button>
            @endif
        </div>
    @endif

    @if (!$selectedAccountId)
        <div class="text-center py-12">
            <x-icon name="building-library" class="w-12 h-12 text-dark-400 mx-auto mb-3" />
            <h3 class="text-lg font-semibold text-dark-900 dark:text-white mb-2">Select an Account</h3>
            <p class="text-dark-600 dark:text-dark-400">Choose an account to view payments</p>
        </div>
    @endif
</div>
