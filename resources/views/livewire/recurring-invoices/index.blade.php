<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Recurring Invoices
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Automate your billing process with smart templates
                </p>
            </div>

            <!-- Key Metrics -->
            <div class="flex gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $this->activeTemplatesCount }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Active Templates</div>
                </div>
                <div class="w-px bg-gray-200 dark:bg-gray-600"></div>
                <div class="text-center">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">
                        Rp {{ number_format($this->totalProjectedRevenue / 1000000, 1) }}M
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Projected {{ now()->year }}</div>
                </div>
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
    <livewire:recurring-invoices.edit-template />
    <livewire:recurring-invoices.view-template />
</div>
