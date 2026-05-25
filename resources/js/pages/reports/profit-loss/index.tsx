import { Head, router, usePage } from '@inertiajs/react';
import { endOfMonth, endOfYear, format as formatDateFns, startOfMonth, startOfYear, subMonths, subYears } from 'date-fns';
import { id as idLocale } from 'date-fns/locale';
import { Printer, TriangleAlert } from 'lucide-react';
import * as React from 'react';

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { PageHeader } from '@/components/shared/page-header';
import { AppLayout } from '@/layouts/app-layout';
import { cn, formatCurrency } from '@/lib/utils';
import type { SharedProps } from '@/types';

/* ───────────────────────────────── types ─── */

interface CategoryRow {
    category_id: number | null;
    category_label: string;
    amount: number;
}

interface GroupBlock {
    by_category: CategoryRow[];
    total: number;
}

interface Report {
    period: { start: string; end: string };
    revenue: {
        invoice: number;
        non_invoice: number;
        non_invoice_by_category: CategoryRow[];
        total: number;
    };
    cogs: {
        invoice: number;
        manual: number;
        manual_by_category: CategoryRow[];
        total: number;
    };
    gross_profit: number;
    opex: GroupBlock;
    operating_profit: number;
    other_income: GroupBlock;
    other_expense: GroupBlock;
    pre_tax_profit: number;
    tax: GroupBlock;
    net_profit: number;
    unclassified: {
        income: GroupBlock;
        expense: GroupBlock;
    };
}

interface Props extends SharedProps {
    report: Report;
    filters: { start_date: string; end_date: string };
    company: { name: string; address: string | null; npwp: string | null } | null;
}

/* ───────────────────────────────── helpers ─── */

function formatPeriod(start: string, end: string): string {
    const s = new Date(start + 'T00:00:00');
    const e = new Date(end + 'T00:00:00');
    return `${formatDateFns(s, 'd MMMM yyyy', { locale: idLocale })} — ${formatDateFns(e, 'd MMMM yyyy', { locale: idLocale })}`;
}

type PresetKey = 'this_month' | 'last_month' | 'ytd' | 'last_year' | 'custom';

function presetRange(key: PresetKey, current: { start: string; end: string }): { start: Date; end: Date } {
    const today = new Date();
    switch (key) {
        case 'this_month': return { start: startOfMonth(today), end: today };
        case 'last_month': {
            const lm = subMonths(today, 1);
            return { start: startOfMonth(lm), end: endOfMonth(lm) };
        }
        case 'ytd': return { start: startOfYear(today), end: today };
        case 'last_year': {
            const ly = subYears(today, 1);
            return { start: startOfYear(ly), end: endOfYear(ly) };
        }
        case 'custom': return { start: new Date(current.start + 'T00:00:00'), end: new Date(current.end + 'T00:00:00') };
    }
}

function detectPreset(start: string, end: string): PresetKey {
    const today = new Date();
    const todayStr = formatDateFns(today, 'yyyy-MM-dd');
    const presets: { key: PresetKey; range: { start: Date; end: Date } }[] = [
        { key: 'this_month', range: { start: startOfMonth(today), end: today } },
        { key: 'last_month', range: { start: startOfMonth(subMonths(today, 1)), end: endOfMonth(subMonths(today, 1)) } },
        { key: 'ytd', range: { start: startOfYear(today), end: today } },
        { key: 'last_year', range: { start: startOfYear(subYears(today, 1)), end: endOfYear(subYears(today, 1)) } },
    ];
    for (const p of presets) {
        const ps = formatDateFns(p.range.start, 'yyyy-MM-dd');
        const pe = p.key === 'this_month' || p.key === 'ytd' ? todayStr : formatDateFns(p.range.end, 'yyyy-MM-dd');
        if (ps === start && pe === end) return p.key;
    }
    return 'custom';
}

/* ───────────────────────────────── row primitives ─── */

interface RowProps {
    label: string;
    amount: number;
    indent?: 0 | 1 | 2;
    bold?: boolean;
    divider?: 'none' | 'top' | 'top-double';
    muted?: boolean;
    negative?: boolean;
}

function Row({ label, amount, indent = 0, bold = false, divider = 'none', muted = false, negative = false }: RowProps) {
    const isLoss = amount < 0;
    return (
        <div className={cn(
            'flex justify-between items-baseline gap-4 py-1.5',
            divider === 'top' && 'border-t border-secondary-200 dark:border-dark-600 pt-2 mt-1',
            divider === 'top-double' && 'border-t-2 border-dark-900 dark:border-dark-100 pt-2 mt-2',
            indent === 1 && 'pl-6',
            indent === 2 && 'pl-12',
            bold && 'font-semibold text-dark-900 dark:text-dark-50',
            muted && 'text-dark-500 dark:text-dark-400 text-sm',
        )}>
            <span>{label}</span>
            <span className={cn(
                'tabular-nums shrink-0',
                isLoss && 'text-red-600 dark:text-red-400',
            )}>
                {negative ? `(${formatCurrency(Math.abs(amount))})` : formatCurrency(amount)}
            </span>
        </div>
    );
}

function SectionTitle({ children }: { children: React.ReactNode }) {
    return (
        <h3 className="mt-6 mb-1 text-sm font-bold uppercase tracking-wide text-dark-900 dark:text-dark-50">{children}</h3>
    );
}

/* ───────────────────────────────── main page ─── */

export default function ProfitLossIndex() {
    const { report, filters, company } = usePage<Props>().props;

    const [start, setStart] = React.useState<Date>(new Date(filters.start_date + 'T00:00:00'));
    const [end, setEnd] = React.useState<Date>(new Date(filters.end_date + 'T00:00:00'));

    const activePreset = detectPreset(filters.start_date, filters.end_date);

    function applyPreset(key: PresetKey) {
        const r = presetRange(key, filters);
        setStart(r.start);
        setEnd(r.end);
        navigate(r.start, r.end);
    }

    function navigate(s: Date, e: Date) {
        router.get('/reports/profit-loss', {
            start_date: formatDateFns(s, 'yyyy-MM-dd'),
            end_date: formatDateFns(e, 'yyyy-MM-dd'),
        }, { preserveState: false });
    }

    const presets: { key: PresetKey; label: string }[] = [
        { key: 'this_month', label: 'Bulan ini' },
        { key: 'last_month', label: 'Bulan lalu' },
        { key: 'ytd', label: 'Tahun berjalan' },
        { key: 'last_year', label: 'Tahun lalu' },
    ];

    const hasUnclassified = report.unclassified.income.total > 0 || report.unclassified.expense.total > 0;

    return (
        <>
            <Head title="Laporan Laba Rugi" />

            <div className="space-y-6 print:space-y-3">
                {/* Header — hidden on print */}
                <div className="print:hidden">
                    <PageHeader
                        title="Laporan Laba Rugi"
                        description="Ringkasan pendapatan, modal, beban, dan laba bersih perusahaan"
                        action={(
                            <Button variant="primary" size="sm" icon={<Printer className="h-4 w-4" />} onClick={() => window.print()}>
                                Cetak
                            </Button>
                        )}
                    />
                </div>

                {/* Filter — hidden on print */}
                <Card className="print:hidden">
                    <CardContent className="p-4 space-y-4">
                        <div className="flex flex-wrap items-center gap-2">
                            {presets.map((p) => (
                                <Button
                                    key={p.key}
                                    variant={activePreset === p.key ? 'primary' : 'outline'}
                                    size="sm"
                                    onClick={() => applyPreset(p.key)}
                                >
                                    {p.label}
                                </Button>
                            ))}
                            {activePreset === 'custom' && (
                                <span className="text-xs px-3 py-1 rounded-md bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-medium">
                                    Periode khusus
                                </span>
                            )}
                        </div>

                        <div className="flex flex-col sm:flex-row sm:items-end gap-3">
                            <div className="w-full sm:w-72">
                                <label className="block text-xs font-medium text-dark-600 dark:text-dark-400 mb-1">Periode</label>
                                <DatePicker
                                    mode="range"
                                    value={{ from: start, to: end }}
                                    onChange={(v) => {
                                        if (v && 'from' in v) {
                                            if (v.from) setStart(v.from);
                                            if (v.to) setEnd(v.to);
                                        }
                                    }}
                                />
                            </div>
                            <Button
                                variant="primary"
                                size="sm"
                                onClick={() => navigate(start, end)}
                            >
                                Terapkan
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Report content — visible on print */}
                <Card>
                    <CardContent className="p-8 print:p-2 space-y-1">
                        {/* Print header */}
                        <div className="text-center mb-6 print:mb-4">
                            {company && (
                                <>
                                    <h1 className="text-xl font-bold text-dark-900 dark:text-dark-50">{company.name}</h1>
                                    {company.address && <p className="text-sm text-dark-600 dark:text-dark-400">{company.address}</p>}
                                    {company.npwp && <p className="text-sm text-dark-600 dark:text-dark-400">NPWP: {company.npwp}</p>}
                                </>
                            )}
                            <h2 className="text-lg font-bold mt-4 uppercase tracking-wider text-dark-900 dark:text-dark-50">Laporan Laba Rugi</h2>
                            <p className="text-sm text-dark-600 dark:text-dark-400">Periode: {formatPeriod(report.period.start, report.period.end)}</p>
                        </div>

                        {/* PENDAPATAN */}
                        <SectionTitle>Pendapatan</SectionTitle>
                        <Row label="Pendapatan dari Invoice (kas)" amount={report.revenue.invoice} indent={1} />
                        {report.revenue.non_invoice_by_category.map((r) => (
                            <Row key={`rev-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} />
                        ))}
                        <Row label="Total Pendapatan" amount={report.revenue.total} divider="top" bold />

                        {/* HARGA POKOK */}
                        <SectionTitle>Harga Pokok Penjualan (HPP)</SectionTitle>
                        <Row label="HPP Invoice (cost-recovery)" amount={report.cogs.invoice} indent={1} />
                        {report.cogs.manual_by_category.map((r) => (
                            <Row key={`cogs-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} />
                        ))}
                        <Row label="Total HPP" amount={report.cogs.total} divider="top" bold />

                        {/* LABA KOTOR */}
                        <Row label="LABA KOTOR" amount={report.gross_profit} divider="top-double" bold />

                        {/* BEBAN OPERASIONAL */}
                        <SectionTitle>Beban Operasional</SectionTitle>
                        {report.opex.by_category.length === 0 ? (
                            <Row label="(tidak ada)" amount={0} indent={1} muted />
                        ) : (
                            report.opex.by_category.map((r) => (
                                <Row key={`opex-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} />
                            ))
                        )}
                        <Row label="Total Beban Operasional" amount={report.opex.total} divider="top" bold />

                        {/* LABA USAHA */}
                        <Row label="LABA USAHA" amount={report.operating_profit} divider="top-double" bold />

                        {/* PENDAPATAN / BEBAN LAIN */}
                        {(report.other_income.total > 0 || report.other_expense.total > 0) && (
                            <>
                                <SectionTitle>Pendapatan & Beban Lain</SectionTitle>
                                {report.other_income.by_category.map((r) => (
                                    <Row key={`oi-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} />
                                ))}
                                {report.other_expense.by_category.map((r) => (
                                    <Row key={`oe-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} negative />
                                ))}
                                <Row label="Total Pendapatan/Beban Lain (netto)" amount={report.other_income.total - report.other_expense.total} divider="top" bold />
                            </>
                        )}

                        {/* LABA SEBELUM PAJAK */}
                        <Row label="LABA SEBELUM PAJAK" amount={report.pre_tax_profit} divider="top-double" bold />

                        {/* PAJAK */}
                        {report.tax.total > 0 && (
                            <>
                                <SectionTitle>Pajak</SectionTitle>
                                {report.tax.by_category.map((r) => (
                                    <Row key={`tax-${r.category_id}`} label={r.category_label} amount={r.amount} indent={1} />
                                ))}
                                <Row label="Total Pajak" amount={report.tax.total} divider="top" bold />
                            </>
                        )}

                        {/* LABA BERSIH */}
                        <Row label="LABA BERSIH" amount={report.net_profit} divider="top-double" bold />
                    </CardContent>
                </Card>

                {/* Unclassified warning — visible on print so the reader knows the report is incomplete */}
                {hasUnclassified && (
                    <Card className="border-orange-300 dark:border-orange-700 bg-orange-50/50 dark:bg-orange-900/10">
                        <CardContent className="p-4">
                            <div className="flex items-start gap-3">
                                <TriangleAlert className="h-5 w-5 text-orange-600 dark:text-orange-400 shrink-0 mt-0.5" />
                                <div className="flex-1 space-y-2">
                                    <h4 className="font-semibold text-orange-900 dark:text-orange-200">Ada transaksi yang belum diklasifikasi</h4>
                                    <p className="text-sm text-orange-800 dark:text-orange-300">
                                        Transaksi berikut <strong>belum dihitung</strong> dalam Laba Rugi di atas karena kategorinya belum dipetakan ke grup Laba Rugi. Buka <a href="/transaction-categories" className="underline">halaman Kategori</a> dan klasifikasikan supaya laporan ini lengkap.
                                    </p>

                                    {report.unclassified.income.total > 0 && (
                                        <div className="mt-2">
                                            <p className="text-xs font-medium text-orange-900 dark:text-orange-200">Pemasukan belum diklasifikasi:</p>
                                            {report.unclassified.income.by_category.map((r) => (
                                                <div key={`ui-${r.category_id ?? 'none'}`} className="flex justify-between text-sm text-orange-800 dark:text-orange-300">
                                                    <span>{r.category_label}</span>
                                                    <span className="tabular-nums">{formatCurrency(r.amount)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}

                                    {report.unclassified.expense.total > 0 && (
                                        <div className="mt-2">
                                            <p className="text-xs font-medium text-orange-900 dark:text-orange-200">Pengeluaran belum diklasifikasi:</p>
                                            {report.unclassified.expense.by_category.map((r) => (
                                                <div key={`ue-${r.category_id ?? 'none'}`} className="flex justify-between text-sm text-orange-800 dark:text-orange-300">
                                                    <span>{r.category_label}</span>
                                                    <span className="tabular-nums">{formatCurrency(r.amount)}</span>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

ProfitLossIndex.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
