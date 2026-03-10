{{-- Skeleton placeholder untuk Invoices Listing (stats + filter + table) --}}
<div class="space-y-6 animate-pulse">

    {{-- Stats Cards Skeleton (4 cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ([
            ['from' => 'from-blue-400',    'to' => 'to-blue-600'],
            ['from' => 'from-red-400',     'to' => 'to-red-600'],
            ['from' => 'from-emerald-400', 'to' => 'to-emerald-600'],
            ['from' => 'from-amber-400',   'to' => 'to-amber-600'],
        ] as $card)
            <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl p-5 overflow-hidden relative">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-[#27272a] rounded-xl shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-2.5 bg-gray-200 dark:bg-[#27272a] rounded w-20"></div>
                        <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded w-32"></div>
                        <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-24"></div>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-linear-to-r {{ $card['from'] }} {{ $card['to'] }} opacity-40"></div>
            </div>
        @endforeach
    </div>

    {{-- Filter Section Skeleton --}}
    <div class="space-y-3">
        {{-- Filter Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach (range(1, 4) as $i)
                <div class="space-y-1.5">
                    <div class="h-2.5 bg-gray-200 dark:bg-[#27272a] rounded w-16"></div>
                    <div class="h-9 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
                </div>
            @endforeach
        </div>
        {{-- Status row --}}
        <div class="flex items-center justify-between">
            <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-28"></div>
            <div class="flex gap-2">
                <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-28"></div>
                <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-24"></div>
            </div>
        </div>
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden">
        {{-- Toolbar --}}
        <div class="px-4 py-3 border-b border-gray-100 dark:border-white/8 flex items-center justify-between gap-4">
            <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-24"></div>
            <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-52"></div>
        </div>

        {{-- Header --}}
        <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3 flex gap-4">
            <div class="w-5 h-4 bg-gray-200 dark:bg-[#27272a] rounded"></div>
            @foreach ([24, 40, 20, 20, 28, 16, 16, 16] as $w)
                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded flex-1" style="max-width: {{ $w }}%"></div>
            @endforeach
        </div>

        {{-- Rows --}}
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-3.5 border-b border-gray-100 dark:border-white/8 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50/50 dark:bg-[#1e1e1e]/30' : '' }}">
                <div class="w-4 h-4 bg-gray-200 dark:bg-[#27272a] rounded shrink-0"></div>
                <div class="flex-1 space-y-1.5" style="max-width:14%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-2/3"></div>
                </div>
                <div class="flex items-center gap-2 flex-1" style="max-width:22%">
                    <div class="w-8 h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg shrink-0"></div>
                    <div class="space-y-1.5 flex-1">
                        <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-3/4"></div>
                        <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-1/2"></div>
                    </div>
                </div>
                <div class="flex-1 space-y-1.5" style="max-width:12%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-2/3"></div>
                </div>
                <div class="flex-1 space-y-1.5" style="max-width:12%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-2/3"></div>
                </div>
                <div class="flex-1 space-y-1.5" style="max-width:14%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full ml-auto"></div>
                    <div class="h-1 bg-gray-200 dark:bg-[#27272a] rounded-full w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-1/2 ml-auto"></div>
                </div>
                <div class="shrink-0" style="width:80px">
                    <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded-full w-full"></div>
                </div>
                <div class="flex gap-1.5 shrink-0">
                    @foreach (range(1, 4) as $btn)
                        <div class="w-7 h-7 bg-gray-200 dark:bg-[#27272a] rounded-lg"></div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Pagination --}}
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-32"></div>
            <div class="flex gap-1.5">
                @foreach (range(1, 5) as $i)
                    <div class="h-8 w-8 bg-gray-200 dark:bg-[#27272a] rounded-lg"></div>
                @endforeach
            </div>
        </div>
    </div>

</div>
