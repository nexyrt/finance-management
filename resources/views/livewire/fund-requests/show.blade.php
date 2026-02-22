<div>
    @if ($fundRequest)
        <x-modal title="{{ __('pages.fund_request_details') }}" wire="modal" size="4xl" center>
            {{-- HEADER --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="document-text" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('pages.fund_request_details') }}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('pages.fund_request_details_description') }}</p>
                    </div>
                </div>
            </x-slot:title>

            {{-- CONTENT --}}
            <div class="space-y-6">
                {{-- Request Header --}}
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.fund_request_information') }}</h4>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_number') }}</label>
                            <p class="text-sm font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $fundRequest->request_number ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.requestor') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->user->name }}</p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_status') }}</label>
                            @php
                                $statusColors = [
                                    'draft' => 'secondary',
                                    'pending' => 'yellow',
                                    'approved' => 'green',
                                    'rejected' => 'red',
                                    'disbursed' => 'emerald',
                                ];
                                $color = $statusColors[$fundRequest->status] ?? 'secondary';
                            @endphp
                            <p><x-badge :text="ucfirst($fundRequest->status)" :color="$color" /></p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.request_priority') }}</label>
                            @php
                                $priorityColors = ['low' => 'green', 'medium' => 'blue', 'high' => 'yellow', 'urgent' => 'red'];
                                $pColor = $priorityColors[$fundRequest->priority] ?? 'secondary';
                            @endphp
                            <p><x-badge :text="ucfirst($fundRequest->priority)" :color="$pColor" /></p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.needed_by') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ \Carbon\Carbon::parse($fundRequest->needed_by_date)->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.created_date') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ \Carbon\Carbon::parse($fundRequest->created_at)->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('common.total_amount') }}</label>
                            <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="lg:col-span-3">
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.fund_request_title') }}</label>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ translate_text($fundRequest->title) }}</p>
                        </div>
                        <div class="lg:col-span-3">
                            <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.purpose') }}</label>
                            <p class="text-sm text-dark-700 dark:text-dark-300">{{ $fundRequest->purpose }}</p>
                        </div>

                        {{-- Attachment --}}
                        @if ($fundRequest->attachment_path)
                            <div class="lg:col-span-3">
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.attachment') }}</label>
                                <div class="flex items-center gap-2 mt-1">
                                    <x-icon name="document" class="w-4 h-4 text-dark-500" />
                                    <a href="{{ Storage::url($fundRequest->attachment_path) }}"
                                       target="_blank"
                                       class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                        {{ $fundRequest->attachment_name }}
                                    </a>
                                </div>
                            </div>
                        @endif
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
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">#</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.item_description') }}</th>
                                    <th class="text-left pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.category') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.quantity') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('pages.unit_price') }}</th>
                                    <th class="text-right pb-2 px-2 text-dark-700 dark:text-dark-300 font-semibold">{{ __('common.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fundRequest->items as $index => $item)
                                    <tr class="border-b border-secondary-100 dark:border-dark-700">
                                        <td class="py-2 px-2 text-dark-600 dark:text-dark-400">{{ $index + 1 }}</td>
                                        <td class="py-2 px-2">
                                            <div class="flex flex-col">
                                                <span class="font-medium text-dark-900 dark:text-dark-50">{{ $item->description }}</span>
                                                @if ($item->notes)
                                                    <span class="text-xs text-dark-500 dark:text-dark-400">{{ $item->notes }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 text-dark-700 dark:text-dark-300 text-xs">{{ $item->category->full_path }}</td>
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
                                    <td colspan="5" class="py-3 px-2 text-right text-dark-900 dark:text-dark-50">{{ __('common.total') }}:</td>
                                    <td class="py-3 px-2 text-right text-lg text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Review Information (if reviewed) --}}
                @if ($fundRequest->reviewed_by)
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.review_information') }}</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.reviewed_by') }}</label>
                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->reviewer->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.reviewed_at') }}</label>
                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ \Carbon\Carbon::parse($fundRequest->reviewed_at)->format('d M Y H:i') }}
                                </p>
                            </div>
                            @if ($fundRequest->review_notes)
                                <div class="lg:col-span-2">
                                    <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.review_notes') }}</label>
                                    <p class="text-sm text-dark-700 dark:text-dark-300">{{ $fundRequest->review_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Disbursement Information (if disbursed) --}}
                @if ($fundRequest->disbursed_by)
                    <div class="space-y-4">
                        <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('pages.disbursement_information') }}</h4>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.disbursed_by') }}</label>
                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">{{ $fundRequest->disburser->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.disbursement_date') }}</label>
                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ \Carbon\Carbon::parse($fundRequest->disbursement_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.disbursed_at') }}</label>
                                <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                    {{ \Carbon\Carbon::parse($fundRequest->disbursed_at)->format('d M Y H:i') }}
                                </p>
                            </div>
                            @if ($fundRequest->disbursement_notes)
                                <div class="lg:col-span-2">
                                    <label class="text-xs text-dark-500 dark:text-dark-400">{{ __('pages.disbursement_notes') }}</label>
                                    <p class="text-sm text-dark-700 dark:text-dark-300">{{ $fundRequest->disbursement_notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- FOOTER --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="$set('modal', false)"
                              color="zinc"
                              class="w-full sm:w-auto">
                        {{ __('common.close') }}
                    </x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif
</div>
