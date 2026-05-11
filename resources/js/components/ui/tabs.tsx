import * as React from 'react';
import { cn } from '@/lib/utils';

interface TabItem {
    value: string;
    label: string;
    icon?: React.ReactNode;
    badge?: number | string;
}

interface TabsProps {
    items: TabItem[];
    value: string;
    onChange: (value: string) => void;
    className?: string;
    storageKey?: string;
}

function Tabs({ items, value, onChange, className }: TabsProps) {
    return (
        <div
            className={cn(
                'inline-flex items-center gap-1 p-1 rounded-xl',
                'bg-zinc-100 dark:bg-dark-700',
                'border border-zinc-200 dark:border-dark-600',
                className,
            )}
        >
            {items.map((item) => (
                <button
                    key={item.value}
                    type="button"
                    onClick={() => onChange(item.value)}
                    className={cn(
                        'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                        value === item.value
                            ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-zinc-50 dark:hover:bg-dark-600',
                    )}
                >
                    {item.icon && <span className="h-4 w-4 shrink-0">{item.icon}</span>}
                    <span>{item.label}</span>
                    {item.badge !== undefined && (
                        <span className="ml-1 px-1.5 py-0.5 text-xs font-bold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full">
                            {item.badge}
                        </span>
                    )}
                </button>
            ))}
        </div>
    );
}

interface TabsPanelProps {
    value: string;
    activeValue: string;
    children: React.ReactNode;
    className?: string;
}

function TabsPanel({ value, activeValue, children, className }: TabsPanelProps) {
    if (value !== activeValue) return null;
    return (
        <div
            className={cn(
                'animate-in fade-in-0 slide-in-from-bottom-1 duration-150',
                className,
            )}
        >
            {children}
        </div>
    );
}

export { Tabs, TabsPanel };
export type { TabItem };
