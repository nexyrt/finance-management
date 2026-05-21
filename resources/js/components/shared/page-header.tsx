import * as React from 'react';
import { cn } from '@/lib/utils';

interface PageHeaderProps {
    title: string;
    description?: string;
    action?: React.ReactNode;
    className?: string;
}

export function PageHeader({ title, description, action, className }: PageHeaderProps) {
    return (
        <div
            className={cn(
                'flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4',
                className,
            )}
        >
            <div className="space-y-1">
                <h1 className="text-4xl font-bold bg-linear-to-r from-gray-900 via-blue-800 to-indigo-800 dark:from-white dark:via-blue-200 dark:to-indigo-200 bg-clip-text text-transparent">
                    {title}
                </h1>
                {description && (
                    <p className="text-gray-600 dark:text-zinc-400 text-lg">{description}</p>
                )}
            </div>
            {action && <div className="shrink-0">{action}</div>}
        </div>
    );
}
