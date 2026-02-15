<div>
    <x-modal wire="modal" size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="pencil-square" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">{{ __('feedback.edit_title') }}</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">{{ __('feedback.edit_subtitle') }}</p>
                </div>
            </div>
        </x-slot:title>

        <form wire:submit="save" class="space-y-6">
            {{-- Type & Priority --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-select.styled wire:model="type" :options="$this->types" :label="__('feedback.feedback_type') . ' *'"
                        :placeholder="__('feedback.select_type')" />
                </div>
                <div>
                    <x-select.styled wire:model="priority" :options="$this->priorities" :label="__('feedback.priority') . ' *'"
                        :placeholder="__('feedback.select_priority')" />
                </div>
            </div>

            {{-- Title --}}
            <div>
                <x-input wire:model="title" :label="__('common.title') . ' *'" :placeholder="__('feedback.title_placeholder')" />
            </div>

            {{-- Description (Rich Text Editor) --}}
            <div x-data="feedbackEditorUpdate" wire:ignore>
                <label class="block text-sm font-medium text-dark-700 dark:text-dark-300 mb-1">{{ __('common.description') }} *</label>
                <div x-ref="editor" class="bg-white dark:bg-dark-800 rounded-b-lg" style="min-height: 150px;"></div>
            </div>

            {{-- Existing Attachment --}}
            @if ($existingAttachment && !$removeAttachment)
                <div class="p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="paper-clip" class="w-4 h-4 text-dark-500" />
                            <span class="text-sm text-dark-700 dark:text-dark-300">{{ $existingAttachment }}</span>
                        </div>
                        <x-button wire:click="markRemoveAttachment" color="red" size="xs" icon="trash" flat>
                            {{ __('common.delete') }}
                        </x-button>
                    </div>
                </div>
            @endif

            {{-- New Attachment --}}
            <div class="space-y-2" x-data="clipboardPaste">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-2">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">
                        {{ $existingAttachment && !$removeAttachment ? __('feedback.replace_attachment') : __('common.attachment') }}
                    </h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400 mt-1">
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded text-[10px] font-medium">
                            <x-icon name="clipboard" class="w-3 h-3" />
                            {{ __('feedback.paste_hint') }}
                        </span>
                    </p>
                </div>

                {{-- Paste indicator --}}
                <div x-show="isPasting" x-transition class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border-2 border-dashed border-primary-300 dark:border-primary-700">
                    <div class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-primary-700 dark:text-primary-300">{{ __('feedback.processing_clipboard') }}</span>
                    </div>
                </div>

                <x-upload wire:model="attachment" :label="__('feedback.file_label')" :tip="__('feedback.file_tip')"
                    accept="image/jpeg,image/png,application/pdf" delete delete-method="deleteUpload" />
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="close" color="zinc"
                    class="w-full sm:w-auto order-2 sm:order-1">
                    {{ __('common.cancel') }}
                </x-button>
                <x-button wire:click="save" color="primary" icon="check" loading="save"
                    class="w-full sm:w-auto order-1 sm:order-2">
                    {{ __('common.save') }}
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

@script
<script>
    // Alpine.js component for Quill rich text editor (Update form)
    Alpine.data('feedbackEditorUpdate', () => ({
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

    // Alpine.js component for handling clipboard paste
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

                    if (!blob) {
                        this.isPasting = false;
                        return;
                    }

                    const maxSize = 5 * 1024 * 1024;
                    if (blob.size > maxSize) {
                        this.isPasting = false;
                        window.$wireui.notify({
                            title: @js(__('feedback.file_too_large')),
                            description: @js(__('feedback.max_image_size')),
                            icon: 'error'
                        });
                        return;
                    }

                    const timestamp = new Date().getTime();
                    const extension = blob.type.split('/')[1] || 'png';
                    const file = new File([blob], `screenshot-${timestamp}.${extension}`, {
                        type: blob.type
                    });

                    try {
                        await @this.upload('attachment', file, () => {
                            this.isPasting = false;
                        }, (error) => {
                            this.isPasting = false;
                            window.$wireui.notify({
                                title: @js(__('feedback.upload_failed')),
                                description: @js(__('feedback.upload_error')),
                                icon: 'error'
                            });
                        });
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
