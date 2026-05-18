import { useEffect, useState } from 'react';
import WalletCard, { type CardPosition } from './WalletCard';
import type { BankAccount } from '../index';

interface WalletStackProps {
    accounts: BankAccount[];
    selectedIdx: number;
    onSelect: (idx: number) => void;
}

export default function WalletStack({ accounts, selectedIdx, onSelect }: WalletStackProps) {
    const [isFanout, setIsFanout] = useState(false);
    const count = accounts.length;

    useEffect(() => {
        const handler = (e: KeyboardEvent) => {
            if (e.key === 'ArrowLeft') onSelect(Math.max(0, selectedIdx - 1));
            if (e.key === 'ArrowRight') onSelect(Math.min(count - 1, selectedIdx + 1));
        };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, [selectedIdx, count, onSelect]);

    if (count === 0) return null;

    function getPosition(idx: number): CardPosition {
        const delta = idx - selectedIdx;
        if (delta === 0) return 'front';
        if (delta === 1 || delta === -1) return 'mid';
        if (delta === 2 || delta === -2) return 'back';
        return 'hidden';
    }

    return (
        <div className="flex flex-col items-center gap-6">
            {/* Stack container */}
            <div
                className="relative flex items-center justify-center"
                style={{
                    width: isFanout ? Math.min(count * 80 + 280, 700) : 340,
                    height: 220,
                    transition: 'width 300ms cubic-bezier(0.4,0,0.2,1)',
                }}
                onMouseEnter={() => count > 1 && setIsFanout(true)}
                onMouseLeave={() => setIsFanout(false)}
            >
                {accounts.map((account, idx) => (
                    <div
                        key={account.id}
                        className={isFanout ? 'absolute' : 'absolute'}
                        style={
                            isFanout
                                ? { left: '50%', top: 10, marginLeft: -170 }
                                : { left: '50%', top: 0, marginLeft: -170 }
                        }
                    >
                        <WalletCard
                            account={account}
                            position={getPosition(idx)}
                            isSelected={idx === selectedIdx}
                            stackIndex={idx}
                            totalCards={count}
                            isFanout={isFanout}
                            onClick={() => {
                                setIsFanout(false);
                                onSelect(idx);
                            }}
                        />
                    </div>
                ))}
            </div>

            {/* Position dots */}
            {count > 1 && (
                <div className="flex items-center gap-2">
                    {accounts.map((_, idx) => (
                        <button
                            key={idx}
                            onClick={() => onSelect(idx)}
                            aria-label={`Pilih rekening ${idx + 1}`}
                            className={[
                                'rounded-full transition-all duration-200',
                                idx === selectedIdx
                                    ? 'w-6 h-2 bg-white'
                                    : 'w-2 h-2 bg-white/30 hover:bg-white/50',
                            ].join(' ')}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}
