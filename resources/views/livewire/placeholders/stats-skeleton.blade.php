{{-- Skeleton placeholder untuk stats-only / listing-only sections --}}
<div class="space-y-4 animate-pulse">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach (range(1, 4) as $i)
            <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-gray-200 dark:bg-[#27272a] rounded-xl shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-3 bg-gray-200 dark:bg-[#27272a] rounded w-2/3"></div>
                        <div class="h-6 bg-gray-200 dark:bg-[#27272a] rounded w-3/4"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
