import * as React from 'react';
import { cn } from '@/lib/utils';

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    hint?: string;
    error?: string;
    icon?: React.ReactNode;
    iconRight?: React.ReactNode;
}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
    ({ className, label, hint, error, icon, iconRight, id, type = 'text', ...props }, ref) => {
        const inputId = id ?? React.useId();
        return (
            <div className="w-full">
                {label && (
                    <label
                        htmlFor={inputId}
                        className="mb-1.5 block text-sm font-medium text-dark-900 dark:text-dark-300"
                    >
                        {label}
                    </label>
                )}
                <div className="relative">
                    {icon && (
                        <div className="pointer-events-none absolute inset-y-0 left-3 flex items-center text-dark-400 dark:text-dark-400">
                            <span className="h-4 w-4">{icon}</span>
                        </div>
                    )}
                    <input
                        id={inputId}
                        type={type}
                        ref={ref}
                        className={cn(
                            'flex h-9 w-full rounded-lg border text-sm transition-colors',
                            'bg-white dark:bg-dark-800',
                            'text-dark-900 dark:text-dark-300',
                            'placeholder:text-dark-400 dark:placeholder:text-dark-400',
                            error
                                ? 'border-red-500 dark:border-red-500 focus:ring-red-500'
                                : 'border-secondary-300 dark:border-dark-600 focus:ring-primary-500',
                            'focus:outline-none focus:ring-2 focus:ring-offset-0',
                            'disabled:cursor-not-allowed disabled:bg-secondary-50 dark:disabled:bg-dark-600 disabled:opacity-60',
                            icon ? 'pl-9' : 'px-3',
                            iconRight ? 'pr-9' : 'pr-3',
                            'py-1.5',
                            className,
                        )}
                        {...props}
                    />
                    {iconRight && (
                        <div className="pointer-events-none absolute inset-y-0 right-3 flex items-center text-dark-400 dark:text-dark-400">
                            <span className="h-4 w-4">{iconRight}</span>
                        </div>
                    )}
                </div>
                {error && <p className="mt-1 text-xs text-red-600 dark:text-red-400">{error}</p>}
                {hint && !error && (
                    <p className="mt-1 text-xs text-dark-500 dark:text-dark-400">{hint}</p>
                )}
            </div>
        );
    },
);
Input.displayName = 'Input';

export { Input };
