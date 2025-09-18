<x-modal wire title="Edit Draft Invoice" size="6xl" center>
    @if ($invoice)
        <form id="edit-invoice-form" wire:submit="save" class="space-y-4">
            <!-- Invoice Info -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icon name="building-office" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        <span class="text-blue-800 dark:text-blue-200">{{ $invoice->client->name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icon name="document-duplicate" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                        <span class="text-blue-800 dark:text-blue-200">{{ $invoice->template->template_name }}</span>
                    </div>
                    <x-badge text="Draft" color="amber" />
                </div>
            </div>

            <!-- Scheduled Date -->
            <x-date wire:model="invoiceData.scheduled_date" label="Scheduled Date" required />

            <!-- Items Section -->
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="font-medium text-gray-900 dark:text-zinc-100">Items</h3>
                    <div x-data="{ selected: @entangle('selectedItems').live }" x-show="selected.length > 0" class="flex items-center gap-2">
                        <span class="text-xs text-gray-500" x-text="`${selected.length} selected`"></span>
                        <x-button wire:click="bulkDeleteItems" loading="bulkDeleteItems" color="red" size="xs"
                            outline icon="trash">
                            Delete Selected
                        </x-button>
                    </div>
                </div>

                @if (count($items) > 0)
                    <div class="space-y-2">
                        @foreach ($items as $index => $item)
                            <div
                                class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3 border border-gray-200 dark:border-zinc-600">
                                <!-- Mobile Layout -->
                                <div class="block lg:hidden space-y-3">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="selectedItems"
                                                value="{{ $index }}"
                                                class="rounded border-gray-300 dark:border-zinc-600">
                                            <div
                                                class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                                <span
                                                    class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                            </div>
                                        </div>
                                        <x-button wire:click="removeItem({{ $index }})" color="red"
                                            size="xs" outline icon="trash"
                                            loading="removeItem({{ $index }})" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-select.styled wire:model="items.{{ $index }}.client_id"
                                            :options="$this->clientOptions" searchable placeholder="Client" />
                                        <x-select.styled wire:model="items.{{ $index }}.service_id"
                                            :options="$this->serviceOptions" searchable placeholder="Service..."
                                            x-on:select="$wire.fillServiceData({{ $index }})" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-input wire:model="items.{{ $index }}.service_name"
                                            placeholder="Custom service" />
                                        <x-input wire:model="items.{{ $index }}.quantity" type="number"
                                            min="1" placeholder="Qty" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <x-wireui-currency prefix="Rp "
                                            wire:model.blur="items.{{ $index }}.unit_price"
                                            placeholder="Price" />
                                        <x-wireui-currency prefix="Rp "
                                            wire:model.blur="items.{{ $index }}.cogs_amount"
                                            placeholder="COGS" />
                                    </div>

                                    <div class="text-center">
                                        @php
                                            $profit = ($item['amount'] ?? 0) - ($item['cogs_amount'] ?? 0);
                                            $profitClass = $profit >= 0 ? 'green' : 'red';
                                        @endphp
                                        <x-badge text="Rp {{ number_format($profit, 0, ',', '.') }}"
                                            color="{{ $profitClass }}" light />
                                    </div>
                                </div>

                                <!-- Desktop Layout -->
                                <div class="hidden lg:flex gap-2 items-center">
                                    <div class="w-8 flex justify-center">
                                        <input type="checkbox" wire:model="selectedItems" value="{{ $index }}"
                                            class="rounded border-gray-300 dark:border-zinc-600">
                                    </div>
                                    <div class="w-8 flex justify-center">
                                        <div
                                            class="w-6 h-6 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $index + 1 }}</span>
                                        </div>
                                    </div>

                                    <!-- Client -->
                                    <div class="flex-1">
                                        <x-select.styled wire:model="items.{{ $index }}.client_id"
                                            :options="$this->clientOptions" searchable placeholder="Client" />
                                    </div>

                                    <!-- Service -->
                                    <div class="flex-1 space-y-1">
                                        <x-select.styled wire:model="items.{{ $index }}.service_id"
                                            :options="$this->serviceOptions" searchable placeholder="Service..."
                                            x-on:select="$wire.fillServiceData({{ $index }})" />
                                        <x-input wire:model="items.{{ $index }}.service_name"
                                            placeholder="Custom service" />
                                    </div>

                                    <!-- Qty -->
                                    <div class="w-20">
                                        <x-input wire:model.blur="items.{{ $index }}.quantity" type="number"
                                            min="1" placeholder="1" />
                                    </div>

                                    <!-- Price & COGS -->
                                    <div class="flex-1 space-y-1">
                                        <x-wireui-currency prefix="Rp "
                                            wire:model.blur="items.{{ $index }}.unit_price"
                                            placeholder="Price" />
                                        <x-wireui-currency prefix="Rp "
                                            wire:model.blur="items.{{ $index }}.cogs_amount"
                                            placeholder="COGS" />
                                    </div>

                                    <!-- Profit -->
                                    <div class="w-24 flex justify-center">
                                        @php
                                            $profit = ($item['amount'] ?? 0) - ($item['cogs_amount'] ?? 0);
                                            $profitClass = $profit >= 0 ? 'green' : 'red';
                                        @endphp
                                        <x-badge text="Rp {{ number_format($profit, 0, ',', '.') }}"
                                            color="{{ $profitClass }}" light />
                                    </div>

                                    <!-- Actions -->
                                    <div class="w-16 flex justify-center">
                                        <x-button wire:click="removeItem({{ $index }})" color="red"
                                            size="xs" outline icon="trash"
                                            loading="removeItem({{ $index }})" />
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Items -->
                    <div class="flex justify-end items-center gap-2">
                        <x-input wire:model="itemsToAdd" type="number" min="1" placeholder="1"
                            class="w-fit" />
                        <x-button wire:click="addMultipleItems" color="primary" size="xs" icon="plus"
                            loading="addMultipleItems">
                            Add Items
                        </x-button>
                    </div>
                @else
                    <div
                        class="text-center py-6 text-gray-500 dark:text-zinc-400 border border-dashed border-gray-300 dark:border-zinc-600 rounded-lg">
                        <p class="text-sm">No items</p>
                        <x-button wire:click="addItem" color="primary" size="xs" icon="plus"
                            loading="addItem" class="mt-2">
                            Add First Item
                        </x-button>
                    </div>
                @endif
            </div>

            <!-- Discount -->
            <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" wire:model.live="hasDiscount" id="hasDiscount"
                        class="rounded border-gray-300 dark:border-zinc-600">
                    <label for="hasDiscount" class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                        Discount
                    </label>

                    @if ($hasDiscount)
                        <div class="flex gap-2 flex-1">
                            <x-select.styled wire:model="discount.type" :options="[
                                ['label' => 'Fixed', 'value' => 'fixed'],
                                ['label' => '%', 'value' => 'percentage'],
                            ]" class="w-20" />

                            @if ($discount['type'] === 'percentage')
                                <x-input wire:model="discount.value" suffix="%" placeholder="0"
                                    class="w-24" />
                            @else
                                <x-wireui-currency prefix="Rp " wire:model="discount.value" class="w-24" />
                            @endif

                            <x-input wire:model="discount.reason" placeholder="Reason" class="flex-1" />
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            <div
                class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-primary-200 dark:border-primary-700">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <x-icon name="calendar-days" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($invoiceData['scheduled_date'])->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-3 items-center">
                        @if ($hasDiscount)
                            <x-badge text="-Rp {{ number_format($this->discountAmount, 0, ',', '.') }}"
                                color="red" light />
                        @endif
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-zinc-400">Total</div>
                            <div class="font-bold text-xl text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex justify-between w-full">
                <x-button wire:click="$set('modal', false)" color="gray" size="sm">
                    Cancel
                </x-button>
                <x-button type="submit" form="edit-invoice-form" color="primary" loading="save" icon="check"
                    size="sm">
                    Update Invoice
                </x-button>
            </div>
        </x-slot:footer>
    @endif
</x-modal>
