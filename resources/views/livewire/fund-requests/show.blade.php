<div>
    @if ($fundRequest)
        @php
            $statusColors = [
                'draft'     => ['badge' => 'secondary', 'bg' => 'bg-zinc-100 dark:bg-dark-700',        'text' => 'text-zinc-600 dark:text-zinc-400',         'icon' => 'pencil-square'],
                'pending'   => ['badge' => 'yellow',    'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',  'text' => 'text-yellow-700 dark:text-yellow-400',      'icon' => 'clock'],
                'approved'  => ['badge' => 'green',     'bg' => 'bg-green-50 dark:bg-green-900/20',    'text' => 'text-green-700 dark:text-green-400',        'icon' => 'check-circle'],
                'rejected'  => ['badge' => 'red',       'bg' => 'bg-red-50 dark:bg-red-900/20',        'text' => 'text-red-700 dark:text-red-400',            'icon' => 'x-circle'],
                'disbursed' => ['badge' => 'emerald',   'bg' => 'bg-emerald-50 dark:bg-emerald-900/20','text' => 'text-emerald-700 dark:text-emerald-400',    'icon' => 'banknotes'],
            ];
            $priorityColors = [
                'low'    => ['badge' => 'green',  'bg' => 'bg-green-50 dark:bg-green-900/20',   'text' => 'text-green-700 dark:text-green-400'],
                'medium' => ['badge' => 'blue',   'bg' => 'bg-blue-50 dark:bg-blue-900/20',     'text' => 'text-blue-700 dark:text-blue-400'],
                'high'   => ['badge' => 'yellow', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'text' => 'text-yellow-700 dark:text-yellow-400'],
                'urgent' => ['badge' => 'red',    'bg' => 'bg-red-50 dark:bg-red-900/20',       'text' => 'text-red-700 dark:text-red-400'],
            ];
            $sc = $statusColors[$fundRequest->status]   ?? $statusColors['draft'];
            $pc = $priorityColors[$fundRequest->priority] ?? $priorityColors['medium'];
        @endphp

        <x-modal wire="modal" size="4xl" center>
            {{-- HEADER --}}
            <x-slot:title>
                <div class="flex items-start gap-4 my-3">
                    {{-- Icon (warna sesuai status) --}}
                    <div class="h-12 w-12 {{ $sc['bg'] }} rounded-xl flex items-center justify-center shrink-0">
                        <x-icon name="{{ $sc['icon'] }}" class="w-6 h-6 {{ $sc['text'] }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-0.5">
                            <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ translate_text($fundRequest->title) }}</h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-2 py-0.5 rounded-md border border-primary-200 dark:border-primary-800">
                                {{ $fundRequest->request_number }}
                            </span>
                            <x-badge :text="__('pages.fr_status_' . $fundRequest->status)"    :color="$sc['badge']" size="sm" />
                            <x-badge :text="__('pages.fr_priority_' . $fundRequest->priority)" :color="$pc['badge']" size="sm" />
                        </div>
                    </div>
                </div>
            </x-slot:title>

            {{-- CONTENT --}}
            <div class="space-y-6">

                {{-- ── Ringkasan Keuangan ── --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="flex items-center gap-3 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-200 dark:border-primary-800">
                        <div class="h-10 w-10 bg-primary-100 dark:bg-primary-900/40 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <p class="text-xs text-primary-600 dark:text-primary-400 font-medium">{{ __('common.total_amount') }}</p>
                            <p class="text-lg font-bold text-primary-700 dark:text-primary-300">
                                Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                        <div class="h-10 w-10 bg-zinc-100 dark:bg-dark-600 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="user" class="w-5 h-5 text-dark-500 dark:text-dark-400" />
                        </div>
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400 font-medium">{{ __('pages.requestor') }}</p>
                            <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $fundRequest->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-zinc-50 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                        <div class="h-10 w-10 bg-zinc-100 dark:bg-dark-600 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="calendar" class="w-5 h-5 text-dark-500 dark:text-dark-400" />
                        </div>
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400 font-medium">{{ __('pages.needed_by') }}</p>
                            <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                {{ \Carbon\Carbon::parse($fundRequest->needed_by_date)->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ── Informasi Pengajuan ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="h-6 w-6 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="information-circle" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_request_information') }}</h4>
                        <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 pl-9">
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mb-0.5">{{ __('pages.created_date') }}</p>
                            <p class="text-sm font-medium text-dark-900 dark:text-dark-50">
                                {{ \Carbon\Carbon::parse($fundRequest->created_at)->format('d M Y, H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mb-0.5">{{ __('pages.request_number') }}</p>
                            <p class="text-sm font-mono font-semibold text-primary-600 dark:text-primary-400">{{ $fundRequest->request_number ?? '-' }}</p>
                        </div>
                        <div class="col-span-2 lg:col-span-3">
                            <p class="text-xs text-dark-500 dark:text-dark-400 mb-0.5">{{ __('pages.purpose') }}</p>
                            <p class="text-sm text-dark-700 dark:text-dark-300 leading-relaxed">{{ $fundRequest->purpose }}</p>
                        </div>

                        {{-- Attachment --}}
                        @if ($fundRequest->attachment_path)
                            <div class="col-span-2 lg:col-span-3">
                                <p class="text-xs text-dark-500 dark:text-dark-400 mb-1">{{ __('pages.attachment') }}</p>
                                <a href="{{ Storage::url($fundRequest->attachment_path) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-zinc-200 dark:border-dark-600 bg-white dark:bg-dark-800 text-sm text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                                    <x-icon name="document" class="w-4 h-4 shrink-0" />
                                    <span>{{ $fundRequest->attachment_name }}</span>
                                    <x-icon name="arrow-top-right-on-square" class="w-3.5 h-3.5 ml-1 opacity-60" />
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Rincian Anggaran ── --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="h-6 w-6 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center shrink-0">
                            <x-icon name="list-bullet" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.request_items') }}</h4>
                        <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                        <span class="text-xs text-dark-500 dark:text-dark-400">{{ $fundRequest->items->count() }} {{ __('pages.items') }}</span>
                    </div>

                    <div class="rounded-xl border border-zinc-200 dark:border-dark-600 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-zinc-50 dark:bg-dark-700 border-b border-zinc-200 dark:border-dark-600">
                                    <th class="text-left py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide w-8">#</th>
                                    <th class="text-left py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide">{{ __('pages.item_description') }}</th>
                                    <th class="text-left py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide hidden sm:table-cell">{{ __('pages.category') }}</th>
                                    <th class="text-right py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide hidden md:table-cell">{{ __('pages.quantity') }}</th>
                                    <th class="text-right py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide hidden md:table-cell">{{ __('pages.unit_price') }}</th>
                                    <th class="text-right py-2.5 px-4 text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide">{{ __('common.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-dark-700">
                                @foreach ($fundRequest->items as $index => $item)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-dark-700/50 transition-colors">
                                        <td class="py-3 px-4 text-dark-500 dark:text-dark-400 text-xs">{{ $index + 1 }}</td>
                                        <td class="py-3 px-4">
                                            <p class="font-medium text-dark-900 dark:text-dark-50">{{ $item->description }}</p>
                                            @if ($item->notes)
                                                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ $item->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 hidden sm:table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-dark-600 text-xs text-dark-600 dark:text-dark-300">
                                                {{ $item->category->full_path }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-right text-dark-700 dark:text-dark-300 hidden md:table-cell">{{ $item->quantity }}</td>
                                        <td class="py-3 px-4 text-right text-dark-700 dark:text-dark-300 hidden md:table-cell">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-semibold text-dark-900 dark:text-dark-50">
                                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-linear-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border-t border-primary-200 dark:border-primary-800">
                                    <td colspan="5" class="py-3 px-4 text-right text-sm font-semibold text-dark-700 dark:text-dark-300">{{ __('common.total') }}</td>
                                    <td class="py-3 px-4 text-right text-base font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($fundRequest->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ── Timeline Proses ── --}}
                @if ($fundRequest->reviewed_by || $fundRequest->disbursed_by)
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="h-6 w-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center shrink-0">
                                <x-icon name="arrow-path" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.process_timeline') }}</h4>
                            <div class="flex-1 h-px bg-zinc-200 dark:bg-dark-600"></div>
                        </div>

                        <div class="pl-3 space-y-0">
                            {{-- Step: Pengajuan --}}
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/40 border-2 border-primary-400 dark:border-primary-600 flex items-center justify-center shrink-0">
                                        <x-icon name="document-plus" class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                    </div>
                                    @if ($fundRequest->reviewed_by)
                                        <div class="w-0.5 h-8 bg-zinc-200 dark:bg-dark-600 my-1"></div>
                                    @endif
                                </div>
                                <div class="pb-4 pt-1">
                                    <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fund_request_submitted_step') }}</p>
                                    <p class="text-xs text-dark-500 dark:text-dark-400">
                                        {{ $fundRequest->user->name }} · {{ \Carbon\Carbon::parse($fundRequest->created_at)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Step: Review --}}
                            @if ($fundRequest->reviewed_by)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        @php
                                            $reviewIcon  = $fundRequest->status === 'rejected' ? 'x-circle' : 'check-circle';
                                            $reviewBg    = $fundRequest->status === 'rejected' ? 'bg-red-100 dark:bg-red-900/40 border-red-400 dark:border-red-600' : 'bg-green-100 dark:bg-green-900/40 border-green-400 dark:border-green-600';
                                            $reviewColor = $fundRequest->status === 'rejected' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400';
                                        @endphp
                                        <div class="h-8 w-8 rounded-full {{ $reviewBg }} border-2 flex items-center justify-center shrink-0">
                                            <x-icon name="{{ $reviewIcon }}" class="w-4 h-4 {{ $reviewColor }}" />
                                        </div>
                                        @if ($fundRequest->disbursed_by)
                                            <div class="w-0.5 h-8 bg-zinc-200 dark:bg-dark-600 my-1"></div>
                                        @endif
                                    </div>
                                    <div class="pb-4 pt-1 flex-1">
                                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                                            {{ $fundRequest->status === 'rejected' ? __('pages.fr_status_rejected') : __('pages.fr_status_approved') }}
                                        </p>
                                        <p class="text-xs text-dark-500 dark:text-dark-400">
                                            {{ $fundRequest->reviewer->name }} · {{ \Carbon\Carbon::parse($fundRequest->reviewed_at)->format('d M Y, H:i') }}
                                        </p>
                                        @if ($fundRequest->review_notes)
                                            <p class="mt-1.5 text-xs text-dark-700 dark:text-dark-300 italic bg-zinc-50 dark:bg-dark-700 rounded-lg px-3 py-2 border border-zinc-200 dark:border-dark-600">
                                                "{{ $fundRequest->review_notes }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Step: Pencairan --}}
                            @if ($fundRequest->disbursed_by)
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center">
                                        <div class="h-8 w-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 border-2 border-emerald-400 dark:border-emerald-600 flex items-center justify-center shrink-0">
                                            <x-icon name="banknotes" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                        </div>
                                    </div>
                                    <div class="pb-4 pt-1 flex-1">
                                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('pages.fr_status_disbursed') }}</p>
                                        <p class="text-xs text-dark-500 dark:text-dark-400">
                                            {{ $fundRequest->disburser->name }} · {{ \Carbon\Carbon::parse($fundRequest->disbursement_date)->format('d M Y') }}
                                        </p>
                                        @if ($fundRequest->disbursement_notes)
                                            <p class="mt-1.5 text-xs text-dark-700 dark:text-dark-300 italic bg-zinc-50 dark:bg-dark-700 rounded-lg px-3 py-2 border border-zinc-200 dark:border-dark-600">
                                                "{{ $fundRequest->disbursement_notes }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>

            {{-- FOOTER --}}
            <x-slot:footer>
                <div class="flex justify-end">
                    <x-button wire:click="$set('modal', false)" color="zinc" class="w-full sm:w-auto">
                        {{ __('common.close') }}
                    </x-button>
                </div>
            </x-slot:footer>
        </x-modal>
    @endif
</div>
