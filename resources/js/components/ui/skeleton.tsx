import { cn } from '@/lib/utils';

function Skeleton({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) {
    return (
        <div
            className={cn('animate-pulse rounded-md bg-secondary-200 dark:bg-dark-700', className)}
            {...props}
        />
    );
}

export { Skeleton };
