{{-- Skeleton placeholder untuk QuickActionsOverview component --}}
<div class="grid grid-cols-1 lg:grid-cols-7 gap-4 lg:gap-6 animate-pulse">
    {{-- Quick Actions Card - 2 cols --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-4 lg:p-6 h-full">
            <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded w-32 mb-4"></div>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-3">
                @foreach (range(1, 3) as $i)
                    <div class="h-16 bg-gray-200 dark:bg-dark-700 rounded-xl"></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Chart Area - 5 cols --}}
    <div class="lg:col-span-5">
        <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-4 lg:p-6 h-full min-h-[400px] flex flex-col">
            {{-- Chart Header --}}
            <div class="flex items-center justify-between mb-4">
                <div class="h-6 bg-gray-200 dark:bg-dark-700 rounded w-44"></div>
                <div class="flex items-center gap-4">
                    @foreach (range(1, 3) as $i)
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-gray-200 dark:bg-dark-700 rounded-full"></div>
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-16"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Chart Placeholder --}}
            <div class="flex-1 flex items-end gap-2 pb-8 pt-4">
                @foreach (range(1, 12) as $i)
                    <div class="flex-1 flex gap-1 items-end">
                        <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(30, 90) }}%"></div>
                        <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(20, 70) }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
