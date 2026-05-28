import * as React from 'react';
import { cn } from '@/lib/utils';

interface EmptyStateProps {
    icon?: React.ReactNode;
    title: string;
    description?: string;
    action?: React.ReactNode;
    className?: string;
}

export function EmptyState({ icon, title, description, action, className }: EmptyStateProps) {
    return (
        <div className={cn('flex flex-col items-center justify-center py-16 px-4 text-center', className)}>
            {icon && (
                <div className="h-16 w-16 rounded-xl bg-secondary-100 dark:bg-dark-700 flex items-center justify-center mb-4 text-dark-400 dark:text-dark-500">
                    {icon}
                </div>
            )}
            <h3 className="text-lg font-semibold text-dark-900 dark:text-dark-50 mb-1">{title}</h3>
            {description && (
                <p className="text-sm text-dark-500 dark:text-dark-400 max-w-sm mb-6">{description}</p>
            )}
            {action && <div>{action}</div>}
        </div>
    );
}
