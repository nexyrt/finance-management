import { ClipboardPaste, ExternalLink, Paperclip, Upload, X, ZoomIn, ZoomOut } from 'lucide-react';
import * as React from 'react';
import { useDropzone } from 'react-dropzone';
import { cn } from '@/lib/utils';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

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

function isImageUrl(url: string): boolean {
    const path = url.split('?')[0];
    const ext = path.split('.').pop()?.toLowerCase() ?? '';
    return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext);
}

function isPdfUrl(url: string): boolean {
    const path = url.split('?')[0];
    const ext = path.split('.').pop()?.toLowerCase() ?? '';
    return ext === 'pdf';
}

/* ── Image viewer with cursor-following zoom ── */
function ImageViewer({ url }: { url: string }) {
    const [zoom, setZoom] = React.useState(1);
    const [origin, setOrigin] = React.useState({ x: 50, y: 50 });
    const containerRef = React.useRef<HTMLDivElement>(null);

    const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
        if (zoom <= 1) return;
        const rect = e.currentTarget.getBoundingClientRect();
        const x = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
        const y = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
        setOrigin({ x, y });
    };

    /* Non-passive wheel listener so we can preventDefault and stop page scroll */
    const handleWheel = React.useCallback((e: WheelEvent) => {
        e.preventDefault();
        setZoom((prev) => Math.min(Math.max(prev - e.deltaY * 0.005, 1), 5));
    }, []);

    React.useEffect(() => {
        const el = containerRef.current;
        if (!el) return;
        el.addEventListener('wheel', handleWheel, { passive: false });
        return () => el.removeEventListener('wheel', handleWheel);
    }, [handleWheel]);

    const handleClick = (e: React.MouseEvent<HTMLDivElement>) => {
        const rect = e.currentTarget.getBoundingClientRect();
        const x = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
        const y = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
        if (zoom > 1) {
            setZoom(1);
            setOrigin({ x: 50, y: 50 });
        } else {
            setOrigin({ x, y });
            setZoom(2.5);
        }
    };

    return (
        <div className="space-y-3">
            <div
                ref={containerRef}
                className={cn(
                    'relative overflow-hidden rounded-xl border border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-800',
                    zoom > 1 ? 'cursor-zoom-out' : 'cursor-zoom-in',
                )}
                style={{ maxHeight: '62vh' }}
                onMouseMove={handleMouseMove}
                onClick={handleClick}
            >
                <img
                    src={url}
                    alt="Preview"
                    className="w-full select-none"
                    style={{
                        display: 'block',
                        maxHeight: '62vh',
                        objectFit: 'contain',
                        transform: `scale(${zoom})`,
                        transformOrigin: `${origin.x}% ${origin.y}%`,
                        transition: zoom === 1 ? 'transform 0.2s ease, transform-origin 0.2s ease' : 'transform-origin 0.04s linear',
                    }}
                    draggable={false}
                />
            </div>

            <div className="flex items-center justify-between px-1">
                <div className="flex items-center gap-2">
                    <button
                        type="button"
                        onClick={() => { setZoom((p) => Math.max(1, +(p - 0.5).toFixed(1))); if (zoom - 0.5 <= 1) setOrigin({ x: 50, y: 50 }); }}
                        disabled={zoom <= 1}
                        className="p-1.5 rounded-lg hover:bg-secondary-100 dark:hover:bg-dark-700 text-dark-500 dark:text-dark-400 transition-colors disabled:opacity-30"
                    >
                        <ZoomOut className="w-4 h-4" />
                    </button>
                    <span className="text-xs tabular-nums text-dark-500 dark:text-dark-400 w-12 text-center">
                        {Math.round(zoom * 100)}%
                    </span>
                    <button
                        type="button"
                        onClick={() => setZoom((p) => Math.min(5, +(p + 0.5).toFixed(1)))}
                        disabled={zoom >= 5}
                        className="p-1.5 rounded-lg hover:bg-secondary-100 dark:hover:bg-dark-700 text-dark-500 dark:text-dark-400 transition-colors disabled:opacity-30"
                    >
                        <ZoomIn className="w-4 h-4" />
                    </button>
                </div>
                <p className="text-xs text-dark-400 dark:text-dark-500">
                    Scroll untuk zoom · Klik untuk toggle
                </p>
            </div>
        </div>
    );
}

/* ── PDF viewer ── */
function PdfViewer({ url }: { url: string }) {
    return (
        <div
            className="rounded-xl overflow-hidden border border-secondary-200 dark:border-dark-600"
            style={{ height: '70vh' }}
        >
            <iframe
                src={url}
                className="w-full h-full"
                title="PDF Preview"
            />
        </div>
    );
}

/* ── File preview dialog ── */
interface FilePreviewDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    fileName: string;
    fileUrl: string;
}

function FilePreviewDialog({ open, onOpenChange, fileName, fileUrl }: FilePreviewDialogProps) {
    const isImage = isImageUrl(fileUrl);
    const isPdf = isPdfUrl(fileUrl);

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="4xl">
                <DialogHeader>
                    <div className="flex items-center justify-between gap-4 py-1">
                        <DialogTitle className="truncate text-sm font-medium text-dark-900 dark:text-dark-50 max-w-xs">
                            {fileName}
                        </DialogTitle>
                        <a
                            href={fileUrl}
                            target="_blank"
                            rel="noreferrer"
                            className="shrink-0 inline-flex items-center gap-1.5 text-xs text-dark-500 hover:text-primary-600 dark:text-dark-400 dark:hover:text-primary-400 transition-colors"
                        >
                            <ExternalLink className="w-3.5 h-3.5" />
                            Buka tab baru
                        </a>
                    </div>
                </DialogHeader>

                <div className="px-6 pb-6">
                    {isImage && <ImageViewer url={fileUrl} />}
                    {isPdf && <PdfViewer url={fileUrl} />}
                    {!isImage && !isPdf && (
                        <div className="flex flex-col items-center justify-center py-16 text-dark-400 dark:text-dark-500">
                            <Paperclip className="w-8 h-8 mb-3" />
                            <p className="text-sm">Preview tidak tersedia untuk jenis file ini.</p>
                            <a
                                href={fileUrl}
                                target="_blank"
                                rel="noreferrer"
                                className="mt-3 text-sm text-primary-600 dark:text-primary-400 hover:underline"
                            >
                                Buka di tab baru
                            </a>
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}

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
    const containerRef = React.useRef<HTMLDivElement>(null);

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
                            onClick={() => setPreviewOpen(true)}
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
                    <span className="text-sm truncate text-primary-700 dark:text-primary-300 flex-1">
                        {value.name}
                    </span>
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

            {/* File preview dialog — rendered outside the chip so it's not inside any parent form */}
            {existingFileUrl && existingFileName && (
                <FilePreviewDialog
                    open={previewOpen}
                    onOpenChange={setPreviewOpen}
                    fileName={existingFileName}
                    fileUrl={existingFileUrl}
                />
            )}
        </div>
    );
}
