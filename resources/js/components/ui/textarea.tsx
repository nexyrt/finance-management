import * as React from 'react';
import { cn } from '@/lib/utils';

export interface TextareaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
    label?: string;
    hint?: string;
    error?: string;
}

const Textarea = React.forwardRef<HTMLTextAreaElement, TextareaProps>(
    ({ className, label, hint, error, id, ...props }, ref) => {
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
                <textarea
                    id={inputId}
                    ref={ref}
                    className={cn(
                        'flex min-h-[80px] w-full rounded-lg border px-3 py-2 text-sm transition-colors',
                        'bg-white dark:bg-dark-800',
                        'text-dark-900 dark:text-dark-300',
                        'placeholder:text-dark-400 dark:placeholder:text-dark-400',
                        error
                            ? 'border-red-500 dark:border-red-500 focus:ring-red-500'
                            : 'border-secondary-300 dark:border-dark-600 focus:ring-primary-500',
                        'focus:outline-none focus:ring-2 focus:ring-offset-0',
                        'disabled:cursor-not-allowed disabled:bg-secondary-50 dark:disabled:bg-dark-600 disabled:opacity-60',
                        'resize-y',
                        className,
                    )}
                    {...props}
                />
                {error && <p className="mt-1 text-xs text-red-600 dark:text-red-400">{error}</p>}
                {hint && !error && (
                    <p className="mt-1 text-xs text-dark-500 dark:text-dark-400">{hint}</p>
                )}
            </div>
        );
    },
);
Textarea.displayName = 'Textarea';

export { Textarea };
