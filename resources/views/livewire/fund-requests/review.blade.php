<div>
    @if ($fundRequest)
        <x-modal title="{{ __('pages.review_fund_request') }}" wire="modal" size="3xl" center persistent>
            {{-- HEADER --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="clipboard-document-check" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.review_fund_request') }}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.review_fund_request_description') }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- CONTENT --}}
            <div class="space-y-6">
                {{-- Request Information --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.fund_request_information') }}</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_number') }}</label>
                            <p class="text-sm font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $fundRequest->request_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.requestor') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.priority') }}</label>
                            @php
                                $priorityColors = ['low' => 'green', 'medium' => 'blue', 'high' => 'yellow', 'urgent' => 'red'];
                                $color = $priorityColors[$fundRequest->priority] ?? 'secondary';
                            @endphp
                            <p><x-badge :text="ucfirst($fundRequest->priority)" :color="$color" size="sm" /></p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.needed_by') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ \Carbon\Carbon::parse($fundRequest->needed_by_date)->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total_amount') }}</label>
                            <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_request_title') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ translate_text($fundRequest->title) }}</p>
                        </div>
                        <div class="col-span-2">
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.purpose') }}</label>
                            <p class="text-sm text-dark-700 dark:text-dark-300">{{ $fundRequest->purpose }}</p>
                        </div>
                    </div>
                </div>

                {{-- Budget Items --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.request_items') }}</h4>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-secondary-200 dark:border-dark-600">
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.item_description') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.category') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.quantity') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.unit_price') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('common.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fundRequest->items as $item)
                                    <tr class="border-b border-secondary-100 dark:border-dark-700">
                                        <td class="py-2 px-2">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-dark-900 dark:text-dark-50">{{ $item->description }}</span>
                                                @if ($item->notes)
                                                    <span class="text-xs text-dark-500 dark:text-dark-400">{{ $item->notes }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 text-dark-700 dark:text-dark-300">{{ $item->category->full_path }}</td>
                                        <td class="py-2 px-2 text-right text-dark-700 dark:text-dark-300">{{ $item->quantity }}</td>
                                        <td class="py-2 px-2 text-right text-dark-700 dark:text-dark-300">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2 px-2 text-right font-semibold text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-secondary-50 dark:bg-dark-700 font-bold">
                                    <td colspan="4" class="py-3 px-2 text-right text-dark-900 dark:text-dark-50">{{ __('common.total') }}:</td>
                                    <td class="py-3 px-2 text-right text-lg text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Review Notes --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.review_notes_label') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.review_notes_hint') }}</p>
                    </div>

                    <x-textarea wire:model="reviewNotes"
                                placeholder="{{ __('pages.review_notes_placeholder') }}"
                                rows="3" />
                </div>
            </div>

            {{-- FOOTER --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)"
                              color="zinc"
                              class="w-full sm:w-auto order-3 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button wire:click="reject"
                              color="red"
                              icon="x-mark"
                              loading="reject"
                              class="w-full sm:w-auto order-2 sm:order-2">
                        {{ __('pages.reject_button') }}
                    </x-button>

                    <x-button wire:click="approve"
                              color="green"
                              icon="check"
                              loading="approve"
                              class="w-full sm:w-auto order-1 sm:order-3">
                        {{ __('pages.approve_button') }}
                    </x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif
</div>
