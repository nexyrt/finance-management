import { format } from 'date-fns';
import { id as idLocale } from 'date-fns/locale';
import { CalendarDays, X } from 'lucide-react';
import * as React from 'react';
import { DayPicker } from 'react-day-picker';
import 'react-day-picker/style.css';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from './popover';

interface DatePickerProps {
    value?: Date | null;
    onChange: (date: Date | null) => void;
    label?: string;
    hint?: string;
    error?: string;
    placeholder?: string;
    disabled?: boolean;
    clearable?: boolean;
    className?: string;
    minDate?: Date;
    maxDate?: Date;
}

export function DatePicker({
    value,
    onChange,
    label,
    hint,
    error,
    placeholder = 'Pilih tanggal...',
    disabled,
    clearable = true,
    className,
    minDate,
    maxDate,
}: DatePickerProps) {
    const [open, setOpen] = React.useState(false);
    const inputId = React.useId();

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
                            value
                                ? 'text-dark-900 dark:text-dark-300'
                                : 'text-dark-400 dark:text-dark-400',
                        )}
                    >
                        <span>{value ? format(value, 'dd MMM yyyy', { locale: idLocale }) : placeholder}</span>
                        <div className="flex items-center gap-1 shrink-0 ml-2">
                            {clearable && value && !disabled && (
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
                            <CalendarDays className="h-4 w-4 text-dark-400 dark:text-dark-400" />
                        </div>
                    </button>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0" align="start">
                    <DayPicker
                        mode="single"
                        selected={value ?? undefined}
                        onSelect={(date) => {
                            onChange(date ?? null);
                            setOpen(false);
                        }}
                        locale={idLocale}
                        disabled={[
                            ...(minDate ? [{ before: minDate }] : []),
                            ...(maxDate ? [{ after: maxDate }] : []),
                        ]}
                        classNames={{
                            root: 'p-3',
                            months: 'flex flex-col',
                            month: 'space-y-4',
                            caption: 'flex justify-center pt-1 relative items-center',
                            caption_label: 'text-sm font-medium text-dark-900 dark:text-dark-50',
                            nav: 'space-x-1 flex items-center',
                            nav_button:
                                'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100 border border-secondary-200 dark:border-dark-600 rounded-lg flex items-center justify-center',
                            nav_button_previous: 'absolute left-1',
                            nav_button_next: 'absolute right-1',
                            table: 'w-full border-collapse space-y-1',
                            head_row: 'flex',
                            head_cell:
                                'text-dark-500 dark:text-dark-400 rounded-md w-9 font-normal text-[0.8rem]',
                            row: 'flex w-full mt-2',
                            cell: 'h-9 w-9 text-center text-sm p-0 relative',
                            day: 'h-9 w-9 p-0 font-normal rounded-lg hover:bg-zinc-100 dark:hover:bg-dark-600 text-dark-900 dark:text-dark-300 flex items-center justify-center',
                            day_selected:
                                'bg-primary-600 text-white hover:bg-primary-700 hover:text-white focus:bg-primary-600 focus:text-white',
                            day_today: 'font-semibold text-primary-600 dark:text-primary-400',
                            day_outside: 'opacity-30',
                            day_disabled: 'opacity-30 cursor-not-allowed',
                        }}
                    />
                </PopoverContent>
            </Popover>
            {error && <p className="mt-1 text-xs text-red-600 dark:text-red-400">{error}</p>}
            {hint && !error && (
                <p className="mt-1 text-xs text-dark-500 dark:text-dark-400">{hint}</p>
            )}
        </div>
    );
}
