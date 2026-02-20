<div>
    {{-- Modal --}}
    <x-modal :title="__('pages.inline_category_title')" center wire="modal" size="lg">
        <form id="inline-category-create" wire:submit="save" class="space-y-4">

            {{-- Type Info --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex gap-3">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium">
                            {{ __('pages.inline_category_creating_for') }}
                            <span class="font-bold">
                                {{ $transactionType === 'credit' ? __('pages.inline_income_type') : __('pages.inline_expense_type') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Label Input --}}
            <div>
                <x-input :label="__('pages.inline_category_name_label')" wire:model="label"
                    :hint="__('pages.inline_category_name_hint')"
                    :placeholder="__('pages.inline_category_name_placeholder')"
                    required />
            </div>

            {{-- Parent Selector (conditional) --}}
            @if (count($this->parentOptions) > 0)
                <div>
                    <x-select.styled :label="__('pages.inline_parent_category_label')" wire:model="parent_id"
                        :options="$this->parentOptions"
                        :placeholder="__('pages.inline_parent_category_placeholder')"
                        searchable />
                    <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                        <x-icon name="information-circle" class="w-4 h-4 inline" />
                        {{ __('pages.inline_parent_hint') }}
                    </p>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex gap-3">
                        <x-icon name="information-circle" class="w-5 h-5 text-gray-600 dark:text-gray-400 flex-shrink-0" />
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            <p>{{ __('pages.inline_no_parent_message', ['type' => $transactionType === 'credit' ? __('pages.inline_no_parent_income') : __('pages.inline_no_parent_expense')]) }}</p>
                            <p class="mt-1 text-xs">{{ __('pages.inline_will_be_parent') }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button color="gray" wire:click="$set('modal', false)">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="inline-category-create" color="blue" loading="save" icon="check">
                    {{ __('pages.save_category_inline_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
