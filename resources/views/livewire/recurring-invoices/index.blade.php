<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                Recurring Invoices
            </h1>
            <p class="text-dark-600 dark:text-dark-400 text-lg">
                Automate your billing process with smart templates
            </p>
        </div>

        <!-- Key Metrics -->
        <div
            class="flex gap-6 bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-zinc-200 dark:border-dark-600 p-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ number_format($this->activeTemplatesCount, 0, ',', '.') }}
                </div>
                <div class="text-sm text-dark-600 dark:text-dark-400">Active Templates</div>
            </div>
            <div class="w-px bg-zinc-200 dark:bg-dark-600"></div>
            <div class="text-center">
                <div class="text-xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($this->totalProjectedRevenue, 0, ',', '.') }}
                </div>
                <div class="text-sm text-dark-600 dark:text-dark-400">Projected {{ now()->year }}</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <x-tab selected="Templates">
        <x-tab.items tab="Templates">
            <x-slot:right>
                <x-icon name="document-text" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.templates-tab />
        </x-tab.items>
        <x-tab.items tab="Monthly">
            <x-slot:right>
                <x-icon name="calendar" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.monthly-tab />
        </x-tab.items>
        <x-tab.items tab="Analytics">
            <x-slot:right>
                <x-icon name="chart-bar" class="w-5 h-5" />
            </x-slot:right>
            <livewire:recurring-invoices.analytics-tab />
        </x-tab.items>
    </x-tab>

    <!-- Modal Components -->
    <livewire:recurring-invoices.view-template />
</div>
