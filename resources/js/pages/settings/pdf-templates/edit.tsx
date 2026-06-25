import * as React from 'react';
import { router, usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import { AppLayout } from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import {
    Type,
    Image as ImageIcon,
    Table2,
    LayoutGrid,
    Trash2,
    ZoomIn,
    ZoomOut,
    Bold as BoldIcon,
    Italic,
    Underline,
    Strikethrough,
    AlignLeft,
    AlignCenter,
    AlignRight,
    AlignJustify,
    AlignStartVertical,
    AlignCenterVertical,
    AlignEndVertical,
    GripVertical,
    Copy,
    Undo2,
    Redo2,
    Eye,
    Pencil,
    Plus,
    Minus,
    Save,
    FileDown,
    Lock,
    Unlock,
    RotateCcw,
    ArrowLeft,
    ChevronUp,
    ChevronDown,
    GripHorizontal,
    Upload,
    Minus as LineIcon,
    Square as RectIcon,
    Repeat2,
    Layers,
} from 'lucide-react';
import type { SharedProps } from '@/types';

// ponytail: koordinat px @96dpi. A4 = 794x1123.
const A4 = { w: 794, h: 1123 };

const DEFAULT_MARGINS = { top: 40, right: 40, bottom: 40, left: 40 };

// ── Font map (ONE source of truth — editor CSS stack + DomPDF family name) ──────
export const FONT_MAP = [
    { label: 'Helvetica / Arial',  cssFontStack: 'Helvetica, Arial, sans-serif',     dompdfFamily: 'Helvetica' },
    { label: 'Times New Roman',    cssFontStack: '"Times New Roman", Times, serif',   dompdfFamily: 'Times New Roman' },
    { label: 'Courier',            cssFontStack: '"Courier New", Courier, monospace', dompdfFamily: 'Courier' },
    { label: 'DejaVu Sans',        cssFontStack: '"DejaVu Sans", sans-serif',         dompdfFamily: 'DejaVu Sans' },
] as const;

export type FontLabel = typeof FONT_MAP[number]['label'];

export interface CustomFontEntry {
    id: number;
    name: string;
    url: string;
}

export function fontCssStack(label: FontLabel | string | undefined, customFonts: CustomFontEntry[] = []): string {
    const curated = FONT_MAP.find((f) => f.label === label);
    if (curated) return curated.cssFontStack;
    if (label && customFonts.some((f) => f.name === label)) return `"${label}"`;
    return 'Helvetica, Arial, sans-serif';
}

// ── Types ────────────────────────────────────────────────────────────────────

type Text = {
    id: number; type: 'text';
    x: number; y: number;
    content: string; fontSize: number; bold: boolean; color: string;
    fontFamily?: FontLabel;
    italic?: boolean;
    underline?: boolean;
    strikethrough?: boolean;
    highlight?: string | null;
    align?: 'left' | 'center' | 'right' | 'justify';
    valign?: 'top' | 'middle' | 'bottom';
    lineHeight?: number;
    letterSpacing?: number;
    padding?: number;
    borderWidth?: number;
    borderColor?: string;
    fill?: string | null;
    width?: number;
    height?: number;
};

type Img = {
    id: number; type: 'image';
    x: number; y: number;
    src: string; width: number; height?: number; lockAspect?: boolean;
    opacity?: number;
    borderWidth?: number;
    borderColor?: string;
    borderRadius?: number;
};

type TableColumn = {
    key: string;
    label: string;
    width: number;
    align: 'left' | 'center' | 'right';
    format: 'text' | 'number' | 'rupiah';
};

type TableEl = {
    id: number; type: 'table';
    x: number; y: number;
    width: number;
    columns: TableColumn[];
    showFooterSum: boolean;
    headerGroups?: Array<{ label: string; span: number; align?: 'left' | 'center' | 'right' }>;
};

type GridCell = {
    text: string;
    align: 'left' | 'center' | 'right';
    bold: boolean;
    color: string;
    fill?: string;
    colSpan?: number;
    rowSpan?: number;
    merged?: boolean;
};

type GridEl = {
    id: number; type: 'grid';
    x: number; y: number;
    width: number;
    cols: number;
    rows: number;
    colWidths: number[];
    cells: GridCell[][];
    border: { width: number; color: string };
    anchorCell?: { row: number; col: number } | null;
};

type RectEl = {
    id: number; type: 'rect';
    x: number; y: number;
    width: number; height: number;
    fill?: string | null;
    borderWidth: number;
    borderColor: string;
    borderRadius: number;
};

type LineEl = {
    id: number; type: 'line';
    x: number; y: number;
    length: number;
    thickness: number;
    color: string;
    orientation: 'h' | 'v';
};

type El = Text | Img | TableEl | GridEl | RectEl | LineEl;
type BandEl = Text | Img | GridEl | RectEl | LineEl;

// ── Banded layout types ──────────────────────────────────────────────────────

type BandName = 'header' | 'content' | 'footerFlow' | 'footerFixed';

type Band = {
    height: number;
    elements: BandEl[];
};

type HeaderBand = Band & { repeat: boolean };

type BandedLayout = {
    paper: { margins: { top: number; right: number; bottom: number; left: number } };
    bands: {
        header: HeaderBand;
        content: { table: TableEl | null };
        footerFlow: Band;
        footerFixed: Band;
    };
};

// ── Catalog types ─────────────────────────────────────────────────────────────

interface TokenEntry { path: string; label: string; }

interface ItemColumnEntry {
    key: string;
    label: string;
    align: 'left' | 'center' | 'right';
    format: 'text' | 'number' | 'rupiah';
    default: boolean;
}

interface TemplateProps { id: number; name: string; layout: unknown; }

interface Props extends SharedProps {
    template: TemplateProps;
    tokenCatalog: TokenEntry[];
    sampleData: Record<string, string>;
    itemColumnCatalog: ItemColumnEntry[];
    sampleItems: Array<Record<string, string>>;
    customFonts: CustomFontEntry[];
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeDefaultTable(id: number, catalog: ItemColumnEntry[], x: number, y: number): TableEl {
    const defaults = catalog.filter((c) => c.default);
    const widths: Record<string, number> = {
        no: 36, description: 290, quantity: 72, unit: 80, unit_price: 130, amount: 130,
        cogs_amount: 130, is_tax_deposit: 100,
    };
    const columns: TableColumn[] = defaults.map((c) => ({
        key: c.key, label: c.label, width: widths[c.key] ?? 100, align: c.align, format: c.format,
    }));
    return { id, type: 'table', x, y, width: 714, columns, showFooterSum: false };
}

const TABLE_HEADER_H = 28;
const TABLE_ROW_H = 24;
const TABLE_PLACEHOLDER_ROWS = 3;

function tableEditorHeight(el: TableEl): number {
    const groupRowH = (el.headerGroups?.length ?? 0) > 0 ? TABLE_HEADER_H : 0;
    return groupRowH + TABLE_HEADER_H + TABLE_ROW_H * TABLE_PLACEHOLDER_ROWS + 2;
}

function tablePreviewHeight(el: TableEl, sampleItems: Array<Record<string, string>>): number {
    const groupRowH = (el.headerGroups?.length ?? 0) > 0 ? TABLE_HEADER_H : 0;
    return groupRowH + TABLE_HEADER_H + TABLE_ROW_H * Math.max(1, sampleItems.length) + 2;
}

function makeGridCell(): GridCell {
    return { text: '', align: 'left', bold: false, color: '#0f172a' };
}

function makeDefaultGrid(id: number, x: number, y: number): GridEl {
    const cols = 3; const rows = 3; const width = 300;
    const colWidth = Math.floor(width / cols);
    return {
        id, type: 'grid', x, y, width, cols, rows,
        colWidths: Array.from({ length: cols }, () => colWidth),
        cells: Array.from({ length: rows }, () => Array.from({ length: cols }, makeGridCell)),
        border: { width: 1, color: '#cbd5e1' },
    };
}

const GRID_ROW_H = 24;
function gridEditorHeight(el: GridEl): number {
    return GRID_ROW_H * el.rows + el.border.width * (el.rows + 1);
}

function makeDefaultRect(id: number, x: number, y: number): RectEl {
    return { id, type: 'rect', x, y, width: 200, height: 40, fill: null, borderWidth: 1, borderColor: '#0f172a', borderRadius: 0 };
}

function makeDefaultLine(id: number, x: number, y: number): LineEl {
    return { id, type: 'line', x, y, length: 300, thickness: 1, color: '#0f172a', orientation: 'h' };
}

function lineElWidth(el: LineEl): number { return el.orientation === 'h' ? el.length : el.thickness; }
function lineElHeight(el: LineEl): number { return el.orientation === 'h' ? el.thickness : el.length; }

// ── Legacy migration ──────────────────────────────────────────────────────────

function migrateToLegacyBanded(oldEls: El[]): BandedLayout['bands'] {
    const tableEl = oldEls.find((e): e is TableEl => e.type === 'table') ?? null;
    const bandEls = oldEls.filter((e) => e.type !== 'table') as BandEl[];
    return {
        header: { height: 200, repeat: false, elements: bandEls },
        content: { table: tableEl },
        footerFlow: { height: 80, elements: [] },
        footerFixed: { height: 40, elements: [] },
    };
}

const DEFAULT_BANDS: BandedLayout['bands'] = {
    header: {
        height: 180, repeat: false,
        elements: [{
            id: 1, type: 'text', x: 60, y: 40, content: 'Invoice {{invoice.number}}',
            fontSize: 20, bold: true, color: '#0f172a', fontFamily: 'Helvetica / Arial',
            italic: false, underline: false, strikethrough: false,
            align: 'left', lineHeight: 1.2, letterSpacing: 0, padding: 0, width: 300,
        }],
    },
    content: { table: null },
    footerFlow: { height: 120, elements: [] },
    footerFixed: { height: 50, elements: [] },
};

function bandLabel(band: BandName): string {
    if (band === 'header') return 'Header';
    if (band === 'content') return 'Konten';
    if (band === 'footerFlow') return 'Footer Flow';
    return 'Footer Tetap';
}

// ── Margin Settings Panel ─────────────────────────────────────────────────────

type Margins = { top: number; right: number; bottom: number; left: number };

function MarginSettingsPanel({
    margins,
    onChangeMargins,
}: {
    margins: Margins;
    onChangeMargins: (m: Margins) => void;
}) {
    const set = (key: keyof Margins, v: number) =>
        onChangeMargins({ ...margins, [key]: Math.max(0, v) });

    return (
        <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden shadow-sm">
            <div className="px-3 py-2 bg-zinc-50 dark:bg-dark-800 border-b border-secondary-200 dark:border-dark-600">
                <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-400 dark:text-dark-500">Margin Halaman</span>
            </div>
            <div className="p-3 space-y-2">
                {/* Visual margin diagram */}
                <div className="flex items-center justify-center py-1">
                    <div className="relative w-20 h-24 border-2 border-zinc-200 dark:border-dark-600 rounded-md bg-zinc-50 dark:bg-dark-800">
                        <div
                            className="absolute inset-0 border border-dashed border-primary-300 dark:border-primary-700/50 rounded-sm"
                            style={{
                                top: Math.round((margins.top / 40) * 8),
                                right: Math.round((margins.right / 40) * 8),
                                bottom: Math.round((margins.bottom / 40) * 8),
                                left: Math.round((margins.left / 40) * 8),
                            }}
                        />
                        <span className="absolute inset-0 flex items-center justify-center text-[8px] text-dark-300 dark:text-dark-600 font-medium">A4</span>
                    </div>
                </div>
                <Row label="Atas">
                    <NumField value={margins.top} onChange={(v) => set('top', v)} unit="px" />
                </Row>
                <Row label="Kanan">
                    <NumField value={margins.right} onChange={(v) => set('right', v)} unit="px" />
                </Row>
                <Row label="Bawah">
                    <NumField value={margins.bottom} onChange={(v) => set('bottom', v)} unit="px" />
                </Row>
                <Row label="Kiri">
                    <NumField value={margins.left} onChange={(v) => set('left', v)} unit="px" />
                </Row>
            </div>
        </div>
    );
}

// ── Band Settings Panel ───────────────────────────────────────────────────────

function BandSettingsPanel({
    bands,
    onChangeBands,
}: {
    bands: BandedLayout['bands'];
    onChangeBands: (patch: Partial<BandedLayout['bands']>) => void;
}) {
    return (
        <div className="space-y-2">
            {/* Section title */}
            <span className="block text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-400 dark:text-dark-500 px-0.5">
                Ukuran Band
            </span>

            {/* Header band card */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden shadow-sm">
                <div className="flex items-center gap-2 px-3 py-2 bg-blue-50/60 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/20">
                    <span className="inline-block w-2 h-2 rounded-full bg-blue-400 shrink-0" />
                    <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-600 dark:text-dark-300 flex-1">Header</span>
                    <span className="text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Tetap</span>
                </div>
                <div className="p-3 space-y-2">
                    <Row label="Tinggi">
                        <NumField
                            value={bands.header.height}
                            onChange={(v) => onChangeBands({ header: { ...bands.header, height: Math.max(20, v) } })}
                            unit="px"
                        />
                    </Row>
                    {/* Header repeat toggle — inline switch style */}
                    <div className="flex items-center gap-2">
                        <span className="w-12 shrink-0 text-xs text-dark-500 dark:text-dark-400">Ulangi</span>
                        <button
                            role="switch"
                            aria-checked={bands.header.repeat}
                            onClick={() => onChangeBands({ header: { ...bands.header, repeat: !bands.header.repeat } })}
                            className={`relative inline-flex h-5 w-9 shrink-0 items-center rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-1 ${
                                bands.header.repeat ? 'bg-primary-600' : 'bg-zinc-200 dark:bg-dark-500'
                            }`}
                        >
                            <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-200 ${
                                bands.header.repeat ? 'translate-x-4' : 'translate-x-0.5'
                            }`} />
                        </button>
                        <span className="text-xs text-dark-500 dark:text-dark-400 flex items-center gap-1">
                            <Repeat2 className="w-3 h-3" />
                            {bands.header.repeat ? 'Tiap halaman' : 'Hal. pertama saja'}
                        </span>
                    </div>
                </div>
            </div>

            {/* Content band card */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden shadow-sm">
                <div className="flex items-center gap-2 px-3 py-2 bg-emerald-50/60 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900/20">
                    <span className="inline-block w-2 h-2 rounded-full bg-emerald-400 shrink-0" />
                    <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-600 dark:text-dark-300 flex-1">Konten</span>
                    <span className="text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Dinamis ⇕</span>
                </div>
                <div className="px-3 py-2.5">
                    <p className="text-[11px] text-dark-400 dark:text-dark-500 leading-relaxed">Tinggi otomatis — mengikuti jumlah baris item invoice.</p>
                </div>
            </div>

            {/* Footer Flow band card */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden shadow-sm">
                <div className="flex items-center gap-2 px-3 py-2 bg-amber-50/60 dark:bg-amber-900/10 border-b border-amber-100 dark:border-amber-900/20">
                    <span className="inline-block w-2 h-2 rounded-full bg-amber-400 shrink-0" />
                    <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-600 dark:text-dark-300 flex-1">Footer Flow</span>
                    <span className="text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Setelah konten</span>
                </div>
                <div className="p-3">
                    <Row label="Tinggi">
                        <NumField
                            value={bands.footerFlow.height}
                            onChange={(v) => onChangeBands({ footerFlow: { ...bands.footerFlow, height: Math.max(20, v) } })}
                            unit="px"
                        />
                    </Row>
                </div>
            </div>

            {/* Footer Fixed band card */}
            <div className="rounded-xl border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 overflow-hidden shadow-sm">
                <div className="flex items-center gap-2 px-3 py-2 bg-purple-50/60 dark:bg-purple-900/10 border-b border-purple-100 dark:border-purple-900/20">
                    <span className="inline-block w-2 h-2 rounded-full bg-purple-400 shrink-0" />
                    <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-600 dark:text-dark-300 flex-1">Footer Tetap</span>
                    <span className="text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">Tiap halaman</span>
                </div>
                <div className="p-3 space-y-2">
                    <Row label="Tinggi">
                        <NumField
                            value={bands.footerFixed.height}
                            onChange={(v) => onChangeBands({ footerFixed: { ...bands.footerFixed, height: Math.max(20, v) } })}
                            unit="px"
                        />
                    </Row>
                    <p className="text-[11px] text-dark-400 dark:text-dark-500 leading-relaxed">Dipaku di bagian bawah setiap halaman, di atas margin bawah.</p>
                </div>
            </div>
        </div>
    );
}

// ── Main component ────────────────────────────────────────────────────────────

export default function PdfTemplateEdit() {
    const { template, tokenCatalog, sampleData, itemColumnCatalog, sampleItems, customFonts } = usePage<Props>().props;

    // Inject @font-face for custom fonts
    React.useEffect(() => {
        if (!customFonts?.length) return;
        const id = 'custom-fonts-style';
        let style = document.getElementById(id) as HTMLStyleElement | null;
        if (!style) {
            style = document.createElement('style');
            style.id = id;
            document.head.appendChild(style);
        }
        style.textContent = customFonts
            .map((f) => `@font-face { font-family: "${f.name}"; src: url("${f.url}") format("truetype"); }`)
            .join('\n');
        return () => { style?.remove(); };
    }, [customFonts]);

    const resolve = (text: string | null | undefined): string =>
        (text ?? '').replace(/\{\{([\w.]+)\}\}/g, (match, path: string) =>
            Object.prototype.hasOwnProperty.call(sampleData, path) ? sampleData[path] : match,
        );

    // Compute initial bands + margins from layout (banded, legacy array, or default)
    const { initialBands, initialMargins } = React.useMemo(() => {
        const layout = template.layout;
        if (layout && typeof layout === 'object' && !Array.isArray(layout) && 'bands' in (layout as object)) {
            const bl = layout as BandedLayout;
            return { initialBands: bl.bands, initialMargins: bl.paper?.margins ?? { ...DEFAULT_MARGINS } };
        }
        if (Array.isArray(layout) && layout.length > 0) {
            return { initialBands: migrateToLegacyBanded(layout as El[]), initialMargins: { ...DEFAULT_MARGINS } };
        }
        return { initialBands: DEFAULT_BANDS, initialMargins: { ...DEFAULT_MARGINS } };
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const [bands, setBands] = React.useState<BandedLayout['bands']>(initialBands);
    const [margins, setMargins] = React.useState<{ top: number; right: number; bottom: number; left: number }>(initialMargins);
    const [activeBand, setActiveBand] = React.useState<BandName>('header');
    const [selectedId, setSelectedId] = React.useState<number | null>(null);
    const [selectedBand, setSelectedBand] = React.useState<BandName | null>(null);
    const [selectedCell, setSelectedCell] = React.useState<{ row: number; col: number } | null>(null);
    const [rangeEnd, setRangeEnd] = React.useState<{ row: number; col: number } | null>(null);
    const [anchorCell, setAnchorCell] = React.useState<{ row: number; col: number } | null>(null);
    const [editingCell, setEditingCell] = React.useState<{ row: number; col: number } | null>(null);
    const [saving, setSaving] = React.useState(false);
    const [editingId, setEditingId] = React.useState<number | null>(null);
    const [preview, setPreview] = React.useState(false);
    const [fieldMenu, setFieldMenu] = React.useState(false);
    const [zoom, setZoom] = React.useState(0.6);
    const [dragOver, setDragOver] = React.useState(false);
    const [pratinjauOpen, setPratinjauOpen] = React.useState(false);
    const [overLayerId, setOverLayerId] = React.useState<number | null>(null);

    // Per-band refs for drag calculations
    const headerRef = React.useRef<HTMLDivElement>(null);
    const contentRef = React.useRef<HTMLDivElement>(null);
    const footerFlowRef = React.useRef<HTMLDivElement>(null);
    const footerFixedRef = React.useRef<HTMLDivElement>(null);
    const canvasRef = React.useRef<HTMLDivElement>(null);
    const textContentRef = React.useRef<HTMLTextAreaElement>(null);
    const fileRef = React.useRef<HTMLInputElement>(null);
    const pendingImg = React.useRef<{ x: number; y: number; band: BandName } | null>(null);
    const dragLayerId = React.useRef<number | null>(null);
    const zoomAnchor = React.useRef<{ cx: number; cy: number; clientX: number; clientY: number } | null>(null);
    const clipboard = React.useRef<BandEl | null>(null);
    const history = React.useRef<{ past: BandedLayout['bands'][]; future: BandedLayout['bands'][] }>({ past: [], future: [] });

    // nextId: computed once from initial bands
    const allInitialEls = [
        ...initialBands.header.elements,
        ...(initialBands.content.table ? [initialBands.content.table] : []),
        ...initialBands.footerFlow.elements,
        ...initialBands.footerFixed.elements,
    ];
    const nextId = React.useRef(Math.max(0, ...allInitialEls.map((e) => e.id)) + 1);

    // ── Band element helpers ──────────────────────────────────────────────────

    const getBandEls = React.useCallback((band: BandName): BandEl[] => {
        if (band === 'header') return bands.header.elements;
        if (band === 'footerFlow') return bands.footerFlow.elements;
        if (band === 'footerFixed') return bands.footerFixed.elements;
        return [];
    }, [bands]);

    const setBandEls = React.useCallback((band: BandName, els: BandEl[]) => {
        setBands((prev) => {
            if (band === 'header') return { ...prev, header: { ...prev.header, elements: els } };
            if (band === 'footerFlow') return { ...prev, footerFlow: { ...prev.footerFlow, elements: els } };
            if (band === 'footerFixed') return { ...prev, footerFixed: { ...prev.footerFixed, elements: els } };
            return prev;
        });
    }, []);

    // Find which band owns an element id
    const findElBand = React.useCallback((id: number): BandName | null => {
        if (bands.header.elements.some((e) => e.id === id)) return 'header';
        if (bands.content.table?.id === id) return 'content';
        if (bands.footerFlow.elements.some((e) => e.id === id)) return 'footerFlow';
        if (bands.footerFixed.elements.some((e) => e.id === id)) return 'footerFixed';
        return null;
    }, [bands]);

    const getBandRef = (band: BandName): React.RefObject<HTMLDivElement | null> => {
        if (band === 'header') return headerRef;
        if (band === 'content') return contentRef;
        if (band === 'footerFlow') return footerFlowRef;
        return footerFixedRef;
    };

    // Find element by id across all bands
    const findEl = React.useCallback((id: number): El | null => {
        const fromBand = [...bands.header.elements, ...bands.footerFlow.elements, ...bands.footerFixed.elements]
            .find((e) => e.id === id);
        if (fromBand) return fromBand;
        if (bands.content.table?.id === id) return bands.content.table;
        return null;
    }, [bands]);

    const selected = selectedId != null ? findEl(selectedId) : null;

    const selectedRange: { r1: number; c1: number; r2: number; c2: number } | null =
        anchorCell && rangeEnd
            ? {
                r1: Math.min(anchorCell.row, rangeEnd.row),
                c1: Math.min(anchorCell.col, rangeEnd.col),
                r2: Math.max(anchorCell.row, rangeEnd.row),
                c2: Math.max(anchorCell.col, rangeEnd.col),
            }
            : anchorCell
              ? { r1: anchorCell.row, c1: anchorCell.col, r2: anchorCell.row, c2: anchorCell.col }
              : null;

    // ── Undo/redo ─────────────────────────────────────────────────────────────

    const snapshot = () =>
        setBands((prev) => {
            const h = history.current;
            h.past.push(prev);
            if (h.past.length > 100) h.past.shift();
            h.future = [];
            return prev;
        });

    const undo = () =>
        setBands((prev) => {
            const h = history.current;
            if (!h.past.length) return prev;
            h.future.push(prev);
            return h.past.pop()!;
        });

    const redo = () =>
        setBands((prev) => {
            const h = history.current;
            if (!h.future.length) return prev;
            h.past.push(prev);
            return h.future.pop()!;
        });

    // ── Update helpers ────────────────────────────────────────────────────────

    const update = React.useCallback((id: number, patch: Partial<El>) => {
        const band = findElBand(id);
        if (!band) return;
        if (band === 'content') {
            setBands((prev) => ({
                ...prev,
                content: { table: prev.content.table ? { ...prev.content.table, ...patch } as TableEl : null },
            }));
            return;
        }
        setBandEls(band, getBandEls(band).map((e) => (e.id === id ? ({ ...e, ...patch } as BandEl) : e)));
    }, [findElBand, getBandEls, setBandEls]);

    const setContentTable = (patchOrNull: Partial<TableEl> | null) => {
        setBands((prev) => ({
            ...prev,
            content: {
                table: patchOrNull === null
                    ? null
                    : {
                        ...(prev.content.table ?? {
                            id: nextId.current++, type: 'table', x: 0, y: 0,
                            width: 714, columns: [], showFooterSum: false,
                        }),
                        ...patchOrNull,
                    } as TableEl,
            },
        }));
    };

    // ── Drag in band ──────────────────────────────────────────────────────────

    const startDragInBand = (e: React.PointerEvent, el: BandEl | TableEl, bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        setSelectedId(el.id);
        const band = findElBand(el.id);
        if (band) setSelectedBand(band);
        const rect = bandRef.current!.getBoundingClientRect();
        const dx = (e.clientX - rect.left) / zoom - el.x;
        const dy = (e.clientY - rect.top) / zoom - el.y;
        let moved = false;
        const move = (ev: PointerEvent) => {
            if (!moved) { moved = true; snapshot(); }
            const x = Math.round((ev.clientX - rect.left) / zoom - dx);
            const y = Math.round((ev.clientY - rect.top) / zoom - dy);
            update(el.id, { x, y });
        };
        const up = () => {
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerup', up);
        };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startTextResize = (e: React.PointerEvent, el: Text, axis: 'width' | 'height' | 'both', bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        const rect = bandRef.current!.getBoundingClientRect();
        const x0 = el.x; const h0 = el.height;
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            const patch: Partial<Text> = {};
            if (axis === 'width' || axis === 'both') patch.width = Math.max(40, Math.round((ev.clientX - rect.left) / zoom - x0));
            if ((axis === 'height' || axis === 'both') && h0 !== undefined) {
                patch.height = Math.max(20, Math.round((ev.clientY - rect.top) / zoom - el.y));
            }
            update(el.id, patch);
        };
        const up = () => { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startTableResize = (e: React.PointerEvent, el: TableEl | GridEl, bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        const rect = bandRef.current!.getBoundingClientRect();
        const x0 = el.x;
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            update(el.id, { width: Math.max(100, Math.round((ev.clientX - rect.left) / zoom - x0)) });
        };
        const up = () => { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startRectResize = (e: React.PointerEvent, el: RectEl, bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        const rect = bandRef.current!.getBoundingClientRect();
        const x0 = el.x; const y0 = el.y;
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            update(el.id, {
                width: Math.max(4, Math.round((ev.clientX - rect.left) / zoom - x0)),
                height: Math.max(4, Math.round((ev.clientY - rect.top) / zoom - y0)),
            });
        };
        const up = () => { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startLineResize = (e: React.PointerEvent, el: LineEl, bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        const rect = bandRef.current!.getBoundingClientRect();
        const x0 = el.x; const y0 = el.y;
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            if (el.orientation === 'h') update(el.id, { length: Math.max(4, Math.round((ev.clientX - rect.left) / zoom - x0)) });
            else update(el.id, { length: Math.max(4, Math.round((ev.clientY - rect.top) / zoom - y0)) });
        };
        const up = () => { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startImgResize = (e: React.PointerEvent, el: Img, corner: 'nw' | 'ne' | 'sw' | 'se', bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.stopPropagation();
        setSelectedId(el.id);
        const rect = bandRef.current!.getBoundingClientRect();
        const w0 = el.width; const h0 = el.height ?? w0; const ratio = w0 / h0;
        const left0 = el.x; const top0 = el.y;
        const right0 = el.x + w0; const bottom0 = el.y + h0;
        const west = corner === 'nw' || corner === 'sw';
        const north = corner === 'nw' || corner === 'ne';
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            const px = (ev.clientX - rect.left) / zoom;
            const py = (ev.clientY - rect.top) / zoom;
            let newW = Math.max(20, west ? right0 - px : px - left0);
            let newH = Math.max(20, north ? bottom0 - py : py - top0);
            if (el.lockAspect) { newH = newW / ratio; }
            const newX = west ? right0 - newW : left0;
            const newY = north ? bottom0 - newH : top0;
            update(el.id, { x: Math.round(newX), y: Math.round(newY), width: Math.round(newW), height: Math.round(newH) });
        };
        const up = () => { window.removeEventListener('pointermove', move); window.removeEventListener('pointerup', up); };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    // ── Add element helpers ────────────────────────────────────────────────────

    const addText = (x = 80, y = 40, content = 'Teks baru') => {
        if (activeBand === 'content') return;
        snapshot();
        const id = nextId.current++;
        const el: BandEl = {
            id, type: 'text', x, y, content,
            fontSize: 14, bold: false, color: '#0f172a',
            fontFamily: 'Helvetica / Arial',
            italic: false, underline: false, strikethrough: false,
            align: 'left', lineHeight: 1.2, letterSpacing: 0, padding: 0, width: 200,
        };
        setBandEls(activeBand, [...getBandEls(activeBand), el]);
        setSelectedId(id);
        setSelectedBand(activeBand);
    };

    const addImage = (file: File, x = 80, y = 80, targetBand: BandName = activeBand) => {
        if (targetBand === 'content') return;
        const reader = new FileReader();
        reader.onload = () => {
            const src = reader.result as string;
            const probe = new Image();
            probe.onload = () => {
                snapshot();
                const id = nextId.current++;
                const width = 160;
                const height = Math.round(width * (probe.naturalHeight / probe.naturalWidth));
                const el: BandEl = { id, type: 'image', x, y, src, width, height, lockAspect: true };
                setBandEls(targetBand, [...getBandEls(targetBand), el]);
                setSelectedId(id);
                setSelectedBand(targetBand);
            };
            probe.src = src;
        };
        reader.readAsDataURL(file);
    };

    const addTable = () => {
        if (bands.content.table) return;
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultTable(id, itemColumnCatalog, 0, 0);
        setBands((prev) => ({ ...prev, content: { table: el } }));
        setSelectedId(id);
        setSelectedBand('content');
        setActiveBand('content');
    };

    const addGrid = (x = 80, y = 40) => {
        if (activeBand === 'content') return;
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultGrid(id, x, y);
        setBandEls(activeBand, [...getBandEls(activeBand), el]);
        setSelectedId(id);
        setSelectedBand(activeBand);
        setSelectedCell(null);
    };

    const addRect = (x = 80, y = 40) => {
        if (activeBand === 'content') return;
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultRect(id, x, y);
        setBandEls(activeBand, [...getBandEls(activeBand), el]);
        setSelectedId(id);
        setSelectedBand(activeBand);
    };

    const addLine = (x = 80, y = 40) => {
        if (activeBand === 'content') return;
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultLine(id, x, y);
        setBandEls(activeBand, [...getBandEls(activeBand), el]);
        setSelectedId(id);
        setSelectedBand(activeBand);
    };

    const setImgSize = (el: Img, dim: 'width' | 'height', v: number) => {
        if (el.lockAspect && el.height) {
            const r = el.width / el.height;
            if (dim === 'width') update(el.id, { width: v, height: Math.max(1, Math.round(v / r)) });
            else update(el.id, { width: Math.max(1, Math.round(v * r)), height: v });
        } else {
            update(el.id, dim === 'width' ? { width: v } : { height: v });
        }
    };

    const resetImage = (el: Img) => {
        const probe = new Image();
        probe.onload = () => { snapshot(); update(el.id, { width: probe.naturalWidth, height: probe.naturalHeight }); };
        probe.src = el.src;
    };

    const updateTableColumn = (tableId: number, colIdx: number, patch: Partial<TableColumn>) => {
        const t = bands.content.table;
        if (!t || t.id !== tableId) return;
        setContentTable({ columns: t.columns.map((c, i) => i === colIdx ? { ...c, ...patch } : c) });
    };

    const moveTableColumn = (tableId: number, from: number, direction: -1 | 1) => {
        const t = bands.content.table;
        if (!t || t.id !== tableId) return;
        const to = from + direction;
        if (to < 0 || to >= t.columns.length) return;
        const cols = [...t.columns];
        [cols[from], cols[to]] = [cols[to], cols[from]];
        setContentTable({ columns: cols });
    };

    const removeTableColumn = (tableId: number, colIdx: number) => {
        const t = bands.content.table;
        if (!t || t.id !== tableId) return;
        snapshot();
        setContentTable({ columns: t.columns.filter((_, i) => i !== colIdx) });
    };

    const addTableColumn = (tableId: number, key: string) => {
        const t = bands.content.table;
        if (!t || t.id !== tableId) return;
        const entry = itemColumnCatalog.find((c) => c.key === key);
        if (!entry || t.columns.some((c) => c.key === key)) return;
        const widths: Record<string, number> = {
            no: 36, description: 290, quantity: 72, unit: 80, unit_price: 130, amount: 130,
            cogs_amount: 130, is_tax_deposit: 100,
        };
        snapshot();
        const newCol: TableColumn = { key: entry.key, label: entry.label, width: widths[key] ?? 100, align: entry.align, format: entry.format };
        setContentTable({ columns: [...t.columns, newCol] });
    };

    const updateGridCell = (gridId: number, row: number, col: number, patch: Partial<GridCell>) => {
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const cells = e.cells.map((r, ri) => r.map((c, ci) => (ri === row && ci === col ? { ...c, ...patch } : c)));
            return { ...e, cells };
        }));
    };

    const updateGrid = (gridId: number, patch: Partial<GridEl>) => {
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => (e.id === gridId && e.type === 'grid' ? { ...e, ...patch } as GridEl : e)));
    };

    const mergeRange = (gridId: number, r1: number, c1: number, r2: number, c2: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const cells = e.cells.map((row, ri) =>
                row.map((cell, ci) => {
                    if (ri === r1 && ci === c1) return { ...cell, colSpan: c2 - c1 + 1, rowSpan: r2 - r1 + 1, merged: false };
                    if (ri >= r1 && ri <= r2 && ci >= c1 && ci <= c2) return { ...cell, text: '', colSpan: 1, rowSpan: 1, merged: true };
                    return cell;
                })
            );
            return { ...e, cells };
        }));
        setAnchorCell({ row: r1, col: c1 });
        setRangeEnd({ row: r1, col: c1 });
        setSelectedCell({ row: r1, col: c1 });
    };

    const unmergeCell = (gridId: number, row: number, col: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const keeper = e.cells[row]?.[col];
            if (!keeper) return e;
            const cs = keeper.colSpan ?? 1; const rs = keeper.rowSpan ?? 1;
            const cells = e.cells.map((rowCells, ri) =>
                rowCells.map((cell, ci) => {
                    if (ri === row && ci === col) return { ...cell, colSpan: 1, rowSpan: 1, merged: false };
                    if (ri >= row && ri < row + rs && ci >= col && ci < col + cs) return { ...cell, merged: false, colSpan: 1, rowSpan: 1 };
                    return cell;
                })
            );
            return { ...e, cells };
        }));
    };

    const addGridRow = (gridId: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            return { ...e, rows: e.rows + 1, cells: [...e.cells, Array.from({ length: e.cols }, makeGridCell)] };
        }));
    };

    const removeGridRow = (gridId: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid' || e.rows <= 1) return e;
            return { ...e, rows: e.rows - 1, cells: e.cells.slice(0, -1) };
        }));
    };

    const addGridCol = (gridId: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            return { ...e, cols: e.cols + 1, colWidths: [...e.colWidths, Math.floor(e.width / (e.cols + 1))], cells: e.cells.map((row) => [...row, makeGridCell()]) };
        }));
    };

    const removeGridCol = (gridId: number) => {
        snapshot();
        const band = findElBand(gridId);
        if (!band || band === 'content') return;
        setBandEls(band, getBandEls(band).map((e) => {
            if (e.id !== gridId || e.type !== 'grid' || e.cols <= 1) return e;
            return { ...e, cols: e.cols - 1, colWidths: e.colWidths.slice(0, -1), cells: e.cells.map((row) => row.slice(0, -1)) };
        }));
    };

    // ── Remove / duplicate / move layer ───────────────────────────────────────

    const remove = (id: number) => {
        snapshot();
        const band = findElBand(id);
        if (!band) return;
        if (band === 'content') {
            setBands((prev) => ({ ...prev, content: { table: null } }));
        } else {
            setBandEls(band, getBandEls(band).filter((e) => e.id !== id));
        }
        setSelectedId(null);
        setSelectedBand(null);
        setSelectedCell(null);
        setEditingCell(null);
    };

    const duplicate = (src: BandEl) => {
        snapshot();
        const srcBand = findElBand(src.id);
        if (!srcBand || srcBand === 'content') return;
        const id = nextId.current++;
        const copy = { ...src, id, x: src.x + 20, y: src.y + 20 } as BandEl;
        setBandEls(srcBand, [...getBandEls(srcBand), copy]);
        setSelectedId(id);
        setSelectedBand(srcBand);
        clipboard.current = copy;
    };

    const moveLayer = (draggedId: number, targetId: number, band: BandName) => {
        if (draggedId === targetId) return;
        snapshot();
        const els = getBandEls(band);
        const display = [...els].reverse();
        const from = display.findIndex((e) => e.id === draggedId);
        const to = display.findIndex((e) => e.id === targetId);
        if (from < 0 || to < 0) return;
        const [moved] = display.splice(from, 1);
        display.splice(to, 0, moved);
        setBandEls(band, display.reverse());
    };

    const insertToken = (path: string) => {
        if (!selected || selected.type !== 'text') return;
        snapshot();
        const token = `{{${path}}}`;
        const ta = textContentRef.current;
        if (ta && document.activeElement === ta) {
            const s = ta.selectionStart ?? selected.content.length;
            const en = ta.selectionEnd ?? s;
            update(selected.id, { content: selected.content.slice(0, s) + token + selected.content.slice(en) });
        } else {
            update(selected.id, { content: selected.content + token });
        }
    };

    // ── Save ──────────────────────────────────────────────────────────────────

    const save = () => {
        setSaving(true);
        const layout: BandedLayout = {
            paper: { margins },
            bands,
        };
        router.post(
            `/settings/pdf-templates/${template.id}/save`,
            { layout },
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => toast.success('Template tersimpan'),
                onError: () => toast.error('Gagal menyimpan'),
                onFinish: () => setSaving(false),
            },
        );
    };

    const openPdf = () => window.open(`/settings/pdf-templates/${template.id}/pdf`, '_blank');

    // B5: open PDF in a new tab with a fixed item count for pagination preview.
    const openPdfWithItems = (n: number) =>
        window.open(`/settings/pdf-templates/${template.id}/pdf?items=${n}`, '_blank');

    // ── Drop on band ──────────────────────────────────────────────────────────

    const dropOnBand = (e: React.DragEvent, band: BandName, bandRef: React.RefObject<HTMLDivElement | null>) => {
        e.preventDefault();
        setDragOver(false);
        if (band === 'content') return;
        const rect = bandRef.current!.getBoundingClientRect();
        const x = Math.max(0, Math.round((e.clientX - rect.left) / zoom));
        const y = Math.max(0, Math.round((e.clientY - rect.top) / zoom));
        const file = e.dataTransfer.files?.[0];
        if (file?.type.startsWith('image/')) { addImage(file, x, y, band); return; }
        const kind = e.dataTransfer.getData('kind');
        const prevActive = activeBand;
        setActiveBand(band);
        if (kind === 'text') addText(x, y);
        else if (kind === 'image') { pendingImg.current = { x, y, band }; fileRef.current?.click(); }
        else if (kind === 'table') addTable();
        else if (kind === 'grid') addGrid(x, y);
        else if (kind === 'rect') addRect(x, y);
        else if (kind === 'line') addLine(x, y);
        else setActiveBand(prevActive);
    };

    // ── Clipboard helpers ─────────────────────────────────────────────────────

    const imgToPngBlob = (src: string) =>
        new Promise<Blob>((res, rej) => {
            const img = new Image();
            img.onload = () => {
                const c = document.createElement('canvas');
                c.width = img.naturalWidth; c.height = img.naturalHeight;
                c.getContext('2d')!.drawImage(img, 0, 0);
                c.toBlob((b) => (b ? res(b) : rej(new Error('toBlob null'))), 'image/png');
            };
            img.onerror = rej; img.src = src;
        });

    const copyToOS = async (el: BandEl) => {
        try {
            if (el.type === 'text') await navigator.clipboard.writeText(el.content);
            else if (el.type === 'image') {
                const png = await imgToPngBlob(el.src);
                await navigator.clipboard.write([new ClipboardItem({ 'image/png': png })]);
            }
        } catch { /* clipboard may fail */ }
    };

    // ── Keyboard shortcuts ────────────────────────────────────────────────────

    React.useEffect(() => {
        const inField = (t: EventTarget | null) =>
            t instanceof HTMLElement && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable);
        const onKey = (e: KeyboardEvent) => {
            if (inField(e.target)) return;
            const mod = e.ctrlKey || e.metaKey;
            if (mod && e.key.toLowerCase() === 'z') { e.preventDefault(); if (e.shiftKey) redo(); else undo(); return; }
            if (mod && e.key.toLowerCase() === 'c' && selected) {
                clipboard.current = selected as BandEl;
                copyToOS(selected as BandEl);
            }
            if (mod && e.key.toLowerCase() === 'd' && selected) {
                e.preventDefault();
                duplicate(selected as BandEl);
            }
            if ((e.key === 'Delete' || e.key === 'Backspace') && selected) {
                e.preventDefault();
                remove(selected.id);
            }
        };
        const onPaste = (e: ClipboardEvent) => {
            if (inField(e.target)) return;
            const imageItem = Array.from(e.clipboardData?.items ?? []).find((it) => it.type.startsWith('image/'));
            const file = imageItem?.getAsFile();
            const text = e.clipboardData?.getData('text');
            if (file) { e.preventDefault(); addImage(file, 100, 40); }
            else if (text) { e.preventDefault(); addText(100, 40, text); }
            else if (clipboard.current) { e.preventDefault(); duplicate(clipboard.current); }
        };
        window.addEventListener('keydown', onKey);
        window.addEventListener('paste', onPaste);
        return () => { window.removeEventListener('keydown', onKey); window.removeEventListener('paste', onPaste); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [selected, activeBand, bands]);

    // ── Zoom wheel ────────────────────────────────────────────────────────────

    React.useEffect(() => {
        const node = canvasRef.current;
        if (!node) return;
        const onWheel = (e: WheelEvent) => {
            if (!e.ctrlKey && !e.metaKey) return;
            e.preventDefault();
            const factor = e.deltaY < 0 ? 1.1 : 1 / 1.1;
            const newZoom = Math.min(3, Math.max(0.2, +(zoom * factor).toFixed(3)));
            if (newZoom === zoom) return;
            zoomAnchor.current = { cx: 0, cy: 0, clientX: e.clientX, clientY: e.clientY };
            setZoom(newZoom);
        };
        node.addEventListener('wheel', onWheel, { passive: false });
        return () => node.removeEventListener('wheel', onWheel);
    }, [zoom]);

    // ── Fit-to-width on mount ─────────────────────────────────────────────────
    // Fire once: wait until the canvas container has a real width (rAF loop),
    // then set zoom so the A4 page fits with 24px padding on each side.
    const fitDoneRef = React.useRef(false);
    React.useEffect(() => {
        if (fitDoneRef.current) return;
        const node = canvasRef.current;
        if (!node) return;
        let rafId: number;
        const tryFit = () => {
            const w = node.clientWidth;
            if (w > 0) {
                const fitted = Math.min(Math.max((w - 48) / A4.w, 0.25), 1);
                setZoom(+fitted.toFixed(3));
                fitDoneRef.current = true;
            } else {
                rafId = requestAnimationFrame(tryFit);
            }
        };
        rafId = requestAnimationFrame(tryFit);
        return () => cancelAnimationFrame(rafId);
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    // Fix image height if missing
    React.useEffect(() => {
        const allImgs = [
            ...bands.header.elements,
            ...bands.footerFlow.elements,
            ...bands.footerFixed.elements,
        ].filter((e): e is Img => e.type === 'image' && e.height == null);
        allImgs.forEach((el) => {
            const probe = new Image();
            probe.onload = () => {
                update(el.id, { height: Math.round(el.width * (probe.naturalHeight / probe.naturalWidth)), lockAspect: (el as Img).lockAspect ?? true });
            };
            probe.src = el.src;
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [bands]);

    // ── Band canvas render helper ──────────────────────────────────────────────

    const renderBandElements = (bandName: BandName, bandRef: React.RefObject<HTMLDivElement | null>, elements: BandEl[]) => {
        return elements.map((el) => {
            const isSel = selectedId === el.id;
            const isEditing = editingId === el.id;

            if (el.type === 'grid') {
                const height = gridEditorHeight(el);
                return (
                    <div
                        key={el.id}
                        onPointerDown={(e) => {
                            if (editingCell) return;
                            startDragInBand(e, el, bandRef);
                            setSelectedCell(null);
                        }}
                        className={`absolute cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                        style={{ left: el.x, top: el.y, width: el.width, height, touchAction: 'none' }}
                    >
                        <GridCanvas
                            el={el}
                            preview={preview}
                            resolve={resolve}
                            selectedCell={isSel ? selectedCell : null}
                            editingCell={isSel ? editingCell : null}
                            onCellPointerDown={(r, c, e) => {
                                e.stopPropagation();
                                setSelectedId(el.id);
                                setSelectedBand(bandName);
                                if (e.shiftKey && anchorCell && selectedId === el.id) {
                                    setRangeEnd({ row: r, col: c });
                                    if (r !== anchorCell.row || c !== anchorCell.col) setSelectedCell(null);
                                } else {
                                    setAnchorCell({ row: r, col: c });
                                    setRangeEnd({ row: r, col: c });
                                    setSelectedCell({ row: r, col: c });
                                }
                            }}
                            onCellDoubleClick={(r, c) => {
                                if (preview) return;
                                setSelectedId(el.id);
                                setSelectedBand(bandName);
                                setSelectedCell({ row: r, col: c });
                                setEditingCell({ row: r, col: c });
                            }}
                            onCellCommit={(r, c, text) => {
                                if (text !== el.cells[r]?.[c]?.text) { snapshot(); updateGridCell(el.id, r, c, { text }); }
                                setEditingCell(null);
                            }}
                            onCellEscape={() => setEditingCell(null)}
                            selectedRange={isSel ? selectedRange : null}
                            rangeAnchor={isSel ? anchorCell : null}
                        />
                        {isSel && !preview && (
                            <span
                                onPointerDown={(e) => startTableResize(e, el, bandRef)}
                                className="absolute right-0 top-0 bottom-0 w-2 cursor-ew-resize flex items-center justify-center"
                            >
                                <span className="w-1 h-6 rounded-sm bg-primary-500 opacity-70" />
                            </span>
                        )}
                    </div>
                );
            }

            if (el.type === 'rect') {
                return (
                    <div
                        key={el.id}
                        onPointerDown={(e) => startDragInBand(e, el, bandRef)}
                        className={`absolute cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                        style={{
                            left: el.x, top: el.y, touchAction: 'none',
                            width: el.width, height: el.height,
                            backgroundColor: el.fill ?? undefined,
                            border: el.borderWidth > 0 ? `${el.borderWidth}px solid ${el.borderColor}` : undefined,
                            borderRadius: el.borderRadius > 0 ? `${el.borderRadius}px` : undefined,
                            boxSizing: 'border-box',
                        }}
                    >
                        {isSel && !preview && (
                            <span
                                onPointerDown={(e) => startRectResize(e, el, bandRef)}
                                className="absolute right-0 bottom-0 translate-x-full translate-y-full z-10 h-2.5 w-2.5 rounded-sm border border-primary-500 bg-white cursor-nwse-resize"
                            />
                        )}
                    </div>
                );
            }

            if (el.type === 'line') {
                const lw = lineElWidth(el); const lh = lineElHeight(el);
                return (
                    <div
                        key={el.id}
                        onPointerDown={(e) => startDragInBand(e, el, bandRef)}
                        className={`absolute cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                        style={{ left: el.x, top: el.y, touchAction: 'none', width: lw, height: lh, backgroundColor: el.color, flexShrink: 0 }}
                    >
                        {isSel && !preview && (
                            <span
                                onPointerDown={(e) => startLineResize(e, el, bandRef)}
                                className={`absolute z-10 h-2.5 w-2.5 rounded-sm border border-primary-500 bg-white ${
                                    el.orientation === 'h'
                                        ? 'right-0 top-1/2 -translate-y-1/2 translate-x-full cursor-ew-resize'
                                        : 'bottom-0 left-1/2 -translate-x-1/2 translate-y-full cursor-ns-resize'
                                }`}
                            />
                        )}
                    </div>
                );
            }

            if (el.type === 'text') {
                const hasBox = el.width !== undefined;
                const boxStyle: React.CSSProperties = hasBox ? {
                    width: el.width,
                    ...(el.height !== undefined ? { height: el.height } : {}),
                    padding: el.padding ?? 0,
                    border: (el.borderWidth && el.borderWidth > 0) ? `${el.borderWidth}px solid ${el.borderColor ?? '#000000'}` : undefined,
                    backgroundColor: el.fill ?? undefined,
                    boxSizing: 'border-box',
                    overflow: 'hidden',
                    display: 'flex',
                    flexDirection: 'column',
                    justifyContent: el.valign === 'bottom' ? 'flex-end' : el.valign === 'middle' ? 'center' : 'flex-start',
                } : {};
                return (
                    <div
                        key={el.id}
                        onPointerDown={(e) => {
                            if (isEditing) { e.stopPropagation(); return; }
                            startDragInBand(e, el, bandRef);
                        }}
                        className={`absolute select-none ${isEditing ? 'cursor-text' : 'cursor-move'} ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                        style={{ left: el.x, top: el.y, touchAction: 'none', ...boxStyle }}
                    >
                        {preview ? (
                            <span style={{
                                fontSize: el.fontSize, fontWeight: el.bold ? 700 : 400,
                                color: el.color, fontFamily: fontCssStack(el.fontFamily, customFonts),
                                fontStyle: el.italic ? 'italic' : 'normal',
                                textDecoration: [el.underline ? 'underline' : '', el.strikethrough ? 'line-through' : ''].filter(Boolean).join(' ') || 'none',
                                backgroundColor: el.highlight ?? undefined,
                                textAlign: el.align ?? 'left',
                                lineHeight: el.lineHeight ?? 1.2,
                                letterSpacing: el.letterSpacing ? `${el.letterSpacing}px` : undefined,
                                ...(hasBox ? { whiteSpace: 'pre-wrap', wordBreak: 'break-word', display: 'block', width: '100%' } : { whiteSpace: 'nowrap' }),
                            }}>
                                {resolve(el.content)}
                            </span>
                        ) : (
                            <EditableText
                                el={el}
                                editing={isEditing}
                                customFonts={customFonts}
                                onStartEdit={() => setEditingId(el.id)}
                                onCommit={(v) => {
                                    if (v !== el.content) { snapshot(); update(el.id, { content: v }); }
                                    setEditingId(null);
                                }}
                            />
                        )}
                        {isSel && !preview && hasBox && (
                            <>
                                <span
                                    onPointerDown={(e) => startTextResize(e, el, 'width', bandRef)}
                                    className="absolute right-0 top-1/2 -translate-y-1/2 translate-x-full z-10 h-5 w-2 cursor-ew-resize flex items-center justify-center"
                                >
                                    <span className="w-1 h-4 rounded-sm bg-primary-500 opacity-70" />
                                </span>
                                {el.height !== undefined && (
                                    <span
                                        onPointerDown={(e) => startTextResize(e, el, 'both', bandRef)}
                                        className="absolute right-0 bottom-0 translate-x-full translate-y-full z-10 h-2.5 w-2.5 rounded-sm border border-primary-500 bg-white cursor-nwse-resize"
                                    />
                                )}
                            </>
                        )}
                    </div>
                );
            }

            // image
            if (el.type === 'image') {
                return (
                    <div
                        key={el.id}
                        onPointerDown={(e) => {
                            if (isEditing) { e.stopPropagation(); return; }
                            startDragInBand(e, el, bandRef);
                        }}
                        className={`absolute select-none cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                        style={{ left: el.x, top: el.y, touchAction: 'none', width: el.width, height: el.height }}
                    >
                        <div style={{
                            width: '100%', height: '100%',
                            opacity: el.opacity !== undefined ? el.opacity / 100 : undefined,
                            border: (el.borderWidth && el.borderWidth > 0) ? `${el.borderWidth}px solid ${el.borderColor ?? '#000000'}` : undefined,
                            borderRadius: el.borderRadius ? `${el.borderRadius}px` : undefined,
                            overflow: 'hidden', boxSizing: 'border-box',
                        }}>
                            <img src={el.src} alt="" draggable={false} style={{ width: '100%', height: '100%', maxWidth: 'none' }} className="pointer-events-none block" />
                        </div>
                        {isSel && !preview && (['nw', 'ne', 'sw', 'se'] as const).map((corner) => (
                            <span
                                key={corner}
                                onPointerDown={(e) => startImgResize(e, el, corner, bandRef)}
                                className={`absolute z-10 h-2.5 w-2.5 rounded-sm border border-primary-500 bg-white ${
                                    corner === 'nw' ? 'left-0 top-0 -translate-x-full -translate-y-full cursor-nwse-resize'
                                    : corner === 'ne' ? 'right-0 top-0 translate-x-full -translate-y-full cursor-nesw-resize'
                                    : corner === 'sw' ? 'left-0 bottom-0 -translate-x-full translate-y-full cursor-nesw-resize'
                                    : 'right-0 bottom-0 translate-x-full translate-y-full cursor-nwse-resize'
                                }`}
                            />
                        ))}
                    </div>
                );
            }

            return null;
        });
    };

    // ── Band badge colours ────────────────────────────────────────────────────

    const bandBadgeColor: Record<BandName, string> = {
        header: 'bg-blue-100/80 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        content: 'bg-green-100/80 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        footerFlow: 'bg-orange-100/80 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        footerFixed: 'bg-purple-100/80 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
    };

    const bandSubtitle: Record<BandName, string> = {
        header: bands.header.repeat ? 'Tiap halaman' : 'Halaman pertama',
        content: 'Dinamis ⇕',
        footerFlow: 'Setelah konten',
        footerFixed: 'Tiap halaman',
    };

    // ── Layer groups for left panel ───────────────────────────────────────────

    const layerGroups: { band: BandName; els: (BandEl | TableEl)[] }[] = [
        { band: 'header', els: [...bands.header.elements].reverse() },
        { band: 'content', els: bands.content.table ? [bands.content.table] : [] },
        { band: 'footerFlow', els: [...bands.footerFlow.elements].reverse() },
        { band: 'footerFixed', els: [...bands.footerFixed.elements].reverse() },
    ];

    const totalElCount = bands.header.elements.length
        + (bands.content.table ? 1 : 0)
        + bands.footerFlow.elements.length
        + bands.footerFixed.elements.length;

    const tableEl = bands.content.table;

    return (
        <div className="flex flex-col h-[calc(100vh-4rem)]">
            {/* ── Top header bar ── */}
            <div className="flex items-center gap-2 px-3 py-2 border-b border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-900 shrink-0">
                {/* Left: back + title */}
                <Button variant="ghost" size="sm" onClick={() => router.visit('/settings/pdf-templates')} className="gap-1.5 text-dark-500 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50 shrink-0">
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600 shrink-0" />
                <span className="text-sm font-semibold text-dark-900 dark:text-dark-100 truncate min-w-0 flex-1">{template.name}</span>
                <span className="text-[11px] font-medium text-dark-400 dark:text-dark-500 shrink-0 hidden md:inline">Banded</span>

                {/* Right: document actions */}
                <div className="flex items-center gap-0.5 shrink-0 ml-2">
                    {/* Undo / Redo */}
                    <Button variant="ghost" size="icon" onClick={() => undo()} title="Undo (Ctrl+Z)" className="w-7 h-7">
                        <Undo2 className="w-3.5 h-3.5" />
                    </Button>
                    <Button variant="ghost" size="icon" onClick={() => redo()} title="Redo (Ctrl+Shift+Z)" className="w-7 h-7">
                        <Redo2 className="w-3.5 h-3.5" />
                    </Button>

                    <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600 mx-1 shrink-0" />

                    {/* Zoom controls */}
                    <Button variant="ghost" size="icon" onClick={() => setZoom((z) => Math.max(0.2, +(z - 0.1).toFixed(2)))} className="w-7 h-7" title="Perkecil">
                        <ZoomOut className="w-3.5 h-3.5" />
                    </Button>
                    <button
                        onClick={() => {
                            const node = canvasRef.current;
                            if (!node) return;
                            const w = node.clientWidth;
                            if (w > 0) setZoom(+Math.min(Math.max((w - 48) / A4.w, 0.25), 1).toFixed(3));
                        }}
                        className="text-[11px] tabular-nums font-medium text-dark-500 dark:text-dark-400 w-10 text-center select-none hover:text-dark-900 dark:hover:text-dark-50 transition-colors duration-150"
                        title="Klik untuk fit lebar"
                    >
                        {Math.round(zoom * 100)}%
                    </button>
                    <Button variant="ghost" size="icon" onClick={() => setZoom((z) => Math.min(3, +(z + 0.1).toFixed(2)))} className="w-7 h-7" title="Perbesar">
                        <ZoomIn className="w-3.5 h-3.5" />
                    </Button>

                    <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600 mx-1 shrink-0" />

                    {/* Preview toggle */}
                    <Button
                        variant={preview ? 'primary' : 'ghost'}
                        size="sm"
                        className="gap-1 text-xs"
                        onClick={() => { setPreview((p) => !p); setEditingId(null); }}
                    >
                        {preview ? <Pencil className="w-3.5 h-3.5" /> : <Eye className="w-3.5 h-3.5" />}
                        <span className="hidden sm:inline">{preview ? 'Edit' : 'Preview'}</span>
                    </Button>

                    <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600 mx-1 shrink-0" />

                    {/* Pratinjau N-item dropdown */}
                    <div className="relative">
                        <Button
                            variant="ghost"
                            size="sm"
                            className="gap-1 text-xs text-dark-600 dark:text-dark-300"
                            onClick={() => setPratinjauOpen((o) => !o)}
                            title="Pratinjau PDF dengan N item"
                        >
                            <FileDown className="w-3.5 h-3.5" />
                            <span className="hidden sm:inline">Pratinjau</span>
                            <ChevronDown className="w-3 h-3 opacity-60" />
                        </Button>
                        {pratinjauOpen && (
                            <>
                                <div className="fixed inset-0 z-40" onClick={() => setPratinjauOpen(false)} />
                                <div className="absolute top-full mt-1.5 right-0 z-50 min-w-[148px] bg-white dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-xl shadow-xl overflow-hidden">
                                    <p className="px-3 py-2 text-[10px] font-semibold uppercase tracking-[0.08em] text-dark-400 dark:text-dark-500 border-b border-secondary-200 dark:border-dark-600">
                                        Jumlah item
                                    </p>
                                    {([3, 10, 25, 60] as const).map((n) => (
                                        <button
                                            key={n}
                                            onClick={() => { setPratinjauOpen(false); openPdfWithItems(n); }}
                                            className="w-full text-left px-3 py-2 text-xs text-dark-700 dark:text-dark-300 hover:bg-zinc-50 dark:hover:bg-dark-600 transition-colors duration-100 flex items-center justify-between gap-3"
                                        >
                                            <span className="font-medium">{n} item</span>
                                            <span className="text-[10px] text-dark-400 dark:text-dark-500 tabular-nums">
                                                {n <= 5 ? '1 hal.' : n <= 20 ? '~2 hal.' : n <= 40 ? '~3 hal.' : '~5 hal.'}
                                            </span>
                                        </button>
                                    ))}
                                </div>
                            </>
                        )}
                    </div>

                    {/* PDF — quick export */}
                    <Button variant="ghost" size="sm" onClick={openPdf} className="gap-1 text-xs text-dark-600 dark:text-dark-300" title="Cetak PDF (data contoh)">
                        <Eye className="w-3.5 h-3.5" />
                        <span className="hidden sm:inline">PDF</span>
                    </Button>

                    <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600 mx-1 shrink-0" />

                    {/* Save — primary CTA */}
                    <Button variant="primary" size="sm" onClick={save} disabled={saving} className="gap-1.5 text-xs font-semibold">
                        <Save className="w-3.5 h-3.5" />
                        {saving ? 'Menyimpan…' : 'Simpan'}
                    </Button>
                </div>
            </div>

            {/* 3-column editor */}
            <div className="flex flex-1 overflow-hidden">

                {/* ── KIRI: Layers ── */}
                <aside className="w-52 shrink-0 border-r border-secondary-200 dark:border-dark-600 flex flex-col bg-white dark:bg-dark-900">
                    <PanelHeader title="Layers" meta={totalElCount ? String(totalElCount) : undefined} />
                    <div className="flex-1 overflow-auto p-2 space-y-0.5">
                        {totalElCount === 0 && (
                            <div className="px-3 py-6 text-center">
                                <Layers className="w-6 h-6 mx-auto mb-2 text-dark-300 dark:text-dark-600" />
                                <p className="text-xs text-dark-400 dark:text-dark-500 leading-relaxed">Belum ada elemen.<br />Seret dari toolbar.</p>
                            </div>
                        )}
                        {layerGroups.map(({ band, els }) => (
                            els.length > 0 && (
                                <div key={band} className="pb-1">
                                    {/* Band group header */}
                                    <div className="flex items-center gap-1.5 px-2 py-1.5 mt-1">
                                        <span className={`inline-block w-1.5 h-1.5 rounded-full shrink-0 ${
                                            band === 'header' ? 'bg-blue-400' :
                                            band === 'content' ? 'bg-emerald-400' :
                                            band === 'footerFlow' ? 'bg-amber-400' : 'bg-purple-400'
                                        }`} />
                                        <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-400 dark:text-dark-500">
                                            {bandLabel(band)}
                                        </span>
                                    </div>
                                    {/* Layer rows */}
                                    <div className="space-y-0.5 pl-1">
                                        {els.map((el) => {
                                            const active = selectedId === el.id;
                                            const isOver = overLayerId === el.id;
                                            return (
                                                <div
                                                    key={el.id}
                                                    draggable={band !== 'content'}
                                                    onClick={() => { setSelectedId(el.id); setSelectedBand(band); setActiveBand(band); }}
                                                    onDragStart={(ev) => {
                                                        dragLayerId.current = el.id;
                                                        ev.dataTransfer.effectAllowed = 'move';
                                                        ev.stopPropagation();
                                                    }}
                                                    onDragOver={(ev) => {
                                                        ev.preventDefault();
                                                        if (dragLayerId.current != null && overLayerId !== el.id) setOverLayerId(el.id);
                                                    }}
                                                    onDrop={(ev) => {
                                                        ev.preventDefault(); ev.stopPropagation();
                                                        if (dragLayerId.current != null) moveLayer(dragLayerId.current, el.id, band);
                                                        dragLayerId.current = null; setOverLayerId(null);
                                                    }}
                                                    onDragEnd={() => { dragLayerId.current = null; setOverLayerId(null); }}
                                                    className={`group flex items-center gap-1.5 rounded-lg pl-1.5 pr-1 py-1.5 transition-colors duration-150 ${
                                                        active
                                                            ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-200'
                                                            : 'text-dark-700 dark:text-dark-300 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                                    } ${isOver ? 'ring-1 ring-primary-400/60 ring-inset' : ''}`}
                                                >
                                                    <GripVertical className="w-3 h-3 shrink-0 text-dark-300 dark:text-dark-600 opacity-0 group-hover:opacity-100 transition-opacity" />
                                                    <span className={`grid place-items-center h-5 w-5 rounded-md shrink-0 transition-colors ${
                                                        active
                                                            ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-300'
                                                            : 'bg-zinc-100 dark:bg-dark-700 text-dark-400 dark:text-dark-500 group-hover:bg-zinc-200 dark:group-hover:bg-dark-600'
                                                    }`}>
                                                        {el.type === 'text' ? <Type className="w-3 h-3" />
                                                            : el.type === 'image' ? <ImageIcon className="w-3 h-3" />
                                                            : el.type === 'grid' ? <LayoutGrid className="w-3 h-3" />
                                                            : el.type === 'rect' ? <RectIcon className="w-3 h-3" />
                                                            : el.type === 'line' ? <LineIcon className="w-3 h-3" />
                                                            : <Table2 className="w-3 h-3" />}
                                                    </span>
                                                    <span className={`flex-1 truncate text-xs font-medium ${active ? 'text-primary-700 dark:text-primary-200' : 'text-dark-700 dark:text-dark-300'}`}>
                                                        {el.type === 'text' ? (el.content.length > 18 ? el.content.slice(0, 18) + '…' : el.content || '(kosong)')
                                                            : el.type === 'image' ? 'Gambar'
                                                            : el.type === 'grid' ? `Grid ${(el as GridEl).rows}×${(el as GridEl).cols}`
                                                            : el.type === 'rect' ? 'Kotak'
                                                            : el.type === 'line' ? 'Garis'
                                                            : 'Tabel Item'}
                                                    </span>
                                                    <button
                                                        onClick={(ev) => { ev.stopPropagation(); remove(el.id); }}
                                                        className="grid place-items-center h-5 w-5 rounded-md text-dark-300 dark:text-dark-600 opacity-0 group-hover:opacity-100 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 transition-all duration-150"
                                                    >
                                                        <Trash2 className="w-3 h-3" />
                                                    </button>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            )
                        ))}
                    </div>
                </aside>

                {/* ── TENGAH: Kanvas ── */}
                <div className="relative flex-1 overflow-hidden">
                    <div ref={canvasRef} className="absolute inset-0 overflow-auto" style={{ backgroundColor: '#1a1a1d' }}>
                        <div className="min-h-full flex items-start justify-center py-10 px-10">
                            {/* Outer sizing wrapper — exact A4 footprint at current zoom */}
                            <div style={{ width: A4.w * zoom, height: A4.h * zoom, flexShrink: 0 }}>
                                {/* ── A4 PAPER SHEET — true portrait 793×1123px ── */}
                                <div
                                    className={`relative bg-white shadow-xl ${dragOver ? 'ring-2 ring-primary-400' : ''}`}
                                    style={{
                                        width: A4.w,
                                        height: A4.h,
                                        transform: `scale(${zoom})`,
                                        transformOrigin: 'top left',
                                        overflow: 'hidden',
                                    }}
                                >
                                    {/* ── MARGIN GUIDE — dashed rect showing printable area ── */}
                                    <div
                                        aria-hidden
                                        className="absolute pointer-events-none z-[100]"
                                        style={{
                                            top: margins.top,
                                            left: margins.left,
                                            width: A4.w - margins.left - margins.right,
                                            bottom: margins.bottom,
                                            border: '1.5px dashed rgba(99,102,241,0.30)',
                                        }}
                                    />

                                    {/* ── FLOWING BANDS: Header → Content → FooterFlow ── */}
                                    {/* These stack top-to-bottom inside the top margin area */}

                                    {/* ── HEADER BAND ── */}
                                    <div
                                        ref={headerRef}
                                        className={`relative transition-shadow duration-150 ${
                                            activeBand === 'header'
                                                ? 'ring-2 ring-inset ring-blue-400/50 bg-blue-50/20 dark:bg-blue-900/5'
                                                : ''
                                        }`}
                                        style={{
                                            width: A4.w,
                                            height: bands.header.height,
                                            overflow: 'visible',
                                            borderBottom: activeBand === 'header'
                                                ? '2px solid rgba(96,165,250,0.5)'
                                                : '1px solid rgba(226,232,240,0.8)',
                                        }}
                                        onPointerDown={() => { setActiveBand('header'); setSelectedId(null); setSelectedBand(null); }}
                                        onDragOver={(e) => { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; setDragOver(true); }}
                                        onDragLeave={() => setDragOver(false)}
                                        onDrop={(e) => dropOnBand(e, 'header', headerRef)}
                                    >
                                        {/* Band label — top-left corner tab */}
                                        <div className="absolute top-1 left-1 flex items-center gap-1 pointer-events-none z-10">
                                            <span className="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-300 dark:text-dark-600">HEADER</span>
                                            <span className={`text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none ${bandBadgeColor.header}`}>
                                                {bandSubtitle.header}
                                            </span>
                                        </div>
                                        {renderBandElements('header', headerRef, bands.header.elements)}
                                    </div>

                                    {/* Band separator */}
                                    <div className="flex items-center w-full" style={{ height: 16 }}>
                                        <div className="flex-1 border-t border-dashed border-slate-200/70 dark:border-dark-700/70" />
                                        <span className="mx-2 shrink-0 text-[8px] font-medium tracking-widest uppercase text-slate-300 dark:text-dark-600 select-none">Konten</span>
                                        <div className="flex-1 border-t border-dashed border-slate-200/70 dark:border-dark-700/70" />
                                    </div>

                                    {/* ── CONTENT BAND ── */}
                                    <div
                                        ref={contentRef}
                                        className={`relative transition-shadow duration-150 ${
                                            activeBand === 'content'
                                                ? 'ring-2 ring-inset ring-emerald-400/50 bg-emerald-50/20 dark:bg-emerald-900/5'
                                                : ''
                                        }`}
                                        style={{
                                            width: A4.w,
                                            minHeight: 80,
                                            overflow: 'visible',
                                            borderBottom: activeBand === 'content'
                                                ? '2px solid rgba(52,211,153,0.5)'
                                                : '1px solid rgba(226,232,240,0.8)',
                                        }}
                                        onPointerDown={() => { setActiveBand('content'); if (!tableEl) { setSelectedId(null); setSelectedBand(null); } }}
                                    >
                                        {/* Band label */}
                                        <div className="absolute top-1 left-1 flex items-center gap-1 pointer-events-none z-10">
                                            <span className="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-300 dark:text-dark-600">KONTEN</span>
                                            <span className={`text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none ${bandBadgeColor.content}`}>
                                                {bandSubtitle.content}
                                            </span>
                                        </div>
                                        {tableEl ? (() => {
                                            const isSel = selectedId === tableEl.id;
                                            const height = preview ? tablePreviewHeight(tableEl, sampleItems) : tableEditorHeight(tableEl);
                                            const rows = preview ? sampleItems : null;
                                            return (
                                                <div
                                                    onPointerDown={(e) => {
                                                        e.stopPropagation();
                                                        setSelectedId(tableEl.id);
                                                        setSelectedBand('content');
                                                        setActiveBand('content');
                                                    }}
                                                    className={`absolute cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                                                    style={{ left: tableEl.x + margins.left, top: 22, width: tableEl.width, height, touchAction: 'none' }}
                                                >
                                                    <TablePreview el={tableEl} rows={rows} />
                                                    {isSel && !preview && (
                                                        <span
                                                            onPointerDown={(e) => startTableResize(e, tableEl, contentRef)}
                                                            className="absolute right-0 top-0 bottom-0 w-2 cursor-ew-resize flex items-center justify-center"
                                                        >
                                                            <span className="w-1 h-6 rounded-sm bg-primary-500 opacity-70" />
                                                        </span>
                                                    )}
                                                </div>
                                            );
                                        })() : (
                                            <div className="flex items-center justify-center" style={{ paddingTop: 30, paddingBottom: 20, minHeight: 80 }}>
                                                <button
                                                    onClick={() => { setActiveBand('content'); addTable(); }}
                                                    className="flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-dashed border-slate-200 dark:border-dark-600 text-sm text-slate-400 dark:text-dark-500 hover:border-primary-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-150"
                                                >
                                                    <Table2 className="w-4 h-4" />
                                                    + Tabel Item
                                                </button>
                                            </div>
                                        )}
                                        {/* Spacer below table */}
                                        {tableEl && <div style={{ height: tableEditorHeight(tableEl) + 22 + 16 }} />}
                                    </div>

                                    {/* Band separator */}
                                    <div className="flex items-center w-full" style={{ height: 16 }}>
                                        <div className="flex-1 border-t border-dashed border-slate-200/70 dark:border-dark-700/70" />
                                        <span className="mx-2 shrink-0 text-[8px] font-medium tracking-widest uppercase text-slate-300 dark:text-dark-600 select-none">Footer Flow</span>
                                        <div className="flex-1 border-t border-dashed border-slate-200/70 dark:border-dark-700/70" />
                                    </div>

                                    {/* ── FOOTER FLOW BAND ── */}
                                    <div
                                        ref={footerFlowRef}
                                        className={`relative transition-shadow duration-150 ${
                                            activeBand === 'footerFlow'
                                                ? 'ring-2 ring-inset ring-amber-400/50 bg-amber-50/20 dark:bg-amber-900/5'
                                                : ''
                                        }`}
                                        style={{
                                            width: A4.w,
                                            height: bands.footerFlow.height,
                                            overflow: 'visible',
                                            borderBottom: activeBand === 'footerFlow'
                                                ? '2px solid rgba(251,191,36,0.5)'
                                                : '1px solid rgba(226,232,240,0.8)',
                                        }}
                                        onPointerDown={() => { setActiveBand('footerFlow'); setSelectedId(null); setSelectedBand(null); }}
                                        onDragOver={(e) => { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; setDragOver(true); }}
                                        onDragLeave={() => setDragOver(false)}
                                        onDrop={(e) => dropOnBand(e, 'footerFlow', footerFlowRef)}
                                    >
                                        {/* Band label */}
                                        <div className="absolute top-1 left-1 flex items-center gap-1 pointer-events-none z-10">
                                            <span className="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-300 dark:text-dark-600">FOOTER FLOW</span>
                                            <span className={`text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none ${bandBadgeColor.footerFlow}`}>
                                                {bandSubtitle.footerFlow}
                                            </span>
                                        </div>
                                        {renderBandElements('footerFlow', footerFlowRef, bands.footerFlow.elements)}
                                    </div>

                                    {/* ── FOOTER FIXED BAND — pinned to bottom of A4 page ── */}
                                    {/* position: absolute so it always sits just above the bottom margin, */}
                                    {/* regardless of how tall the flowing bands above are.             */}
                                    <div
                                        ref={footerFixedRef}
                                        className={`absolute transition-shadow duration-150 ${
                                            activeBand === 'footerFixed'
                                                ? 'ring-2 ring-inset ring-purple-400/50 bg-purple-50/20 dark:bg-purple-900/5'
                                                : ''
                                        }`}
                                        style={{
                                            left: 0,
                                            right: 0,
                                            bottom: margins.bottom,
                                            height: bands.footerFixed.height,
                                            overflow: 'visible',
                                            borderTop: activeBand === 'footerFixed'
                                                ? '2px solid rgba(192,132,252,0.5)'
                                                : '1px dashed rgba(200,200,220,0.6)',
                                            zIndex: 20,
                                            backgroundColor: activeBand === 'footerFixed'
                                                ? undefined
                                                : 'rgba(250,250,255,0.6)',
                                        }}
                                        onPointerDown={() => { setActiveBand('footerFixed'); setSelectedId(null); setSelectedBand(null); }}
                                        onDragOver={(e) => { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; setDragOver(true); }}
                                        onDragLeave={() => setDragOver(false)}
                                        onDrop={(e) => dropOnBand(e, 'footerFixed', footerFixedRef)}
                                    >
                                        {/* Band label — shown at top-left of the fixed footer */}
                                        <div className="absolute top-1 left-1 flex items-center gap-1 pointer-events-none z-10">
                                            <span className="text-[8px] font-bold uppercase tracking-[0.12em] text-slate-300 dark:text-dark-600">FOOTER TETAP</span>
                                            <span className={`text-[9px] font-semibold px-1.5 py-0.5 rounded-md leading-none ${bandBadgeColor.footerFixed}`}>
                                                {bandSubtitle.footerFixed}
                                            </span>
                                        </div>
                                        {renderBandElements('footerFixed', footerFixedRef, bands.footerFixed.elements)}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        {/* ── Active-band indicator — canvas overlay (top-left) ── */}
                    <div className="absolute top-3 left-3 z-20 pointer-events-none">
                        <div className="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-dark-900/80 dark:bg-dark-800/90 backdrop-blur-sm border border-white/10 shadow-lg">
                            <span className={`inline-block w-2 h-2 rounded-full shrink-0 ${
                                activeBand === 'header' ? 'bg-blue-400' :
                                activeBand === 'content' ? 'bg-emerald-400' :
                                activeBand === 'footerFlow' ? 'bg-amber-400' : 'bg-purple-400'
                            }`} />
                            <span className="text-[10px] font-semibold text-white/80 tracking-wide">{bandLabel(activeBand)}</span>
                        </div>
                    </div>

                    {/* ── FLOATING ELEMENT-INSERT TOOLBAR ── */}
                    <div className="absolute bottom-5 left-1/2 -translate-x-1/2 z-30 flex items-center gap-0.5 p-1.5 rounded-xl bg-white dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 shadow-lg">
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'text'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => addText()} disabled={activeBand === 'content'} title="Teks — klik tambah / seret ke band">
                                <Type className="w-4 h-4" />
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'image'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => fileRef.current?.click()} disabled={activeBand === 'content'} title="Gambar">
                                <ImageIcon className="w-4 h-4" />
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'grid'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => addGrid()} disabled={activeBand === 'content'} title="Grid statis">
                                <LayoutGrid className="w-4 h-4" />
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'rect'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => addRect()} disabled={activeBand === 'content'} title="Kotak">
                                <RectIcon className="w-4 h-4" />
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'line'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => addLine()} disabled={activeBand === 'content'} title="Garis">
                                <LineIcon className="w-4 h-4" />
                            </Button>
                        </div>
                        <div className="w-px h-6 bg-secondary-200 dark:bg-dark-600 mx-1" />
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'table'); }}>
                            <Button variant="ghost" size="icon" className="h-8 w-8 text-dark-600 dark:text-dark-300 hover:text-dark-900 dark:hover:text-dark-50" onClick={() => addTable()} disabled={!!bands.content.table} title={bands.content.table ? 'Tabel sudah ada' : 'Tabel item (band Konten)'}>
                                <Table2 className="w-4 h-4" />
                            </Button>
                        </div>
                    </div>

                    <input
                        ref={fileRef}
                        type="file"
                        accept="image/*"
                        className="hidden"
                        onChange={(e) => {
                            const file = e.target.files?.[0];
                            if (file) {
                                const p = pendingImg.current;
                                pendingImg.current = null;
                                addImage(file, p?.x, p?.y, p?.band ?? activeBand);
                            }
                            e.target.value = '';
                        }}
                    />
                </div>

                {/* ── KANAN: Inspector ── */}
                <aside className="w-72 shrink-0 border-l border-secondary-200 dark:border-dark-600 flex flex-col bg-white dark:bg-dark-900">
                    <PanelHeader
                        title={
                            selected
                                ? selected.type === 'text' ? 'Teks'
                                    : selected.type === 'image' ? 'Gambar'
                                    : selected.type === 'grid' ? 'Grid'
                                    : selected.type === 'rect' ? 'Kotak'
                                    : selected.type === 'line' ? 'Garis'
                                    : 'Tabel Item'
                                : 'Properti'
                        }
                        meta={selected ? undefined : 'Halaman'}
                    />

                    {!selected ? (
                        <div className="flex-1 overflow-auto px-3 py-3 space-y-3">
                            <p className="text-[11px] text-dark-400 dark:text-dark-500 leading-relaxed">
                                Klik elemen di kanvas untuk memilihnya. Atur margin halaman dan tinggi band di bawah.
                            </p>
                            <MarginSettingsPanel
                                margins={margins}
                                onChangeMargins={(m) => setMargins(m)}
                            />
                            <BandSettingsPanel
                                bands={bands}
                                onChangeBands={(patch) => { snapshot(); setBands((prev) => ({ ...prev, ...patch })); }}
                            />
                        </div>
                    ) : (
                        <div
                            className="flex-1 overflow-auto px-3"
                            onFocusCapture={(e) => {
                                const t = e.target as HTMLElement;
                                if (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA') snapshot();
                            }}
                        >
                            {selected.type === 'text' && (
                                <TextInspector
                                    el={selected as Text}
                                    contentRef={textContentRef}
                                    tokenCatalog={tokenCatalog}
                                    sampleData={sampleData}
                                    fieldMenu={fieldMenu}
                                    setFieldMenu={setFieldMenu}
                                    onInsertToken={insertToken}
                                    onUpdate={(patch) => update(selected.id, patch)}
                                    onSnapshot={snapshot}
                                    customFonts={customFonts}
                                />
                            )}

                            {selected.type === 'image' && (
                                <>
                                    <Section title="Ukuran">
                                        <Row label="Lebar">
                                            <NumField value={selected.width} onChange={(v) => setImgSize(selected as Img, 'width', v)} />
                                        </Row>
                                        <Row label="Tinggi">
                                            <NumField value={Math.round((selected as Img).height ?? 0)} onChange={(v) => setImgSize(selected as Img, 'height', v)} />
                                        </Row>
                                        <Row label="Rasio">
                                            <button
                                                onClick={() => { snapshot(); update(selected.id, { lockAspect: !(selected as Img).lockAspect }); }}
                                                className={`flex items-center gap-2 h-8 w-full rounded-lg border px-2.5 text-sm transition-colors ${
                                                    (selected as Img).lockAspect
                                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                                        : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                                }`}
                                            >
                                                {(selected as Img).lockAspect ? <Lock className="w-4 h-4" /> : <Unlock className="w-4 h-4" />}
                                                {(selected as Img).lockAspect ? 'Terkunci' : 'Bebas'}
                                            </button>
                                        </Row>
                                        <Button variant="zinc" size="sm" className="w-full" onClick={() => resetImage(selected as Img)}>
                                            <RotateCcw className="w-4 h-4" /> Reset ke ukuran asli
                                        </Button>
                                        <Button variant="zinc" size="sm" className="w-full" onClick={() => fileRef.current?.click()}>
                                            <ImageIcon className="w-4 h-4" /> Ganti gambar
                                        </Button>
                                    </Section>
                                    <Section title="Tampilan">
                                        <Row label="Opasitas">
                                            <div className="flex items-center gap-2">
                                                <input type="range" min={0} max={100} step={1}
                                                    value={(selected as Img).opacity ?? 100}
                                                    onChange={(e) => update(selected.id, { opacity: +e.target.value })}
                                                    className="flex-1 accent-primary-600"
                                                />
                                                <span className="text-xs tabular-nums w-8 text-right text-dark-500 dark:text-dark-400">{(selected as Img).opacity ?? 100}%</span>
                                            </div>
                                        </Row>
                                        <Row label="Radius">
                                            <NumField value={(selected as Img).borderRadius ?? 0} onChange={(v) => update(selected.id, { borderRadius: Math.max(0, v) })} unit="px" />
                                        </Row>
                                        <Row label="Border">
                                            <NumField value={(selected as Img).borderWidth ?? 0} onChange={(v) => update(selected.id, { borderWidth: Math.max(0, v) })} unit="px" />
                                        </Row>
                                        {((selected as Img).borderWidth ?? 0) > 0 && (
                                            <Row label="Warna border">
                                                <Swatch value={(selected as Img).borderColor ?? '#000000'} onChange={(v) => update(selected.id, { borderColor: v })} />
                                            </Row>
                                        )}
                                    </Section>
                                </>
                            )}

                            {selected.type === 'table' && (
                                <TableInspector
                                    el={selected as TableEl}
                                    catalog={itemColumnCatalog}
                                    onUpdate={(patch) => { snapshot(); setContentTable(patch); }}
                                    onUpdateColumn={(idx, patch) => { snapshot(); updateTableColumn(selected.id, idx, patch); }}
                                    onMoveColumn={(idx, dir) => { snapshot(); moveTableColumn(selected.id, idx, dir); }}
                                    onRemoveColumn={(idx) => removeTableColumn(selected.id, idx)}
                                    onAddColumn={(key) => addTableColumn(selected.id, key)}
                                />
                            )}

                            {selected.type === 'grid' && (
                                <GridInspector
                                    el={selected as GridEl}
                                    selectedCell={selectedCell}
                                    selectedRange={selectedRange}
                                    onAddRow={() => addGridRow(selected.id)}
                                    onRemoveRow={() => removeGridRow(selected.id)}
                                    onAddCol={() => addGridCol(selected.id)}
                                    onRemoveCol={() => removeGridCol(selected.id)}
                                    onUpdateGrid={(patch) => { snapshot(); updateGrid(selected.id, patch); }}
                                    onUpdateCell={(r, c, patch) => { snapshot(); updateGridCell(selected.id, r, c, patch); }}
                                    onMerge={(r1, c1, r2, c2) => mergeRange(selected.id, r1, c1, r2, c2)}
                                    onUnmerge={(row, col) => unmergeCell(selected.id, row, col)}
                                />
                            )}

                            {selected.type === 'rect' && (
                                <RectInspector
                                    el={selected as RectEl}
                                    onUpdate={(patch) => { snapshot(); update(selected.id, patch); }}
                                />
                            )}

                            {selected.type === 'line' && (
                                <LineInspector
                                    el={selected as LineEl}
                                    onUpdate={(patch) => { snapshot(); update(selected.id, patch); }}
                                />
                            )}

                            {/* Posisi (shared) */}
                            <Section title="Posisi">
                                <Row label="X">
                                    <NumField value={Math.round(selected.x)} onChange={(v) => update(selected.id, { x: v })} />
                                </Row>
                                <Row label="Y">
                                    <NumField value={Math.round(selected.y)} onChange={(v) => update(selected.id, { y: v })} />
                                </Row>
                                {(selected.type === 'table' || selected.type === 'grid') && (
                                    <Row label="Lebar">
                                        <NumField value={(selected as TableEl | GridEl).width} onChange={(v) => update(selected.id, { width: Math.max(100, v) })} />
                                    </Row>
                                )}
                                {selected.type === 'rect' && (
                                    <>
                                        <Row label="Lebar"><NumField value={(selected as RectEl).width} onChange={(v) => update(selected.id, { width: Math.max(4, v) })} /></Row>
                                        <Row label="Tinggi"><NumField value={(selected as RectEl).height} onChange={(v) => update(selected.id, { height: Math.max(4, v) })} /></Row>
                                    </>
                                )}
                                {selected.type === 'line' && (
                                    <Row label="Panjang"><NumField value={(selected as LineEl).length} onChange={(v) => update(selected.id, { length: Math.max(4, v) })} /></Row>
                                )}
                            </Section>

                            <Section title="">
                                {selected.type !== 'table' && (
                                    <Button variant="zinc" size="sm" className="w-full" onClick={() => duplicate(selected as BandEl)}>
                                        <Copy className="w-4 h-4" /> Gandakan <span className="text-xs opacity-60">Ctrl+D</span>
                                    </Button>
                                )}
                                <Button variant="red" size="sm" className="w-full" onClick={() => remove(selected.id)}>
                                    <Trash2 className="w-4 h-4" /> Hapus elemen
                                </Button>
                            </Section>
                        </div>
                    )}
                </aside>
            </div>
        </div>
    );
}

// ── Text Inspector ────────────────────────────────────────────────────────────

function TextInspector({
    el,
    contentRef,
    tokenCatalog,
    sampleData,
    fieldMenu,
    setFieldMenu,
    onInsertToken,
    onUpdate,
    onSnapshot,
    customFonts,
}: {
    el: Text;
    contentRef: React.RefObject<HTMLTextAreaElement | null>;
    tokenCatalog: { path: string; label: string }[];
    sampleData: Record<string, string>;
    fieldMenu: boolean;
    setFieldMenu: (v: boolean | ((o: boolean) => boolean)) => void;
    onInsertToken: (path: string) => void;
    onUpdate: (patch: Partial<Text>) => void;
    onSnapshot: () => void;
    customFonts: CustomFontEntry[];
}) {
    const hasBox = el.width !== undefined;

    // ── Upload font state ──────────────────────────────────────────────────────
    const [uploadOpen, setUploadOpen] = React.useState(false);
    const [uploadName, setUploadName] = React.useState('');
    const [uploadFile, setUploadFile] = React.useState<File | null>(null);
    const [uploading, setUploading] = React.useState(false);
    const uploadFileRef = React.useRef<HTMLInputElement>(null);

    const handleFontUpload = () => {
        if (!uploadName.trim() || !uploadFile) {
            return;
        }
        setUploading(true);
        const formData = new FormData();
        formData.append('name', uploadName.trim());
        formData.append('file', uploadFile);
        router.post('/settings/pdf-templates/custom-fonts', formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                toast.success(`Font "${uploadName}" berhasil diunggah.`);
                setUploadOpen(false);
                setUploadName('');
                setUploadFile(null);
                if (uploadFileRef.current) {
                    uploadFileRef.current.value = '';
                }
            },
            onError: (errors) => {
                const msg = Object.values(errors)[0] ?? 'Gagal mengunggah font.';
                toast.error(String(msg));
            },
            onFinish: () => setUploading(false),
        });
    };

    const toggleBtn = (active: boolean, onClick: () => void, title: string, children: React.ReactNode) => (
        <button
            onClick={() => { onSnapshot(); onClick(); }}
            title={title}
            className={`grid place-items-center h-8 w-8 rounded-lg border transition-colors ${
                active
                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                    : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
            }`}
        >
            {children}
        </button>
    );

    return (
        <>
            {/* ── Konten ── */}
            <Section title="Konten">
                <textarea
                    ref={contentRef}
                    value={el.content}
                    onChange={(e) => onUpdate({ content: e.target.value })}
                    rows={2}
                    className={`${inputCn} h-auto py-1.5 font-mono text-xs leading-relaxed`}
                />
                <div className="relative">
                    <Button variant="zinc" size="sm" className="w-full" onClick={() => setFieldMenu((o) => !o)}>
                        <Plus className="w-4 h-4" /> Sisipkan field
                    </Button>
                    {fieldMenu && (
                        <div className="absolute z-20 left-0 right-0 mt-1 max-h-56 overflow-auto rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 shadow-lg p-1">
                            {tokenCatalog.map((t) => (
                                <button
                                    key={t.path}
                                    onClick={() => { onInsertToken(t.path); setFieldMenu(false); }}
                                    className="w-full text-left px-2 py-1.5 rounded-md hover:bg-zinc-50 dark:hover:bg-dark-600"
                                >
                                    <div className="text-xs font-mono text-primary-600 dark:text-primary-400">{`{{${t.path}}}`}</div>
                                    <div className="text-[11px] text-dark-400 dark:text-dark-500 truncate">
                                        {sampleData[t.path] ?? t.label}
                                    </div>
                                </button>
                            ))}
                        </div>
                    )}
                </div>
                <p className="text-[11px] text-dark-400 dark:text-dark-500">
                    Token diganti data asli saat <strong>Preview</strong> / cetak.
                </p>
            </Section>

            {/* ── Font ── */}
            <Section title="Font">
                {/* Font family — curated + custom fonts in one <select>, plus upload */}
                <Row label="Jenis">
                    <select
                        value={el.fontFamily ?? 'Helvetica / Arial'}
                        onChange={(e) => onUpdate({ fontFamily: e.target.value as FontLabel })}
                        className={`${inputCn} pr-2`}
                    >
                        <optgroup label="Bawaan">
                            {FONT_MAP.map((f) => (
                                <option key={f.label} value={f.label} style={{ fontFamily: f.cssFontStack }}>
                                    {f.label}
                                </option>
                            ))}
                        </optgroup>
                        {customFonts.length > 0 && (
                            <optgroup label="Font kustom">
                                {customFonts.map((f) => (
                                    <option key={f.name} value={f.name} style={{ fontFamily: `"${f.name}"` }}>
                                        {f.name}
                                    </option>
                                ))}
                            </optgroup>
                        )}
                    </select>
                </Row>

                {/* Upload font control */}
                <div className="space-y-1.5">
                    <button
                        onClick={() => setUploadOpen((o) => !o)}
                        className="flex items-center gap-1.5 text-[11px] text-primary-600 dark:text-primary-400 hover:underline"
                    >
                        <Upload className="w-3 h-3" />
                        {uploadOpen ? 'Batal unggah' : '+ Unggah font (.ttf)'}
                    </button>

                    {uploadOpen && (
                        <div className="space-y-2 rounded-lg border border-secondary-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700 p-2.5">
                            <div>
                                <label className="block text-[11px] text-dark-500 dark:text-dark-400 mb-1">Nama font *</label>
                                <input
                                    type="text"
                                    placeholder="mis. Poppins"
                                    value={uploadName}
                                    onChange={(e) => setUploadName(e.target.value)}
                                    className={`${inputCn} h-7 text-xs`}
                                />
                            </div>
                            <div>
                                <label className="block text-[11px] text-dark-500 dark:text-dark-400 mb-1">File .ttf *</label>
                                <input
                                    ref={uploadFileRef}
                                    type="file"
                                    accept=".ttf"
                                    onChange={(e) => setUploadFile(e.target.files?.[0] ?? null)}
                                    className="block w-full text-xs text-dark-700 dark:text-dark-300 file:mr-2 file:rounded file:border-0 file:bg-primary-50 file:px-2 file:py-0.5 file:text-[11px] file:font-medium file:text-primary-700 dark:file:bg-primary-900/30 dark:file:text-primary-300 cursor-pointer"
                                />
                            </div>
                            <Button
                                variant="primary"
                                size="sm"
                                className="w-full"
                                disabled={!uploadName.trim() || !uploadFile || uploading}
                                onClick={handleFontUpload}
                            >
                                <Upload className="w-3.5 h-3.5" />
                                {uploading ? 'Mengunggah…' : 'Unggah font'}
                            </Button>
                        </div>
                    )}

                    {/* Installed custom fonts list with delete */}
                    {customFonts.length > 0 && (
                        <div className="space-y-1 mt-1">
                            {customFonts.map((f) => (
                                <div
                                    key={f.id}
                                    className="flex items-center gap-1.5 rounded-md px-2 py-1 bg-zinc-50 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600"
                                >
                                    <span
                                        className="flex-1 truncate text-[11px] text-dark-700 dark:text-dark-300"
                                        style={{ fontFamily: `"${f.name}"` }}
                                    >
                                        {f.name}
                                    </span>
                                    <button
                                        onClick={() =>
                                            router.delete(`/settings/pdf-templates/custom-fonts/${f.id}`, {
                                                preserveScroll: true,
                                                onSuccess: () => toast.success(`Font "${f.name}" dihapus.`),
                                            })
                                        }
                                        className="grid place-items-center h-5 w-5 rounded text-dark-400 hover:text-red-500 dark:hover:text-red-400 transition"
                                        title="Hapus font"
                                    >
                                        <Trash2 className="w-3 h-3" />
                                    </button>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
                {/* Size + color */}
                <Row label="Ukuran">
                    <NumField value={el.fontSize} onChange={(v) => onUpdate({ fontSize: Math.max(4, v) })} unit="px" />
                </Row>
                <Row label="Warna teks">
                    <Swatch value={el.color} onChange={(v) => onUpdate({ color: v })} />
                </Row>
                {/* Style toggles: B I U S */}
                <Row label="Gaya">
                    <div className="flex gap-1">
                        {toggleBtn(el.bold, () => onUpdate({ bold: !el.bold }), 'Tebal (Bold)', <BoldIcon className="w-4 h-4" />)}
                        {toggleBtn(el.italic ?? false, () => onUpdate({ italic: !el.italic }), 'Miring (Italic)', <Italic className="w-4 h-4" />)}
                        {toggleBtn(el.underline ?? false, () => onUpdate({ underline: !el.underline }), 'Garis bawah', <Underline className="w-4 h-4" />)}
                        {toggleBtn(el.strikethrough ?? false, () => onUpdate({ strikethrough: !el.strikethrough }), 'Garis tengah', <Strikethrough className="w-4 h-4" />)}
                    </div>
                </Row>
                {/* Highlight */}
                <Row label="Sorot">
                    <div className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={!!el.highlight}
                            onChange={(e) => onUpdate({ highlight: e.target.checked ? '#fef08a' : null })}
                            className="accent-primary-600"
                        />
                        {el.highlight && (
                            <Swatch value={el.highlight} onChange={(v) => onUpdate({ highlight: v })} />
                        )}
                        {!el.highlight && (
                            <span className="text-xs text-dark-400 dark:text-dark-500">Tidak aktif</span>
                        )}
                    </div>
                </Row>
            </Section>

            {/* ── Paragraf ── */}
            <Section title="Paragraf">
                {/* H-align */}
                <Row label="Rata H">
                    <div className="flex gap-1">
                        {([
                            { value: 'left',    icon: <AlignLeft className="w-3.5 h-3.5" />,    title: 'Kiri' },
                            { value: 'center',  icon: <AlignCenter className="w-3.5 h-3.5" />,  title: 'Tengah' },
                            { value: 'right',   icon: <AlignRight className="w-3.5 h-3.5" />,   title: 'Kanan' },
                            { value: 'justify', icon: <AlignJustify className="w-3.5 h-3.5" />, title: 'Rata penuh' },
                        ] as const).map(({ value, icon, title }) => (
                            <button
                                key={value}
                                onClick={() => onUpdate({ align: value })}
                                title={title}
                                className={`flex-1 grid place-items-center h-8 rounded-lg border transition-colors ${
                                    (el.align ?? 'left') === value
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                                        : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                }`}
                            >
                                {icon}
                            </button>
                        ))}
                    </div>
                </Row>
                {/* V-align (only meaningful when height is set) */}
                <Row label="Rata V">
                    <div className="flex gap-1">
                        {([
                            { value: 'top',    icon: <AlignStartVertical className="w-3.5 h-3.5" />,  title: 'Atas' },
                            { value: 'middle', icon: <AlignCenterVertical className="w-3.5 h-3.5" />, title: 'Tengah' },
                            { value: 'bottom', icon: <AlignEndVertical className="w-3.5 h-3.5" />,   title: 'Bawah' },
                        ] as const).map(({ value, icon, title }) => (
                            <button
                                key={value}
                                onClick={() => onUpdate({ valign: value })}
                                title={title}
                                className={`flex-1 grid place-items-center h-8 rounded-lg border transition-colors ${
                                    (el.valign ?? 'top') === value
                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                                        : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                }`}
                            >
                                {icon}
                            </button>
                        ))}
                    </div>
                </Row>
                <Row label="Line-height">
                    <div className="relative">
                        <input
                            type="number" step={0.1} min={0.5} max={5}
                            value={el.lineHeight ?? 1.2}
                            onChange={(e) => onUpdate({ lineHeight: Math.max(0.5, +e.target.value) })}
                            className={`${inputCn} pr-8`}
                        />
                        <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] text-dark-400 dark:text-dark-500 pointer-events-none">×</span>
                    </div>
                </Row>
                <Row label="Spasi huruf">
                    <NumField value={el.letterSpacing ?? 0} onChange={(v) => onUpdate({ letterSpacing: v })} unit="px" />
                </Row>
            </Section>

            {/* ── Kotak ── */}
            <Section title="Kotak">
                <Row label="Lebar">
                    <div className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={hasBox}
                            onChange={(e) => {
                                onSnapshot();
                                onUpdate(e.target.checked ? { width: 200 } : { width: undefined, height: undefined });
                            }}
                            className="accent-primary-600 shrink-0"
                            title="Aktifkan mode kotak teks"
                        />
                        {hasBox ? (
                            <NumField value={el.width!} onChange={(v) => onUpdate({ width: Math.max(20, v) })} unit="px" />
                        ) : (
                            <span className="text-xs text-dark-400 dark:text-dark-500">Otomatis (lebar mengikuti teks)</span>
                        )}
                    </div>
                </Row>
                {hasBox && (
                    <>
                        <Row label="Tinggi">
                            <div className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={el.height !== undefined}
                                    onChange={(e) => {
                                        onSnapshot();
                                        onUpdate({ height: e.target.checked ? 40 : undefined });
                                    }}
                                    className="accent-primary-600 shrink-0"
                                />
                                {el.height !== undefined ? (
                                    <NumField value={el.height} onChange={(v) => onUpdate({ height: Math.max(10, v) })} unit="px" />
                                ) : (
                                    <span className="text-xs text-dark-400 dark:text-dark-500">Otomatis</span>
                                )}
                            </div>
                        </Row>
                        <Row label="Padding">
                            <NumField value={el.padding ?? 0} onChange={(v) => onUpdate({ padding: Math.max(0, v) })} unit="px" />
                        </Row>
                        <Row label="Border">
                            <NumField
                                value={el.borderWidth ?? 0}
                                onChange={(v) => onUpdate({ borderWidth: Math.max(0, v) })}
                                unit="px"
                            />
                        </Row>
                        {(el.borderWidth ?? 0) > 0 && (
                            <Row label="Warna border">
                                <Swatch value={el.borderColor ?? '#000000'} onChange={(v) => onUpdate({ borderColor: v })} />
                            </Row>
                        )}
                        <Row label="Isi kotak">
                            <div className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={!!el.fill}
                                    onChange={(e) => onUpdate({ fill: e.target.checked ? '#ffffff' : null })}
                                    className="accent-primary-600"
                                />
                                {el.fill && (
                                    <Swatch value={el.fill} onChange={(v) => onUpdate({ fill: v })} />
                                )}
                                {!el.fill && (
                                    <span className="text-xs text-dark-400 dark:text-dark-500">Transparan</span>
                                )}
                            </div>
                        </Row>
                    </>
                )}
            </Section>
        </>
    );
}

// ── Grid canvas render ────────────────────────────────────────────────────────

interface GridCanvasProps {
    el: GridEl;
    preview: boolean;
    resolve: (text: string) => string;
    selectedCell: { row: number; col: number } | null;
    editingCell: { row: number; col: number } | null;
    selectedRange: { r1: number; c1: number; r2: number; c2: number } | null;
    rangeAnchor: { row: number; col: number } | null;
    onCellPointerDown: (row: number, col: number, e: React.PointerEvent) => void;
    onCellDoubleClick: (row: number, col: number) => void;
    onCellCommit: (row: number, col: number, text: string) => void;
    onCellEscape: (row: number, col: number) => void;
}

function GridCanvas({
    el, preview, resolve,
    selectedCell, editingCell,
    selectedRange, rangeAnchor,
    onCellPointerDown, onCellDoubleClick, onCellCommit, onCellEscape,
}: GridCanvasProps) {
    const bw = el.border.width;
    const bc = el.border.color;

    return (
        <table
            style={{
                width: el.width,
                borderCollapse: 'collapse',
                tableLayout: 'fixed',
                fontFamily: 'Helvetica, Arial, sans-serif',
                fontSize: 10,
                userSelect: 'none',
            }}
        >
            <tbody>
                {el.cells.map((rowCells, ri) => (
                    <tr key={ri}>
                        {rowCells.map((cell, ci) => {
                            if (cell.merged) return null;

                            const isSel = selectedCell?.row === ri && selectedCell?.col === ci;
                            const isEditing = editingCell?.row === ri && editingCell?.col === ci;
                            const displayText = preview ? resolve(cell.text) : cell.text;

                            const inRange = selectedRange
                                ? (ri >= selectedRange.r1 && ri <= selectedRange.r2 && ci >= selectedRange.c1 && ci <= selectedRange.c2)
                                : false;
                            const isAnchor = rangeAnchor?.row === ri && rangeAnchor?.col === ci;

                            return (
                                <td
                                    key={ci}
                                    colSpan={cell.colSpan ?? 1}
                                    rowSpan={cell.rowSpan ?? 1}
                                    onPointerDown={(e) => onCellPointerDown(ri, ci, e)}
                                    onDoubleClick={() => onCellDoubleClick(ri, ci)}
                                    style={{
                                        width: el.colWidths[ci] ?? 'auto',
                                        height: GRID_ROW_H,
                                        border: `${bw}px solid ${bc}`,
                                        padding: '2px 4px',
                                        textAlign: cell.align,
                                        fontWeight: cell.bold ? 700 : 400,
                                        color: cell.color,
                                        backgroundColor: inRange
                                            ? '#eff6ff'
                                            : (cell.fill ?? 'transparent'),
                                        outline: isAnchor ? '2px solid #3b82f6' : inRange ? '1px solid #93c5fd' : 'none',
                                        outlineOffset: -2,
                                        verticalAlign: 'middle',
                                        overflow: 'hidden',
                                        cursor: isEditing ? 'text' : 'default',
                                        position: 'relative',
                                    }}
                                >
                                    {isEditing ? (
                                        <GridCellEditor
                                            initial={cell.text}
                                            onCommit={(v) => onCellCommit(ri, ci, v)}
                                            onEscape={() => onCellEscape(ri, ci)}
                                            style={{
                                                fontWeight: cell.bold ? 700 : 400,
                                                color: cell.color,
                                                textAlign: cell.align,
                                            }}
                                        />
                                    ) : (
                                        <span style={{ whiteSpace: 'nowrap', overflow: 'hidden', display: 'block' }}>
                                            {displayText}
                                        </span>
                                    )}
                                </td>
                            );
                        })}
                    </tr>
                ))}
            </tbody>
        </table>
    );
}

/** Inline editor for a single grid cell — contentEditable span. */
function GridCellEditor({
    initial, onCommit, onEscape, style,
}: {
    initial: string;
    onCommit: (v: string) => void;
    onEscape: () => void;
    style?: React.CSSProperties;
}) {
    const ref = React.useRef<HTMLSpanElement>(null);

    React.useEffect(() => {
        if (!ref.current) return;
        ref.current.textContent = initial;
        ref.current.focus();
        // Select all text
        const range = document.createRange();
        range.selectNodeContents(ref.current);
        const sel = window.getSelection();
        sel?.removeAllRanges();
        sel?.addRange(range);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return (
        <span
            ref={ref}
            contentEditable
            suppressContentEditableWarning
            onBlur={(e) => onCommit(e.currentTarget.textContent ?? '')}
            onKeyDown={(e) => {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); e.currentTarget.blur(); }
                else if (e.key === 'Escape') {
                    e.currentTarget.textContent = initial;
                    e.currentTarget.blur();
                    onEscape();
                }
                e.stopPropagation(); // don't bubble to canvas Delete handler
            }}
            onPointerDown={(e) => e.stopPropagation()}
            style={{
                display: 'block',
                outline: 'none',
                whiteSpace: 'nowrap',
                width: '100%',
                ...style,
            }}
        />
    );
}

// ── Grid Inspector ─────────────────────────────────────────────────────────────

function GridInspector({
    el,
    selectedCell,
    selectedRange,
    onAddRow, onRemoveRow, onAddCol, onRemoveCol,
    onUpdateGrid, onUpdateCell,
    onMerge, onUnmerge,
}: {
    el: GridEl;
    selectedCell: { row: number; col: number } | null;
    selectedRange: { r1: number; c1: number; r2: number; c2: number } | null;
    onAddRow: () => void;
    onRemoveRow: () => void;
    onAddCol: () => void;
    onRemoveCol: () => void;
    onUpdateGrid: (patch: Partial<GridEl>) => void;
    onUpdateCell: (row: number, col: number, patch: Partial<GridCell>) => void;
    onMerge: (r1: number, c1: number, r2: number, c2: number) => void;
    onUnmerge: (row: number, col: number) => void;
}) {
    const cell = selectedCell != null ? el.cells[selectedCell.row]?.[selectedCell.col] : null;

    return (
        <>
            {/* ── Grid structure ── */}
            <Section title="Struktur Grid">
                {/* Rows */}
                <div className="flex items-center gap-2">
                    <span className="w-12 shrink-0 text-xs text-dark-500 dark:text-dark-400">Baris</span>
                    <div className="flex items-center gap-1 flex-1">
                        <button
                            onClick={onRemoveRow}
                            disabled={el.rows <= 1}
                            className="grid place-items-center h-7 w-7 rounded-lg border border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700 disabled:opacity-30 transition"
                            title="Hapus baris terakhir"
                        >
                            <Minus className="w-3.5 h-3.5" />
                        </button>
                        <span className="flex-1 text-center text-sm tabular-nums font-medium text-dark-900 dark:text-dark-50">{el.rows}</span>
                        <button
                            onClick={onAddRow}
                            className="grid place-items-center h-7 w-7 rounded-lg border border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700 transition"
                            title="Tambah baris"
                        >
                            <Plus className="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
                {/* Cols */}
                <div className="flex items-center gap-2">
                    <span className="w-12 shrink-0 text-xs text-dark-500 dark:text-dark-400">Kolom</span>
                    <div className="flex items-center gap-1 flex-1">
                        <button
                            onClick={onRemoveCol}
                            disabled={el.cols <= 1}
                            className="grid place-items-center h-7 w-7 rounded-lg border border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700 disabled:opacity-30 transition"
                            title="Hapus kolom terakhir"
                        >
                            <Minus className="w-3.5 h-3.5" />
                        </button>
                        <span className="flex-1 text-center text-sm tabular-nums font-medium text-dark-900 dark:text-dark-50">{el.cols}</span>
                        <button
                            onClick={onAddCol}
                            className="grid place-items-center h-7 w-7 rounded-lg border border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700 transition"
                            title="Tambah kolom"
                        >
                            <Plus className="w-3.5 h-3.5" />
                        </button>
                    </div>
                </div>
            </Section>

            {/* ── Border ── */}
            <Section title="Garis Border">
                <Row label="Tebal">
                    <NumField
                        value={el.border.width}
                        onChange={(v) => onUpdateGrid({ border: { ...el.border, width: Math.max(0, v) } })}
                        unit="px"
                    />
                </Row>
                <Row label="Warna">
                    <Swatch
                        value={el.border.color}
                        onChange={(v) => onUpdateGrid({ border: { ...el.border, color: v } })}
                    />
                </Row>
            </Section>

            {/* ── Cell properties (shown when cell is selected) ── */}
            {cell != null && selectedCell != null ? (
                <Section title={`Sel [${selectedCell.row + 1}, ${selectedCell.col + 1}]`}>
                    {/* Alignment */}
                    <Row label="Rata">
                        <div className="flex gap-1">
                            {([
                                { value: 'left', icon: <AlignLeft className="w-3.5 h-3.5" />, title: 'Kiri' },
                                { value: 'center', icon: <AlignCenter className="w-3.5 h-3.5" />, title: 'Tengah' },
                                { value: 'right', icon: <AlignRight className="w-3.5 h-3.5" />, title: 'Kanan' },
                            ] as const).map(({ value, icon, title }) => (
                                <button
                                    key={value}
                                    onClick={() => onUpdateCell(selectedCell.row, selectedCell.col, { align: value })}
                                    title={title}
                                    className={`flex-1 grid place-items-center h-7 rounded-lg border transition-colors ${
                                        cell.align === value
                                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                                            : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                    }`}
                                >
                                    {icon}
                                </button>
                            ))}
                        </div>
                    </Row>
                    {/* Bold */}
                    <Row label="Tebal">
                        <button
                            onClick={() => onUpdateCell(selectedCell.row, selectedCell.col, { bold: !cell.bold })}
                            className={`grid place-items-center h-8 w-8 rounded-lg border transition-colors ${
                                cell.bold
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                                    : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                            }`}
                            title="Bold"
                        >
                            <BoldIcon className="w-4 h-4" />
                        </button>
                    </Row>
                    {/* Text color */}
                    <Row label="Warna">
                        <Swatch
                            value={cell.color}
                            onChange={(v) => onUpdateCell(selectedCell.row, selectedCell.col, { color: v })}
                        />
                    </Row>
                    {/* Fill color */}
                    <Row label="Isi">
                        <Swatch
                            value={cell.fill ?? '#ffffff'}
                            onChange={(v) => onUpdateCell(selectedCell.row, selectedCell.col, { fill: v })}
                        />
                    </Row>
                    {/* Merge/unmerge */}
                    {((cell.colSpan ?? 1) > 1 || (cell.rowSpan ?? 1) > 1) && (
                        <Button variant="zinc" size="sm" className="w-full mt-1"
                            onClick={() => onUnmerge(selectedCell.row, selectedCell.col)}>
                            Pisahkan sel
                        </Button>
                    )}
                </Section>
            ) : (
                <Section title="Sel">
                    {selectedRange && (selectedRange.r1 !== selectedRange.r2 || selectedRange.c1 !== selectedRange.c2) ? (
                        <>
                            <p className="text-[11px] text-dark-400 dark:text-dark-500 text-center py-1">
                                {`${selectedRange.r2 - selectedRange.r1 + 1} baris × ${selectedRange.c2 - selectedRange.c1 + 1} kolom dipilih`}
                            </p>
                            <Button variant="primary" size="sm" className="w-full"
                                onClick={() => onMerge(selectedRange.r1, selectedRange.c1, selectedRange.r2, selectedRange.c2)}>
                                Gabungkan sel
                            </Button>
                        </>
                    ) : (
                        <p className="text-[11px] text-dark-400 dark:text-dark-500 text-center py-1">
                            Klik sel di kanvas untuk mengatur propertinya.
                        </p>
                    )}
                </Section>
            )}
        </>
    );
}

// ── Table canvas render ────────────────────────────────────────────────────────

function TablePreview({
    el, rows,
}: { el: TableEl; rows: Array<Record<string, string>> | null }) {
    const placeholderRows = rows ?? Array.from({ length: TABLE_PLACEHOLDER_ROWS }, (_, i) => {
        const row: Record<string, string> = {};
        el.columns.forEach((c) => {
            row[c.key] = c.format === 'rupiah'
                ? (i === 0 ? 'Rp 1.500.000' : i === 1 ? 'Rp 2.000.000' : 'Rp 500.000')
                : c.format === 'number'
                    ? (c.key === 'no' ? String(i + 1) : (i === 0 ? '2' : i === 1 ? '1' : '3'))
                    : (c.key === 'description' ? `Item Contoh ${i + 1}` : c.key === 'unit' ? 'pcs' : '—');
        });
        return row;
    });

    const alignClass = (align: string) => align === 'right' ? 'text-right' : align === 'center' ? 'text-center' : 'text-left';

    return (
        <div className="w-full h-full overflow-hidden rounded border border-blue-200 dark:border-blue-900/40 bg-white dark:bg-dark-800 select-none text-[10px]" style={{ fontFamily: 'Helvetica, Arial, sans-serif' }}>
            {/* Group header row */}
            {(el.headerGroups?.length ?? 0) > 0 && (
                <div className="flex bg-slate-200 dark:bg-dark-600 border-b border-slate-300 dark:border-dark-500" style={{ height: TABLE_HEADER_H }}>
                    {(el.headerGroups ?? []).map((g, gi) => {
                        const startCol = (el.headerGroups ?? []).slice(0, gi).reduce((s, g2) => s + (g2.span ?? 1), 0);
                        const pxWidth = el.columns.slice(startCol, startCol + (g.span ?? 1)).reduce((s, c) => s + c.width, 0);
                        const alignClass = g.align === 'right' ? 'text-right' : g.align === 'left' ? 'text-left' : 'text-center';
                        return (
                            <div
                                key={gi}
                                className={`shrink-0 px-2 flex items-center font-semibold text-dark-800 dark:text-dark-100 truncate ${alignClass} border-r border-slate-300 dark:border-dark-500 last:border-r-0`}
                                style={{ width: pxWidth }}
                            >
                                {g.label}
                            </div>
                        );
                    })}
                </div>
            )}
            {/* Header row */}
            <div className="flex bg-slate-100 dark:bg-dark-700 border-b border-slate-200 dark:border-dark-600" style={{ height: TABLE_HEADER_H }}>
                {el.columns.map((col) => (
                    <div
                        key={col.key}
                        className={`shrink-0 px-2 flex items-center font-semibold text-dark-700 dark:text-dark-200 truncate ${alignClass(col.align)} border-r border-slate-200 dark:border-dark-600 last:border-r-0`}
                        style={{ width: col.width }}
                    >
                        {col.label}
                    </div>
                ))}
            </div>
            {/* Data rows */}
            {placeholderRows.map((row, i) => (
                <div
                    key={i}
                    className={`flex border-b border-slate-100 dark:border-dark-700 last:border-b-0 ${i % 2 === 1 ? 'bg-slate-50 dark:bg-dark-750' : ''}`}
                    style={{ height: TABLE_ROW_H }}
                >
                    {el.columns.map((col) => (
                        <div
                            key={col.key}
                            className={`shrink-0 px-2 flex items-center text-dark-700 dark:text-dark-300 truncate ${alignClass(col.align)} border-r border-slate-100 dark:border-dark-700 last:border-r-0`}
                            style={{ width: col.width }}
                        >
                            {row[col.key] ?? ''}
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
}

// ── Table Inspector ────────────────────────────────────────────────────────────

function TableInspector({
    el, catalog, onUpdate, onUpdateColumn, onMoveColumn, onRemoveColumn, onAddColumn,
}: {
    el: TableEl;
    catalog: ItemColumnEntry[];
    onUpdate: (patch: Partial<TableEl>) => void;
    onUpdateColumn: (idx: number, patch: Partial<TableColumn>) => void;
    onMoveColumn: (idx: number, dir: -1 | 1) => void;
    onRemoveColumn: (idx: number) => void;
    onAddColumn: (key: string) => void;
}) {
    const [addOpen, setAddOpen] = React.useState(false);
    const usedKeys = new Set(el.columns.map((c) => c.key));
    const available = catalog.filter((c) => !usedKeys.has(c.key));

    return (
        <>
            {/* Footer sum toggle */}
            <Section title="Opsi Tabel">
                <Row label="Total baris">
                    <button
                        onClick={() => onUpdate({ showFooterSum: !el.showFooterSum })}
                        className={`flex items-center gap-2 h-8 w-full rounded-lg border px-2.5 text-sm transition-colors ${
                            el.showFooterSum
                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                        }`}
                    >
                        {el.showFooterSum ? '✓ Tampilkan' : 'Sembunyikan'}
                    </button>
                </Row>
            </Section>

            {/* Header Groups */}
            <Section title="Grup Header">
                {(el.headerGroups ?? []).length > 0 && (() => {
                    const totalSpan = (el.headerGroups ?? []).reduce((s, g) => s + (g.span ?? 1), 0);
                    const mismatch = totalSpan !== el.columns.length;
                    return (
                        <>
                            {mismatch && (
                                <p className="text-[11px] text-yellow-600 dark:text-yellow-400 mb-1">
                                    Jumlah span ({totalSpan}) ≠ jumlah kolom ({el.columns.length})
                                </p>
                            )}
                            <div className="space-y-1.5">
                                {(el.headerGroups ?? []).map((g, gi) => (
                                    <div key={gi} className="rounded-lg border border-secondary-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700 p-2 space-y-1.5">
                                        <div className="flex items-center gap-1.5">
                                            <span className="w-10 shrink-0 text-[11px] text-dark-500 dark:text-dark-400">Label</span>
                                            <input
                                                type="text"
                                                value={g.label}
                                                onChange={(e) => {
                                                    const groups = [...(el.headerGroups ?? [])];
                                                    groups[gi] = { ...groups[gi], label: e.target.value };
                                                    onUpdate({ headerGroups: groups });
                                                }}
                                                className={`${inputCn} text-xs h-7 flex-1`}
                                            />
                                            <button
                                                onClick={() => {
                                                    const groups = (el.headerGroups ?? []).filter((_, i) => i !== gi);
                                                    onUpdate({ headerGroups: groups });
                                                }}
                                                className="grid place-items-center h-6 w-6 rounded text-dark-400 hover:text-red-500 dark:hover:text-red-400 transition"
                                                title="Hapus grup"
                                            >
                                                <Trash2 className="w-3 h-3" />
                                            </button>
                                        </div>
                                        <div className="flex items-center gap-1.5">
                                            <span className="w-10 shrink-0 text-[11px] text-dark-500 dark:text-dark-400">Span</span>
                                            <input
                                                type="number"
                                                min={1}
                                                value={g.span ?? 1}
                                                onChange={(e) => {
                                                    const groups = [...(el.headerGroups ?? [])];
                                                    groups[gi] = { ...groups[gi], span: Math.max(1, +e.target.value) };
                                                    onUpdate({ headerGroups: groups });
                                                }}
                                                className={`${inputCn} h-7 text-xs w-16`}
                                            />
                                            <div className="flex gap-1 ml-auto">
                                                {(['left', 'center', 'right'] as const).map((a) => (
                                                    <button
                                                        key={a}
                                                        onClick={() => {
                                                            const groups = [...(el.headerGroups ?? [])];
                                                            groups[gi] = { ...groups[gi], align: a };
                                                            onUpdate({ headerGroups: groups });
                                                        }}
                                                        className={`px-1.5 py-0.5 rounded text-[11px] border transition-colors ${
                                                            (g.align ?? 'center') === a
                                                                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                                                : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-600'
                                                        }`}
                                                    >
                                                        {a === 'left' ? '⬅' : a === 'center' ? '↔' : '➡'}
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    );
                })()}
                <Button variant="zinc" size="sm" className="w-full mt-1"
                    onClick={() => onUpdate({ headerGroups: [...(el.headerGroups ?? []), { label: '', span: 1, align: 'center' }] })}>
                    <Plus className="w-4 h-4" /> Tambah Grup
                </Button>
            </Section>

            {/* Column list */}
            <Section title="Kolom">
                <div className="space-y-1.5">
                    {el.columns.map((col, idx) => (
                        <div key={col.key} className="rounded-lg border border-secondary-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-700 overflow-hidden">
                            {/* Column header row */}
                            <div className="flex items-center gap-1.5 px-2 py-1.5">
                                <GripHorizontal className="w-3.5 h-3.5 shrink-0 text-dark-400 dark:text-dark-500" />
                                <span className="flex-1 text-xs font-medium text-dark-700 dark:text-dark-300 truncate">{col.label}</span>
                                <button
                                    disabled={idx === 0}
                                    onClick={() => onMoveColumn(idx, -1)}
                                    className="grid place-items-center h-5 w-5 rounded text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 disabled:opacity-30"
                                    title="Pindah ke atas"
                                >
                                    <ChevronUp className="w-3.5 h-3.5" />
                                </button>
                                <button
                                    disabled={idx === el.columns.length - 1}
                                    onClick={() => onMoveColumn(idx, 1)}
                                    className="grid place-items-center h-5 w-5 rounded text-dark-400 hover:text-dark-700 dark:hover:text-dark-200 disabled:opacity-30"
                                    title="Pindah ke bawah"
                                >
                                    <ChevronDown className="w-3.5 h-3.5" />
                                </button>
                                <button
                                    onClick={() => onRemoveColumn(idx)}
                                    disabled={el.columns.length <= 1}
                                    className="grid place-items-center h-5 w-5 rounded text-dark-400 hover:text-red-500 dark:hover:text-red-400 disabled:opacity-30 transition"
                                    title="Hapus kolom"
                                >
                                    <Trash2 className="w-3 h-3" />
                                </button>
                            </div>
                            {/* Column detail fields */}
                            <div className="px-2 pb-2 space-y-1.5 border-t border-secondary-200 dark:border-dark-600 pt-1.5">
                                <div className="flex items-center gap-1.5">
                                    <span className="w-14 shrink-0 text-[11px] text-dark-500 dark:text-dark-400">Label</span>
                                    <input
                                        type="text"
                                        value={col.label}
                                        onChange={(e) => onUpdateColumn(idx, { label: e.target.value })}
                                        className={`${inputCn} text-xs h-7`}
                                    />
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <span className="w-14 shrink-0 text-[11px] text-dark-500 dark:text-dark-400">Lebar</span>
                                    <div className="relative flex-1">
                                        <input
                                            type="number"
                                            value={col.width}
                                            min={20}
                                            onChange={(e) => onUpdateColumn(idx, { width: Math.max(20, +e.target.value) })}
                                            className={`${inputCn} h-7 text-xs pr-7`}
                                        />
                                        <span className="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] text-dark-400 dark:text-dark-500 pointer-events-none">px</span>
                                    </div>
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <span className="w-14 shrink-0 text-[11px] text-dark-500 dark:text-dark-400">Rata</span>
                                    <div className="flex gap-1">
                                        {(['left', 'center', 'right'] as const).map((a) => (
                                            <button
                                                key={a}
                                                onClick={() => onUpdateColumn(idx, { align: a })}
                                                className={`px-2 py-0.5 rounded text-[11px] border transition-colors ${
                                                    col.align === a
                                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                                        : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-600'
                                                }`}
                                            >
                                                {a === 'left' ? '⬅' : a === 'center' ? '↔' : '➡'}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Add column button */}
                {available.length > 0 && (
                    <div className="relative mt-1">
                        <Button variant="zinc" size="sm" className="w-full" onClick={() => setAddOpen((o) => !o)}>
                            <Plus className="w-4 h-4" /> Tambah kolom
                        </Button>
                        {addOpen && (
                            <div className="absolute z-20 left-0 right-0 mt-1 rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-700 shadow-lg p-1">
                                {available.map((c) => (
                                    <button
                                        key={c.key}
                                        onClick={() => { onAddColumn(c.key); setAddOpen(false); }}
                                        className="w-full text-left px-2 py-1.5 rounded-md hover:bg-zinc-50 dark:hover:bg-dark-600"
                                    >
                                        <div className="text-xs font-medium text-dark-700 dark:text-dark-300">{c.label}</div>
                                        <div className="text-[11px] text-dark-400 dark:text-dark-500">{c.format} · {c.align}</div>
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                )}
            </Section>
        </>
    );
}

// ── Rect Inspector ────────────────────────────────────────────────────────────

function RectInspector({
    el,
    onUpdate,
}: {
    el: RectEl;
    onUpdate: (patch: Partial<RectEl>) => void;
}) {
    return (
        <>
            <Section title="Ukuran">
                <Row label="Lebar">
                    <NumField value={el.width} onChange={(v) => onUpdate({ width: Math.max(4, v) })} />
                </Row>
                <Row label="Tinggi">
                    <NumField value={el.height} onChange={(v) => onUpdate({ height: Math.max(4, v) })} />
                </Row>
            </Section>
            <Section title="Tampilan">
                <Row label="Isi">
                    <div className="flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={!!el.fill}
                            onChange={(e) => onUpdate({ fill: e.target.checked ? '#ffffff' : null })}
                            className="accent-primary-600"
                        />
                        {el.fill ? (
                            <Swatch value={el.fill} onChange={(v) => onUpdate({ fill: v })} />
                        ) : (
                            <span className="text-xs text-dark-400 dark:text-dark-500">Transparan</span>
                        )}
                    </div>
                </Row>
                <Row label="Border">
                    <NumField value={el.borderWidth} onChange={(v) => onUpdate({ borderWidth: Math.max(0, v) })} unit="px" />
                </Row>
                {el.borderWidth > 0 && (
                    <Row label="Warna border">
                        <Swatch value={el.borderColor} onChange={(v) => onUpdate({ borderColor: v })} />
                    </Row>
                )}
                <Row label="Radius">
                    <NumField value={el.borderRadius} onChange={(v) => onUpdate({ borderRadius: Math.max(0, v) })} unit="px" />
                </Row>
            </Section>
        </>
    );
}

// ── Line Inspector ────────────────────────────────────────────────────────────

function LineInspector({
    el,
    onUpdate,
}: {
    el: LineEl;
    onUpdate: (patch: Partial<LineEl>) => void;
}) {
    return (
        <>
            <Section title="Orientasi">
                <div className="flex gap-1">
                    {([
                        { value: 'h' as const, label: 'Horizontal' },
                        { value: 'v' as const, label: 'Vertikal' },
                    ]).map(({ value, label }) => (
                        <button
                            key={value}
                            onClick={() => onUpdate({ orientation: value })}
                            className={`flex-1 h-8 rounded-lg border text-xs font-medium transition-colors ${
                                el.orientation === value
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300'
                                    : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                            }`}
                        >
                            {label}
                        </button>
                    ))}
                </div>
            </Section>
            <Section title="Tampilan">
                <Row label="Panjang">
                    <NumField value={el.length} onChange={(v) => onUpdate({ length: Math.max(4, v) })} unit="px" />
                </Row>
                <Row label="Tebal">
                    <NumField value={el.thickness} onChange={(v) => onUpdate({ thickness: Math.max(1, v) })} unit="px" />
                </Row>
                <Row label="Warna">
                    <Swatch value={el.color} onChange={(v) => onUpdate({ color: v })} />
                </Row>
            </Section>
        </>
    );
}

// ── Shared primitives ──────────────────────────────────────────────────────────

const inputCn =
    'h-8 w-full rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 px-2.5 text-sm text-dark-900 dark:text-dark-50 tabular-nums focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500';

function PanelHeader({ title, meta }: { title: string; meta?: string }) {
    return (
        <div className="flex items-center justify-between px-3 h-9 shrink-0 border-b border-secondary-200 dark:border-dark-600 bg-zinc-50 dark:bg-dark-800">
            <span className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-400 dark:text-dark-500">{title}</span>
            {meta && (
                <span className="text-[10px] tabular-nums font-medium text-dark-500 dark:text-dark-400 bg-zinc-100 dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 rounded-full px-1.5 py-0.5">{meta}</span>
            )}
        </div>
    );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <section className="py-3 first:pt-3 space-y-2.5 border-b border-secondary-200 dark:border-dark-600 last:border-b-0">
            {title && (
                <h4 className="text-[10px] font-semibold uppercase tracking-[0.1em] text-dark-400 dark:text-dark-500">
                    {title}
                </h4>
            )}
            {children}
        </section>
    );
}

function Row({ label, children }: { label: string; children: React.ReactNode }) {
    return (
        <div className="flex items-center gap-2">
            <span className="w-12 shrink-0 text-xs text-dark-500 dark:text-dark-400">{label}</span>
            <div className="flex-1 min-w-0">{children}</div>
        </div>
    );
}

function NumField({ value, onChange, unit = 'px' }: { value: number; onChange: (v: number) => void; unit?: string }) {
    return (
        <div className="relative">
            <input type="number" value={value} onChange={(e) => onChange(+e.target.value)} className={`${inputCn} pr-8`} />
            <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] text-dark-400 dark:text-dark-500 pointer-events-none">{unit}</span>
        </div>
    );
}

function Swatch({ value, onChange }: { value: string; onChange: (v: string) => void }) {
    return (
        <div className="flex items-center gap-2 h-8 rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 pl-1.5 pr-2.5">
            <input
                type="color"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                className="h-5 w-5 shrink-0 cursor-pointer rounded border-0 bg-transparent p-0 appearance-none"
            />
            <input
                type="text"
                value={value.toUpperCase()}
                onChange={(e) => onChange(e.target.value)}
                className="flex-1 min-w-0 bg-transparent text-xs tabular-nums uppercase text-dark-700 dark:text-dark-300 outline-none"
            />
        </div>
    );
}

function EditableText({
    el, editing, customFonts, onStartEdit, onCommit,
}: { el: Text; editing: boolean; customFonts: CustomFontEntry[]; onStartEdit: () => void; onCommit: (v: string) => void }) {
    const ref = React.useRef<HTMLSpanElement>(null);
    const hasBox = el.width !== undefined;

    React.useEffect(() => {
        if (!editing || !ref.current) return;
        const node = ref.current;
        node.textContent = el.content;
        node.focus();
        const range = document.createRange();
        range.selectNodeContents(node);
        const sel = window.getSelection();
        sel?.removeAllRanges();
        sel?.addRange(range);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [editing]);

    const textStyle: React.CSSProperties = {
        fontSize: el.fontSize,
        fontWeight: el.bold ? 700 : 400,
        color: el.color,
        fontFamily: fontCssStack(el.fontFamily, customFonts),
        fontStyle: el.italic ? 'italic' : 'normal',
        textDecoration: [
            el.underline ? 'underline' : '',
            el.strikethrough ? 'line-through' : '',
        ].filter(Boolean).join(' ') || 'none',
        backgroundColor: el.highlight ?? undefined,
        textAlign: el.align ?? 'left',
        lineHeight: el.lineHeight ?? 1.2,
        letterSpacing: el.letterSpacing ? `${el.letterSpacing}px` : undefined,
        ...(hasBox
            ? { whiteSpace: 'pre-wrap', wordBreak: 'break-word', display: 'block', width: '100%' }
            : { whiteSpace: 'nowrap' }),
        ...(editing ? { outline: 'none', cursor: 'text' } : {}),
    };

    return (
        <span
            ref={ref}
            contentEditable={editing}
            suppressContentEditableWarning
            onDoubleClick={onStartEdit}
            onBlur={(e) => onCommit(e.currentTarget.textContent ?? '')}
            onKeyDown={(e) => {
                if (e.key === 'Enter' && !e.shiftKey && !hasBox) { e.preventDefault(); e.currentTarget.blur(); }
                else if (e.key === 'Escape') { e.currentTarget.textContent = el.content; e.currentTarget.blur(); }
            }}
            style={textStyle}
        >
            {editing ? null : el.content}
        </span>
    );
}

PdfTemplateEdit.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;

