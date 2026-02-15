<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <h1
                class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-600 to-blue-700 dark:from-white dark:via-blue-300 dark:to-blue-200 bg-clip-text text-transparent">
                {{ __('pages.all_transactions') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-base sm:text-lg">
                {{ __('pages.manage_all_transactions_from_all_accounts') }}
            </p>
        </div>
        <div class="flex gap-3">
            <x-button wire:click="createTransaction(1)" loading="createTransaction" color="blue" icon="plus">
                {{ __('pages.add_transaction') }}
            </x-button>
            <x-button wire:click="openTransfer" loading="openTransfer" color="blue" icon="arrow-path">
                {{ __('pages.transfer_funds') }}
            </x-button>
        </div>
    </div>

    {{-- Stats Cards (Synced with Listing filters) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-down" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_income') }}</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->stats['total_income'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="arrow-up" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_expense') }}</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        Rp {{ number_format($this->stats['total_expense'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 border border-zinc-200 dark:border-dark-600 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-icon name="clipboard-document-list" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.total_transactions') }}</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($this->stats['total_transactions'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Transactions Table Component --}}
    <livewire:transactions.listing @transaction-deleted="$refresh" />

    {{-- Additional Components --}}
    <livewire:transactions.transfer @transfer-completed="$refresh" />
    <livewire:transactions.create @transaction-created="$refresh" />
    <livewire:transactions.inline-category-create />
</div>
