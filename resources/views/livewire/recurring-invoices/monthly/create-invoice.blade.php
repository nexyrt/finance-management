<div>
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary" size="sm">
        {{ __('pages.ri_create_invoice_btn') }}
    </x-button>

    <x-modal wire :title="__('pages.ri_create_invoice_modal_title')" size="2xl" center>
        <form id="create-invoice-form" wire:submit="save" class="space-y-4">
            <!-- Template Selection -->
            <x-select.styled wire:model.live="invoice.template_id" :options="$this->availableTemplates" :label="__('pages.ri_template_select_label')" searchable
                :placeholder="__('pages.ri_template_select_placeholder')" required />

            <!-- Scheduled Date -->
            <x-date wire:model.live="invoice.scheduled_date" :label="__('pages.ri_scheduled_date_label')" required />

            <!-- Template Preview -->
            @if ($this->selectedTemplate)
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-2">{{ __('pages.ri_template_preview_title') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">{{ __('pages.ri_preview_client_label') }}</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ $this->selectedTemplate->client->name }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">{{ __('pages.ri_preview_amount_label') }}</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ $this->selectedTemplate->formatted_total_amount }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">{{ __('pages.ri_preview_frequency_label') }}</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ __('pages.ri_freq_' . $this->selectedTemplate->frequency) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">{{ __('pages.ri_preview_items_label') }}</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ __('pages.ri_preview_items_count', ['count' => count($this->selectedTemplate->invoice_template['items'] ?? [])]) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="zinc" size="sm">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button type="submit" form="create-invoice-form" color="primary" loading="save" icon="check"
                    size="sm">
                    {{ __('pages.ri_create_invoice_btn') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
