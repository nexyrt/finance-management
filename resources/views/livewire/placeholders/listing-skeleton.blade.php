{{-- Skeleton placeholder untuk Listing-only components (tanpa stats cards) --}}
<div class="space-y-4 animate-pulse">
    {{-- Filter Bar Skeleton --}}
    <div class="flex gap-3">
        <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded-xl w-40"></div>
        <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded-xl w-40"></div>
        <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded-xl w-64 ml-auto"></div>
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
        @foreach (range(1, 10) as $row)
            <div class="px-4 py-4 border-b border-gray-100 dark:border-dark-700 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50 dark:bg-dark-800/50' : '' }}">
                @foreach (range(1, 6) as $col)
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1"></div>
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
