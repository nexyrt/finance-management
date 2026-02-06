{{-- resources/views/livewire/clients/delete.blade.php --}}
<div>
    <x-modal wire="clientDeleteModal" id="client-delete-modal" center>
        <x-slot:header>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.delete_client') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.confirm_client_deletion') }}</p>
                </div>
            </div>
        </x-slot:header>

        @if($client)
            <div class="space-y-6">
                {{-- Client Info --}}
                <div class="border border-secondary-200 dark:border-dark-600 rounded-xl p-4">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            @if($client->logo)
                                <img class="h-12 w-12 rounded-xl object-cover" src="{{ $client->logo }}" alt="{{ $client->name }}">
                            @else
                                <div class="h-12 w-12 rounded-xl flex items-center justify-center {{ $client->type === 'individual' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-purple-50 dark:bg-purple-900/20' }}">
                                    <x-icon name="{{ $client->type === 'individual' ? 'user' : 'building-office' }}"
                                        class="w-6 h-6 {{ $client->type === 'individual' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" />
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-dark-900 dark:text-dark-50">{{ $client->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <x-badge text="{{ $client->type === 'individual' ? __('pages.individual') : __('pages.company') }}"
                                         color="{{ $client->type === 'individual' ? 'blue' : 'purple' }}" />
                                @if($client->NPWP)
                                    <span class="text-xs text-dark-600 dark:text-dark-400 font-mono">
                                        {{ $client->NPWP }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Warning Message --}}
                @if($client?->invoices?->count() > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200/50 dark:border-red-700/50">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 bg-red-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <x-icon name="exclamation-triangle" class="w-4 h-4 text-red-600 dark:text-red-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-red-900 dark:text-red-100 mb-1">{{ __('pages.attention') }}</h4>
                                <p class="text-sm text-red-800 dark:text-red-200 mb-3">
                                    {!! __('pages.client_has_invoices', ['count' => $client->invoices->count()]) !!}
                                </p>
                                <div class="bg-red-100 dark:bg-red-800/30 rounded-lg p-3">
                                    <div class="text-sm text-red-800 dark:text-red-200">
                                        <div class="font-medium mb-1">{{ __('pages.total_invoice_value') }}</div>
                                        <div class="text-lg font-bold">Rp {{ number_format($client->invoices->sum('total_amount'), 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200/50 dark:border-green-700/50">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <x-icon name="check-circle" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            </div>
                            <p class="text-sm text-green-800 dark:text-green-200">
                                {{ __('pages.client_no_invoices') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="$toggle('clientDeleteModal')" color="secondary" outline class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button wire:click="confirm" x-on:click="$modalClose('client-delete-modal')" color="red" icon="trash" loading="confirm"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('pages.delete_client') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>
