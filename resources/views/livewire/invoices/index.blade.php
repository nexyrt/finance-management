{{-- resources/views/livewire/invoices/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Manajemen Invoice
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Kelola dan lacak semua invoice Anda di sini.
            </p>
        </div>
        {{-- <livewire:invoices.create @invoice-created="$refresh" /> --}}
        <x-button size="sm" href="{{ route('invoices.create') }}" wire:navigate title="Create" text="Create Invoice"
            prefix="true">
            <x-slot:left>
                <x-icon name="plus" class="w-4 h-4" />
            </x-slot:left>
        </x-button>
    </div>

    {{-- Stats Cards (Synced with Listing filters) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="chart-bar" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($this->stats['total_revenue'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total COSS (Cost of Services Sales)</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->stats['total_cogs'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Total Profit</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->stats['total_profit'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-green-500 dark:text-green-400">
                        {{ number_format($this->stats['profit_margin'], 1) }}% margin
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Outstanding Profit</p>
                    <p class="text-xl font-bold text-orange-600 dark:text-orange-400">
                        Rp {{ number_format($this->stats['outstanding_profit'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-orange-500 dark:text-orange-400">
                        From Rp {{ number_format($this->stats['paid_profit'], 0, ',', '.') }} revenue
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoices Table Component --}}
    <livewire:invoices.listing @invoice-sent="$refresh" @invoice-deleted="$refresh" />

    {{-- Child Components --}}
    <livewire:invoices.show />
    <livewire:invoices.delete />
    <livewire:payments.create />
    <livewire:payments.edit />
</div>
