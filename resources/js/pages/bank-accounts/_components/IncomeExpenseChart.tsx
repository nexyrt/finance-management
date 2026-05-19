import { formatCurrency } from '@/lib/utils';

interface MonthData {
    label: string;
    income: number;
    expense: number;
}

interface IncomeExpenseChartProps {
    months: MonthData[];
    height?: number;
}

export default function IncomeExpenseChart({ months, height = 128 }: IncomeExpenseChartProps) {
    if (!months || months.length === 0) {
        return (
            <div className="flex items-center justify-center" style={{ height }}>
                <p className="text-xs text-dark-500 dark:text-dark-400 italic">Belum ada data.</p>
            </div>
        );
    }

    const maxVal = Math.max(...months.flatMap((m) => [m.income, m.expense]), 1);
    const LABEL_H = 20;       // px reserved for month labels at bottom
    const LEGEND_H = 20;      // px for legend row
    const chartH = height - LABEL_H - LEGEND_H;
    const totalMonths = months.length;

    // Layout in percentage of total width
    const groupPct = 100 / totalMonths;
    const barPadPct = groupPct * 0.18;       // padding inside each group
    const innerPct = groupPct - barPadPct * 2;
    const gapPct = innerPct * 0.15;
    const barPct = (innerPct - gapPct) / 2;

    return (
        <div className="w-full select-none" aria-hidden>
            {/* Chart area */}
            <div className="relative w-full" style={{ height: chartH }}>
                {/* Subtle gridlines */}
                {[0.25, 0.5, 0.75, 1].map((fraction) => (
                    <div
                        key={fraction}
                        className="absolute w-full border-t border-dark-700/40 dark:border-dark-600/30"
                        style={{ bottom: `${fraction * 100}%` }}
                    />
                ))}

                {/* Bars */}
                <div className="absolute inset-0 flex items-end">
                    {months.map((m, i) => {
                        const incomeH = m.income > 0 ? Math.max((m.income / maxVal) * 100, 2) : 0;
                        const expenseH = m.expense > 0 ? Math.max((m.expense / maxVal) * 100, 2) : 0;

                        return (
                            <div
                                key={i}
                                className="relative flex items-end justify-center"
                                style={{ width: `${groupPct}%`, height: '100%', paddingLeft: `${barPadPct}%`, paddingRight: `${barPadPct}%` }}
                            >
                                {/* Income bar */}
                                <div
                                    title={`Masuk: ${formatCurrency(m.income)}`}
                                    className="rounded-t-sm transition-all duration-500"
                                    style={{
                                        width: `${barPct}%`,
                                        height: `${incomeH}%`,
                                        minHeight: m.income > 0 ? 2 : 0,
                                        marginRight: `${gapPct / 2}%`,
                                        backgroundColor: m.income > 0 ? '#34d399' : 'rgba(52,211,153,0.12)',
                                    }}
                                />
                                {/* Expense bar */}
                                <div
                                    title={`Keluar: ${formatCurrency(m.expense)}`}
                                    className="rounded-t-sm transition-all duration-500"
                                    style={{
                                        width: `${barPct}%`,
                                        height: `${expenseH}%`,
                                        minHeight: m.expense > 0 ? 2 : 0,
                                        marginLeft: `${gapPct / 2}%`,
                                        backgroundColor: m.expense > 0 ? '#f87171' : 'rgba(248,113,113,0.12)',
                                    }}
                                />
                            </div>
                        );
                    })}
                </div>
            </div>

            {/* Month labels */}
            <div className="flex" style={{ height: LABEL_H }}>
                {months.map((m, i) => (
                    <div
                        key={i}
                        className="flex items-center justify-center text-[9px] text-dark-400 dark:text-dark-500 truncate"
                        style={{ width: `${groupPct}%` }}
                    >
                        {m.label}
                    </div>
                ))}
            </div>

            {/* Legend */}
            <div className="flex items-center gap-4" style={{ height: LEGEND_H }}>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full shrink-0" style={{ backgroundColor: '#34d399' }} />
                    <span className="text-[10px] text-dark-500 dark:text-dark-400">Pemasukan</span>
                </div>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full shrink-0" style={{ backgroundColor: '#f87171' }} />
                    <span className="text-[10px] text-dark-500 dark:text-dark-400">Pengeluaran</span>
                </div>
            </div>
        </div>
    );
}
