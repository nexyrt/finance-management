import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/lib/utils';
import { Building2, Edit2, Trash2, TrendingDown, TrendingUp } from 'lucide-react';
import type { BankAccount } from '../index';

interface InsightPanelProps {
    account: BankAccount;
    onEdit: () => void;
    onDelete: () => void;
}

export default function InsightPanel({ account, onEdit, onDelete }: InsightPanelProps) {
    const trendUp = (account.trend ?? 0) >= 0;

    return (
        <div className="flex flex-col gap-4 h-full">
            {/* Smart insight */}
            <div className="bg-gray-50 dark:bg-dark-800 rounded-xl p-3 border border-secondary-200 dark:border-dark-600">
                <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-widest mb-1">Insight</p>
                <p className="text-xs text-dark-900 dark:text-dark-300 leading-relaxed">
                    {account.smart_insight ?? '—'}
                </p>
            </div>

            {/* Net 30d */}
            <div>
                <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-widest mb-1.5">Net 30 Hari</p>
                <div className="flex items-center gap-1.5">
                    {trendUp ? (
                        <TrendingUp className="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                    ) : (
                        <TrendingDown className="w-3.5 h-3.5 text-rose-500 shrink-0" />
                    )}
                    <span
                        className={`text-sm font-bold tabular-nums ${
                            trendUp ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'
                        }`}
                    >
                        {trendUp ? '+' : ''}{formatCurrency(account.trend ?? 0)}
                    </span>
                </div>
                <p className="text-[11px] text-dark-500 dark:text-dark-500 mt-0.5 tabular-nums">
                    {trendUp ? '↑' : '↓'} {Math.abs(account.trend_percentage ?? 0).toFixed(1)}% dari saldo awal
                </p>
            </div>

            {/* Bank metadata */}
            <div className="space-y-1.5">
                <p className="text-[10px] text-dark-400 dark:text-dark-500 uppercase tracking-widest">Rekening</p>
                <div className="flex items-start gap-1.5">
                    <Building2 className="w-3 h-3 text-dark-400 dark:text-dark-500 mt-0.5 shrink-0" />
                    <div>
                        <p className="text-xs text-dark-900 dark:text-dark-300 font-medium">{account.bank_name}</p>
                        {account.branch && (
                            <p className="text-[11px] text-dark-500 dark:text-dark-500">{account.branch}</p>
                        )}
                        <p className="text-[11px] font-mono text-dark-400 dark:text-dark-500 mt-0.5">
                            {account.account_number}
                        </p>
                    </div>
                </div>
            </div>

            {/* Actions */}
            <div className="flex flex-col gap-2 mt-auto">
                <Button
                    variant="outline"
                    size="sm"
                    onClick={onEdit}
                    className="w-full justify-start gap-2 text-xs"
                >
                    <Edit2 className="w-3.5 h-3.5" />
                    Edit Rekening
                </Button>
                <Button
                    variant="ghost"
                    size="sm"
                    onClick={onDelete}
                    className="w-full justify-start gap-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-700"
                >
                    <Trash2 className="w-3.5 h-3.5" />
                    Hapus Rekening
                </Button>
            </div>
        </div>
    );
}
