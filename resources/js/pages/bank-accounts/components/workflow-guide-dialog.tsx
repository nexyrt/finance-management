import {
    ArrowDownLeft,
    ArrowRight,
    ArrowUpRight,
    BarChart3,
    Banknote,
    Building2,
    Calculator,
    Calendar,
    CheckCircle2,
    Lightbulb,
    Map,
    MousePointer2,
    PlusCircle,
    Tag,
    TrendingDown,
    TrendingUp,
} from 'lucide-react';
import * as React from 'react';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { cn } from '@/lib/utils';

interface Props {
    open: boolean;
    onOpenChange: (open: boolean) => void;
}

type TabKey = 'accounts' | 'transactions' | 'analytics';

const TAB_DEFS: { key: TabKey; label: string; icon: React.ComponentType<{ className?: string }> }[] = [
    { key: 'accounts', label: 'Rekening', icon: Building2 },
    { key: 'transactions', label: 'Transaksi', icon: ArrowRight },
    { key: 'analytics', label: 'Analitik', icon: BarChart3 },
];

export function WorkflowGuideDialog({ open, onOpenChange }: Props) {
    const [tab, setTab] = React.useState<TabKey>('accounts');

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent size="3xl">
                <DialogHeader>
                    <div className="flex items-center gap-4 py-2">
                        <div className="h-12 w-12 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center shrink-0">
                            <Map className="w-6 h-6 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div>
                            <DialogTitle className="text-xl font-bold text-dark-900 dark:text-dark-50">
                                Panduan Manajemen Rekening
                            </DialogTitle>
                            <p className="text-sm text-dark-600 dark:text-dark-400 mt-0.5">
                                Ikuti alur kerja untuk mengelola keuangan dengan baik.
                            </p>
                        </div>
                    </div>
                </DialogHeader>

                <div className="px-6 py-5 space-y-5">
                    {/* Tab strip */}
                    <div className="inline-flex w-full items-center gap-1 p-1 bg-zinc-100 dark:bg-dark-700 rounded-xl border border-zinc-200 dark:border-dark-600">
                        {TAB_DEFS.map(({ key, label, icon: Icon }) => (
                            <button
                                key={key}
                                onClick={() => setTab(key)}
                                className={cn(
                                    'flex flex-1 items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200',
                                    tab === key
                                        ? 'bg-white dark:bg-dark-800 text-dark-900 dark:text-dark-50 shadow-sm border border-zinc-200 dark:border-dark-600'
                                        : 'text-dark-500 dark:text-dark-400 hover:text-dark-800 dark:hover:text-dark-200',
                                )}
                            >
                                <Icon className="w-3.5 h-3.5 shrink-0" />
                                <span>{label}</span>
                            </button>
                        ))}
                    </div>

                    {/* Tab content */}
                    {tab === 'accounts' && <AccountsTab />}
                    {tab === 'transactions' && <TransactionsTab />}
                    {tab === 'analytics' && <AnalyticsTab />}
                </div>

                <DialogFooter>
                    <Button onClick={() => onOpenChange(false)} className="ml-auto">
                        <CheckCircle2 className="w-4 h-4" />
                        Mengerti
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

/* ─── Sub-sections ─────────────────────────────────────────── */

function AccountsTab() {
    return (
        <div className="space-y-4 animate-in fade-in duration-200">
            <div className="relative">
                <div className="absolute left-6 top-10 bottom-10 w-0.5 bg-linear-to-b from-blue-300 via-purple-300 to-emerald-300 dark:from-blue-700 dark:via-purple-700 dark:to-emerald-700 hidden sm:block" />
                <div className="space-y-4">
                    <Step
                        n={1}
                        color="blue"
                        icon={<PlusCircle className="w-5 h-5" />}
                        title="Tambahkan Rekening Bank"
                        desc="Mulai dengan menambahkan rekening bank yang akan dilacak transaksinya."
                        tips={['Gunakan nama yang mudah diingat', 'Masukkan saldo awal yang akurat']}
                    />
                    <Step
                        n={2}
                        color="purple"
                        icon={<MousePointer2 className="w-5 h-5" />}
                        title="Pilih Rekening untuk Dikelola"
                        desc="Klik rekening di sidebar untuk melihat detail dan kelola transaksinya."
                        tips={['Klik avatar/nama rekening', 'Detail muncul di panel kanan']}
                    />
                    <Step
                        n={3}
                        color="emerald"
                        icon={<BarChart3 className="w-5 h-5" />}
                        title="Analisa & Pantau Kinerja"
                        desc="Pantau arus kas via grafik & gunakan filter periode untuk insight."
                    />
                </div>
            </div>

            <NoteCard
                tone="amber"
                icon={<Calculator className="w-5 h-5" />}
                title="Cara Saldo Dihitung"
                desc="Saldo dihitung otomatis dari rumus berikut:"
            >
                <div className="bg-amber-100 dark:bg-amber-900/30 rounded-lg p-2.5 font-mono text-xs text-amber-800 dark:text-amber-200 mt-2">
                    Saldo Awal + Pembayaran + Pemasukan − Pengeluaran
                </div>
            </NoteCard>

            <TipsCard
                tips={[
                    { kind: 'good', text: 'Pisahkan rekening operasional dan tabungan' },
                    { kind: 'good', text: 'Update transaksi secara berkala (mingguan)' },
                    { kind: 'warn', text: 'Hapus rekening hanya jika tidak ada transaksi terkait' },
                ]}
            />
        </div>
    );
}

function TransactionsTab() {
    return (
        <div className="space-y-4 animate-in fade-in duration-200">
            <div>
                <h4 className="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-3">
                    Jenis Transaksi
                </h4>
                <div className="grid grid-cols-2 gap-3">
                    <div className="p-4 bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/40 rounded-xl">
                        <div className="flex items-center gap-2 mb-2">
                            <div className="h-8 w-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <ArrowDownLeft className="w-4 h-4 text-green-600 dark:text-green-400" />
                            </div>
                            <span className="text-sm font-semibold text-green-800 dark:text-green-200">
                                Pemasukan (Credit)
                            </span>
                        </div>
                        <p className="text-xs text-green-700 dark:text-green-300">
                            Uang masuk ke rekening — penjualan, transfer masuk, refund.
                        </p>
                    </div>
                    <div className="p-4 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/40 rounded-xl">
                        <div className="flex items-center gap-2 mb-2">
                            <div className="h-8 w-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <ArrowUpRight className="w-4 h-4 text-red-600 dark:text-red-400" />
                            </div>
                            <span className="text-sm font-semibold text-red-800 dark:text-red-200">
                                Pengeluaran (Debit)
                            </span>
                        </div>
                        <p className="text-xs text-red-700 dark:text-red-300">
                            Uang keluar dari rekening — pembelian, biaya admin, gaji.
                        </p>
                    </div>
                </div>
            </div>

            <NoteCard
                tone="blue"
                icon={<PlusCircle className="w-5 h-5" />}
                title="Menambah Transaksi"
                desc="Klik tombol 'Pemasukan' atau 'Pengeluaran' di pojok kanan atas detail rekening."
            >
                <div className="grid grid-cols-2 gap-2 mt-2">
                    <Bullet color="blue" text="Isi nominal & deskripsi" />
                    <Bullet color="blue" text="Lampirkan bukti (opsional)" />
                </div>
            </NoteCard>

            <NoteCard
                tone="purple"
                icon={<ArrowRight className="w-5 h-5" />}
                title="Transfer Antar Rekening"
                desc="Pindahkan dana antar rekening Anda dengan otomatis mencatat debit/credit pasangan."
            >
                <div className="flex items-center gap-2 text-xs mt-2">
                    <span className="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-md font-medium">
                        Sumber
                    </span>
                    <ArrowRight className="w-3.5 h-3.5 text-purple-400" />
                    <span className="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-md font-medium">
                        Tujuan
                    </span>
                    <span className="text-purple-500 dark:text-purple-400 ml-1">otomatis tercatat</span>
                </div>
            </NoteCard>

            <NoteCard
                tone="emerald"
                icon={<Banknote className="w-5 h-5" />}
                title="Pembayaran dari Invoice"
                desc="Pembayaran invoice yang dialokasikan ke rekening ini muncul di tab 'Pembayaran'."
            />

            <div className="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
                <div className="flex items-start gap-3">
                    <Tag className="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0 mt-0.5" />
                    <div>
                        <h4 className="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-1">
                            Kategorisasi Penting
                        </h4>
                        <p className="text-xs text-dark-500 dark:text-dark-400">
                            Selalu pilih kategori — donut chart breakdown bergantung pada kategori transaksi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}

function AnalyticsTab() {
    return (
        <div className="space-y-4 animate-in fade-in duration-200">
            <NoteCard
                tone="blue"
                icon={<Calendar className="w-5 h-5" />}
                title="Statistik Periode"
                desc="Empat metrik utama di kepala detail rekening:"
            >
                <div className="grid grid-cols-2 gap-2 mt-3">
                    <MetricCard color="green" label="Pemasukan" desc="Credit + pembayaran" />
                    <MetricCard color="red" label="Pengeluaran" desc="Total debit" />
                    <MetricCard color="blue" label="Arus Bersih" desc="Selisih dua angka di atas" />
                    <MetricCard color="purple" label="Transaksi" desc="Jumlah catatan periode" />
                </div>
            </NoteCard>

            <NoteCard
                tone="purple"
                icon={<BarChart3 className="w-5 h-5" />}
                title="Grafik 12 Bulan"
                desc="Bar chart membandingkan pemasukan vs pengeluaran selama 12 bulan terakhir. Pola musiman dan tren jadi mudah terlihat."
            />

            <NoteCard
                tone="emerald"
                icon={<TrendingUp className="w-5 h-5" />}
                title="Indikator Tren"
                desc="Setiap rekening memiliki indikator naik/turun berdasarkan pemasukan bulan ini vs bulan lalu."
            >
                <div className="grid grid-cols-2 gap-2 mt-2">
                    <div className="flex items-center gap-2 text-xs p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-md">
                        <TrendingUp className="w-3.5 h-3.5 shrink-0" />
                        Tren naik dari bulan lalu
                    </div>
                    <div className="flex items-center gap-2 text-xs p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-md">
                        <TrendingDown className="w-3.5 h-3.5 shrink-0" />
                        Tren turun dari bulan lalu
                    </div>
                </div>
            </NoteCard>
        </div>
    );
}

/* ─── Reusable subparts ────────────────────────────────────── */

interface StepProps {
    n: number;
    color: 'blue' | 'purple' | 'emerald';
    title: string;
    desc: string;
    icon: React.ReactNode;
    tips?: string[];
}

function Step({ n, color, title, desc, icon, tips }: StepProps) {
    const colorMap = {
        blue: 'bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-900/40 text-blue-900 dark:text-blue-200 text-blue-700 dark:text-blue-300 text-blue-600 dark:text-blue-400 bg-blue-600',
        purple: 'bg-purple-50 dark:bg-purple-900/10 border-purple-200 dark:border-purple-900/40 text-purple-900 dark:text-purple-200 text-purple-700 dark:text-purple-300 text-purple-600 dark:text-purple-400 bg-purple-600',
        emerald: 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-900/40 text-emerald-900 dark:text-emerald-200 text-emerald-700 dark:text-emerald-300 text-emerald-600 dark:text-emerald-400 bg-emerald-600',
    };

    const tokens = colorMap[color].split(' ');
    const [bgClass, bgDark, borderClass, borderDark, titleClass, titleDark, descClass, descDark, iconClass, iconDark, badge] = tokens;

    return (
        <div className="flex gap-4">
            <div className={cn('shrink-0 w-12 h-12 rounded-full flex items-center justify-center z-10', badge)}>
                <span className="text-white font-bold text-sm">{n}</span>
            </div>
            <div className={cn('flex-1 rounded-xl p-4 border', bgClass, bgDark, borderClass, borderDark)}>
                <div className="flex items-start gap-3">
                    <span className={cn('shrink-0 mt-0.5', iconClass, iconDark)}>{icon}</span>
                    <div className="flex-1">
                        <h4 className={cn('font-semibold mb-1', titleClass, titleDark)}>{title}</h4>
                        <p className={cn('text-sm mb-2', descClass, descDark)}>{desc}</p>
                        {tips && (
                            <div className="grid grid-cols-2 gap-2">
                                {tips.map((t, i) => (
                                    <div key={i} className={cn('flex items-start gap-2 text-xs', iconClass, iconDark)}>
                                        <CheckCircle2 className="w-3.5 h-3.5 shrink-0 mt-0.5" />
                                        <span>{t}</span>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

interface NoteCardProps {
    tone: 'amber' | 'blue' | 'purple' | 'emerald';
    icon: React.ReactNode;
    title: string;
    desc: string;
    children?: React.ReactNode;
}

function NoteCard({ tone, icon, title, desc, children }: NoteCardProps) {
    const tones = {
        amber: 'bg-amber-50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-900/40 text-amber-900 dark:text-amber-200 text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
        blue: 'bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-900/40 text-blue-900 dark:text-blue-200 text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
        purple: 'bg-purple-50 dark:bg-purple-900/10 border-purple-200 dark:border-purple-900/40 text-purple-900 dark:text-purple-200 text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
        emerald: 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-200 dark:border-emerald-900/40 text-emerald-900 dark:text-emerald-200 text-emerald-700 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
    };
    const t = tones[tone].split(' ');
    const [bg, bgDark, border, borderDark, titleC, titleDark, descC, descDark, iconBg, iconBgDark, iconC, iconDark] = t;

    return (
        <div className={cn('p-4 rounded-xl border', bg, bgDark, border, borderDark)}>
            <div className="flex items-start gap-3">
                <div className={cn('h-9 w-9 rounded-xl flex items-center justify-center shrink-0', iconBg, iconBgDark)}>
                    <span className={cn(iconC, iconDark)}>{icon}</span>
                </div>
                <div className="flex-1">
                    <h4 className={cn('text-sm font-semibold mb-1', titleC, titleDark)}>{title}</h4>
                    <p className={cn('text-xs', descC, descDark)}>{desc}</p>
                    {children}
                </div>
            </div>
        </div>
    );
}

function MetricCard({ color, label, desc }: { color: 'green' | 'red' | 'blue' | 'purple'; label: string; desc: string }) {
    const labelMap = {
        green: 'text-green-700 dark:text-green-300',
        red: 'text-red-700 dark:text-red-300',
        blue: 'text-blue-700 dark:text-blue-300',
        purple: 'text-purple-700 dark:text-purple-300',
    };
    return (
        <div className="p-2.5 bg-blue-100/50 dark:bg-blue-900/20 rounded-lg">
            <p className={cn('text-xs font-semibold', labelMap[color])}>{label}</p>
            <p className="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{desc}</p>
        </div>
    );
}

function Bullet({ color, text }: { color: 'blue'; text: string }) {
    const t = {
        blue: 'text-blue-600 dark:text-blue-400',
    };
    return (
        <div className={cn('flex items-start gap-2 text-xs', t[color])}>
            <CheckCircle2 className="w-3.5 h-3.5 shrink-0 mt-0.5" />
            <span>{text}</span>
        </div>
    );
}

function TipsCard({ tips }: { tips: { kind: 'good' | 'warn'; text: string }[] }) {
    return (
        <div className="p-4 bg-gray-50 dark:bg-dark-700 rounded-xl border border-gray-200 dark:border-dark-600">
            <div className="flex items-start gap-3">
                <Lightbulb className="w-5 h-5 text-yellow-500 dark:text-yellow-400 shrink-0 mt-0.5" />
                <div className="flex-1">
                    <h4 className="text-sm font-semibold text-dark-900 dark:text-dark-50 mb-2">Tips</h4>
                    <ul className="space-y-1.5 text-xs text-dark-500 dark:text-dark-400">
                        {tips.map((t, i) => (
                            <li key={i} className="flex items-start gap-2">
                                <CheckCircle2 className={cn('w-3.5 h-3.5 shrink-0 mt-0.5', t.kind === 'good' ? 'text-green-500' : 'text-red-500')} />
                                <span>{t.text}</span>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </div>
    );
}
