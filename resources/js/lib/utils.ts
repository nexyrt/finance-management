import { type ClassValue, clsx } from 'clsx';
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
