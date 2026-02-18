@props([
    'label'    => null,
    'hint'     => null,
    'multiple' => false,
    'accept'   => 'image/jpeg,image/jpg,image/png,application/pdf',
])

@php
    $wireModelValue = $attributes->wire('model')->value();
@endphp

<div x-data="{
    files: [],
    dragging: false,
    dragCounter: 0,
    uploading: false,
    progress: 0,
    multiple: {{ $multiple ? 'true' : 'false' }},

    handleFiles(fileList) {
        if (!this.multiple) this.files = [];

        Array.from(fileList).forEach(file => {
            const exists = this.files.some(f => f.name === file.name && f.size === file.size);
            if (exists) return;

            this.files.push({
                name: file.name,
                size: file.size,
                type: file.type,
                preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null,
            });

            @if($wireModelValue)
                if (this.multiple) {
                    $wire.uploadMultiple('{{ $wireModelValue }}', [file],
                        () => {},
                        () => {},
                        (e) => { this.progress = e.detail.progress; }
                    );
                } else {
                    this.uploading = true;
                    this.progress = 0;
                    $wire.upload('{{ $wireModelValue }}', file,
                        () => { this.uploading = false; this.progress = 0; },
                        () => { this.uploading = false; },
                        (e) => { this.progress = e.detail.progress; }
                    );
                }
            @endif
        });
    },

    handleInputChange(event) {
        this.handleFiles(event.target.files);
        event.target.value = '';
    },

    handleDrop(event) {
        this.dragCounter = 0;
        this.dragging = false;
        this.handleFiles(event.dataTransfer.files);
    },

    remove(index) {
        this.files.splice(index, 1);
        @if($wireModelValue)
            $wire.set('{{ $wireModelValue }}', null);
        @endif
    },

    formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    },

    clearFiles() {
        this.files = [];
    }
}"
x-on:file-upload-reset.window="clearFiles()">

    @if($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1.5">{{ $label }}</label>
    @endif

    {{-- Drop Zone --}}
    <div
        @dragenter.prevent.stop="dragCounter++; dragging = true; $event.dataTransfer.dropEffect = 'copy'"
        @dragover.prevent.stop="dragging = true; $event.dataTransfer.dropEffect = 'copy'"
        @dragleave.prevent.stop="dragCounter--; if (dragCounter === 0) dragging = false"
        @drop.prevent.stop="handleDrop($event)"
        :class="dragging
            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/10'
            : 'border-secondary-300 dark:border-dark-600 hover:border-primary-400 dark:hover:border-primary-600'"
        class="relative border-2 border-dashed rounded-xl transition-colors bg-white dark:bg-dark-800 cursor-pointer"
        @click="$refs.fileInput.click()">

        {{-- Hidden file input --}}
        <input
            x-ref="fileInput"
            type="file"
            {{ $multiple ? 'multiple' : '' }}
            accept="{{ $accept }}"
            x-on:change="handleInputChange($event)"
            class="hidden"
        />

        {{-- Upload progress overlay --}}
        <div x-show="uploading" class="absolute inset-0 bg-white/80 dark:bg-dark-800/80 rounded-xl flex flex-col items-center justify-center gap-2 z-10">
            <div class="w-3/4 bg-secondary-200 dark:bg-dark-600 rounded-full h-1.5">
                <div class="bg-primary-600 h-1.5 rounded-full transition-all" :style="'width: ' + progress + '%'"></div>
            </div>
            <p class="text-xs text-dark-500 dark:text-dark-400" x-text="progress + '%'"></p>
        </div>

        {{-- Empty state --}}
        <div x-show="files.length === 0" class="flex flex-col items-center gap-2 py-5 pointer-events-none">
            <div class="h-10 w-10 bg-secondary-100 dark:bg-dark-700 rounded-xl flex items-center justify-center">
                <x-icon name="arrow-up-tray" class="w-5 h-5 text-secondary-500 dark:text-dark-400" />
            </div>
            <div class="text-center">
                <p class="text-sm font-medium text-dark-700 dark:text-dark-200">Klik atau drag & drop file</p>
                <p class="text-xs text-dark-500 dark:text-dark-400 mt-0.5">
                    JPG, PNG, PDF &mdash; maks. 2MB{{ $multiple ? ' &mdash; bisa lebih dari 1 file' : '' }}
                </p>
            </div>
        </div>

        {{-- File list --}}
        <div x-show="files.length > 0" class="p-3 space-y-2" x-on:click.stop>
            <template x-for="(file, index) in files" :key="index">
                <div class="flex items-center gap-3 p-2 bg-secondary-50 dark:bg-dark-700 rounded-lg">
                    <div class="h-9 w-9 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center bg-white dark:bg-dark-800 border border-secondary-200 dark:border-dark-600">
                        <template x-if="file.preview">
                            <img :src="file.preview" class="h-full w-full object-cover" />
                        </template>
                        <template x-if="!file.preview">
                            <x-icon name="document" class="w-5 h-5 text-red-500" />
                        </template>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-dark-900 dark:text-dark-50 truncate" x-text="file.name"></p>
                        <p class="text-xs text-dark-500 dark:text-dark-400" x-text="formatSize(file.size)"></p>
                    </div>
                    <button type="button"
                        x-on:click.stop="remove(index)"
                        class="flex-shrink-0 h-7 w-7 flex items-center justify-center rounded-lg text-dark-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </template>

            <template x-if="multiple">
                <button type="button"
                    x-on:click.stop="$refs.fileInput.click()"
                    class="w-full flex items-center justify-center gap-1.5 py-1.5 text-xs text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/10 rounded-lg transition-colors">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    Tambah file lain
                </button>
            </template>
        </div>
    </div>

    @if($hint)
        <p class="mt-1.5 text-xs text-dark-500 dark:text-dark-400">{{ $hint }}</p>
    @endif

    @error($wireModelValue)
        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
    @enderror
</div>
