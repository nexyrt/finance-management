import { X, Download, FileText, Image } from 'lucide-react';
import { Dialog, DialogContent } from '@/components/ui/dialog';

interface Props {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    attachmentUrl: string;
    filename?: string;
}

function getExtension(url: string, filename?: string): string {
    const source = filename ?? url;
    return source.split('.').pop()?.toLowerCase() ?? '';
}

export default function AttachmentViewerModal({ open, onOpenChange, attachmentUrl, filename }: Props) {
    const ext = getExtension(attachmentUrl, filename);
    const isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
    const isPdf = ext === 'pdf';
    const displayName = filename ?? attachmentUrl.split('/').pop() ?? 'Lampiran';

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="4xl" className="p-0 overflow-hidden">
                {/* Header */}
                <div className="flex items-center justify-between px-4 py-3 border-b border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700">
                    <div className="flex items-center gap-2 min-w-0">
                        {isImage
                            ? <Image className="w-4 h-4 text-primary-500 shrink-0" />
                            : <FileText className="w-4 h-4 text-rose-500 shrink-0" />
                        }
                        <span className="text-sm font-medium text-dark-900 dark:text-dark-200 truncate">{displayName}</span>
                    </div>
                    <div className="flex items-center gap-2 shrink-0">
                        <a
                            href={attachmentUrl}
                            download={displayName}
                            target="_blank"
                            rel="noreferrer"
                            className="flex items-center gap-1.5 text-xs text-primary-600 dark:text-primary-400 hover:underline px-2 py-1 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                        >
                            <Download className="w-3.5 h-3.5" />
                            Download
                        </a>
                        <button
                            onClick={() => onOpenChange(false)}
                            className="w-7 h-7 rounded-lg flex items-center justify-center text-dark-400 hover:text-dark-700 hover:bg-secondary-100 dark:hover:bg-dark-600 transition-colors"
                        >
                            <X className="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {/* Viewer */}
                <div className="bg-dark-950 flex items-center justify-center" style={{ minHeight: '60vh', maxHeight: '80vh' }}>
                    {isImage ? (
                        <img
                            src={attachmentUrl}
                            alt={displayName}
                            className="max-w-full max-h-[80vh] object-contain"
                            style={{ minHeight: '200px' }}
                        />
                    ) : isPdf ? (
                        <iframe
                            src={attachmentUrl}
                            title={displayName}
                            className="w-full border-0"
                            style={{ height: '80vh' }}
                        />
                    ) : (
                        <div className="flex flex-col items-center gap-3 text-dark-400 py-16">
                            <FileText className="w-12 h-12 opacity-40" />
                            <p className="text-sm">Format tidak didukung untuk preview.</p>
                            <a
                                href={attachmentUrl}
                                download={displayName}
                                className="text-xs text-primary-400 hover:underline"
                            >
                                Download file
                            </a>
                        </div>
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
