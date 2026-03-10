{{-- Skeleton placeholder untuk MyFeedbacks dan AllFeedbacks --}}
<div class="space-y-6 animate-pulse">
    {{-- Filter Grid (2 filters) --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <div class="h-9 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
                <div class="h-9 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
            </div>
            <div class="flex gap-3">
                <div class="h-9 bg-gray-200 dark:bg-[#27272a] rounded-xl w-64"></div>
            </div>
        </div>
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden">
        {{-- Table Header --}}
        <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3 flex gap-4">
            @foreach (range(1, 6) as $i)
                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded flex-1"></div>
            @endforeach
        </div>

        {{-- Table Rows --}}
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-4 border-b border-gray-100 dark:border-white/8 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50 dark:bg-[#1e1e1e]/50' : '' }}">
                @foreach (range(1, 6) as $col)
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded flex-1"></div>
                @endforeach
            </div>
        @endforeach

        {{-- Pagination Skeleton --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-32"></div>
            <div class="flex gap-2">
                @foreach (range(1, 5) as $i)
                    <div class="h-8 w-8 bg-gray-200 dark:bg-[#27272a] rounded-lg"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
