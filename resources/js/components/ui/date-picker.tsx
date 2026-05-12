import { format } from 'date-fns';
import { id as idLocale } from 'date-fns/locale';
import { CalendarDays, ChevronDown, ChevronLeft, ChevronRight, X } from 'lucide-react';
import * as React from 'react';
import { DayPicker, useDayPicker } from 'react-day-picker';
import 'react-day-picker/style.css';
import { cn } from '@/lib/utils';
import { Popover, PopoverContent, PopoverTrigger } from './popover';

/* ─── Types ────────────────────────────────────────────── */

interface BaseProps {
    label?: string;
    hint?: string;
    error?: string;
    placeholder?: string;
    disabled?: boolean;
    clearable?: boolean;
    className?: string;
    minDate?: Date;
    maxDate?: Date;
    fromYear?: number;
    toYear?: number;
}

export interface DatePickerSingleProps extends BaseProps {
    mode?: 'single';
    value?: Date | null;
    onChange: (date: Date | null) => void;
}

export interface DatePickerRangeProps extends BaseProps {
    mode: 'range';
    value?: { from: Date | null; to: Date | null };
    onChange: (range: { from: Date | null; to: Date | null }) => void;
    placeholderTo?: string;
}

export type DatePickerProps = DatePickerSingleProps | DatePickerRangeProps;

/* ─── Inline calendar header dropdown ──────────────────── */

function CalendarDropdown({
    label,
    options,
    selected,
    onSelect,
}: {
    label: string;
    options: { value: number; label: string }[];
    selected: number;
    onSelect: (v: number) => void;
}) {
    const [open, setOpen] = React.useState(false);
    const isMany = options.length > 12;

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <button
                    type="button"
                    className="flex items-center gap-1 px-2 py-1 rounded-lg text-sm font-semibold text-dark-900 dark:text-dark-50 hover:bg-zinc-100 dark:hover:bg-dark-600 transition-colors"
                >
                    {label}
                    <ChevronDown
                        className={cn(
                            'h-3 w-3 text-dark-400 transition-transform duration-150',
                            open && 'rotate-180',
                        )}
                    />
                </button>
            </PopoverTrigger>
            <PopoverContent
                className={cn('p-1.5 max-h-52 overflow-y-auto', isMany ? 'w-28' : 'w-44')}
                align="center"
            >
                <div className={cn(isMany ? 'flex flex-col gap-0.5' : 'grid grid-cols-3 gap-0.5')}>
                    {options.map((opt) => (
                        <button
                            key={opt.value}
                            type="button"
                            onClick={() => {
                                onSelect(opt.value);
                                setOpen(false);
                            }}
                            className={cn(
                                'px-2 py-1.5 rounded-lg text-sm text-center transition-colors',
                                opt.value === selected
                                    ? 'bg-primary-600 text-white font-medium'
                                    : 'text-dark-700 dark:text-dark-300 hover:bg-zinc-50 dark:hover:bg-dark-600',
                            )}
                        >
                            {opt.label}
                        </button>
                    ))}
                </div>
            </PopoverContent>
        </Popover>
    );
}

/* ─── Custom MonthCaption ───────────────────────────────── */

function MonthCaptionInner({
    calendarMonth,
    fromYear,
    toYear,
}: {
    calendarMonth: { date: Date };
    fromYear: number;
    toYear: number;
}) {
    const { goToMonth, nextMonth, previousMonth } = useDayPicker();
    const month = calendarMonth.date;

    const monthOptions = React.useMemo(
        () =>
            Array.from({ length: 12 }, (_, i) => ({
                value: i,
                label: format(new Date(2024, i, 1), 'MMM', { locale: idLocale }),
            })),
        [],
    );

    const yearOptions = React.useMemo(
        () =>
            Array.from({ length: toYear - fromYear + 1 }, (_, i) => ({
                value: fromYear + i,
                label: String(fromYear + i),
            })),
        [fromYear, toYear],
    );

    return (
        <div className="flex items-center justify-between px-1 pb-3">
            <button
                type="button"
                disabled={!previousMonth}
                onClick={() => previousMonth && goToMonth(previousMonth)}
                className="h-8 w-8 flex items-center justify-center rounded-lg text-dark-400 hover:bg-zinc-100 dark:hover:bg-dark-600 hover:text-dark-900 dark:hover:text-dark-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed shrink-0"
            >
                <ChevronLeft className="h-4 w-4" />
            </button>

            <div className="flex items-center gap-0.5">
                <CalendarDropdown
                    label={format(month, 'MMMM', { locale: idLocale })}
                    options={monthOptions}
                    selected={month.getMonth()}
                    onSelect={(m) => goToMonth(new Date(month.getFullYear(), m, 1))}
                />
                <CalendarDropdown
                    label={String(month.getFullYear())}
                    options={yearOptions}
                    selected={month.getFullYear()}
                    onSelect={(y) => goToMonth(new Date(y, month.getMonth(), 1))}
                />
            </div>

            <button
                type="button"
                disabled={!nextMonth}
                onClick={() => nextMonth && goToMonth(nextMonth)}
                className="h-8 w-8 flex items-center justify-center rounded-lg text-dark-400 hover:bg-zinc-100 dark:hover:bg-dark-600 hover:text-dark-900 dark:hover:text-dark-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed shrink-0"
            >
                <ChevronRight className="h-4 w-4" />
            </button>
        </div>
    );
}

/* ─── DatePicker ────────────────────────────────────────── */

export function DatePicker(props: DatePickerProps) {
    const {
        label,
        hint,
        error,
        disabled,
        clearable = true,
        className,
        minDate,
        maxDate,
        fromYear = new Date().getFullYear() - 10,
        toYear = new Date().getFullYear() + 5,
        placeholder = 'Pilih tanggal...',
    } = props;

    const [open, setOpen] = React.useState(false);
    const inputId = React.useId();

    const isRange = props.mode === 'range';
    const singleValue = isRange ? null : ((props as DatePickerSingleProps).value ?? null);
    const rangeValue = isRange ? ((props as DatePickerRangeProps).value ?? null) : null;
    const placeholderTo = isRange
        ? ((props as DatePickerRangeProps).placeholderTo ?? 'Tanggal akhir...')
        : '';
    const hasValue = isRange ? !!(rangeValue?.from || rangeValue?.to) : !!singleValue;

    /* trigger label */
    const triggerLabel = isRange ? (
        !rangeValue?.from && !rangeValue?.to ? (
            <span className="text-dark-400 dark:text-dark-500">{placeholder}</span>
        ) : (
            <div className="flex items-center gap-2 min-w-0 flex-1">
                <span
                    className={cn(
                        'truncate',
                        rangeValue?.from
                            ? 'text-dark-900 dark:text-dark-50'
                            : 'text-dark-400 dark:text-dark-500',
                    )}
                >
                    {rangeValue?.from
                        ? format(rangeValue.from, 'dd MMM yyyy', { locale: idLocale })
                        : placeholder}
                </span>
                <span className="text-dark-300 dark:text-dark-500 shrink-0">→</span>
                <span
                    className={cn(
                        'truncate',
                        rangeValue?.to
                            ? 'text-dark-900 dark:text-dark-50'
                            : 'text-dark-400 dark:text-dark-500',
                    )}
                >
                    {rangeValue?.to
                        ? format(rangeValue.to, 'dd MMM yyyy', { locale: idLocale })
                        : placeholderTo}
                </span>
            </div>
        )
    ) : (
        <span
            className={cn(
                'truncate',
                singleValue ? 'text-dark-900 dark:text-dark-50' : 'text-dark-400 dark:text-dark-500',
            )}
        >
            {singleValue ? format(singleValue, 'dd MMM yyyy', { locale: idLocale }) : placeholder}
        </span>
    );

    /* clear */
    const handleClear = React.useCallback(
        (e: React.MouseEvent | React.KeyboardEvent) => {
            e.stopPropagation();
            if (isRange) {
                (props as DatePickerRangeProps).onChange({ from: null, to: null });
            } else {
                (props as DatePickerSingleProps).onChange(null);
            }
        },
        // eslint-disable-next-line react-hooks/exhaustive-deps
        [isRange],
    );

    /* stable MonthCaption component that closes over fromYear/toYear */
    const MonthCaptionComponent = React.useMemo(() => {
        const fy = fromYear;
        const ty = toYear;
        // eslint-disable-next-line react/display-name
        return ({ calendarMonth }: { calendarMonth: { date: Date } }) => (
            <MonthCaptionInner calendarMonth={calendarMonth} fromYear={fy} toYear={ty} />
        );
    }, [fromYear, toYear]);

    /* DayPicker props vary by mode */
    const pickerProps = isRange
        ? {
              mode: 'range' as const,
              selected: {
                  from: rangeValue?.from ?? undefined,
                  to: rangeValue?.to ?? undefined,
              },
              onSelect: (range: { from?: Date; to?: Date } | undefined) => {
                  (props as DatePickerRangeProps).onChange({
                      from: range?.from ?? null,
                      to: range?.to ?? null,
                  });
                  if (range?.from && range?.to) setOpen(false);
              },
          }
        : {
              mode: 'single' as const,
              selected: singleValue ?? undefined,
              onSelect: (date: Date | undefined) => {
                  (props as DatePickerSingleProps).onChange(date ?? null);
                  setOpen(false);
              },
          };

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
                            'flex h-10 w-full items-center rounded-xl border px-3 text-sm text-left transition-all duration-150',
                            'bg-white dark:bg-dark-800',
                            error
                                ? 'border-red-400 dark:border-red-500 ring-2 ring-red-500/10'
                                : open
                                  ? 'border-primary-500 ring-2 ring-primary-500/15 dark:border-primary-500'
                                  : 'border-secondary-200 dark:border-dark-600 hover:border-secondary-300 dark:hover:border-dark-500',
                            'focus-visible:outline-none',
                            'disabled:cursor-not-allowed disabled:bg-secondary-50 dark:disabled:bg-dark-700 disabled:opacity-60',
                        )}
                    >
                        <CalendarDays
                            className={cn(
                                'h-4 w-4 shrink-0 mr-2.5 transition-colors',
                                hasValue
                                    ? 'text-primary-500 dark:text-primary-400'
                                    : 'text-dark-400 dark:text-dark-500',
                            )}
                        />
                        <div className="flex-1 min-w-0 flex items-center">{triggerLabel}</div>
                        {clearable && hasValue && !disabled && (
                            <span
                                role="button"
                                tabIndex={0}
                                onClick={handleClear}
                                onKeyDown={(e) => e.key === 'Enter' && handleClear(e)}
                                className="ml-1.5 shrink-0 rounded-md p-1 text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 transition-colors"
                            >
                                <X className="h-3 w-3" />
                            </span>
                        )}
                    </button>
                </PopoverTrigger>

                {/* Prevent outer popover closing when month/year inner popovers open */}
                <PopoverContent
                    className="w-auto p-0"
                    align="start"
                    onInteractOutside={(e) => {
                        const target = e.target as HTMLElement;
                        if (target.closest('[data-radix-popper-content-wrapper]')) {
                            e.preventDefault();
                        }
                    }}
                >
                    <DayPicker
                        {...pickerProps}
                        locale={idLocale}
                        disabled={[
                            ...(minDate ? [{ before: minDate }] : []),
                            ...(maxDate ? [{ after: maxDate }] : []),
                        ]}
                        components={{ MonthCaption: MonthCaptionComponent as any }}
                        classNames={{
                            root: 'p-3',
                            months: 'flex flex-col',
                            month: 'space-y-0',
                            month_caption: '',
                            caption_label: 'hidden',
                            nav: 'hidden',
                            month_grid: 'w-full border-collapse',
                            weekdays: 'flex',
                            weekday:
                                'text-dark-400 dark:text-dark-500 w-9 font-medium text-[0.8rem] text-center',
                            weeks: '',
                            week: 'flex w-full mt-2',
                            day: 'h-9 w-9 text-center text-sm p-0 relative',
                            day_button:
                                'h-9 w-9 p-0 font-normal rounded-xl hover:bg-zinc-100 dark:hover:bg-dark-600 text-dark-900 dark:text-dark-200 flex items-center justify-center text-sm transition-colors',
                            selected: '!bg-primary-600 !text-white hover:!bg-primary-700',
                            today: '!font-bold !text-primary-600 dark:!text-primary-400',
                            outside: 'opacity-30',
                            disabled: 'opacity-30 cursor-not-allowed',
                            range_start: '!rounded-r-none',
                            range_end: '!rounded-l-none',
                            range_middle:
                                '!bg-primary-100 dark:!bg-primary-900/30 !rounded-none !text-primary-900 dark:!text-primary-100 hover:!bg-primary-200 dark:hover:!bg-primary-900/50',
                        }}
                    />
                </PopoverContent>
            </Popover>

            {error && <p className="mt-1.5 text-xs text-red-600 dark:text-red-400">{error}</p>}
            {hint && !error && (
                <p className="mt-1.5 text-xs text-dark-500 dark:text-dark-400">{hint}</p>
            )}
        </div>
    );
}
