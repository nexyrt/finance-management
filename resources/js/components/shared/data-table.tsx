import * as React from 'react';
import {
    type ColumnDef,
    type SortingState,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { ArrowDown, ArrowUp, ArrowUpDown, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';
import { Pagination } from './pagination';

export type { ColumnDef };

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface DataTableProps<TData> {
    columns: ColumnDef<TData, unknown>[];
    data: TData[];
    loading?: boolean;
    pagination?: PaginationMeta;
    sorting?: SortingState;
    onSortingChange?: (sorting: SortingState) => void;
    onPageChange?: (page: number) => void;
    emptyMessage?: string;
    className?: string;
}

export function DataTable<TData>({
    columns,
    data,
    loading = false,
    pagination,
    sorting = [],
    onSortingChange,
    onPageChange,
    emptyMessage = 'Tidak ada data.',
    className,
}: DataTableProps<TData>) {
    const table = useReactTable({
        data,
        columns,
        state: { sorting },
        onSortingChange: onSortingChange
            ? (updaterOrValue) => {
                  const next =
                      typeof updaterOrValue === 'function'
                          ? updaterOrValue(sorting)
                          : updaterOrValue;
                  onSortingChange(next);
              }
            : undefined,
        manualSorting: true,
        manualPagination: true,
        getCoreRowModel: getCoreRowModel(),
    });

    return (
        <div className={cn('space-y-4', className)}>
            <div className="overflow-x-auto rounded-xl border border-secondary-200 dark:border-dark-600">
                <table className="w-full text-sm">
                    <thead>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <tr
                                key={headerGroup.id}
                                className="border-b border-secondary-200 dark:border-dark-600 bg-secondary-50 dark:bg-dark-700"
                            >
                                {headerGroup.headers.map((header) => {
                                    const canSort = header.column.getCanSort();
                                    const sorted = header.column.getIsSorted();

                                    return (
                                        <th
                                            key={header.id}
                                            className={cn(
                                                'px-4 py-3 text-left text-xs font-semibold text-dark-600 dark:text-dark-400 uppercase tracking-wide whitespace-nowrap select-none',
                                                canSort && 'cursor-pointer hover:text-dark-900 dark:hover:text-dark-200',
                                            )}
                                            onClick={
                                                canSort
                                                    ? header.column.getToggleSortingHandler()
                                                    : undefined
                                            }
                                        >
                                            {header.isPlaceholder ? null : (
                                                <div className="flex items-center gap-1">
                                                    {flexRender(
                                                        header.column.columnDef.header,
                                                        header.getContext(),
                                                    )}
                                                    {canSort && (
                                                        <span className="shrink-0">
                                                            {sorted === 'asc' ? (
                                                                <ArrowUp className="h-3 w-3" />
                                                            ) : sorted === 'desc' ? (
                                                                <ArrowDown className="h-3 w-3" />
                                                            ) : (
                                                                <ArrowUpDown className="h-3 w-3 opacity-40" />
                                                            )}
                                                        </span>
                                                    )}
                                                </div>
                                            )}
                                        </th>
                                    );
                                })}
                            </tr>
                        ))}
                    </thead>
                    <tbody className="divide-y divide-secondary-100 dark:divide-dark-600">
                        {loading ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="py-16 text-center text-dark-500 dark:text-dark-400"
                                >
                                    <div className="flex items-center justify-center gap-2">
                                        <Loader2 className="h-5 w-5 animate-spin" />
                                        <span className="text-sm">Memuat...</span>
                                    </div>
                                </td>
                            </tr>
                        ) : table.getRowModel().rows.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="py-16 text-center text-sm text-dark-500 dark:text-dark-400"
                                >
                                    {emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            table.getRowModel().rows.map((row) => (
                                <tr
                                    key={row.id}
                                    className="bg-white dark:bg-dark-800 hover:bg-secondary-50 dark:hover:bg-dark-700 transition-colors"
                                >
                                    {row.getVisibleCells().map((cell) => (
                                        <td
                                            key={cell.id}
                                            className="px-4 py-3 text-dark-700 dark:text-dark-300"
                                        >
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {pagination && onPageChange && (
                <Pagination meta={pagination} onPageChange={onPageChange} />
            )}
        </div>
    );
}
