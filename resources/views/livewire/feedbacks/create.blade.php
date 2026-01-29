<div>
    <x-modal wire="modal" size="2xl" center persistent>
        <x-slot:title>
            <div class="flex items-center gap-4 my-3">
                <div class="h-12 w-12 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center">
                    <x-icon name="chat-bubble-left-right" class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-dark-900 dark:text-dark-50">Kirim Feedback</h3>
                    <p class="text-sm text-dark-600 dark:text-dark-400">Bantu kami meningkatkan sistem ini</p>
                </div>
            </div>
        </x-slot:title>

        <form wire:submit="save" class="space-y-6">
            {{-- Type & Priority --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-select.styled wire:model="type" :options="$this->types" label="Jenis Feedback *"
                        placeholder="Pilih jenis..." />
                </div>
                <div>
                    <x-select.styled wire:model="priority" :options="$this->priorities" label="Prioritas *"
                        placeholder="Pilih prioritas..." />
                </div>
            </div>

            {{-- Title --}}
            <div>
                <x-input wire:model="title" label="Judul *" placeholder="Ringkasan singkat feedback Anda..." />
            </div>

            {{-- Description --}}
            <div>
                <x-textarea wire:model="description" label="Deskripsi *"
                    placeholder="Jelaskan secara detail... Untuk bug report, sertakan langkah-langkah untuk mereproduksi masalah."
                    rows="5" />
                <p class="mt-1 text-xs text-dark-500">Maksimal 5000 karakter</p>
            </div>

            {{-- Page URL Info --}}
            @if ($pageUrl)
                <div class="p-3 bg-gray-50 dark:bg-dark-700 rounded-lg">
                    <p class="text-xs text-dark-500 dark:text-dark-400">
                        <x-icon name="link" class="w-3 h-3 inline mr-1" />
                        Dikirim dari: <span class="font-mono text-dark-700 dark:text-dark-300">{{ $pageUrl }}</span>
                    </p>
                </div>
            @endif

            {{-- Attachment --}}
            <div class="space-y-2" x-data="clipboardPaste">
                <div class="border-b border-secondary-200 dark:border-dark-600 pb-2">
                    <h4 class="text-sm font-semibold text-dark-900 dark:text-dark-50">Screenshot / Lampiran</h4>
                    <p class="text-xs text-dark-500 dark:text-dark-400">
                        Opsional - Upload screenshot atau dokumen pendukung
                        <span class="inline-flex items-center gap-1 ml-1 px-1.5 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded text-[10px] font-medium">
                            <x-icon name="clipboard" class="w-3 h-3" />
                            Tekan Ctrl+V untuk paste gambar
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
                        <span class="text-sm text-primary-700 dark:text-primary-300">Memproses gambar dari clipboard...</span>
                    </div>
                </div>

                <x-upload wire:model="attachment" label="File" tip="JPG, PNG, atau PDF (Maks 5MB)"
                    accept="image/jpeg,image/png,application/pdf" delete delete-method="deleteUpload" />
            </div>

            {{-- Help Text --}}
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <div class="flex items-start gap-3">
                    <x-icon name="information-circle" class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0 mt-0.5" />
                    <div class="text-sm text-blue-900 dark:text-blue-200">
                        <p class="font-semibold mb-1">Tips untuk feedback yang baik:</p>
                        <ul class="list-disc list-inside space-y-1 text-xs text-blue-800 dark:text-blue-300">
                            <li><strong>Bug Report:</strong> Sertakan langkah untuk mereproduksi, perilaku yang diharapkan vs yang terjadi</li>
                            <li><strong>Feature Request:</strong> Jelaskan use case dan manfaat fitur yang diinginkan</li>
                            <li><strong>Kritik/Saran:</strong> Berikan konteks dan saran konkret untuk perbaikan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex flex-col sm:flex-row justify-end gap-3">
                <x-button wire:click="resetForm" color="secondary" outline>
                    Batal
                </x-button>
                <x-button wire:click="save" color="primary" icon="paper-airplane" loading="save">
                    Kirim Feedback
                </x-button>
            </div>
        </x-slot:footer>
    </x-modal>
</div>

@script
<script>
    // Alpine.js component for handling clipboard paste
    Alpine.data('clipboardPaste', () => ({
        isPasting: false,

        init() {
            // Listen for paste events on the modal
            this.$el.closest('.ts-modal-content')?.addEventListener('paste', this.handlePaste.bind(this));

            // Also listen on document when modal is open
            document.addEventListener('paste', this.handlePaste.bind(this));
        },

        async handlePaste(e) {
            // Only handle if modal is open
            if (!@this.modal) return;

            const items = e.clipboardData?.items;
            if (!items) return;

            // Look for image in clipboard
            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    e.preventDefault();

                    this.isPasting = true;
                    const blob = item.getAsFile();

                    if (!blob) {
                        this.isPasting = false;
                        return;
                    }

                    // Check file size (5MB limit)
                    const maxSize = 5 * 1024 * 1024; // 5MB in bytes
                    if (blob.size > maxSize) {
                        this.isPasting = false;
                        window.$wireui.notify({
                            title: 'File terlalu besar',
                            description: 'Ukuran gambar maksimal 5MB',
                            icon: 'error'
                        });
                        return;
                    }

                    // Create a File object with timestamp name
                    const timestamp = new Date().getTime();
                    const extension = blob.type.split('/')[1] || 'png';
                    const file = new File([blob], `screenshot-${timestamp}.${extension}`, {
                        type: blob.type
                    });

                    // Upload via Livewire
                    try {
                        await @this.upload('attachment', file, () => {
                            this.isPasting = false;
                        }, (error) => {
                            this.isPasting = false;
                            window.$wireui.notify({
                                title: 'Upload gagal',
                                description: 'Terjadi kesalahan saat mengupload gambar',
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
