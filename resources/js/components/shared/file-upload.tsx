import { ClipboardPaste, Paperclip, Upload, X } from 'lucide-react';
import * as React from 'react';
import { useDropzone } from 'react-dropzone';
import { cn } from '@/lib/utils';
import { FilePreviewDialog } from './file-preview-dialog';

interface FileUploadProps {
    value?: File | null;
    onChange?: (file: File | null) => void;
    label?: string;
    hint?: string;
    error?: string;
    accept?: string[];
    maxSizeMb?: number;
    existingFileName?: string | null;
    existingFileUrl?: string | null;
    onRemoveExisting?: () => void;
    disabled?: boolean;
    className?: string;
}

const EXT_TO_MIME: Record<string, string> = {
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.png': 'image/png',
    '.pdf': 'application/pdf',
};

const IMAGE_MIMES = new Set(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp']);

export function FileUpload({
    value,
    onChange,
    label,
    hint,
    error,
    accept = ['.jpg', '.jpeg', '.png', '.pdf'],
    maxSizeMb = 5,
    existingFileName,
    existingFileUrl,
    onRemoveExisting,
    disabled,
    className,
}: FileUploadProps) {
    const [sizeError, setSizeError] = React.useState<string | null>(null);
    const [pasteHint, setPasteHint] = React.useState(false);
    const [previewOpen, setPreviewOpen] = React.useState(false);
    const [previewData, setPreviewData] = React.useState<{ url: string; name: string; isObjectUrl: boolean } | null>(null);
    const containerRef = React.useRef<HTMLDivElement>(null);

    const openPreview = React.useCallback((url: string, name: string, isObjectUrl = false) => {
        setPreviewData({ url, name, isObjectUrl });
        setPreviewOpen(true);
    }, []);

    const closePreview = React.useCallback(() => {
        setPreviewOpen(false);
        setPreviewData((prev) => {
            if (prev?.isObjectUrl) URL.revokeObjectURL(prev.url);
            return null;
        });
    }, []);

    const acceptedMimes = React.useMemo(
        () => new Set(accept.map((e) => EXT_TO_MIME[e]).filter(Boolean) as string[]),
        [accept],
    );

    const maxBytes = maxSizeMb * 1024 * 1024;

    const processFile = React.useCallback((file: File) => {
        setSizeError(null);
        if (file.size > maxBytes) {
            setSizeError(`Ukuran file melebihi batas ${maxSizeMb}MB.`);
            return;
        }
        if (acceptedMimes.size > 0 && !acceptedMimes.has(file.type)) {
            setSizeError('Format file tidak didukung.');
            return;
        }
        onChange?.(file);
    }, [maxBytes, maxSizeMb, acceptedMimes, onChange]);

    /* ── Clipboard paste (Ctrl+V / Cmd+V) ── */
    const supportsImagePaste = accept.some((e) => ['.jpg', '.jpeg', '.png'].includes(e));

    React.useEffect(() => {
        if (disabled || value || (existingFileName && !value)) return;

        const handlePaste = (e: ClipboardEvent) => {
            const items = e.clipboardData?.items;
            if (!items) return;

            for (const item of Array.from(items)) {
                if (IMAGE_MIMES.has(item.type) && acceptedMimes.has(item.type)) {
                    const file = item.getAsFile();
                    if (file) {
                        e.preventDefault();
                        const named = new File(
                            [file],
                            `clipboard-${Date.now()}.${item.type.split('/')[1] ?? 'png'}`,
                            { type: item.type },
                        );
                        processFile(named);
                        return;
                    }
                }
            }
        };

        document.addEventListener('paste', handlePaste);
        return () => document.removeEventListener('paste', handlePaste);
    }, [disabled, value, existingFileName, acceptedMimes, processFile]);

    /* ── react-dropzone ── */
    const { getRootProps, getInputProps, isDragActive } = useDropzone({
        disabled,
        maxSize: maxBytes,
        accept: accept.reduce<Record<string, string[]>>((acc, ext) => {
            const mime = EXT_TO_MIME[ext];
            if (mime) {
                acc[mime] = [...(acc[mime] ?? []), ext];
            }
            return acc;
        }, {}),
        onDropAccepted: (files) => {
            setSizeError(null);
            onChange?.(files[0] ?? null);
        },
        onDropRejected: (rejections) => {
            const tooLarge = rejections.some((r) =>
                r.errors.some((e) => e.code === 'file-too-large'),
            );
            setSizeError(tooLarge ? `Ukuran file melebihi batas ${maxSizeMb}MB.` : 'Format file tidak didukung.');
        },
        multiple: false,
        noClick: false,
    });

    const displayError = error ?? sizeError ?? undefined;
    const acceptedFormats = accept.map((e) => e.replace('.', '').toUpperCase()).join(', ');

    const showExisting = !value && !!existingFileName;

    return (
        <div ref={containerRef} className={cn('w-full', className)}>
            {label && (
                <label className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300">
                    {label}
                </label>
            )}

            {showExisting ? (
                /* Existing file chip */
                <div className="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800">
                    <Paperclip className="w-4 h-4 shrink-0 text-dark-400 dark:text-dark-500" />
                    {existingFileUrl ? (
                        <button
                            type="button"
                            onClick={() => openPreview(existingFileUrl, existingFileName!)}
                            className="text-sm truncate text-primary-600 dark:text-primary-400 hover:underline flex-1 text-left"
                        >
                            {existingFileName}
                        </button>
                    ) : (
                        <span className="text-sm truncate text-dark-700 dark:text-dark-300 flex-1">
                            {existingFileName}
                        </span>
                    )}
                    {onRemoveExisting && (
                        <button
                            type="button"
                            onClick={onRemoveExisting}
                            className="shrink-0 p-0.5 rounded text-dark-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                            aria-label="Hapus lampiran"
                        >
                            <X className="w-3.5 h-3.5" />
                        </button>
                    )}
                </div>
            ) : value ? (
                /* Newly-selected file chip */
                <div className="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20">
                    <Paperclip className="w-4 h-4 shrink-0 text-primary-600 dark:text-primary-400" />
                    <button
                        type="button"
                        onClick={() => openPreview(URL.createObjectURL(value), value.name, true)}
                        className="text-sm truncate text-primary-700 dark:text-primary-300 hover:text-primary-600 dark:hover:text-primary-400 hover:underline flex-1 text-left"
                    >
                        {value.name}
                    </button>
                    <button
                        type="button"
                        onClick={() => { setSizeError(null); onChange?.(null); }}
                        className="shrink-0 p-0.5 rounded text-primary-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
                        aria-label="Hapus pilihan"
                    >
                        <X className="w-3.5 h-3.5" />
                    </button>
                </div>
            ) : (
                /* Dropzone */
                <div
                    {...getRootProps()}
                    onMouseEnter={() => supportsImagePaste && setPasteHint(true)}
                    onMouseLeave={() => setPasteHint(false)}
                    className={cn(
                        'relative flex flex-col items-center justify-center gap-2 px-4 py-5 rounded-xl border-2 border-dashed transition-colors',
                        isDragActive
                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                            : displayError
                                ? 'border-red-400 dark:border-red-700 bg-red-50/50 dark:bg-red-900/10'
                                : 'border-secondary-300 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800 hover:border-primary-400 dark:hover:border-primary-700 hover:bg-primary-50/50 dark:hover:bg-primary-900/10',
                        disabled && 'opacity-50 cursor-not-allowed',
                    )}
                >
                    <input {...getInputProps()} />

                    <Upload className={cn(
                        'w-5 h-5 transition-colors',
                        isDragActive ? 'text-primary-600 dark:text-primary-400' : 'text-dark-400 dark:text-dark-500',
                    )} />

                    <div className="text-center">
                        <p className="text-sm text-dark-600 dark:text-dark-400">
                            {isDragActive
                                ? 'Lepaskan file di sini...'
                                : (
                                    <>
                                        <span className="text-primary-600 dark:text-primary-400 font-medium">Klik untuk memilih</span>
                                        {' '}atau drag & drop
                                    </>
                                )}
                        </p>
                        <p className="text-xs text-dark-400 dark:text-dark-500 mt-0.5">
                            {acceptedFormats} · maks {maxSizeMb}MB
                        </p>
                    </div>

                    {/* Paste hint — appears on hover when images are accepted */}
                    {supportsImagePaste && pasteHint && !isDragActive && (
                        <div className="absolute bottom-2 right-2 flex items-center gap-1 text-xs text-dark-400 dark:text-dark-500 bg-white dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-lg px-2 py-1 shadow-sm pointer-events-none">
                            <ClipboardPaste className="w-3 h-3" />
                            Ctrl+V untuk paste gambar
                        </div>
                    )}
                </div>
            )}

            {displayError && (
                <p className="mt-1 text-xs text-red-600 dark:text-red-400">{displayError}</p>
            )}
            {hint && !displayError && (
                <p className="mt-1 text-xs text-dark-500 dark:text-dark-400">{hint}</p>
            )}

            {/* File preview dialog */}
            {previewData && (
                <FilePreviewDialog
                    open={previewOpen}
                    onOpenChange={(o) => { if (!o) closePreview(); }}
                    fileName={previewData.name}
                    fileUrl={previewData.url}
                />
            )}
        </div>
    );
}
