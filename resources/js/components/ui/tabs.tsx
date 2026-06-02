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
    variant?: 'pill' | 'underline';
}

interface Indicator {
    /** Distance from the container's left padding edge to the active button. */
    left: number;
    /** Distance from the active button to the container's right padding edge. */
    right: number;
    /** Travel direction since the last change: 1 = right, -1 = left, 0 = none. */
    dir: number;
    ready: boolean;
}

const STRETCH_DURATION = 320;
const STRETCH_DELAY = 130;
const STRETCH_EASE = 'cubic-bezier(0.32, 0.72, 0, 1)';

/**
 * Builds the "stretch & settle" (liquid) transition: the leading edge moves
 * immediately while the trailing edge follows after a delay, so the indicator
 * elongates toward the destination and then contracts onto it.
 */
function stretchTransition(dir: number): string {
    const lead = `${STRETCH_DURATION}ms ${STRETCH_EASE}`;
    const trail = `${STRETCH_DURATION}ms ${STRETCH_EASE} ${STRETCH_DELAY}ms`;
    if (dir > 0) {
        // Moving right: the right edge leads, the left edge trails.
        return `right ${lead}, left ${trail}`;
    }
    if (dir < 0) {
        // Moving left: the left edge leads, the right edge trails.
        return `left ${lead}, right ${trail}`;
    }
    return `left ${lead}, right ${lead}`;
}

/**
 * Measures the active tab button as {left,right} offsets within the container
 * and tracks travel direction. The container uses `ring` (not `border`) so the
 * button's offsetLeft shares the indicator's origin — keeping it pixel-perfect.
 */
function useTabIndicator(activeIndex: number, deps: React.DependencyList) {
    const containerRef = React.useRef<HTMLDivElement>(null);
    const btnRefs = React.useRef<(HTMLButtonElement | null)[]>([]);
    const prevIndex = React.useRef(activeIndex);
    const [indicator, setIndicator] = React.useState<Indicator>({ left: 0, right: 0, dir: 0, ready: false });

    React.useLayoutEffect(() => {
        const measure = (withDirection: boolean) => {
            const container = containerRef.current;
            const btn = btnRefs.current[activeIndex];
            if (!container || !btn) return;
            const dir = withDirection
                ? Math.sign(activeIndex - prevIndex.current)
                : 0;
            setIndicator({
                left: btn.offsetLeft,
                right: container.clientWidth - btn.offsetLeft - btn.offsetWidth,
                dir,
                ready: true,
            });
        };

        measure(true);
        prevIndex.current = activeIndex;

        // Re-measure on resize without re-triggering the stretch (dir = 0 → plain settle).
        const container = containerRef.current;
        if (!container || typeof ResizeObserver === 'undefined') return;
        const ro = new ResizeObserver(() => measure(false));
        ro.observe(container);
        return () => ro.disconnect();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [activeIndex, ...deps]);

    return { containerRef, btnRefs, indicator };
}

function Badge({ active, children, variant }: { active: boolean; children: React.ReactNode; variant: 'pill' | 'underline' }) {
    return (
        <span
            className={cn(
                'ml-0.5 inline-flex min-w-4.5 items-center justify-center rounded-full px-1.5 text-[0.6875rem] font-semibold leading-tight transition-colors duration-200',
                active
                    ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/40 dark:text-primary-300'
                    : 'bg-zinc-200/70 text-dark-500 dark:bg-dark-600 dark:text-dark-400',
                variant === 'pill' && 'py-px',
            )}
        >
            {children}
        </span>
    );
}

function Tabs({ items, value, onChange, className, variant = 'pill' }: TabsProps) {
    const activeIndex = Math.max(0, items.findIndex((i) => i.value === value));
    const { containerRef, btnRefs, indicator } = useTabIndicator(activeIndex, [value, items.length]);

    /* ── Underline variant ── */
    if (variant === 'underline') {
        return (
            <div
                ref={containerRef}
                className={cn(
                    'relative flex items-center border-b border-secondary-200 dark:border-dark-600',
                    className,
                )}
            >
                {/* sliding underline (liquid stretch) */}
                <span
                    aria-hidden
                    className={cn(
                        '-bottom-px absolute h-0.5 rounded-full bg-primary-600 dark:bg-primary-400',
                        indicator.ready ? 'opacity-100' : 'opacity-0',
                    )}
                    style={{
                        left: indicator.left,
                        right: indicator.right,
                        transition: indicator.ready ? stretchTransition(indicator.dir) : 'none',
                    }}
                />
                {items.map((item, i) => {
                    const active = value === item.value;
                    return (
                        <button
                            key={item.value}
                            ref={(el) => { btnRefs.current[i] = el; }}
                            type="button"
                            onClick={() => onChange(item.value)}
                            className={cn(
                                'relative flex items-center gap-2 px-4 py-2.5 text-sm font-medium transition-colors duration-150',
                                active
                                    ? 'text-primary-700 dark:text-primary-400'
                                    : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200',
                            )}
                        >
                            {item.icon && <span className="h-4 w-4 shrink-0">{item.icon}</span>}
                            <span>{item.label}</span>
                            {item.badge !== undefined && <Badge active={active} variant="underline">{item.badge}</Badge>}
                        </button>
                    );
                })}
            </div>
        );
    }

    /* ── Pill variant (segmented) ── */
    return (
        <div
            ref={containerRef}
            className={cn(
                'relative inline-flex items-center gap-1 rounded-xl bg-zinc-100 p-1 ring-1 ring-inset ring-zinc-200 dark:bg-dark-700 dark:ring-dark-600',
                className,
            )}
        >
            {/* sliding thumb (liquid stretch) */}
            <span
                aria-hidden
                className={cn(
                    'absolute top-1 bottom-1 rounded-lg bg-white shadow-sm ring-1 ring-zinc-200/80 dark:bg-dark-900 dark:ring-dark-500',
                    indicator.ready ? 'opacity-100' : 'opacity-0',
                )}
                style={{
                    left: indicator.left,
                    right: indicator.right,
                    transition: indicator.ready ? stretchTransition(indicator.dir) : 'none',
                }}
            />
            {items.map((item, i) => {
                const active = value === item.value;
                return (
                    <button
                        key={item.value}
                        ref={(el) => { btnRefs.current[i] = el; }}
                        type="button"
                        onClick={() => onChange(item.value)}
                        className={cn(
                            'relative z-10 flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition-colors duration-200',
                            active
                                ? 'text-dark-900 dark:text-dark-50'
                                : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200',
                        )}
                    >
                        {item.icon && <span className="h-4 w-4 shrink-0">{item.icon}</span>}
                        <span>{item.label}</span>
                        {item.badge !== undefined && <Badge active={active} variant="pill">{item.badge}</Badge>}
                    </button>
                );
            })}
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
