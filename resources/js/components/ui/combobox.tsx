import { Command as CommandPrimitive } from 'cmdk';
import { Check, ChevronsUpDown, Search, X } from 'lucide-react';
import * as React from 'react';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from './popover';

export interface ComboboxOption {
    value: string | number;
    label: string;
    description?: string;
}

interface ComboboxProps {
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
}

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
}: ComboboxProps) {
    const [open, setOpen] = React.useState(false);
    const [search, setSearch] = React.useState('');
    const inputId = React.useId();

    const selected = options.find((o) => o.value === value);

    const filtered = search
        ? options.filter(
              (o) =>
                  o.label.toLowerCase().includes(search.toLowerCase()) ||
                  (o.description?.toLowerCase().includes(search.toLowerCase()) ?? false),
          )
        : options;

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
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <button
                        id={inputId}
                        type="button"
                        disabled={disabled}
                        className={cn(
                            'flex h-9 w-full items-center justify-between rounded-lg border px-3 py-1.5 text-sm text-left transition-colors',
                            'bg-white dark:bg-dark-800',
                            error
                                ? 'border-red-500 dark:border-red-500'
                                : 'border-secondary-300 dark:border-dark-600',
                            'focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-0',
                            'disabled:cursor-not-allowed disabled:bg-secondary-50 dark:disabled:bg-dark-600 disabled:opacity-60',
                            selected
                                ? 'text-dark-900 dark:text-dark-300'
                                : 'text-dark-400 dark:text-dark-400',
                        )}
                    >
                        <span className="truncate">{selected ? selected.label : placeholder}</span>
                        <div className="flex items-center gap-1 shrink-0 ml-2">
                            {clearable && selected && !disabled && (
                                <span
                                    role="button"
                                    tabIndex={0}
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        onChange(null);
                                    }}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            e.stopPropagation();
                                            onChange(null);
                                        }
                                    }}
                                    className="rounded p-0.5 hover:bg-zinc-100 dark:hover:bg-dark-600"
                                >
                                    <X className="h-3 w-3 text-dark-400" />
                                </span>
                            )}
                            <ChevronsUpDown className="h-3.5 w-3.5 text-dark-400 dark:text-dark-400" />
                        </div>
                    </button>
                </PopoverTrigger>
                <PopoverContent className="w-[var(--radix-popover-trigger-width)] p-0" align="start">
                    <CommandPrimitive shouldFilter={false}>
                        <div className="flex items-center border-b border-secondary-200 dark:border-dark-600 px-3">
                            <Search className="h-4 w-4 shrink-0 text-dark-400 dark:text-dark-400 mr-2" />
                            <CommandPrimitive.Input
                                value={search}
                                onValueChange={setSearch}
                                placeholder={searchPlaceholder}
                                className="flex h-9 w-full bg-transparent py-2 text-sm text-dark-900 dark:text-dark-300 placeholder:text-dark-400 outline-none"
                            />
                        </div>
                        <CommandPrimitive.List className="max-h-60 overflow-y-auto p-1">
                            {filtered.length === 0 && (
                                <p className="py-6 text-center text-sm text-dark-500 dark:text-dark-400">
                                    {emptyText}
                                </p>
                            )}
                            {filtered.map((option) => (
                                <CommandPrimitive.Item
                                    key={option.value}
                                    value={String(option.value)}
                                    onSelect={() => {
                                        onChange(option.value);
                                        setOpen(false);
                                        setSearch('');
                                    }}
                                    className={cn(
                                        'flex cursor-pointer items-center gap-2 rounded-lg px-2.5 py-2 text-sm',
                                        'text-dark-700 dark:text-dark-300',
                                        'hover:bg-zinc-100 dark:hover:bg-dark-600',
                                        'aria-selected:bg-zinc-100 dark:aria-selected:bg-dark-600',
                                        'outline-none',
                                    )}
                                >
                                    <Check
                                        className={cn(
                                            'h-4 w-4 shrink-0 text-primary-600',
                                            option.value === value ? 'opacity-100' : 'opacity-0',
                                        )}
                                    />
                                    <div>
                                        <div>{option.label}</div>
                                        {option.description && (
                                            <div className="text-xs text-dark-500 dark:text-dark-400">
                                                {option.description}
                                            </div>
                                        )}
                                    </div>
                                </CommandPrimitive.Item>
                            ))}
                        </CommandPrimitive.List>
                    </CommandPrimitive>
                </PopoverContent>
            </Popover>
            {error && <p className="mt-1 text-xs text-red-600 dark:text-red-400">{error}</p>}
            {hint && !error && <p className="mt-1 text-xs text-dark-500 dark:text-dark-400">{hint}</p>}
        </div>
    );
}
