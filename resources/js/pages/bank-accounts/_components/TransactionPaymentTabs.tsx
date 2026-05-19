import { useState, useEffect } from 'react';
import { ArrowLeftRight, CreditCard } from 'lucide-react';
import TransactionsTable from './TransactionsTable';
import PaymentsTable from './PaymentsTable';

const STORAGE_KEY = 'ba_active_tab';

interface TransactionPaymentTabsProps {
    accountId: number;
    onCreateClick: (type: 'income' | 'expense' | 'transfer') => void;
    onCategorize: (ids: number[], isBulk: boolean) => void;
    onAttachmentView: (url: string, filename?: string) => void;
    refreshKey?: number;
}

export default function TransactionPaymentTabs({
    accountId,
    onCreateClick,
    onCategorize,
    onAttachmentView,
    refreshKey,
}: TransactionPaymentTabsProps) {
    const [activeTab, setActiveTab] = useState<'transactions' | 'payments'>(() => {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            return saved === 'payments' ? 'payments' : 'transactions';
        } catch {
            return 'transactions';
        }
    });

    function switchTab(tab: 'transactions' | 'payments') {
        setActiveTab(tab);
        try { localStorage.setItem(STORAGE_KEY, tab); } catch {}
    }

    return (
        <div className="flex flex-col gap-4">
            {/* Tab bar — pill/segment style per CLAUDE.md */}
            <div className="inline-flex items-center gap-1 p-1 bg-secondary-100 dark:bg-dark-800 rounded-xl border border-secondary-200 dark:border-dark-600 self-start">
                <button
                    onClick={() => switchTab('transactions')}
                    className={[
                        'flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
                        activeTab === 'transactions'
                            ? 'bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-50 shadow-sm border border-secondary-200 dark:border-dark-600'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-secondary-50 dark:hover:bg-dark-700',
                    ].join(' ')}
                >
                    <ArrowLeftRight className="w-3.5 h-3.5 shrink-0" />
                    <span>Transaksi</span>
                </button>
                <button
                    onClick={() => switchTab('payments')}
                    className={[
                        'flex items-center gap-2 px-4 py-1.5 rounded-lg text-sm font-medium transition-all duration-200',
                        activeTab === 'payments'
                            ? 'bg-white dark:bg-dark-700 text-dark-900 dark:text-dark-50 shadow-sm border border-secondary-200 dark:border-dark-600'
                            : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200 hover:bg-secondary-50 dark:hover:bg-dark-700',
                    ].join(' ')}
                >
                    <CreditCard className="w-3.5 h-3.5 shrink-0" />
                    <span>Pembayaran</span>
                </button>
            </div>

            {/* Tab content */}
            <div>
                {activeTab === 'transactions' ? (
                    <TransactionsTable
                        accountId={accountId}
                        onCreateClick={onCreateClick}
                        onCategorize={onCategorize}
                        onAttachmentView={onAttachmentView}
                        refreshKey={refreshKey}
                    />
                ) : (
                    <PaymentsTable
                        accountId={accountId}
                        onAttachmentView={onAttachmentView}
                        refreshKey={refreshKey}
                    />
                )}
            </div>
        </div>
    );
}
