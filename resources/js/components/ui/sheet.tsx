import * as DialogPrimitive from '@radix-ui/react-dialog';
import { X } from 'lucide-react';
import * as React from 'react';
import { cn } from '@/lib/utils';

const Sheet = DialogPrimitive.Root;
const SheetTrigger = DialogPrimitive.Trigger;
const SheetClose = DialogPrimitive.Close;
const SheetPortal = DialogPrimitive.Portal;

const widthClasses = {
    sm: 'w-full max-w-sm',
    md: 'w-full max-w-md',
    lg: 'w-full max-w-lg',
    xl: 'w-full max-w-xl',
    '2xl': 'w-full max-w-2xl',
    '3xl': 'w-full max-w-3xl',
};

const SheetOverlay = React.forwardRef<
    React.ElementRef<typeof DialogPrimitive.Overlay>,
    React.ComponentPropsWithoutRef<typeof DialogPrimitive.Overlay>
>(({ className, ...props }, ref) => (
    <DialogPrimitive.Overlay
        ref={ref}
        className={cn(
            'fixed inset-0 z-50 bg-black/40 backdrop-blur-sm',
            'data-[state=open]:animate-dialog-overlay-show',
            'data-[state=closed]:animate-dialog-overlay-hide',
            className,
        )}
        {...props}
    />
));
SheetOverlay.displayName = 'SheetOverlay';

interface SheetContentProps extends React.ComponentPropsWithoutRef<typeof DialogPrimitive.Content> {
    size?: keyof typeof widthClasses;
    hideClose?: boolean;
}

const SheetContent = React.forwardRef<
    React.ElementRef<typeof DialogPrimitive.Content>,
    SheetContentProps
>(({ className, children, size = 'lg', hideClose = false, onFocusOutside, ...props }, ref) => (
    <SheetPortal>
        <SheetOverlay />
        <DialogPrimitive.Content
            ref={ref}
            onFocusOutside={(e) => {
                onFocusOutside?.(e);
                if (!e.defaultPrevented) e.preventDefault();
            }}
            className={cn(
                'fixed right-0 top-0 z-50 h-full',
                widthClasses[size],
                'bg-white dark:bg-dark-700',
                'border-l border-secondary-200 dark:border-dark-600',
                'shadow-2xl',
                'flex flex-col',
                'data-[state=open]:animate-sheet-slide-in',
                'data-[state=closed]:animate-sheet-slide-out',
                'focus:outline-none',
                className,
            )}
            {...props}
        >
            {children}
            {!hideClose && (
                <DialogPrimitive.Close className="absolute right-4 top-4 rounded-lg p-1.5 opacity-70 ring-offset-white transition-opacity hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:pointer-events-none dark:ring-offset-dark-700">
                    <X className="h-4 w-4 text-dark-600 dark:text-dark-400" />
                    <span className="sr-only">Close</span>
                </DialogPrimitive.Close>
            )}
        </DialogPrimitive.Content>
    </SheetPortal>
));
SheetContent.displayName = 'SheetContent';

const SheetHeader = ({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) => (
    <div
        className={cn(
            'flex flex-col space-y-1 px-6 py-4 border-b border-secondary-200 dark:border-dark-600 shrink-0',
            className,
        )}
        {...props}
    />
);
SheetHeader.displayName = 'SheetHeader';

const SheetBody = ({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) => (
    <div
        className={cn('flex-1 overflow-y-auto px-6 py-5', className)}
        {...props}
    />
);
SheetBody.displayName = 'SheetBody';

const SheetFooter = ({ className, ...props }: React.HTMLAttributes<HTMLDivElement>) => (
    <div
        className={cn(
            'flex items-center justify-end gap-3 px-6 py-4 border-t border-secondary-200 dark:border-dark-600 shrink-0',
            className,
        )}
        {...props}
    />
);
SheetFooter.displayName = 'SheetFooter';

const SheetTitle = React.forwardRef<
    React.ElementRef<typeof DialogPrimitive.Title>,
    React.ComponentPropsWithoutRef<typeof DialogPrimitive.Title>
>(({ className, ...props }, ref) => (
    <DialogPrimitive.Title
        ref={ref}
        className={cn('text-base font-semibold text-dark-900 dark:text-dark-50 pr-6', className)}
        {...props}
    />
));
SheetTitle.displayName = DialogPrimitive.Title.displayName;

const SheetDescription = React.forwardRef<
    React.ElementRef<typeof DialogPrimitive.Description>,
    React.ComponentPropsWithoutRef<typeof DialogPrimitive.Description>
>(({ className, ...props }, ref) => (
    <DialogPrimitive.Description
        ref={ref}
        className={cn('text-xs text-dark-500 dark:text-dark-400 mt-0.5', className)}
        {...props}
    />
));
SheetDescription.displayName = DialogPrimitive.Description.displayName;

export {
    Sheet,
    SheetBody,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetOverlay,
    SheetPortal,
    SheetTitle,
    SheetTrigger,
};
