import * as React from 'react';
import { cn } from '@/lib/utils';

export interface ColorInputProps {
    value: string;
    onChange: (value: string) => void;
    label?: string;
    error?: string;
    className?: string;
}

/**
 * ColorInput — swatch + hex text field for Archipelago design system.
 * Tokens: dark-800 bg, dark-600 ring/border, primary-500 focus, rounded-md components.
 */
function ColorInput({ value, onChange, label, error, className }: ColorInputProps) {
    const id = React.useId();

    // Keep local text state so user can type partial hex without losing focus
    const [text, setText] = React.useState(value.toUpperCase());

    // Sync when external value changes (e.g. undo/redo)
    React.useEffect(() => {
        setText(value.toUpperCase());
    }, [value]);

    const commitText = (raw: string) => {
        const cleaned = raw.trim();
        // Accept 3-char (#abc) or 6-char (#rrggbb) hex
        if (/^#[0-9a-fA-F]{6}$/.test(cleaned) || /^#[0-9a-fA-F]{3}$/.test(cleaned)) {
            onChange(cleaned.toLowerCase());
            setText(cleaned.toUpperCase());
        } else {
            // Revert invalid input
            setText(value.toUpperCase());
        }
    };

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
            <div
                className={cn(
                    'flex items-center gap-2 h-8 w-full rounded-md border px-1.5 transition-colors',
                    'bg-white dark:bg-dark-800',
                    error
                        ? 'border-red-500 dark:border-red-500 focus-within:ring-2 focus-within:ring-red-500/20'
                        : 'border-secondary-200 dark:border-dark-600 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500',
                )}
            >
                {/* Native color picker — hidden chrome, shows as swatch */}
                <input
                    type="color"
                    value={value}
                    onChange={(e) => {
                        onChange(e.target.value);
                        setText(e.target.value.toUpperCase());
                    }}
                    className="h-5 w-5 shrink-0 cursor-pointer rounded-sm border-0 bg-transparent p-0 appearance-none"
                    tabIndex={-1}
                    aria-hidden
                />
                {/* Hex text field */}
                <input
                    id={id}
                    type="text"
                    value={text}
                    maxLength={7}
                    onChange={(e) => setText(e.target.value.toUpperCase())}
                    onBlur={(e) => commitText(e.target.value)}
                    onKeyDown={(e) => {
                        if (e.key === 'Enter') commitText((e.target as HTMLInputElement).value);
                    }}
                    className="flex-1 min-w-0 bg-transparent text-xs tabular-nums uppercase text-dark-900 dark:text-dark-50 outline-none placeholder:text-dark-400"
                    placeholder="#000000"
                    spellCheck={false}
                />
            </div>
            {error && <p className="mt-1 text-xs text-red-600 dark:text-red-400">{error}</p>}
        </div>
    );
}

ColorInput.displayName = 'ColorInput';

export { ColorInput };
