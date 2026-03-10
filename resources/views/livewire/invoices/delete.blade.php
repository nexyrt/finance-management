<div>
    <x-modal wire="modal" size="lg" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">
                        {{ __('invoice.delete_confirm_title') }}
                    </h3>
                    @if ($invoice)
                        <p class="text-sm text-dark-600 dark:text-dark-400">
                            {{ __('invoice.delete_action_subtitle', ['number' => $invoice->invoice_number]) }}
                        </p>
                    @endif
                </div>
            </div>
        </x-slot:title>

        @if ($invoice)
            {{-- Invoice Info --}}
            <div class="mb-5 p-4 bg-zinc-50 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.client') }}</p>
                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $invoice->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total') }}</p>
                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                            Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.status') }}</p>
                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50 capitalize">
                            {{ $invoice->status }}
                        </p>
                    </div>
                    @if ($invoice->payments->count() > 0)
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.payments') }}</p>
                            <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                {{ $invoice->payments->count() }} (Rp {{ number_format($invoice->amount_paid, 0, ',', '.') }})
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Choice --}}
            <p class="text-sm font-semibold text-dark-700 dark:text-dark-300 mb-3">
                {{ __('invoice.delete_action_title') }}
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Cancel Option --}}
                <button
                    wire:click="$set('action', 'cancel')"
                    type="button"
                    class="text-left p-4 rounded-xl border-2 transition-all duration-200"
                    x-bind:class="$wire.action === 'cancel'
                        ? 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-500'
                        : 'border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 hover:border-yellow-300 dark:hover:border-yellow-600'">
                    <div class="flex items-start gap-3">
                        <div class="h-9 w-9 rounded-lg flex items-center justify-center shrink-0"
                             x-bind:class="$wire.action === 'cancel' ? 'bg-yellow-100 dark:bg-yellow-900/40' : 'bg-zinc-100 dark:bg-dark-700'">
                            <x-icon name="x-circle" class="w-5 h-5"
                                    x-bind:class="$wire.action === 'cancel' ? 'text-yellow-600 dark:text-yellow-400' : 'text-dark-400'" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold"
                               x-bind:class="$wire.action === 'cancel' ? 'text-yellow-700 dark:text-yellow-300' : 'text-dark-900 dark:text-dark-50'">
                                {{ __('invoice.cancel_invoice_label') }}
                            </p>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mt-1 leading-relaxed">
                                {{ __('invoice.cancel_invoice_desc') }}
                            </p>
                        </div>
                    </div>
                </button>

                {{-- Permanent Delete Option --}}
                <button
                    wire:click="$set('action', 'permanent')"
                    type="button"
                    class="text-left p-4 rounded-xl border-2 transition-all duration-200"
                    x-bind:class="$wire.action === 'permanent'
                        ? 'border-red-400 bg-red-50 dark:bg-red-900/20 dark:border-red-500'
                        : 'border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 hover:border-red-300 dark:hover:border-red-600'">
                    <div class="flex items-start gap-3">
                        <div class="h-9 w-9 rounded-lg flex items-center justify-center shrink-0"
                             x-bind:class="$wire.action === 'permanent' ? 'bg-red-100 dark:bg-red-900/40' : 'bg-zinc-100 dark:bg-dark-700'">
                            <x-icon name="trash" class="w-5 h-5"
                                    x-bind:class="$wire.action === 'permanent' ? 'text-red-600 dark:text-red-400' : 'text-dark-400'" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold"
                               x-bind:class="$wire.action === 'permanent' ? 'text-red-700 dark:text-red-300' : 'text-dark-900 dark:text-dark-50'">
                                {{ __('invoice.permanent_delete_label') }}
                            </p>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mt-1 leading-relaxed">
                                {{ __('invoice.permanent_delete_desc') }}
                            </p>
                        </div>
                    </div>
                </button>
            </div>

            {{-- Warning for permanent delete --}}
            <div x-show="$wire.action === 'permanent'"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-4 flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-xl">
                <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                <p class="text-xs text-red-600 dark:text-red-400">
                    {{ __('invoice.delete_permanent_note') }}
                    @if ($invoice->payments->count() > 0)
                        {{ __('invoice.delete_confirm_with_payments', [
                            'payments_count' => $invoice->payments->count(),
                            'total_paid' => number_format($invoice->amount_paid, 0, ',', '.'),
                        ]) }}
                    @endif
                </p>
            </div>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$set('modal', false)"
                          color="zinc"
                          class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>

                <div x-show="$wire.action === 'cancel'" class="w-full sm:w-auto order-1 sm:order-2">
                    <x-button wire:click="cancel"
                              color="yellow"
                              icon="x-circle"
                              loading="cancel"
                              class="w-full">
                        {{ __('invoice.cancel_confirm_btn') }}
                    </x-button>
                </div>

                <div x-show="$wire.action === 'permanent'" class="w-full sm:w-auto order-1 sm:order-2">
                    <x-button wire:click="delete"
                              color="red"
                              icon="trash"
                              loading="delete"
                              class="w-full">
                        {{ __('invoice.delete_confirm_btn') }}
                    </x-button>
                </div>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
