<div>
    <x-modal :title="__('pages.cat_edit_title')" wire size="xl" center>
        @if ($categoryId)
            <form id="category-update" wire:submit="save" class="space-y-4">

                {{-- Warning jika tidak bisa ubah type --}}
                @if (!$this->canChangeType)
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <x-icon name="exclamation-triangle"
                                class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" />
                            <div class="text-sm text-yellow-800 dark:text-yellow-200">
                                <p class="font-medium">{{ __('pages.cat_type_cannot_change') }}</p>
                                <p class="mt-1">{{ __('pages.cat_has_transactions_warning', ['count' => $transactionsCount]) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Type Selector --}}
                <div>
                    <x-select.styled :label="__('pages.cat_type_label')" wire:model.live="type" :disabled="!$this->canChangeType"
                        :options="[
                            ['label' => __('pages.cat_income_option'), 'value' => 'income'],
                            ['label' => __('pages.cat_expense_option'), 'value' => 'expense'],
                            ['label' => __('pages.cat_adjustment_option'), 'value' => 'adjustment'],
                            ['label' => __('pages.cat_transfer_option'), 'value' => 'transfer'],
                        ]" required />
                </div>

                {{-- Label Input --}}
                <div>
                    <x-input :label="__('pages.cat_label_input')" wire:model="label" :hint="__('pages.cat_label_hint')" required />
                </div>

                {{-- Parent Selector (conditional) --}}
                @if ($type && count($this->parentOptions) > 0)
                    <div>
                        <x-select.styled :label="__('pages.cat_parent_label')" wire:model="parent_id"
                            :options="$this->parentOptions"
                            :placeholder="__('pages.cat_parent_placeholder')" />
                        <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                            <x-icon name="information-circle" class="w-4 h-4 inline" />
                            {{ __('pages.cat_parent_keep_hint') }}
                        </p>
                    </div>
                @elseif($type)
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex gap-3">
                            <x-icon name="information-circle"
                                class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                <p class="font-medium">{{ __('pages.cat_is_parent_info') }}</p>
                                <p class="mt-1">{{ __('pages.cat_no_other_parents', ['type' => ucfirst($type)]) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Usage Stats --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <p class="text-sm font-medium text-dark-900 dark:text-dark-50 mb-2">{{ __('pages.cat_usage_stats') }}</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-dark-500 dark:text-dark-400">{{ __('pages.cat_transactions_label') }}</span>
                            <span class="font-bold text-dark-900 dark:text-dark-50 ml-2">
                                {{ $transactionsCount }}
                            </span>
                        </div>
                        <div>
                            <span class="text-dark-500 dark:text-dark-400">{{ __('pages.cat_children_label') }}</span>
                            <span class="font-bold text-dark-900 dark:text-dark-50 ml-2">
                                {{ $childrenCount }}
                            </span>
                        </div>
                    </div>
                </div>

            </form>

            <x-slot:footer>
                <div class="flex justify-between w-full">
                    <x-button color="gray" wire:click="$set('modal', false)">
                        {{ __('common.cancel') }}
                    </x-button>
                    <x-button type="submit" form="category-update" color="green" loading="save" icon="check">
                        {{ __('pages.cat_update_btn') }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
