import { Command as CommandPrimitive } from 'cmdk';
import { Check, ChevronDown, Search, X } from 'lucide-react';
import * as React from 'react';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from './popover';

export interface ComboboxOption {
    value: string | number;
    label: string;
    description?: string;
    disabled?: boolean;
}

interface ComboboxSingleProps {
    multiple?: false;
    options: ComboboxOption[];
    value?: string | number | null;
    onChange: (value: string | number | null) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    label?: string;
    hint?: string;
    error?: string;
    disabled?: boolean;
    clearable?: boolean;
    className?: string;
    emptyText?: string;
    popoverWidth?: string;
}

interface ComboboxMultipleProps {
    multiple: true;
    options: ComboboxOption[];
    value?: (string | number)[];
    onChange: (value: (string | number)[]) => void;
    placeholder?: string;
    searchPlaceholder?: string;
    label?: string;
    hint?: string;
    error?: string;
    disabled?: boolean;
    clearable?: boolean;
    className?: string;
    emptyText?: string;
    popoverWidth?: string;
}

type ComboboxProps = ComboboxSingleProps | ComboboxMultipleProps;

export function Combobox({
    options,
    value,
    onChange,
    placeholder = 'Pilih...',
    searchPlaceholder = 'Cari...',
    label,
    hint,
    error,
    disabled,
    clearable = true,
    className,
    emptyText = 'Tidak ada data.',
    multiple = false,
    popoverWidth,
}: ComboboxProps & { multiple?: boolean }) {
    const [open, setOpen] = React.useState(false);
    const [search, setSearch] = React.useState('');
    const inputId = React.useId();

    /* ── single helpers ── */
    const singleValue = !multiple ? (value as string | number | null | undefined) : null;
    const selected = !multiple ? options.find((o) => o.value === singleValue) : null;

    /* ── multiple helpers ── */
    const multiValue = multiple ? ((value as (string | number)[] | undefined) ?? []) : [];
    const selectedMultiple = multiple ? options.filter((o) => multiValue.includes(o.value)) : [];

    const hasValue = multiple ? multiValue.length > 0 : !!selected;

    const isSelected = (opt: ComboboxOption) =>
        multiple ? multiValue.includes(opt.value) : opt.value === singleValue;

    const toggleMultiple = (optValue: string | number) => {
        const current = multiValue;
        const next = current.includes(optValue)
            ? current.filter((v) => v !== optValue)
            : [...current, optValue];
        (onChange as (v: (string | number)[]) => void)(next);
    };

    const filtered = React.useMemo(() => {
        if (!search) return options;
        const q = search.toLowerCase();
        const result: ComboboxOption[] = [];
        let pendingHeader: ComboboxOption | null = null;
        for (const opt of options) {
            if (opt.disabled) {
                pendingHeader = opt;
            } else if (opt.label.toLowerCase().includes(q) || (opt.description?.toLowerCase().includes(q) ?? false)) {
                if (pendingHeader) { result.push(pendingHeader); pendingHeader = null; }
                result.push(opt);
            }
        }
        return result;
    }, [search, options]);

    /* trigger display label */
    const triggerContent = multiple
        ? multiValue.length === 0
            ? <span className="text-dark-400 dark:text-dark-500 truncate">{placeholder}</span>
            : multiValue.length === 1
              ? <span className="text-dark-900 dark:text-dark-50 truncate">{selectedMultiple[0]?.label}</span>
              : <div className="flex items-center gap-1.5 min-w-0 flex-1">
                    <span className="inline-flex items-center px-1.5 py-0.5 rounded-md text-xs font-medium bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 shrink-0">
                        {multiValue.length}
                    </span>
                    <span className="text-dark-900 dark:text-dark-50 truncate text-sm">
                        {selectedMultiple.map((o) => o.label).join(', ')}
                    </span>
                </div>
        : <span className="truncate">{selected ? selected.label : placeholder}</span>;

    return (
        <div className={cn('w-full', className)}>
            {label && (
                <label
                    htmlFor={inputId}
                    className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300"
                >
                    {label}
                </label>
            )}
            <Popover open={open} onOpenChange={(o) => { setOpen(o); if (!o) setSearch(''); }}>
                <PopoverTrigger asChild>
                    <button
                        id={inputId}
                        type="button"
                        disabled={disabled}
                        className={cn(
                            'flex h-10 w-full items-center justify-between rounded-xl border px-3 text-sm text-left transition-all duration-150',
                            'bg-white dark:bg-dark-800',
                            error
                                ? 'border-red-400 dark:border-red-500 ring-2 ring-red-500/10'
                                : open
                                  ? 'border-primary-500 ring-2 ring-primary-500/15 dark:border-primary-500'
                                  : 'border-secondary-200 dark:border-dark-600 hover:border-secondary-300 dark:hover:border-dark-500',
                            'focus-visible:outline-none',
                            'disabled:cursor-not-allowed disabled:bg-secondary-50 dark:disabled:bg-dark-700 disabled:opacity-60',
                            hasValue
                                ? 'text-dark-900 dark:text-dark-50'
                                : 'text-dark-400 dark:text-dark-500',
                        )}
                    >
                        <div className="flex-1 min-w-0 flex items-center">{triggerContent}</div>
                        <div className="flex items-center gap-0.5 shrink-0 ml-2">
                            {clearable && hasValue && !disabled && (
                                <span
                                    role="button"
                                    tabIndex={0}
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        if (multiple) {
                                            (onChange as (v: (string | number)[]) => void)([]);
                                        } else {
                                            (onChange as (v: string | number | null) => void)(null);
                                        }
                                    }}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            e.stopPropagation();
                                            if (multiple) {
                                                (onChange as (v: (string | number)[]) => void)([]);
                                            } else {
                                                (onChange as (v: string | number | null) => void)(null);
                                            }
                                        }
                                    }}
                                    className="rounded-md p-1 text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 transition-colors"
                                >
                                    <X className="h-3 w-3" />
                                </span>
                            )}
                            <ChevronDown
                                className={cn(
                                    'h-4 w-4 text-dark-400 transition-transform duration-200',
                                    open && 'rotate-180',
                                )}
                            />
                        </div>
                    </button>
                </PopoverTrigger>
                <PopoverContent className={cn('p-0 overflow-hidden min-w-(--radix-popover-trigger-width)', popoverWidth ?? 'w-(--radix-popover-trigger-width)')} align="start">
                    <CommandPrimitive shouldFilter={false}>
                        <div className="flex items-center px-3 border-b border-secondary-100 dark:border-dark-600">
                            <Search className="h-3.5 w-3.5 shrink-0 text-dark-400 mr-2.5" />
                            <CommandPrimitive.Input
                                value={search}
                                onValueChange={setSearch}
                                placeholder={searchPlaceholder}
                                className="flex h-9 w-full bg-transparent py-2 text-sm text-dark-900 dark:text-dark-300 placeholder:text-dark-400 outline-none focus:outline-none focus:ring-0 border-0"
                            />
                        </div>
                        <CommandPrimitive.List
                            className="max-h-60 overflow-y-auto p-1.5"
                            onWheel={(e) => e.stopPropagation()}
                        >
                            {filtered.length === 0 && (
                                <p className="py-8 text-center text-sm text-dark-400 dark:text-dark-500">
                                    {emptyText}
                                </p>
                            )}
                            {filtered.map((option) =>
                                option.disabled ? (
                                    <div
                                        key={option.value}
                                        className="px-2.5 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-dark-400 dark:text-dark-500 select-none"
                                    >
                                        {option.label}
                                    </div>
                                ) : (
                                <CommandPrimitive.Item
                                    key={option.value}
                                    value={String(option.value)}
                                    onSelect={() => {
                                        if (multiple) {
                                            toggleMultiple(option.value);
                                        } else {
                                            (onChange as (v: string | number | null) => void)(option.value);
                                            setOpen(false);
                                            setSearch('');
                                        }
                                    }}
                                    className={cn(
                                        'flex cursor-pointer items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm outline-none transition-colors',
                                        isSelected(option)
                                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                            : 'text-dark-700 dark:text-dark-300 hover:bg-zinc-50 dark:hover:bg-dark-600 aria-selected:bg-zinc-50 dark:aria-selected:bg-dark-600',
                                    )}
                                >
                                    <div
                                        className={cn(
                                            'h-4 w-4 shrink-0 flex items-center justify-center rounded',
                                            isSelected(option)
                                                ? 'bg-primary-600 dark:bg-primary-500 text-white'
                                                : 'border border-secondary-300 dark:border-dark-500 text-transparent',
                                        )}
                                    >
                                        <Check className="h-3 w-3" />
                                    </div>
                                    <div className="min-w-0">
                                        <div className="truncate">{option.label}</div>
                                        {option.description && (
                                            <div className="text-xs text-dark-400 dark:text-dark-500 truncate">
                                                {option.description}
                                            </div>
                                        )}
                                    </div>
                                </CommandPrimitive.Item>
                                )
                            )}
                        </CommandPrimitive.List>
                        {multiple && multiValue.length > 0 && (
                            <div className="border-t border-secondary-100 dark:border-dark-600 p-1.5">
                                <button
                                    type="button"
                                    onClick={() => {
                                        (onChange as (v: (string | number)[]) => void)([]);
                                        setOpen(false);
                                    }}
                                    className="w-full px-2.5 py-1.5 text-xs font-medium text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-600 rounded-lg transition-colors text-left"
                                >
                                    Hapus semua pilihan ({multiValue.length})
                                </button>
                            </div>
                        )}
                    </CommandPrimitive>
                </PopoverContent>
            </Popover>
            {error && <p className="mt-1.5 text-xs text-red-600 dark:text-red-400">{error}</p>}
            {hint && !error && <p className="mt-1.5 text-xs text-dark-500 dark:text-dark-400">{hint}</p>}
        </div>
    );
}
