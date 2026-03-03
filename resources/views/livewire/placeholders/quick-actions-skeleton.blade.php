{{-- Skeleton placeholder untuk QuickActionsOverview component (chart full-width layout) --}}
<div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-4 lg:p-6 animate-pulse">
    {{-- Chart Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="h-6 bg-gray-200 dark:bg-dark-700 rounded w-44"></div>
        <div class="flex items-center gap-4">
            @foreach (range(1, 3) as $i)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-gray-200 dark:bg-dark-700 rounded-full"></div>
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-14"></div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Mini Stats 3-col --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        @foreach (range(1, 3) as $i)
            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-100 dark:border-dark-600">
                <div class="h-8 w-8 bg-gray-200 dark:bg-dark-600 rounded-lg flex-shrink-0"></div>
                <div class="space-y-1.5 flex-1 min-w-0">
                    <div class="h-3 bg-gray-200 dark:bg-dark-600 rounded w-12"></div>
                    <div class="h-4 bg-gray-200 dark:bg-dark-600 rounded w-20"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Chart Bars --}}
    <div class="h-[320px] lg:h-[380px] flex items-end gap-2 pb-6 pt-4">
        @foreach (range(1, 12) as $i)
            <div class="flex-1 flex gap-0.5 items-end">
                <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(30, 85) }}%"></div>
                <div class="flex-1 bg-gray-200 dark:bg-dark-700 rounded-t" style="height: {{ rand(20, 70) }}%"></div>
            </div>
        @endforeach
    </div>
</div>
