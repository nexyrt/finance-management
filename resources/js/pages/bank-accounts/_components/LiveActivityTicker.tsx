import { useEffect, useState } from 'react';
import { formatCurrency } from '@/lib/utils';

interface ActivityEntry {
    id: string;
    date: string;
    type: 'in' | 'out';
    amount: number;
    label: string;
}

interface LiveActivityTickerProps {
    accountId: number;
}

export default function LiveActivityTicker({ accountId }: LiveActivityTickerProps) {
    const [activity, setActivity] = useState<ActivityEntry[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch(`/bank-accounts/${accountId}/activity`, {
            headers: { Accept: 'application/json' },
        })
            .then((r) => {
                if (!r.ok) throw new Error('activity fetch failed');
                return r.json();
            })
            .then((d) => {
                if (d && Array.isArray(d.activity)) {
                    setActivity(d.activity);
                }
            })
            .catch(() => setActivity([]))
            .finally(() => setLoading(false));
    }, [accountId]);

    if (loading) {
        return (
            <div className="space-y-2.5">
                {Array.from({ length: 5 }).map((_, i) => (
                    <div key={i} className="flex items-center gap-3 animate-pulse">
                        <div className="w-6 h-6 rounded-full bg-secondary-200 dark:bg-dark-600 shrink-0" />
                        <div className="flex-1 h-3 rounded bg-secondary-200 dark:bg-dark-700" />
                        <div className="w-20 h-3 rounded bg-secondary-200 dark:bg-dark-700" />
                    </div>
                ))}
            </div>
        );
    }

    if (activity.length === 0) {
        return (
            <p className="text-xs text-dark-500 dark:text-dark-400 italic">Belum ada aktivitas.</p>
        );
    }

    function formatDate(dateStr: string): string {
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        } catch {
            return dateStr;
        }
    }

    return (
        <div className="space-y-1">
            {activity.map((entry) => {
                const isIn = entry.type === 'in';
                return (
                    <div
                        key={entry.id}
                        className="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-600 transition-colors group"
                    >
                        {/* Arrow indicator */}
                        <div
                            className={`w-5 h-5 rounded-full flex items-center justify-center shrink-0 ${
                                isIn
                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                    : 'bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400'
                            }`}
                        >
                            <span className="text-[10px] font-bold leading-none">{isIn ? '↑' : '↓'}</span>
                        </div>

                        {/* Date */}
                        <span className="text-[10px] text-dark-400 dark:text-dark-500 shrink-0 w-10 tabular-nums">
                            {formatDate(entry.date)}
                        </span>

                        {/* Label */}
                        <span className="text-[11px] text-dark-600 dark:text-dark-400 flex-1 truncate">
                            {entry.label}
                        </span>

                        {/* Amount */}
                        <span
                            className={`text-[11px] font-semibold tabular-nums shrink-0 ${
                                isIn ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                            }`}
                        >
                            {isIn ? '+' : '-'}{formatCurrency(entry.amount)}
                        </span>
                    </div>
                );
            })}
        </div>
    );
}
