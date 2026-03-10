<div class="space-y-5">

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        {{-- Search --}}
        <div class="relative flex-1 sm:max-w-72">
            <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                <x-icon name="magnifying-glass" class="w-4 h-4 text-dark-400" />
            </div>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="{{ __('pages.ri_search_templates_placeholder') }}"
                class="w-full pl-9 pr-3 py-2 text-sm border border-dark-200 dark:border-white/10 rounded-lg bg-white dark:bg-[#1e1e1e] text-dark-900 dark:text-dark-50 focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 placeholder-dark-400 dark:placeholder-dark-500 transition-all" />
        </div>

        {{-- Status Filter --}}
        <div class="sm:w-40">
            <x-select.styled wire:model.live="statusFilter" :options="[
                ['label' => __('pages.ri_status_active'), 'value' => 'active'],
                ['label' => __('pages.ri_status_archived'), 'value' => 'archived'],
                ['label' => __('pages.ri_status_all'), 'value' => 'all'],
            ]" select="label:label|value:value" :placeholder="__('pages.ri_status_filter_placeholder')" />
        </div>

        {{-- Spacer --}}
        <div class="hidden sm:block flex-1"></div>

        {{-- Create --}}
        <x-button size="sm" href="{{ route('recurring-invoices.template.create') }}" wire:navigate color="primary">
            <x-slot:left>
                <x-icon name="plus" class="w-4 h-4" />
            </x-slot:left>
            {{ __('pages.ri_create_template_btn') }}
        </x-button>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($this->templates as $template)
            @php
                $total      = $template->recurringInvoices->count();
                $published  = $template->recurringInvoices->where('status', 'published')->count();
                $draft      = $total - $published;
                $progress   = $total > 0 ? ($published / $total) * 100 : 0;
                $isActive   = $template->status === 'active';
                $isComplete = $total > 0 && $published === $total;
            @endphp

            <div class="group relative flex flex-col bg-white dark:bg-[#1e1e1e] rounded-xl border border-dark-200 dark:border-white/10 overflow-hidden hover:border-primary-300 dark:hover:border-primary-700/60 hover:shadow-sm transition-all duration-200">

                {{-- Left edge accent line --}}
                <div class="absolute inset-y-0 left-0 w-[3px] {{ $isActive ? ($isComplete ? 'bg-green-500' : 'bg-primary-500') : 'bg-dark-300 dark:bg-[#161618]' }}"></div>

                <div class="flex flex-col flex-1 pl-6 pr-5 pt-5 pb-4">

                    {{-- Header: Avatar + Names + Status --}}
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg {{ $isActive ? 'bg-primary-50 dark:bg-primary-900/25 text-primary-600 dark:text-primary-400' : 'bg-dark-100 dark:bg-[#27272a] text-dark-500 dark:text-dark-400' }} flex items-center justify-center shrink-0 text-xs font-bold tracking-wide">
                                {{ strtoupper(substr($template->client->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-dark-900 dark:text-dark-50 truncate leading-tight">
                                    {{ $template->client->name }}
                                </p>
                                <p class="text-xs text-dark-500 dark:text-dark-400 truncate mt-0.5">
                                    {{ $template->template_name }}
                                </p>
                            </div>
                        </div>

                        @if ($isActive)
                            <span class="inline-flex items-center gap-1 pl-1.5 pr-2 py-0.5 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 ring-1 ring-green-200 dark:ring-green-800/40 shrink-0 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                {{ __('pages.ri_status_active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-dark-100 dark:bg-[#27272a] text-dark-500 dark:text-dark-400 ring-1 ring-dark-200 dark:ring-dark-600 shrink-0 mt-0.5">
                                <x-icon name="archive-box" class="w-3 h-3" />
                                {{ __('pages.ri_status_archived') }}
                            </span>
                        @endif
                    </div>

                    {{-- Amount + Frequency --}}
                    <div class="flex items-end justify-between mb-4 pb-4 border-b border-dark-100 dark:border-white/8">
                        <div>
                            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1">
                                {{ __('pages.ri_total_amount_label') }}
                            </p>
                            <p class="text-xl font-bold text-dark-900 dark:text-dark-50 tabular-nums">
                                {{ $template->formatted_total_amount }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-medium text-dark-400 dark:text-dark-500 uppercase tracking-wider mb-1">
                                {{ __('pages.ri_frequency_label') }}
                            </p>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-semibold bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-1 ring-primary-200/60 dark:ring-primary-700/30">
                                <x-icon name="arrow-path" class="w-3 h-3 opacity-70" />
                                {{ __('pages.ri_freq_' . $template->frequency) }}
                            </span>
                        </div>
                    </div>

                    {{-- Stats row: Published / Draft / Period --}}
                    <div class="grid grid-cols-3 gap-2 mb-3">
                        <div class="rounded-lg bg-dark-50 dark:bg-[#27272a]/50 px-2 py-2.5 text-center">
                            <p class="text-sm font-bold text-green-600 dark:text-green-400 tabular-nums">{{ $published }}</p>
                            <p class="text-[10px] text-dark-500 dark:text-dark-400 mt-0.5 leading-none">{{ __('pages.ri_published_label') }}</p>
                        </div>
                        <div class="rounded-lg bg-dark-50 dark:bg-[#27272a]/50 px-2 py-2.5 text-center">
                            <p class="text-sm font-bold text-amber-600 dark:text-amber-400 tabular-nums">{{ $draft }}</p>
                            <p class="text-[10px] text-dark-500 dark:text-dark-400 mt-0.5 leading-none">{{ __('pages.ri_draft_label') }}</p>
                        </div>
                        <div class="rounded-lg bg-dark-50 dark:bg-[#27272a]/50 px-2 py-2.5 text-center">
                            <p class="text-[10px] font-semibold text-dark-700 dark:text-dark-300 leading-tight">{{ $template->start_date->format('M \'y') }}</p>
                            <p class="text-[10px] text-dark-400 leading-none my-0.5">—</p>
                            <p class="text-[10px] font-semibold text-dark-700 dark:text-dark-300 leading-tight">{{ $template->end_date->format('M \'y') }}</p>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] text-dark-400 dark:text-dark-500">{{ __('pages.ri_progress_label') }}</span>
                            <span class="text-[11px] font-semibold text-dark-600 dark:text-dark-300 tabular-nums">
                                {{ $published }}/{{ $total }}
                            </span>
                        </div>
                        <div class="h-1 w-full bg-dark-100 dark:bg-[#27272a] rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 ease-out {{ $isComplete ? 'bg-green-500' : 'bg-linear-to-r from-primary-500 to-blue-500' }}"
                                style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1.5 pt-1 mt-auto">
                        {{-- View --}}
                        <button wire:click="viewTemplate({{ $template->id }})"
                            wire:loading.attr="disabled" wire:target="viewTemplate({{ $template->id }})"
                            class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-dark-50 dark:bg-[#27272a]/50 text-dark-600 dark:text-dark-400 hover:bg-dark-100 dark:hover:bg-dark-700 hover:text-dark-900 dark:hover:text-dark-100 border border-dark-200 dark:border-white/10 transition-all duration-150">
                            <x-icon name="eye" class="w-3.5 h-3.5" />
                            {{ __('common.view') }}
                        </button>

                        @if ($isActive)
                            <a href="{{ route('recurring-invoices.template.edit', $template->id) }}" wire:navigate
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-900/30 border border-primary-200/80 dark:border-primary-700/30 transition-all duration-150">
                                <x-icon name="pencil" class="w-3.5 h-3.5" />
                                {{ __('common.edit') }}
                            </a>
                        @else
                            <button wire:click="restoreTemplate({{ $template->id }})"
                                wire:loading.attr="disabled" wire:target="restoreTemplate({{ $template->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/30 border border-green-200/80 dark:border-green-700/30 transition-all duration-150">
                                <x-icon name="arrow-path" class="w-3.5 h-3.5" />
                                {{ __('common.restore') }}
                            </button>
                        @endif

                        <div class="shrink-0">
                            <livewire:recurring-invoices.delete-template
                                :template="$template"
                                :key="'del-' . $template->id"
                                @template-deleted="$refresh" />
                        </div>
                    </div>

                </div>
            </div>
        @empty
            {{-- Empty State --}}
            <div class="col-span-full">
                <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border-2 border-dashed border-dark-200 dark:border-white/10 p-16 text-center">
                    <div class="w-12 h-12 mx-auto mb-4 bg-dark-50 dark:bg-[#27272a] rounded-xl flex items-center justify-center">
                        <x-icon name="document-duplicate" class="w-6 h-6 text-dark-400" />
                    </div>
                    <h3 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1.5">
                        @if ($search)
                            {{ __('pages.ri_no_templates_found', ['search' => $search]) }}
                        @else
                            {{ __('pages.ri_no_templates_yet') }}
                        @endif
                    </h3>
                    <p class="text-sm text-dark-500 dark:text-dark-400 mb-6">
                        @if ($search)
                            {{ __('pages.ri_no_templates_search_hint') }}
                        @else
                            {{ __('pages.ri_no_templates_create_hint') }}
                        @endif
                    </p>
                    @if (!$search)
                        <x-button size="sm" href="{{ route('recurring-invoices.template.create') }}" wire:navigate color="primary">
                            <x-slot:left>
                                <x-icon name="plus" class="w-4 h-4" />
                            </x-slot:left>
                            {{ __('pages.ri_create_template_btn') }}
                        </x-button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

</div>
