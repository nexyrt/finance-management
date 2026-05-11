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
    blue: {
        bg: 'bg-blue-50 dark:bg-blue-900/20',
        icon: 'text-blue-600 dark:text-blue-400',
    },
    green: {
        bg: 'bg-green-50 dark:bg-green-900/20',
        icon: 'text-green-600 dark:text-green-400',
    },
    emerald: {
        bg: 'bg-emerald-50 dark:bg-emerald-900/20',
        icon: 'text-emerald-600 dark:text-emerald-400',
    },
    purple: {
        bg: 'bg-purple-50 dark:bg-purple-900/20',
        icon: 'text-purple-600 dark:text-purple-400',
    },
    red: {
        bg: 'bg-red-50 dark:bg-red-900/20',
        icon: 'text-red-600 dark:text-red-400',
    },
    yellow: {
        bg: 'bg-yellow-50 dark:bg-yellow-900/20',
        icon: 'text-yellow-600 dark:text-yellow-400',
    },
    orange: {
        bg: 'bg-orange-50 dark:bg-orange-900/20',
        icon: 'text-orange-600 dark:text-orange-400',
    },
    indigo: {
        bg: 'bg-indigo-50 dark:bg-indigo-900/20',
        icon: 'text-indigo-600 dark:text-indigo-400',
    },
};

export function StatsCard({ label, value, icon, color = 'blue', className, inModal = false }: StatsCardProps) {
    const colors = colorMap[color];

    const inner = (
        <div className="flex items-center gap-4">
            <div
                className={cn(
                    'h-12 w-12 rounded-xl flex items-center justify-center shrink-0',
                    colors.bg,
                )}
            >
                <span className={cn('w-6 h-6', colors.icon)}>{icon}</span>
            </div>
            <div>
                <p className="text-sm text-dark-600 dark:text-dark-400">{label}</p>
                <p className="text-2xl font-bold text-dark-900 dark:text-dark-50">{value}</p>
            </div>
        </div>
    );

    if (inModal) {
        return (
            <div
                className={cn(
                    'p-4 border border-secondary-200 dark:border-dark-600 rounded-xl',
                    className,
                )}
            >
                {inner}
            </div>
        );
    }

    return (
        <div
            className={cn(
                'p-4 rounded-xl bg-white dark:bg-dark-700',
                'border border-secondary-200 dark:border-dark-600',
                'shadow-sm hover:shadow-lg transition-shadow',
                className,
            )}
        >
            {inner}
        </div>
    );
}
