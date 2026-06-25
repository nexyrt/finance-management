import * as React from 'react';
import { cn } from '@/lib/utils';

export interface SliderProps {
    value: number;
    onChange: (value: number) => void;
    min?: number;
    max?: number;
    step?: number;
    label?: string;
    suffix?: string;
    className?: string;
    disabled?: boolean;
}

/**
 * Slider — styled range input following Archipelago design system.
 * primary-600 accent track/thumb, dark-800 background, Inter typography.
 */
function Slider({
    value,
    onChange,
    min = 0,
    max = 100,
    step = 1,
    label,
    suffix,
    className,
    disabled,
}: SliderProps) {
    const id = React.useId();

    return (
        <div className={cn('w-full', className)}>
            {label && (
                <label
                    htmlFor={id}
                    className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300"
                >
                    {label}
                </label>
            )}
            <div className="flex items-center gap-2">
                <input
                    id={id}
                    type="range"
                    min={min}
                    max={max}
                    step={step}
                    value={value}
                    disabled={disabled}
                    onChange={(e) => onChange(+e.target.value)}
                    className={cn(
                        'flex-1 h-1.5 appearance-none rounded-full cursor-pointer',
                        'bg-secondary-200 dark:bg-dark-600',
                        'accent-primary-600',
                        // Webkit thumb styling via accent-color (Tailwind shorthand)
                        '[&::-webkit-slider-thumb]:appearance-none',
                        '[&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:w-4',
                        '[&::-webkit-slider-thumb]:rounded-full',
                        '[&::-webkit-slider-thumb]:bg-primary-600',
                        '[&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-white',
                        '[&::-webkit-slider-thumb]:shadow-sm',
                        '[&::-webkit-slider-thumb]:cursor-pointer',
                        '[&::-webkit-slider-thumb]:transition-transform',
                        '[&::-webkit-slider-thumb]:hover:scale-110',
                        // Moz thumb
                        '[&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:w-4',
                        '[&::-moz-range-thumb]:rounded-full',
                        '[&::-moz-range-thumb]:bg-primary-600',
                        '[&::-moz-range-thumb]:border-2 [&::-moz-range-thumb]:border-white',
                        '[&::-moz-range-thumb]:shadow-sm',
                        '[&::-moz-range-thumb]:cursor-pointer',
                        '[&::-moz-range-thumb]:border-0',
                        'focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2',
                        'disabled:cursor-not-allowed disabled:opacity-50',
                    )}
                />
                {suffix !== undefined && (
                    <span className="text-xs tabular-nums w-8 text-right shrink-0 text-dark-500 dark:text-dark-400">
                        {value}{suffix}
                    </span>
                )}
            </div>
        </div>
    );
}

Slider.displayName = 'Slider';

export { Slider };
