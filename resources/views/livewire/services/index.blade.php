{{-- resources/views/livewire/services/index.blade.php --}}

<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="space-y-1">
            <h1
                class="text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                {{ __('common.services') }}
            </h1>
            <p class="text-gray-600 dark:text-zinc-400 text-lg">
                {{ __('pages.service_list') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Workflow Guide Button --}}
            <button
                wire:click="$toggle('guideModal')"
                class="h-9 px-4 flex items-center gap-2 rounded-xl border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-dark-500 dark:text-dark-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-300 dark:hover:border-indigo-700 text-sm font-medium transition-all"
            >
                <x-icon name="information-circle" class="w-4 h-4" />
                {{ __('pages.client_guide_btn') }}
            </button>

            <livewire:services.create />
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="squares-2x2" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('common.total') }}
                        {{ __('common.services') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        {{ $stats['total_services'] }}
                    </p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="banknotes" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.average_price') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        @if ($stats['avg_price'])
                            Rp {{ number_format($stats['avg_price'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 bg-purple-50 dark:bg-purple-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="star" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.highest_price') }}</p>
                    <p class="text-2xl font-bold text-dark-900 dark:text-dark-50">
                        @if ($stats['highest_price'])
                            Rp {{ number_format($stats['highest_price'], 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </x-card>

        <x-card class="hover:shadow-lg transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="rectangle-group" class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <div>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.most_category') }}</p>
                    <p class="text-lg font-semibold text-dark-900 dark:text-dark-50">
                        @if ($stats['by_type']->isNotEmpty())
                            {{ translate_text($stats['by_type']->keys()->first()) }}
                            <span
                                class="text-xs text-dark-600 dark:text-dark-400">({{ $stats['by_type']->first() }})</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </x-card>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-end">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
            <div>
                <label
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('common.category') }}</label>
                <x-select.styled wire:model.live="typeFilter"
                    :options="$this->categoryOptions"
                    placeholder="{{ __('pages.all_categories') }}" />
            </div>
        </div>

        <div class="flex gap-2">
            @if ($typeFilter)
                <x-button wire:click="clearFilters" icon="x-mark" color="gray" outline size="sm">
                    {{ __('pages.clear_filter') }}
                </x-button>
            @endif
        </div>
    </div>

    {{-- Services Table --}}
    <x-table :$headers :$sort :rows="$this->services" selectable wire:model="selected" paginate filter>

        {{-- Service Name Column --}}
        @interact('column_name', $row)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="cog-6-tooth" class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <p class="font-semibold text-dark-900 dark:text-dark-50">{{ translate_text($row->name) }}</p>
                    <p class="text-xs text-dark-500 dark:text-dark-400">ID: {{ $row->id }}</p>
                </div>
            </div>
        @endinteract

        {{-- Type Column --}}
        @interact('column_type', $row)
            <x-badge :text="translate_text($row->type)" :color="match ($row->type) {
                'Perizinan' => 'blue',
                'Administrasi Perpajakan' => 'green',
                'Digital Marketing' => 'purple',
                'Sistem Digital' => 'orange',
                default => 'gray',
            }" />
        @endinteract

        {{-- Price Column --}}
        @interact('column_price', $row)
            <p class="font-bold text-lg text-dark-900 dark:text-dark-50">
                Rp {{ number_format($row->price, 0, ',', '.') }}
            </p>
        @endinteract

        {{-- Created At Column --}}
        @interact('column_created_at', $row)
            <div>
                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                    {{ $row->created_at->format('d M Y') }}
                </p>
                <p class="text-xs text-dark-500 dark:text-dark-400">
                    {{ $row->created_at->diffForHumans() }}
                </p>
            </div>
        @endinteract

        {{-- Actions Column --}}
        @interact('column_actions', $row)
            <div class="flex items-center gap-1">
                <x-button.circle icon="pencil" color="green" size="sm" wire:click="edit({{ $row->id }})"
                    title="{{ __('common.edit') }}" />

                <x-button.circle icon="trash" color="red" size="sm" wire:click="confirmDelete({{ $row->id }})"
                    title="{{ __('common.delete') }}" />
            </div>
        @endinteract

    </x-table>

    {{-- Bulk Actions Bar --}}
    <div x-data="{ show: @entangle('selected').live }" x-show="show.length > 0" x-transition
        class="fixed bottom-4 sm:bottom-6 left-4 right-4 sm:left-1/2 sm:right-auto sm:transform sm:-translate-x-1/2 z-50">
        <div
            class="bg-white dark:bg-dark-800 rounded-xl shadow-lg border border-zinc-200 dark:border-dark-600 px-4 sm:px-6 py-4 sm:min-w-96">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="check-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <div class="font-semibold text-dark-900 dark:text-dark-50"
                            x-text="`${show.length} {{ __('common.services') }} {{ __('pages.selected') }}`"></div>
                        <div class="text-xs text-dark-500 dark:text-dark-400">
                            {{ __('pages.select_action_for_selected') }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 justify-end">
                    <x-button wire:click="bulkDelete" size="sm" color="red" icon="trash" loading="bulkDelete"
                        class="whitespace-nowrap">
                        {{ __('common.delete') }}
                    </x-button>
                    <x-button wire:click="$set('selected', [])" size="sm" color="gray" icon="x-mark"
                        class="whitespace-nowrap">
                        {{ __('common.cancel') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Workflow Guide Modal --}}
    <x-modal wire="guideModal" size="3xl" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="map" class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.service_guide_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.service_guide_desc') }}</p>
                </div>
            </div>
        </x-slot:title>

        <div class="space-y-6">
            {{-- Workflow Steps Timeline --}}
            <div class="relative">
                {{-- Connecting line --}}
                <div class="absolute left-6 top-10 bottom-10 w-0.5 bg-gradient-to-b from-blue-300 via-purple-300 to-green-300 dark:from-blue-700 dark:via-purple-700 dark:to-green-700 hidden sm:block"></div>

                <div class="space-y-4">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 z-10">
                            <span class="text-white font-bold text-sm">1</span>
                        </div>
                        <div class="flex-1 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-200 mb-1">{{ __('pages.service_guide_step1_title') }}</h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">{{ __('pages.service_guide_step1_desc') }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                            <span>{{ __('pages.service_guide_step1_tip1') }}</span>
                                        </div>
                                        <div class="flex items-start gap-2 text-xs text-blue-600 dark:text-blue-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                            <span>{{ __('pages.service_guide_step1_tip2') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center shadow-lg shadow-purple-200 dark:shadow-purple-900/40 z-10">
                            <span class="text-white font-bold text-sm">2</span>
                        </div>
                        <div class="flex-1 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="document-text" class="w-5 h-5 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">{{ __('pages.service_guide_step2_title') }}</h4>
                                    <p class="text-sm text-purple-700 dark:text-purple-300 mb-3">{{ __('pages.service_guide_step2_desc') }}</p>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                            <span>{{ __('pages.service_guide_step2_tip1') }}</span>
                                        </div>
                                        <div class="flex items-start gap-2 text-xs text-purple-600 dark:text-purple-400">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" />
                                            <span>{{ __('pages.service_guide_step2_tip2') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40 z-10">
                            <span class="text-white font-bold text-sm">3</span>
                        </div>
                        <div class="flex-1 bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-900/40 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="pencil-square" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" />
                                <div class="flex-1">
                                    <h4 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">{{ __('pages.service_guide_step3_title') }}</h4>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-300">{{ __('pages.service_guide_step3_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Service Categories --}}
            <div class="border-t border-secondary-200 dark:border-dark-600 pt-5">
                <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">{{ __('pages.service_guide_categories_title') }}</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-start gap-3 p-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-900/40 rounded-xl">
                        <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="document-check" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">{{ __('pages.service_cat_perizinan') }}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">{{ __('pages.service_cat_perizinan_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/40 rounded-xl">
                        <div class="h-8 w-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="calculator" class="w-4 h-4 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-green-900 dark:text-green-200">{{ __('pages.service_cat_pajak') }}</p>
                            <p class="text-xs text-green-600 dark:text-green-400">{{ __('pages.service_cat_pajak_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/40 rounded-xl">
                        <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="megaphone" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-purple-900 dark:text-purple-200">{{ __('pages.service_cat_marketing') }}</p>
                            <p class="text-xs text-purple-600 dark:text-purple-400">{{ __('pages.service_cat_marketing_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-900/40 rounded-xl">
                        <div class="h-8 w-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                            <x-icon name="computer-desktop" class="w-4 h-4 text-orange-600 dark:text-orange-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-orange-900 dark:text-orange-200">{{ __('pages.service_cat_digital') }}</p>
                            <p class="text-xs text-orange-600 dark:text-orange-400">{{ __('pages.service_cat_digital_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                <div class="flex items-start gap-3">
                    <x-icon name="light-bulb" class="w-5 h-5 text-yellow-500 dark:text-yellow-400 flex-shrink-0 mt-0.5" />
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.service_guide_tips_title') }}</h4>
                        <ul class="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                            <li class="flex items-start gap-2">
                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" />
                                <span>{{ __('pages.service_guide_tip1') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" />
                                <span>{{ __('pages.service_guide_tip2') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <x-icon name="check-circle" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-green-500" />
                                <span>{{ __('pages.service_guide_tip3') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end">
                <x-button wire:click="$toggle('guideModal')" color="primary" icon="check">
                    {{ __('pages.client_guide_got_it') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>

    {{-- Child Components (satu instance, bukan per baris) --}}
    <livewire:services.edit />
    <livewire:services.delete />
</div>
