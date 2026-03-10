{{-- Skeleton placeholder untuk Reimbursements (AllRequests & MyRequests) --}}
<div class="space-y-6 animate-pulse">

    {{-- Stats Cards Skeleton (4 cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach (range(1, 4) as $i)
            <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-[#27272a] rounded-xl shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-3/4"></div>
                        <div class="h-6 bg-gray-200 dark:bg-[#27272a] rounded w-1/2"></div>
                        <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-2/3"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter Grid Skeleton (3 filters) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach (range(1, 3) as $i)
            <div class="space-y-1.5">
                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-16"></div>
                <div class="h-9 bg-gray-200 dark:bg-[#27272a] rounded-xl"></div>
            </div>
        @endforeach
    </div>

    {{-- Search + Status Row Skeleton --}}
    <div class="flex items-center gap-3">
        <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-64"></div>
        <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded-full w-20"></div>
        <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-28"></div>
    </div>

    {{-- Table Skeleton --}}
    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden">
        {{-- Table toolbar --}}
        <div class="px-4 py-3 border-b border-gray-100 dark:border-white/8 flex items-center justify-between gap-4">
            <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-24"></div>
            <div class="h-8 bg-gray-200 dark:bg-[#27272a] rounded-lg w-48"></div>
        </div>

        {{-- Table Header (8 columns: title, requestor, amount, category, date, status, payment, actions) --}}
        <div class="border-b border-gray-200 dark:border-white/10 px-4 py-3 flex gap-4">
            <div class="w-5 h-4 bg-gray-200 dark:bg-[#27272a] rounded"></div>
            @foreach ([22, 15, 12, 12, 10, 10, 10, 9] as $w)
                <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded" style="width: {{ $w }}%"></div>
            @endforeach
        </div>

        {{-- Table Rows --}}
        @foreach (range(1, 8) as $row)
            <div class="px-4 py-3.5 border-b border-gray-100 dark:border-white/8 flex gap-4 items-center
                {{ $row % 2 === 0 ? 'bg-gray-50/50 dark:bg-[#1e1e1e]/30' : '' }}">
                <div class="w-4 h-4 bg-gray-200 dark:bg-[#27272a] rounded shrink-0"></div>
                {{-- Title --}}
                <div class="flex-1 space-y-1.5" style="max-width:22%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-3/4"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-1/2"></div>
                </div>
                {{-- Requestor --}}
                <div class="flex items-center gap-2 shrink-0" style="width:15%">
                    <div class="w-7 h-7 bg-gray-200 dark:bg-[#27272a] rounded-full shrink-0"></div>
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded flex-1"></div>
                </div>
                {{-- Amount --}}
                <div class="shrink-0 space-y-1" style="width:12%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full"></div>
                    <div class="h-2.5 bg-gray-100 dark:bg-[#27272a] rounded w-2/3"></div>
                </div>
                {{-- Category --}}
                <div class="shrink-0" style="width:12%">
                    <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded-full w-4/5"></div>
                </div>
                {{-- Date --}}
                <div class="shrink-0" style="width:10%">
                    <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-full"></div>
                </div>
                {{-- Status --}}
                <div class="shrink-0" style="width:10%">
                    <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded-full w-full"></div>
                </div>
                {{-- Payment Status --}}
                <div class="shrink-0" style="width:10%">
                    <div class="h-5 bg-gray-200 dark:bg-[#27272a] rounded-full w-full"></div>
                </div>
                {{-- Actions --}}
                <div class="flex gap-1.5 shrink-0">
                    @foreach (range(1, 3) as $btn)
                        <div class="w-7 h-7 bg-gray-200 dark:bg-[#27272a] rounded-lg"></div>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Pagination Skeleton --}}
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
