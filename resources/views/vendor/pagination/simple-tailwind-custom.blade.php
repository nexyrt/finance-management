@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-between">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded bg-zinc-800 text-zinc-500 cursor-not-allowed">
                Previous
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">
                Previous
            </button>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="px-3 py-1 rounded bg-zinc-700 text-white hover:bg-zinc-600 transition-colors">
                Next
            </button>
        @else
            <span class="px-3 py-1 rounded bg-zinc-800 text-zinc-500 cursor-not-allowed">
                Next
            </span>
        @endif
    </nav>
@endif
