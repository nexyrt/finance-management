<div>
    <x-modal wire="modal" size="2xl" center>
        @if ($this->feedback)
            @php
                $typeColor = match($this->feedback->type) {
                    'bug'     => 'red',
                    'feature' => 'purple',
                    default   => 'green',
                };
                $typeBg = match($this->feedback->type) {
                    'bug'     => 'bg-red-50 dark:bg-red-900/20',
                    'feature' => 'bg-purple-50 dark:bg-purple-900/20',
                    default   => 'bg-green-50 dark:bg-green-900/20',
                };
                $typeIcon = match($this->feedback->type) {
                    'bug'     => 'text-red-600 dark:text-red-400',
                    'feature' => 'text-purple-600 dark:text-purple-400',
                    default   => 'text-green-600 dark:text-green-400',
                };
            @endphp

            {{-- Modal Title --}}
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 {{ $typeBg }} rounded-xl flex items-center justify-center shrink-0">
                        <x-icon name="{{ $this->feedback->type_icon }}" class="w-6 h-6 {{ $typeIcon }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate">
                                {{ $this->feedback->title }}
                            </h3>
                            <x-badge :text="$this->feedback->status_label" :color="$this->feedback->status_badge_color" />
                        </div>
                        <p class="text-sm text-dark-600 dark:text-dark-400 mt-0.5 flex items-center gap-2">
                            <span>{{ $this->feedback->user->name }}</span>
                            <span class="opacity-30">·</span>
                            <x-badge :text="$this->feedback->type_label" :color="$this->feedback->type_badge_color" />
                            <x-badge :text="$this->feedback->priority_label" :color="$this->feedback->priority_badge_color" />
                        </p>
                    </div>
                </div>
            </x-slot:title>

            {{-- Body --}}
            <div class="space-y-4">

                {{-- Row 1: Meta info cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                    {{-- Reporter --}}
                    <div class="bg-zinc-50 dark:bg-dark-700/40 border border-zinc-200 dark:border-dark-600 rounded-xl p-3">
                        <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-1">{{ __('feedback.from') }}</p>
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded-lg bg-linear-to-br from-primary-400 to-primary-600 flex items-center justify-center shrink-0">
                                <span class="text-white font-semibold text-[9px]">{{ strtoupper(substr($this->feedback->user->name, 0, 2)) }}</span>
                            </div>
                            <p class="text-sm font-semibold text-dark-900 dark:text-dark-50 truncate">{{ $this->feedback->user->name }}</p>
                        </div>
                    </div>

                    {{-- Submitted date --}}
                    <div class="bg-zinc-50 dark:bg-dark-700/40 border border-zinc-200 dark:border-dark-600 rounded-xl p-3">
                        <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-1">{{ __('common.date') }}</p>
                        <p class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ $this->feedback->created_at->format('d M Y, H:i') }}</p>
                    </div>

                    {{-- Source page --}}
                    @if ($this->feedback->page_url)
                        <div class="bg-zinc-50 dark:bg-dark-700/40 border border-zinc-200 dark:border-dark-600 rounded-xl p-3">
                            <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-1">{{ __('feedback.source_page') }}</p>
                            <a href="{{ $this->feedback->page_url }}" target="_blank"
                                class="inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 transition-colors truncate">
                                <x-icon name="link" class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate">{{ __('common.open') }}</span>
                            </a>
                        </div>
                    @else
                        <div class="bg-zinc-50 dark:bg-dark-700/40 border border-zinc-200 dark:border-dark-600 rounded-xl p-3">
                            <p class="text-xs uppercase tracking-widest text-dark-400 dark:text-dark-500 mb-1">{{ __('feedback.source_page') }}</p>
                            <p class="text-sm text-dark-400 dark:text-dark-500">—</p>
                        </div>
                    @endif

                </div>

                {{-- Row 2: Description --}}
                <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                    <div class="px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                        <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold">{{ __('common.description') }}</h4>
                    </div>
                    <div class="px-4 py-3">
                        <div class="rich-text text-sm text-dark-700 dark:text-dark-300 leading-relaxed">
                            {!! $this->feedback->safe_description !!}
                        </div>
                    </div>
                </div>

                {{-- Row 3: Attachment (if exists) --}}
                @if ($this->feedback->hasAttachment())
                    <div class="border border-zinc-200 dark:border-dark-600 rounded-xl overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-zinc-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700/40">
                            <h4 class="text-xs uppercase tracking-widest text-dark-500 dark:text-dark-400 font-semibold flex items-center gap-2">
                                <x-icon name="paper-clip" class="w-3.5 h-3.5" />
                                {{ __('feedback.attachment_label') }}
                            </h4>
                        </div>
                        <div class="p-4">
                            @if ($this->feedback->isImageAttachment())
                                <img src="{{ $this->feedback->attachment_url }}" alt="Attachment"
                                    class="max-w-full max-h-80 object-contain mx-auto block rounded-xl border border-zinc-200 dark:border-dark-600" />
                            @else
                                <a href="{{ $this->feedback->attachment_url }}" target="_blank"
                                    class="flex items-center gap-3 p-3 bg-zinc-50 dark:bg-dark-700/40 rounded-xl border border-zinc-200 dark:border-dark-600 hover:bg-zinc-100 dark:hover:bg-dark-700 transition-colors">
                                    <div class="h-10 w-10 bg-red-50 dark:bg-red-900/20 rounded-xl flex items-center justify-center shrink-0">
                                        <x-icon name="document" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-dark-900 dark:text-dark-50 truncate text-sm">{{ $this->feedback->attachment_name }}</p>
                                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('feedback.click_to_open') }}</p>
                                    </div>
                                    <x-icon name="arrow-top-right-on-square" class="w-4 h-4 text-dark-400" />
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Row 4: Admin Response (if exists) --}}
                @if ($this->feedback->admin_response)
                    <div class="border border-green-200 dark:border-green-800/50 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-2.5 border-b border-green-200 dark:border-green-800/50 bg-green-50 dark:bg-green-900/10">
                            <div class="w-5 h-5 rounded-lg bg-green-500 dark:bg-green-600 flex items-center justify-center">
                                <x-icon name="check" class="w-3 h-3 text-white" />
                            </div>
                            <h4 class="text-xs uppercase tracking-widest text-green-700 dark:text-green-400 font-semibold">{{ __('feedback.admin_response') }}</h4>
                            <span class="ml-auto text-xs text-green-600 dark:text-green-400">
                                {{ $this->feedback->responder?->name ?? 'Admin' }}
                                <span class="opacity-50 mx-1">·</span>
                                {{ $this->feedback->responded_at?->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <div class="px-4 py-3 bg-green-50 dark:bg-green-900/10">
                            <div class="rich-text text-sm text-green-900 dark:text-green-100 leading-relaxed">
                                {!! $this->feedback->safe_admin_response !!}
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Footer --}}
            <x-slot:footer>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 w-full">
                    {{-- Left: context actions --}}
                    <div class="flex items-center gap-2">
                        @can('respond feedbacks')
                            @if ($this->feedback->canRespond())
                                <x-button wire:click="respondFeedback" color="green" icon="chat-bubble-left-ellipsis" size="sm">
                                    {{ __('feedback.respond') }}
                                </x-button>
                            @endif
                        @endcan
                    </div>

                    {{-- Right: edit + close --}}
                    <div class="flex items-center gap-2">
                        @if ($this->feedback->canEdit() && $this->feedback->user_id === auth()->id())
                            <x-button wire:click="editFeedback" color="blue" icon="pencil" outline size="sm">
                                {{ __('common.edit') }}
                            </x-button>
                        @endif
                        <x-button wire:click="close" color="zinc" class="w-full sm:w-auto">
                            {{ __('common.close') }}
                        </x-button>
                    </div>
                </div>
            </x-slot:footer>

        @endif
    </x-modal>
</div>
