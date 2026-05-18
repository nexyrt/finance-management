import { formatCurrency } from '@/lib/utils';
import BankPattern, { BANK_CONFIG, detectBankKey, getBankInitials } from './BankPattern';
import type { BankAccount } from '../index';

export type CardPosition = 'front' | 'mid' | 'back' | 'hidden';

interface WalletCardProps {
    account: BankAccount;
    position: CardPosition;
    isSelected: boolean;
    stackIndex: number;
    totalCards: number;
    isFanout: boolean;
    onClick: () => void;
}

const positionStyles: Record<CardPosition, string> = {
    front: 'translate-y-0 scale-100 z-30',
    mid: '-translate-y-3 scale-[0.96] z-20',
    back: '-translate-y-6 scale-[0.92] z-10',
    hidden: '-translate-y-10 scale-[0.88] z-0 opacity-0 pointer-events-none',
};

export default function WalletCard({
    account,
    position,
    isSelected,
    stackIndex,
    totalCards,
    isFanout,
    onClick,
}: WalletCardProps) {
    const bankKey = detectBankKey(account.bank_name);
    const config = BANK_CONFIG[bankKey];
    const initials = getBankInitials(account.bank_name);
    const last4 = account.last4_account_number ?? account.account_number.slice(-4);

    const fanAngle = isFanout ? (stackIndex - (totalCards - 1) / 2) * 8 : 0;
    const fanX = isFanout ? (stackIndex - (totalCards - 1) / 2) * 72 : 0;
    const fanY = isFanout ? Math.abs(stackIndex - (totalCards - 1) / 2) * 12 : 0;

    const baseTransition = 'transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)]';

    const blurAmount = position === 'back' ? 'blur-[0.5px]' : '';
    const shadowStyle =
        position === 'front'
            ? 'shadow-[0_24px_48px_rgba(0,0,0,0.5),0_0_0_1px_rgba(255,255,255,0.08)]'
            : position === 'mid'
              ? 'shadow-[0_12px_24px_rgba(0,0,0,0.4)]'
              : 'shadow-[0_6px_16px_rgba(0,0,0,0.3)]';

    const style = isFanout
        ? {
              transform: `translateX(${fanX}px) translateY(${fanY}px) rotate(${fanAngle}deg) scale(${position === 'hidden' ? 0.88 : 1})`,
              transition: 'transform 400ms cubic-bezier(0.32,0.72,0,1), opacity 400ms ease, filter 400ms ease',
              opacity: position === 'hidden' ? 0 : 1,
          }
        : undefined;

    const trendUp = (account.trend_percentage ?? 0) >= 0;

    return (
        <div
            className={[
                'relative w-85 h-50 rounded-2xl cursor-pointer select-none overflow-hidden',
                baseTransition,
                isFanout ? '' : positionStyles[position],
                blurAmount,
                shadowStyle,
                isSelected && !isFanout ? 'ring-2 ring-white/30' : '',
            ]
                .filter(Boolean)
                .join(' ')}
            style={style}
            onClick={onClick}
            role="button"
            tabIndex={0}
            onKeyDown={(e) => e.key === 'Enter' && onClick()}
            aria-label={`${account.account_name} — ${formatCurrency(account.balance)}`}
        >
            {/* Gradient background */}
            <div className={`absolute inset-0 bg-linear-to-br ${config.gradient}`} />

            {/* SVG pattern overlay */}
            <BankPattern bankKey={bankKey} opacity={0.1} />

            {/* Glossy top highlight */}
            <div className="absolute inset-0 bg-linear-to-b from-white/15 via-transparent to-transparent rounded-2xl" />

            {/* Content */}
            <div className="relative z-10 h-full flex flex-col justify-between p-5">
                {/* Top row */}
                <div className="flex items-start justify-between">
                    <div>
                        <p className="text-white/60 text-[10px] font-medium uppercase tracking-widest">
                            {account.bank_name}
                        </p>
                        <p className="text-white font-bold text-base leading-tight mt-0.5">
                            {account.account_name}
                        </p>
                    </div>
                    {/* Bank badge */}
                    <div className="bg-white/15 backdrop-blur-sm border border-white/20 rounded-xl px-2.5 py-1.5">
                        <span className="text-white font-black text-xs tracking-wider">{initials}</span>
                    </div>
                </div>

                {/* Middle — account number */}
                <div>
                    <p className="text-white/50 font-mono text-sm tracking-[0.2em]">
                        •••• •••• ••••{' '}
                        <span className="text-white/80">{last4}</span>
                    </p>
                </div>

                {/* Bottom row */}
                <div className="flex items-end justify-between">
                    <div>
                        <p className="text-white/50 text-[10px] uppercase tracking-widest mb-0.5">Saldo</p>
                        <p className="text-white font-black text-xl tabular-nums leading-none">
                            {formatCurrency(account.balance)}
                        </p>
                    </div>
                    <div className="text-right">
                        <p className="text-white/50 text-[10px] uppercase tracking-widest mb-0.5">30 Hari</p>
                        <p
                            className={`text-sm font-bold tabular-nums ${
                                trendUp ? 'text-emerald-300' : 'text-rose-300'
                            }`}
                        >
                            {trendUp ? '↑' : '↓'} {Math.abs(account.trend_percentage ?? 0).toFixed(1)}%
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
