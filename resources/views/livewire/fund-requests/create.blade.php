<div>
    <x-modal wire="modal" size="4xl" center persistent>
        {{-- HEADER --}}
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center shrink-0">
                    <x-icon name="document-plus" class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.create_fund_request') }}</h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400">{{ __('pages.create_fund_request_description') }}</p>
                </div>
                {{-- Request Number Pill --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-800 shrink-0">
                    <x-icon name="hashtag" class="w-3.5 h-3.5 text-primary-500 dark:text-primary-400" />
                    <span class="font-mono text-xs font-semibold text-primary-600 dark:text-primary-400">{{ $requestNumber }}</span>
                </div>
            </div>
        </x-slot:title>

        {{-- FORM CONTENT --}}
        <form id="create-fund-request-form" wire:submit="saveAsDraft" class="space-y-6">

            {{-- ── Informasi Dasar ── --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <x-icon name="information-circle" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.basic_details_section') }}</h4>
                    <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Judul --}}
                    <div class="lg:col-span-2">
                        <x-input wire:model="title"
                                 :label="__('pages.fund_request_title_label')"
                                 :placeholder="__('pages.fund_request_title_placeholder')" />
                    </div>

                    {{-- Tujuan --}}
                    <div class="lg:col-span-2">
                        <x-textarea wire:model="purpose"
                                    :label="__('pages.purpose_label')"
                                    :placeholder="__('pages.purpose_placeholder')"
                                    rows="3" />
                    </div>

                    {{-- Dibutuhkan Pada --}}
                    <div>
                        <x-date wire:model="needed_by_date"
                                :label="__('pages.needed_by_label')"
                                :min="today()" />
                    </div>

                    {{-- Nomor Pengajuan (editable) --}}
                    <div>
                        <x-input wire:model="requestNumber"
                                 :label="__('pages.request_number')"
                                 placeholder="001/KSN/II/2026"
                                 class="font-mono" />
                        <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_number_auto_hint') }}</p>
                    </div>
                </div>
            </div>

            {{-- ── Prioritas ── --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <x-icon name="flag" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.priority_label') }}</h4>
                    <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    {{-- Low --}}
                    <label class="relative flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150
                        {{ $priority === 'low' ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-sm' : 'border-zinc-200 dark:border-dark-600 hover:border-green-300 dark:hover:border-green-700 hover:bg-green-50/50 dark:hover:bg-green-900/10' }}">
                        <input type="radio" wire:model.live="priority" value="low" class="sr-only">
                        <div class="h-8 w-8 rounded-lg {{ $priority === 'low' ? 'bg-green-100 dark:bg-green-900/40' : 'bg-zinc-100 dark:bg-dark-700' }} flex items-center justify-center transition-colors">
                            <x-icon name="arrow-down-circle" class="w-4 h-4 {{ $priority === 'low' ? 'text-green-600 dark:text-green-400' : 'text-dark-400 dark:text-dark-500' }}" />
                        </div>
                        <span class="text-xs font-semibold {{ $priority === 'low' ? 'text-green-700 dark:text-green-300' : 'text-dark-600 dark:text-dark-400' }}">
                            {{ __('pages.priority_low') }}
                        </span>
                        @if ($priority === 'low')
                            <div class="absolute top-1.5 right-1.5 h-4 w-4 bg-green-500 rounded-full flex items-center justify-center">
                                <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                            </div>
                        @endif
                    </label>

                    {{-- Medium --}}
                    <label class="relative flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150
                        {{ $priority === 'medium' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-sm' : 'border-zinc-200 dark:border-dark-600 hover:border-blue-300 dark:hover:border-blue-700 hover:bg-blue-50/50 dark:hover:bg-blue-900/10' }}">
                        <input type="radio" wire:model.live="priority" value="medium" class="sr-only">
                        <div class="h-8 w-8 rounded-lg {{ $priority === 'medium' ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-zinc-100 dark:bg-dark-700' }} flex items-center justify-center transition-colors">
                            <x-icon name="minus-circle" class="w-4 h-4 {{ $priority === 'medium' ? 'text-blue-600 dark:text-blue-400' : 'text-dark-400 dark:text-dark-500' }}" />
                        </div>
                        <span class="text-xs font-semibold {{ $priority === 'medium' ? 'text-blue-700 dark:text-blue-300' : 'text-dark-600 dark:text-dark-400' }}">
                            {{ __('pages.priority_medium') }}
                        </span>
                        @if ($priority === 'medium')
                            <div class="absolute top-1.5 right-1.5 h-4 w-4 bg-blue-500 rounded-full flex items-center justify-center">
                                <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                            </div>
                        @endif
                    </label>

                    {{-- High --}}
                    <label class="relative flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150
                        {{ $priority === 'high' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 shadow-sm' : 'border-zinc-200 dark:border-dark-600 hover:border-yellow-300 dark:hover:border-yellow-700 hover:bg-yellow-50/50 dark:hover:bg-yellow-900/10' }}">
                        <input type="radio" wire:model.live="priority" value="high" class="sr-only">
                        <div class="h-8 w-8 rounded-lg {{ $priority === 'high' ? 'bg-yellow-100 dark:bg-yellow-900/40' : 'bg-zinc-100 dark:bg-dark-700' }} flex items-center justify-center transition-colors">
                            <x-icon name="arrow-up-circle" class="w-4 h-4 {{ $priority === 'high' ? 'text-yellow-600 dark:text-yellow-400' : 'text-dark-400 dark:text-dark-500' }}" />
                        </div>
                        <span class="text-xs font-semibold {{ $priority === 'high' ? 'text-yellow-700 dark:text-yellow-300' : 'text-dark-600 dark:text-dark-400' }}">
                            {{ __('pages.priority_high') }}
                        </span>
                        @if ($priority === 'high')
                            <div class="absolute top-1.5 right-1.5 h-4 w-4 bg-yellow-500 rounded-full flex items-center justify-center">
                                <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                            </div>
                        @endif
                    </label>

                    {{-- Urgent --}}
                    <label class="relative flex flex-col items-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150
                        {{ $priority === 'urgent' ? 'border-red-500 bg-red-50 dark:bg-red-900/20 shadow-sm' : 'border-zinc-200 dark:border-dark-600 hover:border-red-300 dark:hover:border-red-700 hover:bg-red-50/50 dark:hover:bg-red-900/10' }}">
                        <input type="radio" wire:model.live="priority" value="urgent" class="sr-only">
                        <div class="h-8 w-8 rounded-lg {{ $priority === 'urgent' ? 'bg-red-100 dark:bg-red-900/40' : 'bg-zinc-100 dark:bg-dark-700' }} flex items-center justify-center transition-colors">
                            <x-icon name="exclamation-circle" class="w-4 h-4 {{ $priority === 'urgent' ? 'text-red-600 dark:text-red-400' : 'text-dark-400 dark:text-dark-500' }}" />
                        </div>
                        <span class="text-xs font-semibold {{ $priority === 'urgent' ? 'text-red-700 dark:text-red-300' : 'text-dark-600 dark:text-dark-400' }}">
                            {{ __('pages.priority_urgent') }}
                        </span>
                        @if ($priority === 'urgent')
                            <div class="absolute top-1.5 right-1.5 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center">
                                <x-icon name="check" class="w-2.5 h-2.5 text-white" />
                            </div>
                        @endif
                    </label>
                </div>
            </div>

            {{-- ── Lampiran ── --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <x-icon name="paper-clip" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.attachment') }}</h4>
                    <span class="text-xs text-dark-400 dark:text-dark-500">({{ __('common.optional') }})</span>
                    <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                </div>

                <x-upload wire:model="attachment"
                          :label="__('pages.supporting_document')"
                          :tip="__('pages.supporting_document_tip')"
                          accept="application/pdf,image/jpeg,image/png"
                          :multiple="false" />
            </div>

            {{-- ── Rincian Anggaran ── --}}
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="h-6 w-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center shrink-0">
                        <x-icon name="list-bullet" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.items_section') }}</h4>
                    <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                    <x-button wire:click="addItem" color="primary" size="sm" type="button">
                        <x-slot:left>
                            <x-icon name="plus" class="w-3.5 h-3.5" />
                        </x-slot:left>
                        {{ __('pages.add_item_button') }}
                    </x-button>
                </div>

                @if (count($items) > 0)
                    <div class="rounded-xl border border-zinc-200 dark:border-dark-600 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-zinc-50 dark:bg-dark-700 border-b border-zinc-200 dark:border-dark-600">
                                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide w-8">#</th>
                                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide" style="min-width:240px">{{ __('pages.item_description_label') }}</th>
                                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide" style="min-width:200px">{{ __('pages.category_label') }}</th>
                                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide" style="min-width:80px">{{ __('pages.quantity_label') }}</th>
                                        <th class="text-left py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide" style="min-width:150px">{{ __('pages.unit_price_label') }}</th>
                                        <th class="text-right py-2.5 px-3 text-xs font-semibold text-dark-500 dark:text-dark-400 uppercase tracking-wide" style="min-width:120px">{{ __('common.amount') }}</th>
                                        <th class="py-2.5 px-3 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-dark-700">
                                    @foreach ($items as $index => $item)
                                        <tr class="bg-white dark:bg-dark-800 hover:bg-zinc-50/70 dark:hover:bg-dark-700/50 transition-colors group">
                                            <td class="py-2.5 px-3 text-xs text-dark-400 dark:text-dark-500 font-medium">{{ $index + 1 }}</td>
                                            <td class="py-2.5 px-3" style="min-width:240px">
                                                <div class="space-y-1.5">
                                                    <x-input wire:model.blur="items.{{ $index }}.description"
                                                             :placeholder="__('pages.item_description_placeholder')"
                                                             class="h-8 text-sm" />
                                                    <x-input wire:model="items.{{ $index }}.notes"
                                                             :placeholder="__('pages.additional_notes_placeholder')"
                                                             class="h-7 text-xs text-dark-500 dark:text-dark-400" />
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3" style="min-width:200px">
                                                <x-select.styled wire:model="items.{{ $index }}.category_id"
                                                                 :options="$this->categories"
                                                                 :placeholder="__('pages.select_category')"
                                                                 searchable />
                                            </td>
                                            <td class="py-2.5 px-3" style="min-width:80px">
                                                <x-input type="number"
                                                         wire:model.blur="items.{{ $index }}.quantity"
                                                         placeholder="1"
                                                         min="1"
                                                         class="h-8 text-sm text-center" />
                                            </td>
                                            <td class="py-2.5 px-3" style="min-width:150px">
                                                <x-currency-input wire:model.blur="items.{{ $index }}.unit_price"
                                                                  placeholder="0"
                                                                  class="h-8 text-sm" />
                                            </td>
                                            <td class="py-2.5 px-3 text-right" style="min-width:120px">
                                                <span class="font-semibold text-dark-900 dark:text-dark-50 text-sm">
                                                    Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 w-10">
                                                @if (count($items) > 1)
                                                    <button wire:click="removeItem({{ $index }})"
                                                            type="button"
                                                            class="opacity-0 group-hover:opacity-100 transition-opacity h-7 w-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40"
                                                            title="{{ __('pages.remove_item_button') }}">
                                                        <x-icon name="trash" class="w-3.5 h-3.5" />
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-linear-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border-t border-primary-200 dark:border-primary-800">
                                        <td colspan="5" class="py-3 px-3 text-right text-sm font-semibold text-dark-700 dark:text-dark-300">
                                            {{ __('pages.total_request_amount') }}
                                        </td>
                                        <td class="py-3 px-3 text-right text-base font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-10 rounded-xl border-2 border-dashed border-zinc-300 dark:border-dark-600 bg-zinc-50/50 dark:bg-dark-700/30">
                        <div class="h-12 w-12 bg-zinc-100 dark:bg-dark-700 rounded-xl flex items-center justify-center mb-3">
                            <x-icon name="document-text" class="w-6 h-6 text-dark-400 dark:text-dark-500" />
                        </div>
                        <p class="text-sm text-dark-500 dark:text-dark-400 mb-3">{{ __('pages.no_items_yet') }}</p>
                        <x-button wire:click="addItem" color="primary" size="sm" type="button">
                            <x-slot:left>
                                <x-icon name="plus" class="w-3.5 h-3.5" />
                            </x-slot:left>
                            {{ __('pages.add_item_button') }}
                        </x-button>
                    </div>
                @endif
            </div>

        </form>

        {{-- FOOTER --}}
        <x-slot:footer>
            {{-- Total Summary bar --}}
            @if (count($items) > 0 && $this->totalAmount > 0)
                <div class="flex items-center gap-2 px-1 mr-auto">
                    <span class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total') }}:</span>
                    <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($this->totalAmount, 0, ',', '.') }}
                    </span>
                    <span class="text-xs text-dark-400 dark:text-dark-500">· {{ count($items) }} {{ __('pages.items') }}</span>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-end gap-2">
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
