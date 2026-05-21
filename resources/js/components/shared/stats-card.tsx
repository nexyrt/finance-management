import * as React from 'react';
import { cn } from '@/lib/utils';

interface StatsCardProps {
    label: string;
    value: React.ReactNode;
    icon: React.ReactNode;
    color?: 'blue' | 'green' | 'purple' | 'red' | 'emerald' | 'yellow' | 'orange' | 'indigo';
    className?: string;
    inModal?: boolean;
}

const colorMap = {
    blue:    { accent: 'bg-blue-500',    icon: 'text-blue-500' },
    green:   { accent: 'bg-green-500',   icon: 'text-green-500' },
    emerald: { accent: 'bg-emerald-500', icon: 'text-emerald-500' },
    purple:  { accent: 'bg-purple-500',  icon: 'text-purple-500' },
    red:     { accent: 'bg-red-500',     icon: 'text-red-500' },
    yellow:  { accent: 'bg-yellow-500',  icon: 'text-yellow-500' },
    orange:  { accent: 'bg-orange-500',  icon: 'text-orange-500' },
    indigo:  { accent: 'bg-indigo-500',  icon: 'text-indigo-500' },
};

export function StatsCard({ label, value, icon, color = 'blue', className, inModal = false }: StatsCardProps) {
    const colors = colorMap[color];

    const content = (
        <>
            <div className={cn('h-1', colors.accent)} />
            <div className={inModal ? 'p-4' : 'p-5'}>
                <div className="flex items-start justify-between mb-3">
                    <p className="text-xs font-semibold uppercase tracking-wider text-dark-500 dark:text-dark-400 leading-none">
                        {label}
                    </p>
                    <span className={cn('[&_svg]:w-5 [&_svg]:h-5 shrink-0', colors.icon)}>
                        {icon}
                    </span>
                </div>
                <p className="text-2xl font-bold tabular-nums leading-none text-dark-900 dark:text-dark-50">
                    {value}
                </p>
            </div>
        </>
    );

    if (inModal) {
        return (
            <div
                className={cn(
                    'rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden bg-white dark:bg-dark-700',
                    className,
                )}
            >
                {content}
            </div>
        );
    }

    return (
        <div
            className={cn(
                'rounded-xl border border-secondary-200 dark:border-dark-600 overflow-hidden',
                'bg-white dark:bg-dark-700 hover:shadow-md transition-shadow',
                className,
            )}
        >
            {content}
        </div>
    );
}
