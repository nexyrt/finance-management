{{-- Skeleton placeholder untuk CashFlow components (Expenses, Income, Transfers) --}}
<div class="space-y-6 animate-pulse">
    {{-- Header Skeleton --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-2">
            <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded w-48"></div>
            <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded w-72"></div>
        </div>
        <div class="flex items-center gap-2">
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-28"></div>
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-28"></div>
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-36"></div>
        </div>
    </div>

    {{-- Stats Cards Skeleton (3 cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach (range(1, 3) as $i)
            <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl p-5">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-dark-700 rounded-xl shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-2/3"></div>
                        <div class="h-6 bg-gray-200 dark:bg-dark-700 rounded w-3/4"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter Section Skeleton (4 fields) --}}
    <div class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach (range(1, 4) as $i)
                <div class="space-y-1.5">
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-20"></div>
                    <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded-xl w-full"></div>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-3">
            <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded w-32"></div>
        </div>
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl overflow-hidden">
        {{-- Table Header --}}
        <div class="border-b border-gray-200 dark:border-dark-600 px-4 py-3 flex gap-4">
            @foreach (range(1, 6) as $i)
                <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1"></div>
            @endforeach
        </div>

        {{-- Table Rows --}}
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-4 border-b border-gray-100 dark:border-dark-700 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50 dark:bg-dark-800/50' : '' }}">
                @foreach (range(1, 6) as $col)
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1
                        {{ $col === 1 ? 'w-8 flex-none' : '' }}"></div>
                @endforeach
            </div>
        @endforeach

        {{-- Pagination Skeleton --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-32"></div>
            <div class="flex gap-2">
                @foreach (range(1, 5) as $i)
                    <div class="h-8 w-8 bg-gray-200 dark:bg-dark-700 rounded-lg"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
