import { useEffect, useRef } from 'react';
import { BANK_CONFIG, detectBankKey, getBankInitials } from './BankPattern';
import { formatCurrency } from '@/lib/utils';
import { TrendingUp, TrendingDown, Plus } from 'lucide-react';
import type { BankAccount } from '../index';

interface WalletStackProps {
    accounts: BankAccount[];
    selectedIdx: number;
    onSelect: (idx: number) => void;
    onAddAccount?: () => void;
}

// Accent color per bank for the left indicator strip
const BANK_ACCENT: Record<string, string> = {
    BCA: '#2563eb',
    MANDIRI: '#eab308',
    BNI: '#f97316',
    BRI: '#0ea5e9',
    BSI: '#10b981',
    DEFAULT: '#71717a',
};

export default function WalletStack({ accounts, selectedIdx, onSelect, onAddAccount }: WalletStackProps) {
    const listRef = useRef<HTMLDivElement>(null);
    const count = accounts.length;

    // Keyboard navigation ↑ ↓
    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                onSelect(Math.max(0, selectedIdx - 1));
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                onSelect(Math.min(count - 1, selectedIdx + 1));
            }
        };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [selectedIdx, count, onSelect]);

    // Auto-scroll selected item into view
    useEffect(() => {
        const list = listRef.current;
        if (!list) return;
        const item = list.children[selectedIdx] as HTMLElement | undefined;
        item?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }, [selectedIdx]);

    if (count === 0) return null;

    return (
        <div
            ref={listRef}
            className="flex flex-col gap-1.5 w-full max-w-lg mx-auto max-h-72 overflow-y-auto pr-1"
            style={{ scrollbarWidth: 'thin', scrollbarColor: 'rgba(255,255,255,0.12) transparent' }}
            role="listbox"
            aria-label="Daftar rekening bank"
        >
            {accounts.map((account, idx) => {
                const bankKey = detectBankKey(account.bank_name);
                const config = BANK_CONFIG[bankKey];
                const initials = getBankInitials(account.bank_name);
                const last4 = account.last4_account_number ?? account.account_number.slice(-4);
                const accent = BANK_ACCENT[bankKey] ?? BANK_ACCENT.DEFAULT;
                const trendUp = (account.trend_percentage ?? 0) >= 0;
                const isSelected = idx === selectedIdx;

                return (
                    <button
                        key={account.id}
                        role="option"
                        aria-selected={isSelected}
                        onClick={() => onSelect(idx)}
                        className="group relative w-full text-left outline-none focus-visible:ring-1 focus-visible:ring-white/30 rounded-xl"
                        style={{
                            // Staggered entrance
                            animation: `walletRowIn 280ms cubic-bezier(0.22,1,0.36,1) both`,
                            animationDelay: `${idx * 40}ms`,
                        }}
                    >
                        {/* Card body */}
                        <div
                            className={[
                                'relative flex items-center gap-3 pl-5 pr-3.5 py-3 rounded-xl',
                                'transition-all duration-200 ease-out overflow-hidden',
                                isSelected
                                    ? 'bg-white/11 shadow-[inset_0_1px_0_rgba(255,255,255,0.10),0_4px_16px_rgba(0,0,0,0.35)]'
                                    : 'bg-white/4 hover:bg-white/7',
                            ].join(' ')}
                        >
                            {/* Left accent strip */}
                            <div
                                className="absolute left-0 top-3 bottom-3 rounded-r-full transition-all duration-300"
                                style={{
                                    width: isSelected ? 4 : 2,
                                    background: isSelected ? accent : 'rgba(255,255,255,0.08)',
                                    boxShadow: isSelected ? `0 0 10px 2px ${accent}55` : 'none',
                                }}
                            />

                            {/* Bank badge */}
                            <div
                                className={[
                                    'relative shrink-0 w-9 h-9 rounded-lg flex items-center justify-center',
                                    `bg-linear-to-br ${config.gradient}`,
                                    'shadow-[0_2px_8px_rgba(0,0,0,0.4)]',
                                    'transition-transform duration-200',
                                    'group-hover:scale-105',
                                    isSelected ? 'scale-105' : '',
                                ].join(' ')}
                            >
                                <span className="text-white font-black text-[9px] tracking-wider leading-none">
                                    {initials}
                                </span>
                            </div>

                            {/* Account info */}
                            <div className="flex-1 min-w-0">
                                <div className="flex items-baseline gap-1.5">
                                    <p
                                        className={[
                                            'text-sm font-semibold truncate leading-tight transition-colors duration-200',
                                            isSelected ? 'text-white' : 'text-white/75 group-hover:text-white/90',
                                        ].join(' ')}
                                    >
                                        {account.account_name}
                                    </p>
                                </div>
                                <p className="text-[10px] font-mono text-white/35 mt-0.5 truncate">
                                    {account.bank_name} · ••••{last4}
                                </p>
                            </div>

                            {/* Balance + trend */}
                            <div className="shrink-0 text-right">
                                <p
                                    className={[
                                        'text-sm font-black tabular-nums leading-tight transition-colors duration-200',
                                        isSelected ? 'text-white' : 'text-white/70 group-hover:text-white/85',
                                    ].join(' ')}
                                >
                                    {formatCurrency(account.balance)}
                                </p>
                                <div className={`flex items-center justify-end gap-0.5 mt-0.5 ${trendUp ? 'text-emerald-400' : 'text-rose-400'}`}>
                                    {trendUp
                                        ? <TrendingUp className="w-2.5 h-2.5" />
                                        : <TrendingDown className="w-2.5 h-2.5" />
                                    }
                                    <span className="text-[9px] font-bold tabular-nums">
                                        {trendUp ? '+' : ''}{(account.trend_percentage ?? 0).toFixed(1)}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>
                );
            })}

            {/* Add account row */}
            {onAddAccount && (
                <button
                    onClick={onAddAccount}
                    className={[
                        'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl w-full text-left',
                        'border border-dashed border-white/15 hover:border-white/30',
                        'bg-transparent hover:bg-white/5',
                        'transition-all duration-200',
                    ].join(' ')}
                    style={{
                        animation: `walletRowIn 280ms cubic-bezier(0.22,1,0.36,1) both`,
                        animationDelay: `${count * 40}ms`,
                    }}
                >
                    <div className="w-9 h-9 rounded-lg border border-dashed border-white/20 flex items-center justify-center group-hover:border-white/40 transition-colors duration-200">
                        <Plus className="w-3.5 h-3.5 text-white/40 group-hover:text-white/70 transition-colors duration-200" />
                    </div>
                    <span className="text-[11px] text-white/35 group-hover:text-white/60 font-medium transition-colors duration-200">
                        Tambah rekening baru
                    </span>
                </button>
            )}

            <style>{`
                @keyframes walletRowIn {
                    from { opacity: 0; transform: translateX(-8px); }
                    to   { opacity: 1; transform: translateX(0); }
                }
            `}</style>
        </div>
    );
}
