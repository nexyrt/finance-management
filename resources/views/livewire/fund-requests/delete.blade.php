<div>
    @if ($fundRequest)
        <x-modal title="{{ __('pages.delete_fund_request') }}" wire="modal" size="lg" center>
            {{-- HEADER --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="exclamation-triangle" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.delete_fund_request') }}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.delete_action_irreversible') }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- CONTENT --}}
            <div class="space-y-4">
                <p class="text-dark-700 dark:text-dark-300">
                    {{ __('pages.confirm_delete_fund_request', ['title' => $fundRequest->title]) }}
                </p>

                <div class="p-4 rounded-xl bg-secondary-50 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600">
                    <div class="space-y-2">
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_request_title') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->title }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total_amount') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.items') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ __('pages.items_count', ['count' => $fundRequest->items->count()]) }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.status') }}</label>
                            <p>
                                @php
                                    $statusColors = ['draft' => 'secondary', 'rejected' => 'red'];
                                    $color = $statusColors[$fundRequest->status] ?? 'secondary';
                                @endphp
                                <x-badge :text="ucfirst($fundRequest->status)" :color="$color" size="sm" />
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <x-icon name="exclamation-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-red-800 dark:text-red-200">
                        <p class="font-semibold">{{ __('common.warning') }}:</p>
                        <p>{{ __('pages.delete_fund_request_warning') }}</p>
                    </div>
                </div>
            </div>

            {{-- FOOTER --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)"
                              color="zinc"
                              class="w-full sm:w-auto order-2 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>

                    <x-button wire:click="delete"
                              color="red"
                              icon="trash"
                              loading="delete"
                              class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('pages.delete_request') }}
                    </x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif
</div>
