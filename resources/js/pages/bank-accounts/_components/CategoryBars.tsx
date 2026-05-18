import { formatCurrency } from '@/lib/utils';

interface CategoryEntry {
    label: string;
    total: number;
}

interface CategoryBarsProps {
    categories: CategoryEntry[];
}

const BAR_COLORS = [
    'bg-blue-500',
    'bg-purple-500',
    'bg-amber-500',
    'bg-rose-500',
    'bg-teal-500',
    'bg-zinc-500',
];

export default function CategoryBars({ categories }: CategoryBarsProps) {
    if (!categories || categories.length === 0) {
        return (
            <p className="text-xs text-dark-500 dark:text-dark-400 italic">Belum ada data kategori.</p>
        );
    }

    const maxTotal = Math.max(...categories.map((c) => c.total), 1);
    const grandTotal = categories.reduce((s, c) => s + c.total, 0) || 1;

    return (
        <div className="space-y-2.5">
            {categories.slice(0, 5).map((cat, idx) => {
                const pct = Math.round((cat.total / grandTotal) * 100);
                const barWidth = Math.max((cat.total / maxTotal) * 100, 4);
                return (
                    <div key={cat.label} className="group">
                        <div className="flex items-center justify-between mb-1">
                            <span className="text-[11px] font-medium text-dark-900 dark:text-dark-300 truncate max-w-[60%]">
                                {cat.label}
                            </span>
                            <span className="text-[11px] tabular-nums text-dark-500 dark:text-dark-400 shrink-0 ml-2">
                                {pct}% · {formatCurrency(cat.total)}
                            </span>
                        </div>
                        <div className="h-1.5 bg-secondary-200 dark:bg-dark-700 rounded-full overflow-hidden">
                            <div
                                className={`h-full rounded-full transition-all duration-700 ${BAR_COLORS[idx % BAR_COLORS.length]}`}
                                style={{ width: `${barWidth}%` }}
                            />
                        </div>
                    </div>
                );
            })}
        </div>
    );
}
