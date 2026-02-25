{{-- resources/views/livewire/invoices/index.blade.php --}}

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('pages.invoice_management') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.manage_track_invoices') }}
            </p>
        </div>
        <x-button size="sm" href="{{ route('invoices.create') }}" wire:navigate color="primary">
            <x-slot:left>
                <x-icon name="plus" class="w-4 h-4" />
            </x-slot:left>
            {{ __('invoice.create_invoice') }}
        </x-button>
    </div>

    {{-- Stats + Table (Lazy) --}}
    <livewire:invoices.listing />

    {{-- Child Components --}}
    <livewire:invoices.show />
    <livewire:invoices.delete />
    <livewire:payments.create />
    <livewire:payments.edit />
</div>
