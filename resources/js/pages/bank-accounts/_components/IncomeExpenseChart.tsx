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

export default function IncomeExpenseChart({ months, height = 120 }: IncomeExpenseChartProps) {
    if (!months || months.length === 0) {
        return (
            <div className="flex items-center justify-center h-[120px]">
                <p className="text-xs text-dark-500 dark:text-dark-400 italic">Belum ada data.</p>
            </div>
        );
    }

    const maxVal = Math.max(...months.flatMap((m) => [m.income, m.expense]), 1);
    const barAreaHeight = height - 24; // reserve 24px for labels
    const totalBars = months.length * 2;
    const gap = 2;
    const groupGap = 6;
    const totalGroups = months.length;

    // Calculate bar width dynamically
    const svgWidth = 100; // use viewBox, scale with CSS
    const groupWidth = svgWidth / totalGroups;
    const barWidth = (groupWidth - groupGap) / 2 - gap / 2;

    return (
        <div className="w-full overflow-hidden" aria-hidden>
            <svg
                viewBox={`0 0 ${svgWidth} ${height}`}
                preserveAspectRatio="none"
                className="w-full"
                style={{ height }}
            >
                {months.map((m, i) => {
                    const gx = i * groupWidth;
                    const incomeH = Math.max((m.income / maxVal) * barAreaHeight, m.income > 0 ? 2 : 0);
                    const expenseH = Math.max((m.expense / maxVal) * barAreaHeight, m.expense > 0 ? 2 : 0);
                    const incomeY = barAreaHeight - incomeH;
                    const expenseY = barAreaHeight - expenseH;
                    const bx1 = gx + groupGap / 2;
                    const bx2 = bx1 + barWidth + gap;

                    return (
                        <g key={m.label}>
                            {/* Income bar */}
                            <rect
                                x={bx1}
                                y={incomeY}
                                width={barWidth}
                                height={incomeH}
                                rx={1}
                                fill="#34d399"
                                opacity={m.income > 0 ? 0.85 : 0.15}
                            />
                            {/* Expense bar */}
                            <rect
                                x={bx2}
                                y={expenseY}
                                width={barWidth}
                                height={expenseH}
                                rx={1}
                                fill="#f87171"
                                opacity={m.expense > 0 ? 0.85 : 0.15}
                            />
                            {/* Month label */}
                            <text
                                x={gx + groupWidth / 2}
                                y={barAreaHeight + 14}
                                textAnchor="middle"
                                fontSize={4.5}
                                fill="currentColor"
                                className="text-dark-400"
                                opacity={0.6}
                            >
                                {m.label}
                            </text>
                        </g>
                    );
                })}
            </svg>

            {/* Legend */}
            <div className="flex items-center gap-4 mt-1">
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-emerald-400 shrink-0" />
                    <span className="text-[10px] text-dark-500 dark:text-dark-400">Pemasukan</span>
                </div>
                <div className="flex items-center gap-1.5">
                    <span className="w-2 h-2 rounded-full bg-rose-400 shrink-0" />
                    <span className="text-[10px] text-dark-500 dark:text-dark-400">Pengeluaran</span>
                </div>
            </div>
        </div>
    );
}
