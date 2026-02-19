<div>
    <x-modal wire="modal" size="2xl" center persistent>
        @if ($this->feedback)
            <x-slot:title>
                <div class="flex items-center gap-4 my-3">
                    <div class="h-12 w-12 bg-green-50 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                        <x-icon name="chat-bubble-left-ellipsis" class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('feedback.respond_title') }}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.respond_subtitle') }}</p>
                    </div>
                </div>
            </x-slot:title>

            <div class="space-y-6">
                {{-- Feedback Summary --}}
                <div class="p-4 bg-gray-50 dark:bg-dark-700 rounded-lg">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $this->feedback->type === 'bug' ? 'bg-red-100 dark:bg-red-900/20' : '' }}
                            {{ $this->feedback->type === 'feature' ? 'bg-blue-100 dark:bg-blue-900/20' : '' }}
                            {{ $this->feedback->type === 'feedback' ? 'bg-gray-100 dark:bg-gray-800' : '' }}">
                            <x-icon name="{{ $this->feedback->type_icon }}" class="w-5 h-5
                                {{ $this->feedback->type === 'bug' ? 'text-red-600 dark:text-red-400' : '' }}
                                {{ $this->feedback->type === 'feature' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                {{ $this->feedback->type === 'feedback' ? 'text-gray-600 dark:text-gray-400' : '' }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <x-badge :text="$this->feedback->type_label" :color="$this->feedback->type_badge_color" />
                                <x-badge :text="$this->feedback->priority_label" :color="$this->feedback->priority_badge_color" />
                            </div>
                            <h4 class="font-semibold text-dark-900 dark:text-white">{{ $this->feedback->title }}</h4>
                            <p class="text-sm text-dark-500 dark:text-dark-400 mt-1">
                                {{ __('feedback.from') }} <span class="font-medium text-dark-700 dark:text-dark-300">{{ $this->feedback->user->name }}</span>
                                <span class="mx-1">•</span>
                                {{ $this->feedback->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-white dark:bg-dark-800 rounded border border-gray-200 dark:border-dark-600">
                        <div class="rich-text text-sm text-dark-700 dark:text-dark-300">
                            {!! $this->feedback->safe_description !!}
                        </div>
                    </div>

                    @if ($this->feedback->page_url)
                        <div class="mt-3">
                            <a href="{{ $this->feedback->page_url }}" target="_blank"
                                class="text-xs text-primary-600 hover:text-primary-800 dark:text-primary-400 flex items-center gap-1">
                                <x-icon name="link" class="w-3 h-3" />
                                {{ $this->feedback->page_url }}
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Response Form --}}
                <div class="space-y-4">
                    <div>
                        <x-select.styled wire:model="status" :options="$this->statusOptions" :label="__('common.status') . ' *'"
                            :placeholder="__('feedback.select_status')" />
                    </div>

                    <div x-data="feedbackEditorRespond" wire:ignore>
                        <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">{{ __('feedback.response') }} *</label>
                        <div x-ref="editor" class="bg-white dark:bg-dark-800 rounded-b-lg" style="min-height: 150px;"></div>
                        <p class="mt-1 text-xs text-dark-500">{{ __('feedback.response_notification_hint') }}</p>
                    </div>
                </div>

                {{-- Info --}}
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-start gap-2">
                        <x-icon name="information-circle" class="w-4 h-4 text-blue-500 flex-shrink-0 mt-0.5" />
                        <p class="text-xs text-blue-800 dark:text-blue-200">
                            {{ __('feedback.respond_info') }}
                        </p>
                    </div>
                </div>
            </div>

            <x-slot:footer>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <x-button wire:click="close" color="zinc"
                        class="w-full sm:w-auto order-2 sm:order-1">
                        {{ __('common.cancel') }}
                    </x-button>
                    <x-button wire:click="save" color="green" icon="paper-airplane" loading="save"
                        class="w-full sm:w-auto order-1 sm:order-2">
                        {{ __('feedback.send_response') }}
                    </x-button>
                </div>
            </x-slot:footer>
        @endif
    </x-modal>
</div>

@script
<script>
    Alpine.data('feedbackEditorRespond', () => ({
        quill: null,

        init() {
            this.quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                placeholder: @js(__('feedback.response_placeholder')),
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'blockquote', 'code-block'],
                        ['clean']
                    ]
                }
            });

            const initial = @this.get('response');
            if (initial) {
                this.quill.root.innerHTML = initial;
            }

            this.quill.on('text-change', () => {
                const html = this.quill.root.innerHTML;
                @this.set('response', html === '<p><br></p>' ? '' : html);
            });
        },

        destroy() {
            this.quill = null;
        }
    }));
</script>
@endscript
