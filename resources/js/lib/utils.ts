import { type ClassValue, clsx } from 'clsx';
import { toast } from 'sonner';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatCurrency(value: number | string, locale = 'id-ID'): string {
    return 'Rp ' + Number(value).toLocaleString(locale);
}

export function parseCurrency(value: string): number {
    return parseInt(value.replace(/[^0-9]/g, ''), 10) || 0;
}

export function formatDate(dateString: string, locale = 'id-ID'): string {
    return new Date(dateString).toLocaleDateString(locale, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

const ERROR_TOAST_OPTS = { duration: Infinity, closeButton: true } as const;

/** Toast error tunggal — tidak auto-close, ada tombol X. */
export function toastError(message: string, description?: string): void {
    toast.error(message, { ...ERROR_TOAST_OPTS, description });
}

/**
 * Toast semua error dari Inertia onError / fetch json.errors.
 * 1 error → pesan langsung. >1 error → judul + daftar di description.
 * Selalu console.error agar developer bisa lihat detail lengkap.
 */
export function toastErrors(errs: Record<string, string>, context?: string): void {
    const messages = Object.values(errs);
    console.error(context ? `[${context}]` : '[Error]', errs);

    if (messages.length === 0) {
        toast.error('Terjadi kesalahan tidak diketahui.', ERROR_TOAST_OPTS);
        return;
    }

    if (messages.length === 1) {
        toast.error(messages[0], ERROR_TOAST_OPTS);
        return;
    }

    toast.error(`${messages.length} kesalahan ditemukan`, {
        ...ERROR_TOAST_OPTS,
        description: messages.map((m, i) => `${i + 1}. ${m}`).join('\n'),
    });
}
