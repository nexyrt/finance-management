<div>
    <x-modal wire="modal" size="2xl" center>
        @if ($this->feedback)
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 rounded-xl flex items-center justify-center
                        {{ $this->feedback->type === 'bug' ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                        {{ $this->feedback->type === 'feature' ? 'bg-blue-100 dark:bg-blue-900/20' : '' }}
                        {{ $this->feedback->type === 'feedback' ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                        <x-icon name="{{ $this->feedback->type_icon }}" class="w-6 h-6
                            {{ $this->feedback->type === 'bug' ? 'text-red-600 dark:text-red-400' : '' }}
                            {{ $this->feedback->type === 'feature' ? 'text-blue-600 dark:text-blue-400' : '' }}
                            {{ $this->feedback->type === 'feedback' ? 'text-gray-600 dark:text-gray-400' : '' }}" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50 truncate">{{ $this->feedback->title }}</h3>
                        <div class="flex items-center gap-2 mt-1">
                            <x-badge :text="$this->feedback->type_label" :color="$this->feedback->type_badge_color" />
                            <x-badge :text="$this->feedback->priority_label" :color="$this->feedback->priority_badge_color" />
                            <x-badge :text="$this->feedback->status_label" :color="$this->feedback->status_badge_color" />
                        </div>
                    </div>
                </div>
            </x-slot:title>

            <div class="space-y-6">
                {{-- Reporter Info --}}
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-dark-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">{{ strtoupper(substr($this->feedback->user->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-dark-900 dark:text-white">{{ $this->feedback->user->name }}</p>
                            <p class="text-xs text-dark-500">{{ $this->feedback->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if ($this->feedback->page_url)
                        <a href="{{ $this->feedback->page_url }}" target="_blank"
                            class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 flex items-center gap-1">
                            <x-icon name="link" class="w-3 h-3" />
                            Halaman Asal
                        </a>
                    @endif
                </div>

                {{-- Description --}}
                <div>
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-white mb-2">Deskripsi</h4>
                    <div class="prose prose-sm dark:prose-invert max-w-none p-4 bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-600">
                        {!! nl2br(e($this->feedback->description)) !!}
                    </div>
                </div>

                {{-- Attachment --}}
                @if ($this->feedback->hasAttachment())
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-white mb-2">Lampiran</h4>
                        <div class="p-4 bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-600">
                            @if ($this->feedback->isImageAttachment())
                                <img src="{{ $this->feedback->attachment_url }}" alt="Attachment"
                                    class="max-w-full max-h-96 rounded-lg mx-auto" />
                            @else
                                <a href="{{ $this->feedback->attachment_url }}" target="_blank"
                                    class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-dark-700 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-600 transition">
                                    <div class="h-10 w-10 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                                        <x-icon name="document" class="w-5 h-5 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-dark-900 dark:text-white truncate">{{ $this->feedback->attachment_name }}</p>
                                        <p class="text-xs text-dark-500">Klik untuk membuka</p>
                                    </div>
                                    <x-icon name="arrow-top-right-on-square" class="w-4 h-4 text-dark-400" />
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Admin Response --}}
                @if ($this->feedback->admin_response)
                    <div>
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-white mb-2">Respon Admin</h4>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="h-6 w-6 rounded-full bg-green-500 flex items-center justify-center">
                                    <x-icon name="check" class="w-3 h-3 text-white" />
                                </div>
                                <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ $this->feedback->responder?->name ?? 'Admin' }}
                                </span>
                                <span class="text-xs text-green-600 dark:text-green-400">
                                    {{ $this->feedback->responded_at?->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="prose prose-sm dark:prose-invert max-w-none text-green-900 dark:text-green-100">
                                {!! nl2br(e($this->feedback->admin_response)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="close" color="secondary" outline>
                        Tutup
                    </x-button>

                    @if ($this->feedback->canEdit() && $this->feedback->user_id === auth()->id())
                        <x-button wire:click="editFeedback" color="blue" icon="pencil">
                            Edit
                        </x-button>
                    @endif

                    @can('respond feedbacks')
                        @if ($this->feedback->canRespond())
                            <x-button wire:click="respondFeedback" color="green" icon="chat-bubble-left-ellipsis">
                                Respond
                            </x-button>
                        @endif
                    @endcan
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>
