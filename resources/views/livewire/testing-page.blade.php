<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ translate_text('Testing Page') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">Eksperimen komponen</p>
        </div>
        <x-button wire:click="loadAccounts" color="primary" size="sm">
            <x-slot:left>
                <x-icon name="arrow-path" class="w-4 h-4" />
            </x-slot:left>
            Refresh
        </x-button>
    </div>

    {{-- Error --}}
    @if ($error)
        <div class="flex items-center gap-3 p-4 border border-red-200 dark:border-red-900/30 bg-red-50 dark:bg-red-900/10 rounded-xl">
            <x-icon name="exclamation-circle" class="w-5 h-5 text-red-500 flex-shrink-0" />
            <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
        </div>
    @endif

    {{-- Debug --}}
    <p class="text-xs text-dark-400">Locale: {{ app()->getLocale() }}</p>

    {{-- Content --}}
    @if (empty($accounts))
        <p class="text-sm text-dark-500 dark:text-dark-400">Belum ada data.</p>
    @else
        <x-card>
            <div class="divide-y divide-secondary-200 dark:divide-dark-600">
                @foreach ($accounts as $item)
                    @if (!empty($item['disabled']))
                        {{-- Parent Category --}}
                        <p class="py-2 text-xs font-semibold uppercase tracking-wider text-dark-400 dark:text-dark-500">
                            {{ translate_text($item['label']) }}
                        </p>
                    @else
                        {{-- Child Category --}}
                        <div class="flex items-center justify-between py-2">
                            <p class="text-sm text-dark-900 dark:text-dark-50 pl-3">
                                {{ translate_text(ltrim($item['label'], '↳ ')) }}
                            </p>
                            <span class="text-xs text-dark-400 dark:text-dark-500">ID: {{ $item['value'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-card>
    @endif
</div>
