<div>
    <x-modal wire size="md" center>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="pencil-square" class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.cat_edit_title') }}</h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.cat_page_description') }}</p>
                </div>
            </div>
        </x-slot:title>

        @if ($categoryId)
            <form id="category-update" wire:submit="save" class="space-y-5">

                {{-- Type Cannot Change Warning --}}
                @if (!$this->canChangeType)
                    <div class="flex items-start gap-3 px-4 py-3 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800/50 rounded-xl">
                        <x-icon name="exclamation-triangle" class="w-4 h-4 text-yellow-500 dark:text-yellow-400 shrink-0 mt-0.5" />
                        <div class="text-xs text-yellow-700 dark:text-yellow-300 leading-relaxed">
                            <span class="font-semibold">{{ __('pages.cat_type_cannot_change') }}.</span>
                            {{ __('pages.cat_has_transactions_warning', ['count' => $transactionsCount]) }}
                        </div>
                    </div>
                @endif

                {{-- Type Selector --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                        {{ __('pages.cat_type_label') }}
                    </label>

                    {{-- Type pills --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([
                            ['value' => 'income',     'label' => __('pages.cat_income_option'),     'icon' => 'arrow-trending-up',   'color' => 'emerald'],
                            ['value' => 'expense',    'label' => __('pages.cat_expense_option'),    'icon' => 'arrow-trending-down', 'color' => 'red'],
                            ['value' => 'adjustment', 'label' => __('pages.cat_adjustment_option'), 'icon' => 'scale',               'color' => 'yellow'],
                            ['value' => 'transfer',   'label' => __('pages.cat_transfer_option'),   'icon' => 'arrows-right-left',   'color' => 'blue'],
                        ] as $opt)
                        <button type="button"
                            @if($this->canChangeType) wire:click="$set('type', '{{ $opt['value'] }}')" @endif
                            @class([
                                'flex items-center gap-2.5 px-3 py-2.5 rounded-xl border text-sm font-medium transition-all duration-150 w-full text-left',
                                // disabled state
                                'opacity-60 cursor-not-allowed' => !$this->canChangeType,
                                // selected
                                'bg-' . $opt['color'] . '-50 dark:bg-' . $opt['color'] . '-900/20 border-' . $opt['color'] . '-300 dark:border-' . $opt['color'] . '-700 text-' . $opt['color'] . '-700 dark:text-' . $opt['color'] . '-300 shadow-sm' => $type === $opt['value'],
                                // unselected
                                'bg-white dark:bg-dark-800 border-dark-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:border-dark-300 dark:hover:border-dark-500 hover:bg-dark-50 dark:hover:bg-dark-700' => $type !== $opt['value'] && $this->canChangeType,
                                'bg-white dark:bg-dark-800 border-dark-200 dark:border-dark-600 text-dark-500 dark:text-dark-400' => $type !== $opt['value'] && !$this->canChangeType,
                            ])>
                            <x-icon :name="$opt['icon']" @class([
                                'w-4 h-4 shrink-0',
                                'text-' . $opt['color'] . '-500 dark:text-' . $opt['color'] . '-400' => $type === $opt['value'],
                                'text-dark-400 dark:text-dark-500' => $type !== $opt['value'],
                            ]) />
                            <span>{{ $opt['label'] }}</span>
                        </button>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Label Input --}}
                <div class="space-y-1.5">
                    <div class="flex items-center gap-1.5">
                        <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                            {{ __('pages.cat_label_input') }}
                        </label>
                        <div x-data="{ show: false }" class="relative flex items-center">
                            <button type="button" @mouseenter="show = true" @mouseleave="show = false"
                                class="text-dark-400 dark:text-dark-500 hover:text-dark-600 dark:hover:text-dark-300 transition-colors">
                                <x-icon name="question-mark-circle" class="w-3.5 h-3.5" />
                            </button>
                            <div x-show="show" x-cloak x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 w-56 pointer-events-none">
                                <div class="bg-dark-800 dark:bg-dark-600 text-dark-50 text-xs rounded-lg px-3 py-2 shadow-lg">
                                    <p>{{ __('pages.cat_label_hint') }}</p>
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-dark-800 dark:border-t-dark-600"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <x-input wire:model="label" placeholder="{{ __('pages.cat_label_hint') }}" />
                </div>

                {{-- Parent Selector --}}
                @if ($type && count($this->parentOptions) > 0)
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-1.5">
                            <label class="block text-xs font-semibold text-dark-700 dark:text-dark-300 uppercase tracking-wide">
                                {{ __('pages.cat_parent_label') }}
                            </label>
                            <div x-data="{ show: false }" class="relative flex items-center">
                                <button type="button" @mouseenter="show = true" @mouseleave="show = false"
                                    class="text-dark-400 dark:text-dark-500 hover:text-dark-600 dark:hover:text-dark-300 transition-colors">
                                    <x-icon name="question-mark-circle" class="w-3.5 h-3.5" />
                                </button>
                                <div x-show="show" x-cloak x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 z-50 w-60 pointer-events-none">
                                    <div class="bg-dark-800 dark:bg-dark-600 text-dark-50 text-xs rounded-lg px-3 py-2 shadow-lg">
                                        <p>{{ __('pages.cat_tip_2') }}</p>
                                        <p class="mt-1 text-dark-300">{{ __('pages.cat_parent_keep_hint') }}</p>
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-dark-800 dark:border-t-dark-600"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <x-select.styled wire:model="parent_id"
                            :options="$this->parentOptions"
                            :placeholder="__('pages.cat_parent_placeholder')" />
                    </div>

                @elseif($type)
                    {{-- No parent available info --}}
                    <div class="flex items-start gap-3 px-4 py-3 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/50 rounded-xl">
                        <x-icon name="information-circle" class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0 mt-0.5" />
                        <div class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">
                            <span class="font-semibold">{{ __('pages.cat_is_parent_info') }}.</span>
                            {{ __('pages.cat_no_other_parents', ['type' => ucfirst($type)]) }}
                        </div>
                    </div>
                @endif

                {{-- Hierarchy preview --}}
                @if($type)
                    <div class="flex items-center gap-2 px-3 py-2.5 bg-dark-50 dark:bg-dark-800/60 rounded-xl border border-dark-100 dark:border-dark-600">
                        <x-icon name="folder-open" class="w-4 h-4 text-dark-400 dark:text-dark-500 shrink-0" />
                        <div class="flex items-center gap-1.5 text-xs text-dark-500 dark:text-dark-400 flex-wrap">
                            <span class="font-medium text-dark-700 dark:text-dark-300 uppercase tracking-wide">{{ ucfirst($type) }}</span>
                            @if($parent_id && count($this->parentOptions) > 0)
                                @php $parent = collect($this->parentOptions)->firstWhere('value', $parent_id); @endphp
                                @if($parent)
                                    <x-icon name="chevron-right" class="w-3 h-3" />
                                    <span class="text-dark-600 dark:text-dark-300">{{ $parent['label'] }}</span>
                                @endif
                            @endif
                            @if($label)
                                <x-icon name="chevron-right" class="w-3 h-3" />
                                <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $label }}</span>
                            @else
                                <x-icon name="chevron-right" class="w-3 h-3" />
                                <span class="italic text-dark-400 dark:text-dark-500">{{ __('pages.cat_label_input') }}...</span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Usage Stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center gap-3 px-3 py-2.5 bg-dark-50 dark:bg-dark-800/60 rounded-xl border border-dark-100 dark:border-dark-600">
                        <div class="h-8 w-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="arrow-path" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.cat_transactions_label') }}</p>
                            <p class="text-base font-bold text-dark-900 dark:text-dark-50">{{ $transactionsCount }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 px-3 py-2.5 bg-dark-50 dark:bg-dark-800/60 rounded-xl border border-dark-100 dark:border-dark-600">
                        <div class="h-8 w-8 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="folder" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.cat_children_label') }}</p>
                            <p class="text-base font-bold text-dark-900 dark:text-dark-50">{{ $childrenCount }}</p>
                        </div>
                    </div>
                </div>

            </form>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto order-2 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>
                    <x-button type="submit" form="category-update" color="blue" icon="check" loading="save" class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('pages.cat_update_btn') }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
