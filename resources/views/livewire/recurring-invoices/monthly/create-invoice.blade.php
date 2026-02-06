<div>
    <x-button wire:click="$toggle('modal')" icon="plus" color="primary" size="sm">
        Create Invoice
    </x-button>

    <x-modal wire title="Create Invoice from Template" size="2xl" center>
        <form id="create-invoice-form" wire:submit="save" class="space-y-4">
            <!-- Template Selection -->
            <x-select.styled wire:model.live="invoice.template_id" :options="$this->availableTemplates" label="Template" searchable
                placeholder="Pilih template" required />

            <!-- Scheduled Date -->
            <x-date wire:model.live="invoice.scheduled_date" label="Scheduled Date" required />

            <!-- Template Preview -->
            @if ($this->selectedTemplate)
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <h4 class="font-medium text-blue-900 dark:text-blue-200 mb-2">Template Preview</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Client:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ $this->selectedTemplate->client->name }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Amount:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ $this->selectedTemplate->formatted_total_amount }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Frequency:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ ucfirst(str_replace('_', ' ', $this->selectedTemplate->frequency)) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-300">Items:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-200 ml-2">
                                {{ count($this->selectedTemplate->invoice_template['items'] ?? []) }} items
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="zinc" size="sm">
                    Cancel
                </x-button>
                <x-button type="submit" form="create-invoice-form" color="primary" loading="save" icon="check"
                    size="sm">
                    Create Invoice
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
