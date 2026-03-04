<div>
    <x-modal wire="modal" size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <x-icon name="chat-bubble-left-right" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('feedback.create_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.create_subtitle') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form wire:submit="save" id="create-feedback-form" class="space-y-6">

            {{-- Type & Priority --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('feedback.feedback_type') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('feedback.select_type') }}</p>
                    </div>
                    <x-select.styled wire:model.live="type"
                        :options="$this->types"
                        :label="__('feedback.feedback_type') . ' *'"
                        :placeholder="__('feedback.select_type')" />
                </div>

                <div class="space-y-4">
                    <div class="border-b border-secondary-200 dark:border-dark-600 pb-4">
                        <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">{{ __('feedback.priority') }}</h4>
                        <p class="text-xs text-dark-500 dark:text-dark-400">{{ __('feedback.select_priority') }}</p>
                    </div>
                    <x-select.styled wire:model="priority"
                        :options="$this->priorities"
                        :label="__('feedback.priority') . ' *'"
                        :placeholder="__('feedback.select_priority')" />
                </div>
            </div>

            {{-- Title --}}
            <x-input wire:model="title"
                :label="__('common.title') . ' *'"
                :placeholder="__('feedback.title_placeholder')" />

            {{-- Description --}}
            <div x-data="feedbackEditor" wire:ignore>
                <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">
                    {{ __('common.description') }} *
                </label>
                <div x-ref="editor" class="bg-white dark:bg-dark-800 rounded-b-lg" style="min-height: 150px;"></div>
                <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">{{ __('feedback.max_characters', ['count' => 5000]) }}</p>
            </div>

            {{-- Page URL --}}
            <div>
                <x-input wire:model="pageUrl"
                    :label="__('feedback.page_url')"
                    placeholder="https://..."
                    icon="link" />
                <p class="mt-1 text-xs text-dark-500 dark:text-dark-400">{{ __('feedback.page_url_hint') }}</p>
            </div>

            {{-- Attachment --}}
            <div class="space-y-3" x-data="clipboardPaste">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">{{ __('feedback.screenshot_attachment') }}</h4>
                            <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">{{ __('feedback.attachment_optional') }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-lg text-xs font-medium">
                            <x-icon name="clipboard" class="w-3 h-3" />
                            {{ __('feedback.paste_hint') }}
                        </span>
                    </div>
                </div>

                {{-- Paste indicator --}}
                <div x-show="isPasting" x-transition
                     class="flex items-center gap-2 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-secondary-200 dark:border-dark-600">
                    <svg class="animate-spin h-4 w-4 text-primary-600 dark:text-primary-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-primary-700 dark:text-primary-300">{{ __('feedback.processing_clipboard') }}</span>
                </div>

                <x-upload wire:model="attachment"
                    :label="__('feedback.file_label')"
                    :tip="__('feedback.file_tip')"
                    accept="image/jpeg,image/png,application/pdf"
                    delete
                    delete-method="deleteUpload" />
            </div>

            {{-- Tips --}}
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-secondary-200 dark:border-dark-600">
                <div class="flex items-start gap-3">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-900 dark:text-blue-200">
                        <p class="font-semibold mb-1">{{ __('feedback.tips_title') }}</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-blue-800 dark:text-blue-300">
                            <li><strong>{{ __('feedback.type_bug') }}:</strong> {{ __('feedback.tip_bug') }}</li>
                            <li><strong>{{ __('feedback.type_feature') }}:</strong> {{ __('feedback.tip_feature') }}</li>
                            <li><strong>{{ __('feedback.type_feedback') }}:</strong> {{ __('feedback.tip_feedback') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="resetForm"
                          color="zinc"
                          class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button wire:click="save"
                          color="primary"
                          icon="paper-airplane"
                          loading="save"
                          class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('common.submit') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

@script
<script>
    Alpine.data('feedbackEditor', () => ({
        quill: null,

        init() {
            this.quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                placeholder: @js(__('feedback.description_placeholder')),
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'blockquote', 'code-block'],
                        ['clean']
                    ]
                }
            });

            const initial = @this.get('description');
            if (initial) {
                this.quill.root.innerHTML = initial;
            }

            this.quill.on('text-change', () => {
                const html = this.quill.root.innerHTML;
                @this.set('description', html === '<p><br></p>' ? '' : html);
            });
        },

        destroy() {
            this.quill = null;
        }
    }));

    Alpine.data('clipboardPaste', () => ({
        isPasting: false,

        init() {
            this.$el.closest('.ts-modal-content')?.addEventListener('paste', this.handlePaste.bind(this));
            document.addEventListener('paste', this.handlePaste.bind(this));
        },

        async handlePaste(e) {
            if (!@this.modal) return;

            const items = e.clipboardData?.items;
            if (!items) return;

            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    e.preventDefault();
                    this.isPasting = true;
                    const blob = item.getAsFile();

                    if (!blob) { this.isPasting = false; return; }

                    const maxSize = 5 * 1024 * 1024;
                    if (blob.size > maxSize) {
                        this.isPasting = false;
                        window.dispatchEvent(new CustomEvent('tallstackui:toast', {
                            detail: { type: 'error', title: @js(__('feedback.file_too_large')), description: @js(__('feedback.max_image_size')) }
                        }));
                        return;
                    }

                    const timestamp = new Date().getTime();
                    const extension = blob.type.split('/')[1] || 'png';
                    const file = new File([blob], `screenshot-${timestamp}.${extension}`, { type: blob.type });

                    try {
                        await @this.upload('attachment', file,
                            () => { this.isPasting = false; },
                            () => {
                                this.isPasting = false;
                                window.dispatchEvent(new CustomEvent('tallstackui:toast', {
                                    detail: { type: 'error', title: @js(__('feedback.upload_failed')), description: @js(__('feedback.upload_error')) }
                                }));
                            }
                        );
                    } catch (error) {
                        this.isPasting = false;
                        console.error('Upload error:', error);
                    }
                    break;
                }
            }
        },

        destroy() {
            document.removeEventListener('paste', this.handlePaste.bind(this));
        }
    }));
</script>
@endscript
