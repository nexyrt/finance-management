{{-- Skeleton placeholder untuk QuickActionsOverview component (2-column: bar chart + donut) --}}
<div class="space-y-6 animate-pulse">
    {{-- Mini Stats 3-col --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach (range(1, 3) as $i)
            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-100 dark:border-dark-600">
                <div class="h-8 w-8 bg-gray-200 dark:bg-dark-600 rounded-lg shrink-0"></div>
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="h-3 bg-gray-200 dark:bg-dark-600 rounded w-12"></div>
                    <div class="h-4 bg-gray-200 dark:bg-dark-600 rounded w-20"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts 2-column grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Bar Chart Skeleton (3/5) --}}
        <div class="lg:col-span-3 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded w-40"></div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-gray-200 dark:bg-dark-700 rounded-full"></div>
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-14"></div>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-3 h-3 bg-gray-200 dark:bg-dark-700 rounded-full"></div>
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-14"></div>
                    </div>
                </div>
            </div>
            <div class="h-[280px] flex items-end gap-2 pb-4 pt-2">
                @foreach (range(1, 12) as $i)
                    <div class="flex-1 flex gap-0.5 items-end">
                        <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(30, 85) }}%"></div>
                        <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(20, 70) }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Donut Chart Skeleton (2/5) --}}
        <div class="lg:col-span-2 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-4 lg:p-6">
            <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded w-36 mb-4"></div>
            <div class="flex items-center justify-center py-6">
                <div class="w-40 h-40 rounded-full border-20 border-gray-200 dark:border-dark-700"></div>
            </div>
            <div class="space-y-2 mt-4">
                @foreach (range(1, 4) as $i)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-gray-200 dark:bg-dark-700 rounded-full"></div>
                            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-20"></div>
                        </div>
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-10"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
