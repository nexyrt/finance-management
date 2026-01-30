<div x-data="{ show: false }">
    {{-- Language Button --}}
    <button @click="show = !show"
        x-ref="button"
        type="button"
        class="relative p-2 text-dark-500 hover:text-dark-700 dark:text-dark-400 dark:hover:text-dark-200 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700">
        <span class="text-xl">{{ $availableLocales[$currentLocale]['flag'] }}</span>
    </button>

    {{-- Floating Dropdown using TallStackUI with Teleport --}}
    <template x-teleport="body">
        <x-floating x-show="show"
            x-anchor="$refs.button"
            @click.outside="show = false"
            class="w-48 bg-white dark:bg-dark-800 rounded-xl shadow-2xl border border-gray-200 dark:border-dark-700"
            style="z-index: 9999;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-200 dark:border-dark-700">
                <h3 class="text-sm font-semibold text-dark-900 dark:text-white">{{ __('common.language') }}</h3>
            </div>

            {{-- Language List --}}
            <div class="py-1">
                @foreach($availableLocales as $code => $locale)
                    <button wire:click="switchLanguage('{{ $code }}')"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                            {{ $currentLocale === $code ? 'bg-primary-50 dark:bg-primary-900/10 text-primary-600 dark:text-primary-400' : 'text-dark-700 dark:text-dark-300' }}
                            hover:bg-gray-50 dark:hover:bg-dark-700">
                        <span class="text-xl">{{ $locale['flag'] }}</span>
                        <span class="font-medium">{{ $locale['name'] }}</span>
                        @if($currentLocale === $code)
                            <x-icon name="check" class="w-4 h-4 ml-auto" />
                        @endif
                    </button>
                @endforeach
            </div>
        </x-floating>
    </template>
</div>
