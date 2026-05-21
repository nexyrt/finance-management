import * as React from 'react';
import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from 'lucide-react';
import { cn } from '@/lib/utils';

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface PaginationProps {
    meta: PaginationMeta;
    onPageChange: (page: number) => void;
    className?: string;
}

function getPageNumbers(current: number, last: number): (number | '...')[] {
    if (last <= 7) {
        return Array.from({ length: last }, (_, i) => i + 1);
    }

    const pages: (number | '...')[] = [];

    if (current <= 4) {
        pages.push(1, 2, 3, 4, 5, '...', last);
    } else if (current >= last - 3) {
        pages.push(1, '...', last - 4, last - 3, last - 2, last - 1, last);
    } else {
        pages.push(1, '...', current - 1, current, current + 1, '...', last);
    }

    return pages;
}

export function Pagination({ meta, onPageChange, className }: PaginationProps) {
    const { current_page, last_page, total, from, to } = meta;

    if (last_page <= 1) {
        return null;
    }

    const pages = getPageNumbers(current_page, last_page);

    const btnBase =
        'flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-sm transition-colors disabled:cursor-not-allowed disabled:opacity-40';
    const btnDefault =
        'border border-secondary-200 dark:border-dark-600 text-dark-700 dark:text-dark-300 hover:bg-zinc-100 dark:hover:bg-dark-600 bg-white dark:bg-dark-800';
    const btnActive =
        'bg-primary-600 text-white border border-primary-600 hover:bg-primary-700 font-semibold';

    return (
        <div
            className={cn(
                'flex flex-col sm:flex-row items-center justify-between gap-3',
                className,
            )}
        >
            <p className="text-sm text-dark-500 dark:text-dark-400 order-2 sm:order-1">
                {from != null && to != null ? (
                    <>
                        Menampilkan <span className="font-medium text-dark-700 dark:text-dark-300">{from}</span>–
                        <span className="font-medium text-dark-700 dark:text-dark-300">{to}</span> dari{' '}
                        <span className="font-medium text-dark-700 dark:text-dark-300">{total}</span> data
                    </>
                ) : (
                    <>Total <span className="font-medium">{total}</span> data</>
                )}
            </p>

            <div className="flex items-center gap-1 order-1 sm:order-2">
                <button
                    onClick={() => onPageChange(1)}
                    disabled={current_page === 1}
                    className={cn(btnBase, btnDefault)}
                    aria-label="Halaman pertama"
                >
                    <ChevronsLeft className="h-4 w-4" />
                </button>
                <button
                    onClick={() => onPageChange(current_page - 1)}
                    disabled={current_page === 1}
                    className={cn(btnBase, btnDefault)}
                    aria-label="Halaman sebelumnya"
                >
                    <ChevronLeft className="h-4 w-4" />
                </button>

                {pages.map((page, idx) =>
                    page === '...' ? (
                        <span
                            key={`ellipsis-${idx}`}
                            className="flex h-8 w-8 items-center justify-center text-sm text-dark-400 dark:text-dark-500"
                        >
                            …
                        </span>
                    ) : (
                        <button
                            key={page}
                            onClick={() => onPageChange(page as number)}
                            className={cn(
                                btnBase,
                                page === current_page ? btnActive : btnDefault,
                            )}
                        >
                            {page}
                        </button>
                    ),
                )}

                <button
                    onClick={() => onPageChange(current_page + 1)}
                    disabled={current_page === last_page}
                    className={cn(btnBase, btnDefault)}
                    aria-label="Halaman berikutnya"
                >
                    <ChevronRight className="h-4 w-4" />
                </button>
                <button
                    onClick={() => onPageChange(last_page)}
                    disabled={current_page === last_page}
                    className={cn(btnBase, btnDefault)}
                    aria-label="Halaman terakhir"
                >
                    <ChevronsRight className="h-4 w-4" />
                </button>
            </div>
        </div>
    );
}
