<x-modal wire title="{{ __('common.attachment') }}" size="3xl" center>
    @if ($attachment)
        <x-slot:title>
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="paper-clip" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('common.attachment') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">
                        {{ $sourceType === 'payment' ? __('common.payments') : __('common.transactions') }} -
                        {{ \Carbon\Carbon::parse($attachment->payment_date ?? $attachment->transaction_date)->format('d M Y') }}
                    </p>
                </div>
            </div>
        </x-slot:title>

        {{-- File Info --}}
        <div class="mb-4 p-4 bg-zinc-50 dark:bg-dark-700 rounded-lg border border-zinc-200 dark:border-dark-600">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-dark-900 dark:text-dark-50">
                        {{ $attachment->attachment_name }}
                    </div>
                    <div class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        {{ __('pages.type') }}: {{ strtoupper($attachment->attachment_type) }}
                    </div>
                </div>
                <x-button wire:click="download" color="blue" size="sm" icon="arrow-down-tray">
                    {{ __('common.download') }}
                </x-button>
            </div>
        </div>

        {{-- Preview --}}
        <div class="bg-zinc-100 dark:bg-dark-700 rounded-lg p-4 min-h-[400px] flex items-center justify-center">
            @if ($attachment->isImageAttachment())
                <img src="{{ $attachment->attachment_url }}" alt="Attachment preview"
                    class="max-w-full max-h-[600px] rounded-lg shadow-lg">
            @elseif($attachment->isPdfAttachment())
                <iframe src="{{ $attachment->attachment_url }}"
                    class="w-full h-[600px] rounded-lg border-2 border-zinc-300 dark:border-dark-600">
                </iframe>
            @else
                <div class="text-center">
                    <div
                        class="h-16 w-16 bg-zinc-200 dark:bg-dark-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-icon name="document" class="w-8 h-8 text-zinc-500 dark:text-dark-400" />
                    </div>
                    <p class="text-dark-600 dark:text-dark-400 mb-4">
                        {{ __('pages.transaction_attachment_no_preview') }}
                    </p>
                    <x-button wire:click="download" color="blue" icon="arrow-down-tray">
                        {{ __('common.download') }}
                    </x-button>
                </div>
            @endif
        </div>

        {{-- Additional Info --}}
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                <div class="text-xs text-dark-500 dark:text-dark-400 mb-1">{{ __('common.amount') }}</div>
                <div class="text-lg font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($attachment->amount, 0, ',', '.') }}
                </div>
            </div>

            @if ($attachment->reference_number)
                <div class="p-3 bg-zinc-50 dark:bg-dark-700 rounded-lg">
                    <div class="text-xs text-dark-500 dark:text-dark-400 mb-1">{{ __('common.reference') }}</div>
                    <div class="text-sm font-mono font-medium text-dark-900 dark:text-dark-50">
                        {{ $attachment->reference_number }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <x-slot:footer>
        <div class="flex justify-end">
            <x-button wire:click="$toggle('modal')" color="zinc">
                {{ __('common.close') }}
            </x-button>
        </div>
    </x-slot:footer>
</x-modal>
