import { cva, type VariantProps } from 'class-variance-authority';
import * as React from 'react';
import { cn } from '@/lib/utils';

const badgeVariants = cva(
    'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold transition-colors',
    {
        variants: {
            variant: {
                default: 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300',
                secondary: 'bg-secondary-100 dark:bg-dark-700 text-secondary-700 dark:text-dark-300',
                blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                green: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
                emerald: 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                yellow: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
                orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
                red: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
                purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
                zinc: 'bg-zinc-100 dark:bg-dark-700 text-zinc-700 dark:text-dark-400',
                outline:
                    'border border-secondary-200 dark:border-dark-600 text-dark-700 dark:text-dark-300 bg-transparent',
            },
            size: {
                sm: 'px-2 py-0.5 text-xs',
                md: 'px-2.5 py-0.5 text-xs',
                lg: 'px-3 py-1 text-sm',
            },
        },
        defaultVariants: {
            variant: 'default',
            size: 'md',
        },
    },
);

export interface BadgeProps
    extends React.HTMLAttributes<HTMLDivElement>,
        VariantProps<typeof badgeVariants> {}

function Badge({ className, variant, size, ...props }: BadgeProps) {
    return <div className={cn(badgeVariants({ variant, size }), className)} {...props} />;
}

export { Badge, badgeVariants };
