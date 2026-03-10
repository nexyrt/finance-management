{{-- Skeleton placeholder untuk Listing component --}}
<div class="space-y-4 animate-pulse">

    {{-- Filter Grid Skeleton --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach (range(1, 4) as $i)
            <div class="space-y-1.5">
                <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-16"></div>
                <div class="h-9 bg-gray-200 dark:bg-dark-700 rounded-xl"></div>
            </div>
        @endforeach
    </div>

    {{-- Filter Status Row Skeleton --}}
    <div class="flex items-center justify-between">
        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-28"></div>
        <div class="flex gap-2">
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-28"></div>
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-24"></div>
        </div>
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl overflow-hidden">
        {{-- Table toolbar (search + quantity) --}}
        <div class="px-4 py-3 border-b border-gray-100 dark:border-dark-700 flex items-center justify-between gap-4">
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-24"></div>
            <div class="h-8 bg-gray-200 dark:bg-dark-700 rounded-lg w-52"></div>
        </div>

        {{-- Table Header --}}
        <div class="border-b border-gray-200 dark:border-dark-600 px-4 py-3 flex gap-4">
            <div class="w-5 h-4 bg-gray-200 dark:bg-dark-700 rounded"></div>
            @foreach ([24, 40, 20, 20, 28, 16, 16, 16] as $w)
                <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded flex-1" style="max-width: {{ $w }}%"></div>
            @endforeach
        </div>

        {{-- Table Rows --}}
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-3.5 border-b border-gray-100 dark:border-dark-700 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50/50 dark:bg-dark-800/30' : '' }}">
                <div class="w-4 h-4 bg-gray-200 dark:bg-dark-700 rounded shrink-0"></div>
                {{-- Invoice # --}}
                <div class="flex-1 space-y-1.5" style="max-width:14%">
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-dark-700 rounded w-2/3"></div>
                </div>
                {{-- Client --}}
                <div class="flex items-center gap-2 flex-1" style="max-width:22%">
                    <div class="w-8 h-8 bg-gray-200 dark:bg-dark-700 rounded-lg shrink-0"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-3/4"></div>
                        <div class="h-2.5 bg-gray-100 dark:bg-dark-700 rounded w-1/2"></div>
                    </div>
                </div>
                {{-- Issue date --}}
                <div class="flex-1 space-y-1.5" style="max-width:12%">
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-dark-700 rounded w-2/3"></div>
                </div>
                {{-- Due date --}}
                <div class="flex-1 space-y-1.5" style="max-width:12%">
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-dark-700 rounded w-2/3"></div>
                </div>
                {{-- Amount --}}
                <div class="flex-1 space-y-1.5 text-right" style="max-width:14%">
                    <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-full ml-auto"></div>
                    <div class="h-1 bg-gray-200 dark:bg-dark-700 rounded-full w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-dark-700 rounded w-1/2 ml-auto"></div>
                </div>
                {{-- Status --}}
                <div class="shrink-0" style="width: 80px">
                    <div class="h-5 bg-gray-200 dark:bg-dark-700 rounded-full w-full"></div>
                </div>
                {{-- Actions --}}
                <div class="flex gap-1.5 shrink-0">
                    @foreach (range(1, 4) as $btn)
                        <div class="w-7 h-7 bg-gray-200 dark:bg-dark-700 rounded-lg"></div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Pagination Skeleton --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="h-3 bg-gray-200 dark:bg-dark-700 rounded w-32"></div>
            <div class="flex gap-1.5">
                @foreach (range(1, 5) as $i)
                    <div class="h-8 w-8 bg-gray-200 dark:bg-dark-700 rounded-lg"></div>
                @endforeach
            </div>
        </div>
    </div>

</div>
