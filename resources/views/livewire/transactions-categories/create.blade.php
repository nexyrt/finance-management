<div>
    {{-- Trigger Button --}}
    <x-button wire:click="$toggle('modal')" color="zinc" :icon="$buttonIcon">
        @if($buttonLabel){{ $buttonLabel }}@endif
    </x-button>

    {{-- Modal --}}
    <x-modal :title="__('pages.cat_create_title')" center wire size="xl">
        <form id="category-create" wire:submit="save" class="space-y-4">

            {{-- Type Selector --}}
            <div>
                <x-select.styled :label="__('pages.cat_type_label')" wire:model.live="type" :options="[
                    ['label' => __('pages.cat_income_option'), 'value' => 'income'],
                    ['label' => __('pages.cat_expense_option'), 'value' => 'expense'],
                    ['label' => __('pages.cat_adjustment_option'), 'value' => 'adjustment'],
                    ['label' => __('pages.cat_transfer_option'), 'value' => 'transfer'],
                ]" :placeholder="__('pages.cat_type_placeholder')" />
            </div>

            {{-- Label Input --}}
            <div>
                <x-input :label="__('pages.cat_label_input')" wire:model="label" :hint="__('pages.cat_label_hint')" />
            </div>

            {{-- Parent Selector (conditional) --}}
            @if ($type && count($this->parentOptions) > 0)
                <div>
                    <x-select.styled :label="__('pages.cat_parent_label')" wire:model="parent_id"
                        :options="$this->parentOptions" :placeholder="__('pages.cat_parent_placeholder')" />
                    <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">
                        <x-icon name="information-circle" class="w-4 h-4 inline" />
                        {{ __('pages.cat_parent_hint') }}
                    </p>
                </div>
            @elseif($type)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex gap-3">
                        <x-icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <p class="font-medium">{{ __('pages.cat_no_parent_title') }}</p>
                            <p class="mt-1">{{ __('pages.cat_no_parent_message', ['type' => ucfirst($type)]) }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Helper Text --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <p class="text-sm text-dark-600 dark:text-dark-400">
                    <strong>{{ __('pages.cat_tips_title') }}</strong>
                </p>
                <ul class="mt-2 text-sm text-dark-500 dark:text-dark-400 space-y-1 list-disc list-inside">
                    <li>{{ __('pages.cat_tip_1') }}</li>
                    <li>{{ __('pages.cat_tip_2') }}</li>
                    <li>{{ __('pages.cat_tip_3') }}</li>
                </ul>
            </div>

        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button color="gray" wire:click="$set('modal', false)">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="category-create" color="blue" loading="save" icon="check">
                    {{ __('pages.cat_save_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
