<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Cash Flow Management
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                Monitor and manage all your financial transactions
            </p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <x-tab selected="Overview">
        <x-tab.items tab="Overview">
            <x-slot:right>
                <x-icon name="chart-bar" class="w-5 h-5" />
            </x-slot:right>
            <livewire:cash-flow.overview-tab />
        </x-tab.items>

        <x-tab.items tab="Income">
            <x-slot:right>
                <x-icon name="arrow-trending-up" class="w-5 h-5" />
            </x-slot:right>
            <livewire:cash-flow.income-tab />
        </x-tab.items>

        <x-tab.items tab="Expenses">
            <x-slot:right>
                <x-icon name="arrow-trending-down" class="w-5 h-5" />
            </x-slot:right>
            <livewire:cash-flow.expenses-tab />
        </x-tab.items>

        <x-tab.items tab="Transfers">
            <x-slot:right>
                <x-icon name="arrow-path" class="w-5 h-5" />
            </x-slot:right>
            <livewire:cash-flow.transfers-tab />
        </x-tab.items>

        {{-- <x-tab.items tab="Adjustments">
            <x-slot:right>
                <x-icon name="adjustments-horizontal" class="w-5 h-5" />
            </x-slot:right>
            <livewire:cash-flow.adjustments-tab />
        </x-tab.items> --}}
    </x-tab>

    <livewire:cash-flow.attachment-viewer />
</div>
