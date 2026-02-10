<div>
    <x-modal title="{{ __('pages.create_fund_request') }}" wire="modal" size="4xl" center persistent>
        {{-- HEADER --}}
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="currency-dollar" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.create_fund_request') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.create_fund_request_description') }}</p>
                </div>
            </div>
        </x-slot:title>

        {{-- FORM CONTENT --}}
        <form id="create-fund-request-form" wire:submit="saveAsDraft" class="space-y-6">
            {{-- Request Number (auto-generated, editable) --}}
            <div class="flex items-end gap-3 p-3 rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
                <x-icon name="hashtag" class="w-5 h-5 text-primary-600 dark:text-primary-400 flex-shrink-0 mb-2" />
                <div class="flex-1">
                    <x-input wire:model="requestNumber"
                             label="{{ __('pages.request_number') }}"
                             placeholder="001/KSN/II/2026"
                             class="font-mono" />
                </div>
                <div class="mb-0.5">
                    <p class="text-xs text-primary-600 dark:text-primary-400">{{ __('pages.request_number_auto_hint') }}</p>
                </div>
            </div>

            {{-- Request Header Section --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.basic_details_section') }}</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.basic_details_description') }}</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Title --}}
                    <div class="lg:col-span-2">
                        <x-input wire:model="title" label="{{ __('pages.fund_request_title_label') }}" placeholder="{{ __('pages.fund_request_title_placeholder') }}" />
                    </div>

                    {{-- Purpose --}}
                    <div class="lg:col-span-2">
                        <x-textarea wire:model="purpose" label="{{ __('pages.purpose_label') }}" placeholder="{{ __('pages.purpose_placeholder') }}" rows="3" />
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-2">{{ __('pages.priority_label') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors
                                {{ $priority === 'low' ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-secondary-200 dark:border-dark-600' }}">
                                <input type="radio" wire:model.live="priority" value="low" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <x-icon name="arrow-down" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.priority_low') }}</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors
                                {{ $priority === 'medium' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-secondary-200 dark:border-dark-600' }}">
                                <input type="radio" wire:model.live="priority" value="medium" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <x-icon name="minus" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.priority_medium') }}</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors
                                {{ $priority === 'high' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'border-secondary-200 dark:border-dark-600' }}">
                                <input type="radio" wire:model.live="priority" value="high" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <x-icon name="arrow-up" class="w-4 h-4 text-yellow-600 dark:text-yellow-400" />
                                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.priority_high') }}</span>
                                </div>
                            </label>

                            <label class="flex items-center p-3 border rounded-xl cursor-pointer transition-colors
                                {{ $priority === 'urgent' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-secondary-200 dark:border-dark-600' }}">
                                <input type="radio" wire:model.live="priority" value="urgent" class="sr-only">
                                <div class="flex items-center gap-2">
                                    <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.priority_urgent') }}</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Needed By Date --}}
                    <div>
                        <x-date wire:model="needed_by_date" label="{{ __('pages.needed_by_label') }}" :min="today()" />
                    </div>

                    {{-- Attachment --}}
                    <div class="lg:col-span-2">
                        <x-upload wire:model="attachment"
                                  label="{{ __('pages.supporting_document') }}"
                                  tip="{{ __('pages.supporting_document_tip') }}"
                                  accept="application/pdf,image/jpeg,image/png"
                                  :multiple="false" />
                    </div>
                </div>
            </div>

            {{-- Items Section --}}
            <div class="space-y-4">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.items_section') }}</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.items_section_description') }}</p>
                        </div>
                        <x-button wire:click="addItem" color="primary" size="sm" type="button">
                            <x-slot:left>
                                <x-icon name="plus" class="w-4 h-4" />
                            </x-slot:left>
                            {{ __('pages.add_item_button') }}
                        </x-button>
                    </div>
                </div>

                {{-- Items Table --}}
                @if (count($items) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-secondary-200 dark:border-dark-600">
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 40px;">#</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 250px;">{{ __('pages.item_description_label') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 220px;">{{ __('pages.category_label') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 90px;">{{ __('pages.quantity_label') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 160px;">{{ __('pages.unit_price_label') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 130px;">{{ __('common.amount') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold" style="min-width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $index => $item)
                                    <tr class="border-b border-secondary-100 dark:border-dark-700">
                                        <td class="py-3 px-2 text-dark-600 dark:text-dark-400" style="min-width: 40px;">{{ $index + 1 }}</td>
                                        <td class="py-3 px-2" style="min-width: 250px;">
                                            <x-input wire:model.blur="items.{{ $index }}.description"
                                                     placeholder="{{ __('pages.item_description_placeholder') }}"
                                                     class="h-8" />
                                        </td>
                                        <td class="py-3 px-2" style="min-width: 220px;">
                                            <x-select.styled wire:model="items.{{ $index }}.category_id"
                                                             :options="$this->categories"
                                                             placeholder="{{ __('pages.select_category') }}"
                                                             searchable
                                                             class="h-8" />
                                        </td>
                                        <td class="py-3 px-2" style="min-width: 90px;">
                                            <x-input type="number"
                                                     wire:model.blur="items.{{ $index }}.quantity"
                                                     placeholder="1"
                                                     min="1"
                                                     class="h-8" />
                                        </td>
                                        <td class="py-3 px-2" style="min-width: 160px;">
                                            <x-currency-input wire:model.blur="items.{{ $index }}.unit_price"
                                                            placeholder="0"
                                                            class="h-8" />
                                        </td>
                                        <td class="py-3 px-2 font-semibold text-dark-900 dark:text-dark-50" style="min-width: 130px;">
                                            Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-2" style="min-width: 50px;">
                                            @if (count($items) > 1)
                                                <x-button wire:click="removeItem({{ $index }})"
                                                          color="red"
                                                          size="sm"
                                                          type="button"
                                                          title="{{ __('pages.remove_item_button') }}">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </x-button>
                                            @endif
                                        </td>
                                    </tr>
                                    @if (!empty($item['notes']))
                                        <tr>
                                            <td></td>
                                            <td colspan="6" class="py-2 px-2">
                                                <x-textarea wire:model="items.{{ $index }}.notes"
                                                            placeholder="{{ __('pages.additional_notes_placeholder') }}"
                                                            rows="2" />
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach

                                {{-- Total Row --}}
                                <tr class="bg-secondary-50 dark:bg-dark-700">
                                    <td colspan="5" class="py-3 px-2 text-right font-bold text-dark-900 dark:text-dark-50">
                                        {{ __('pages.total_request_amount') }}:
                                    </td>
                                    <td class="py-3 px-2 text-right font-bold text-lg text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-dark-500 dark:text-dark-400">
                        <x-icon name="document-text" class="w-12 h-12 mx-auto mb-2 opacity-50" />
                        <p>{{ __('pages.no_items_yet') }}</p>
                    </div>
                @endif
            </div>
        </form>

        {{-- FOOTER --}}
        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)"
                          color="zinc"
                          class="w-full sm:w-auto order-3 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>

                <x-button wire:click="saveAsDraft"
                          color="secondary"
                          icon="document"
                          loading="saveAsDraft"
                          class="w-full sm:w-auto order-2 sm:order-2">
                    {{ __('pages.save_as_draft') }}
                </x-button>

                <x-button wire:click="submitForApproval"
                          color="green"
                          icon="paper-airplane"
                          loading="submitForApproval"
                          class="w-full sm:w-auto order-1 sm:order-3">
                    {{ __('pages.submit_for_approval') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
