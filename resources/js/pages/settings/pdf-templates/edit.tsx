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
    AlignLeft,
    AlignCenter,
    AlignRight,
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
} from 'lucide-react';
import type { SharedProps } from '@/types';

// ponytail: koordinat px @96dpi. A4 = 794x1123.
const A4 = { w: 794, h: 1123 };

// ── Types ────────────────────────────────────────────────────────────────────

type Text = {
    id: number; type: 'text';
    x: number; y: number;
    content: string; fontSize: number; bold: boolean; color: string;
};

type Img = {
    id: number; type: 'image';
    x: number; y: number;
    src: string; width: number; height?: number; lockAspect?: boolean;
};

/** One column in the table element — stored in layout JSON. */
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

/** One cell in the static grid element. */
type GridCell = {
    text: string;
    align: 'left' | 'center' | 'right';
    bold: boolean;
    color: string;
    fill?: string;
    colSpan?: number;  // default 1
    rowSpan?: number;  // default 1
    merged?: boolean;  // true = covered by another cell's span
};

type GridEl = {
    id: number; type: 'grid';
    x: number; y: number;
    width: number;
    cols: number;
    rows: number;
    colWidths: number[];        // px per column (sum ≈ width)
    cells: GridCell[][];        // [row][col]
    border: { width: number; color: string };
    anchorCell?: { row: number; col: number } | null;
};

type El = Text | Img | TableEl | GridEl;

// ── Catalog types ─────────────────────────────────────────────────────────────

interface TokenEntry { path: string; label: string; }

/** One entry from ItemColumns::catalogForFrontend() */
interface ItemColumnEntry {
    key: string;
    label: string;
    align: 'left' | 'center' | 'right';
    format: 'text' | 'number' | 'rupiah';
    default: boolean;
}

interface TemplateProps { id: number; name: string; layout: El[]; }

interface Props extends SharedProps {
    template: TemplateProps;
    tokenCatalog: TokenEntry[];
    sampleData: Record<string, string>;
    /** Item column catalog from ItemColumns::catalogForFrontend() */
    itemColumnCatalog: ItemColumnEntry[];
    /**
     * Resolved sample rows: array of {key→value} objects, one per sample item.
     * Built server-side from the latest/sample invoice using the DEFAULT columns.
     * In Preview mode the table renders these rows.
     */
    sampleItems: Array<Record<string, string>>;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

/** Build a default table element placed at (x, y). */
function makeDefaultTable(id: number, catalog: ItemColumnEntry[], x: number, y: number): TableEl {
    const defaults = catalog.filter((c) => c.default);
    const widths: Record<string, number> = {
        no: 36, description: 290, quantity: 72, unit: 80, unit_price: 130, amount: 130,
        cogs_amount: 130, is_tax_deposit: 100,
    };
    const columns: TableColumn[] = defaults.map((c) => ({
        key: c.key,
        label: c.label,
        width: widths[c.key] ?? 100,
        align: c.align,
        format: c.format,
    }));
    return { id, type: 'table', x, y, width: 714, columns, showFooterSum: false };
}

/** Approximate row height in the editor for 3 placeholder rows. */
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

/** Default cell value. */
function makeGridCell(): GridCell {
    return { text: '', align: 'left', bold: false, color: '#0f172a' };
}

/** Build a default 3×3 grid element. */
function makeDefaultGrid(id: number, x: number, y: number): GridEl {
    const cols = 3;
    const rows = 3;
    const width = 300;
    const colWidth = Math.floor(width / cols);
    return {
        id,
        type: 'grid',
        x, y, width,
        cols, rows,
        colWidths: Array.from({ length: cols }, () => colWidth),
        cells: Array.from({ length: rows }, () => Array.from({ length: cols }, makeGridCell)),
        border: { width: 1, color: '#cbd5e1' },
    };
}

/** Height of a grid element for canvas layout. */
const GRID_ROW_H = 24;
function gridEditorHeight(el: GridEl): number {
    return GRID_ROW_H * el.rows + el.border.width * (el.rows + 1);
}

// ── Component ─────────────────────────────────────────────────────────────────

export default function PdfTemplateEdit() {
    const { template, tokenCatalog, sampleData, itemColumnCatalog, sampleItems } = usePage<Props>().props;

    const resolve = (text: string): string =>
        text.replace(/\{\{([\w.]+)\}\}/g, (match, path: string) =>
            Object.prototype.hasOwnProperty.call(sampleData, path) ? sampleData[path] : match,
        );

    const initial: El[] = Array.isArray(template.layout) && template.layout.length
        ? (template.layout as El[])
        : [{ id: 1, type: 'text', x: 60, y: 60, content: 'Invoice {{invoice.number}}', fontSize: 20, bold: true, color: '#0f172a' }];

    const [els, setEls] = React.useState<El[]>(initial);
    const [selectedId, setSelectedId] = React.useState<number | null>(null);
    /** When a grid cell is selected: { row, col } */
    const [selectedCell, setSelectedCell] = React.useState<{ row: number; col: number } | null>(null);
    const [rangeEnd, setRangeEnd] = React.useState<{ row: number; col: number } | null>(null);
    const [anchorCell, setAnchorCell] = React.useState<{ row: number; col: number } | null>(null);
    /** When a grid cell is being inline-edited: { row, col } */
    const [editingCell, setEditingCell] = React.useState<{ row: number; col: number } | null>(null);
    const [saving, setSaving] = React.useState(false);
    const [editingId, setEditingId] = React.useState<number | null>(null);
    const [preview, setPreview] = React.useState(false);
    const [fieldMenu, setFieldMenu] = React.useState(false);
    const [zoom, setZoom] = React.useState(0.6);
    const [dragOver, setDragOver] = React.useState(false);
    const [overLayerId, setOverLayerId] = React.useState<number | null>(null);
    const paperRef = React.useRef<HTMLDivElement>(null);
    const canvasRef = React.useRef<HTMLDivElement>(null);
    const fileRef = React.useRef<HTMLInputElement>(null);
    const contentRef = React.useRef<HTMLTextAreaElement>(null);
    const pendingImg = React.useRef<{ x: number; y: number } | null>(null);
    const dragLayerId = React.useRef<number | null>(null);
    const zoomAnchor = React.useRef<{ cx: number; cy: number; clientX: number; clientY: number } | null>(null);
    const nextId = React.useRef(Math.max(0, ...initial.map((e) => e.id)) + 1);
    const clipboard = React.useRef<El | null>(null);
    const history = React.useRef<{ past: El[][]; future: El[][] }>({ past: [], future: [] });

    const selected = els.find((e) => e.id === selectedId) ?? null;
    const update = (id: number, patch: Partial<El>) =>
        setEls((p) => p.map((e) => (e.id === id ? ({ ...e, ...patch } as El) : e)));

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

    const snapshot = () =>
        setEls((prev) => {
            const h = history.current;
            h.past.push(prev);
            if (h.past.length > 100) h.past.shift();
            h.future = [];
            return prev;
        });

    const undo = () =>
        setEls((prev) => {
            const h = history.current;
            if (!h.past.length) return prev;
            h.future.push(prev);
            return h.past.pop()!;
        });

    const redo = () =>
        setEls((prev) => {
            const h = history.current;
            if (!h.future.length) return prev;
            h.past.push(prev);
            return h.future.pop()!;
        });

    const startDrag = (e: React.PointerEvent, el: El) => {
        e.stopPropagation();
        setSelectedId(el.id);
        const rect = paperRef.current!.getBoundingClientRect();
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

    /** Horizontal resize handle for the table (right edge). */
    const startTableResize = (e: React.PointerEvent, el: TableEl) => {
        e.stopPropagation();
        const rect = paperRef.current!.getBoundingClientRect();
        const x0 = el.x;
        const w0 = el.width;
        let resized = false;
        const move = (ev: PointerEvent) => {
            if (!resized) { resized = true; snapshot(); }
            const pxRight = (ev.clientX - rect.left) / zoom;
            const newW = Math.max(100, Math.round(pxRight - x0));
            update(el.id, { width: newW });
        };
        const up = () => {
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerup', up);
        };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const startResize = (e: React.PointerEvent, el: Img, corner: 'nw' | 'ne' | 'sw' | 'se') => {
        e.stopPropagation();
        setSelectedId(el.id);
        const rect = paperRef.current!.getBoundingClientRect();
        const w0 = el.width;
        const h0 = el.height ?? w0;
        const ratio = w0 / h0;
        const left0 = el.x;
        const top0 = el.y;
        const right0 = el.x + w0;
        const bottom0 = el.y + h0;
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
        const up = () => {
            window.removeEventListener('pointermove', move);
            window.removeEventListener('pointerup', up);
        };
        window.addEventListener('pointermove', move);
        window.addEventListener('pointerup', up);
    };

    const addText = (x = 80, y = 200, content = 'Teks baru') => {
        snapshot();
        const id = nextId.current++;
        setEls((p) => [...p, { id, type: 'text', x, y, content, fontSize: 14, bold: false, color: '#0f172a' }]);
        setSelectedId(id);
    };

    const addImage = (file: File, x = 80, y = 280) => {
        const reader = new FileReader();
        reader.onload = () => {
            const src = reader.result as string;
            const probe = new Image();
            probe.onload = () => {
                snapshot();
                const id = nextId.current++;
                const width = 160;
                const height = Math.round(width * (probe.naturalHeight / probe.naturalWidth));
                setEls((p) => [...p, { id, type: 'image', x, y, src, width, height, lockAspect: true }]);
                setSelectedId(id);
            };
            probe.src = src;
        };
        reader.readAsDataURL(file);
    };

    const addTable = (x = 40, y = 300) => {
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultTable(id, itemColumnCatalog, x, y);
        setEls((p) => [...p, el]);
        setSelectedId(id);
    };

    const addGrid = (x = 80, y = 200) => {
        snapshot();
        const id = nextId.current++;
        const el = makeDefaultGrid(id, x, y);
        setEls((p) => [...p, el]);
        setSelectedId(id);
        setSelectedCell(null);
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
        probe.onload = () => {
            snapshot();
            update(el.id, { width: probe.naturalWidth, height: probe.naturalHeight });
        };
        probe.src = el.src;
    };

    const updateTableColumn = (tableId: number, colIdx: number, patch: Partial<TableColumn>) => {
        setEls((p) => p.map((e) => {
            if (e.id !== tableId || e.type !== 'table') return e;
            const cols = e.columns.map((c, i) => i === colIdx ? { ...c, ...patch } : c);
            return { ...e, columns: cols };
        }));
    };

    const moveTableColumn = (tableId: number, from: number, direction: -1 | 1) => {
        const to = from + direction;
        setEls((p) => p.map((e) => {
            if (e.id !== tableId || e.type !== 'table') return e;
            if (to < 0 || to >= e.columns.length) return e;
            const cols = [...e.columns];
            [cols[from], cols[to]] = [cols[to], cols[from]];
            return { ...e, columns: cols };
        }));
    };

    const removeTableColumn = (tableId: number, colIdx: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== tableId || e.type !== 'table') return e;
            return { ...e, columns: e.columns.filter((_, i) => i !== colIdx) };
        }));
    };

    /** Update a single cell's properties in a grid element. */
    const updateGridCell = (gridId: number, row: number, col: number, patch: Partial<GridCell>) => {
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const cells = e.cells.map((r, ri) =>
                r.map((c, ci) => (ri === row && ci === col ? { ...c, ...patch } : c))
            );
            return { ...e, cells };
        }));
    };

    /** Update grid-level properties (rows/cols restructure, border, colWidths). */
    const updateGrid = (gridId: number, patch: Partial<GridEl>) => {
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            return { ...e, ...patch } as GridEl;
        }));
    };

    const mergeRange = (gridId: number, r1: number, c1: number, r2: number, c2: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const cells = e.cells.map((row, ri) =>
                row.map((cell, ci) => {
                    if (ri === r1 && ci === c1) {
                        // keeper cell
                        return { ...cell, colSpan: c2 - c1 + 1, rowSpan: r2 - r1 + 1, merged: false };
                    }
                    if (ri >= r1 && ri <= r2 && ci >= c1 && ci <= c2) {
                        // covered cell
                        return { ...cell, text: '', colSpan: 1, rowSpan: 1, merged: true };
                    }
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
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const keeper = e.cells[row]?.[col];
            if (!keeper) return e;
            const cs = keeper.colSpan ?? 1;
            const rs = keeper.rowSpan ?? 1;
            const cells = e.cells.map((rowCells, ri) =>
                rowCells.map((cell, ci) => {
                    if (ri === row && ci === col) {
                        return { ...cell, colSpan: 1, rowSpan: 1, merged: false };
                    }
                    if (ri >= row && ri < row + rs && ci >= col && ci < col + cs) {
                        return { ...cell, merged: false, colSpan: 1, rowSpan: 1 };
                    }
                    return cell;
                })
            );
            return { ...e, cells };
        }));
    };

    /** Add a row to the grid (appended at bottom). */
    const addGridRow = (gridId: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const newRow = Array.from({ length: e.cols }, makeGridCell);
            return { ...e, rows: e.rows + 1, cells: [...e.cells, newRow] };
        }));
    };

    /** Remove the last row (min 1). */
    const removeGridRow = (gridId: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid' || e.rows <= 1) return e;
            return { ...e, rows: e.rows - 1, cells: e.cells.slice(0, -1) };
        }));
    };

    /** Add a column to the right. */
    const addGridCol = (gridId: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid') return e;
            const newColWidth = Math.floor(e.width / (e.cols + 1));
            const cells = e.cells.map((row) => [...row, makeGridCell()]);
            return { ...e, cols: e.cols + 1, colWidths: [...e.colWidths, newColWidth], cells };
        }));
    };

    /** Remove the last column (min 1). */
    const removeGridCol = (gridId: number) => {
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== gridId || e.type !== 'grid' || e.cols <= 1) return e;
            const cells = e.cells.map((row) => row.slice(0, -1));
            return { ...e, cols: e.cols - 1, colWidths: e.colWidths.slice(0, -1), cells };
        }));
    };

    const addTableColumn = (tableId: number, key: string) => {
        const entry = itemColumnCatalog.find((c) => c.key === key);
        if (!entry) return;
        const widths: Record<string, number> = {
            no: 36, description: 290, quantity: 72, unit: 80, unit_price: 130, amount: 130,
            cogs_amount: 130, is_tax_deposit: 100,
        };
        snapshot();
        setEls((p) => p.map((e) => {
            if (e.id !== tableId || e.type !== 'table') return e;
            if (e.columns.some((c) => c.key === key)) return e; // already present
            const newCol: TableColumn = { key: entry.key, label: entry.label, width: widths[key] ?? 100, align: entry.align, format: entry.format };
            return { ...e, columns: [...e.columns, newCol] };
        }));
    };

    const save = () => {
        setSaving(true);
        router.post(
            `/settings/pdf-templates/${template.id}/save`,
            { layout: els },
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

    const dropPos = (e: React.DragEvent) => {
        const rect = paperRef.current!.getBoundingClientRect();
        return {
            x: Math.max(0, Math.min(A4.w, (e.clientX - rect.left) / zoom)),
            y: Math.max(0, Math.min(A4.h, (e.clientY - rect.top) / zoom)),
        };
    };

    const onDrop = (e: React.DragEvent) => {
        e.preventDefault();
        setDragOver(false);
        const { x, y } = dropPos(e);
        const file = e.dataTransfer.files?.[0];
        if (file?.type.startsWith('image/')) { addImage(file, x, y); return; }
        const kind = e.dataTransfer.getData('kind');
        if (kind === 'text') addText(x, y);
        else if (kind === 'image') { pendingImg.current = { x, y }; fileRef.current?.click(); }
        else if (kind === 'table') addTable(Math.min(x, A4.w - 100), y);
        else if (kind === 'grid') addGrid(Math.min(x, A4.w - 100), y);
    };

    const remove = (id: number) => {
        snapshot();
        setEls((p) => p.filter((e) => e.id !== id));
        setSelectedId(null);
        setSelectedCell(null);
        setEditingCell(null);
    };

    const moveLayer = (draggedId: number, targetId: number) => {
        if (draggedId === targetId) return;
        snapshot();
        setEls((prev) => {
            const display = [...prev].reverse();
            const from = display.findIndex((e) => e.id === draggedId);
            const to = display.findIndex((e) => e.id === targetId);
            if (from < 0 || to < 0) return prev;
            const [moved] = display.splice(from, 1);
            display.splice(to, 0, moved);
            return display.reverse();
        });
    };

    const insertToken = (path: string) => {
        if (!selected || selected.type !== 'text') return;
        snapshot();
        const token = `{{${path}}}`;
        const ta = contentRef.current;
        if (ta && document.activeElement === ta) {
            const s = ta.selectionStart ?? selected.content.length;
            const en = ta.selectionEnd ?? s;
            update(selected.id, { content: selected.content.slice(0, s) + token + selected.content.slice(en) });
        } else {
            update(selected.id, { content: selected.content + token });
        }
    };

    const duplicate = (src: El) => {
        snapshot();
        const id = nextId.current++;
        const copy = { ...src, id, x: Math.min(A4.w, src.x + 20), y: Math.min(A4.h, src.y + 20) } as El;
        setEls((p) => [...p, copy]);
        setSelectedId(id);
        clipboard.current = copy;
    };

    const imgToPngBlob = (src: string) =>
        new Promise<Blob>((res, rej) => {
            const img = new Image();
            img.onload = () => {
                const c = document.createElement('canvas');
                c.width = img.naturalWidth; c.height = img.naturalHeight;
                c.getContext('2d')!.drawImage(img, 0, 0);
                c.toBlob((b) => (b ? res(b) : rej(new Error('toBlob null'))), 'image/png');
            };
            img.onerror = rej;
            img.src = src;
        });

    const copyToOS = async (el: El) => {
        try {
            if (el.type === 'text') {
                await navigator.clipboard.writeText(el.content);
            } else if (el.type === 'image') {
                const png = await imgToPngBlob(el.src);
                await navigator.clipboard.write([new ClipboardItem({ 'image/png': png })]);
            }
        } catch { /* clipboard bisa gagal — fallback ke clipboard internal */ }
    };

    React.useEffect(() => {
        const inField = (t: EventTarget | null) =>
            t instanceof HTMLElement && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.isContentEditable);

        const onKey = (e: KeyboardEvent) => {
            if (inField(e.target)) return;
            const mod = e.ctrlKey || e.metaKey;
            if (mod && e.key.toLowerCase() === 'z') {
                e.preventDefault();
                if (e.shiftKey) redo(); else undo();
                return;
            }
            if (mod && e.key.toLowerCase() === 'c' && selected) {
                clipboard.current = selected;
                copyToOS(selected);
            }
            if (mod && e.key.toLowerCase() === 'd' && selected) {
                e.preventDefault();
                duplicate(selected);
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
            if (file) { e.preventDefault(); addImage(file, 100, 120); }
            else if (text) { e.preventDefault(); addText(100, 120, text); }
            else if (clipboard.current) { e.preventDefault(); duplicate(clipboard.current); }
        };

        window.addEventListener('keydown', onKey);
        window.addEventListener('paste', onPaste);
        return () => {
            window.removeEventListener('keydown', onKey);
            window.removeEventListener('paste', onPaste);
        };
    }, [selected]);

    React.useEffect(() => {
        const node = canvasRef.current;
        if (!node) return;
        const onWheel = (e: WheelEvent) => {
            if (!e.ctrlKey && !e.metaKey) return;
            e.preventDefault();
            const paper = paperRef.current;
            if (!paper) return;
            const rect = paper.getBoundingClientRect();
            const cx = (e.clientX - rect.left) / zoom;
            const cy = (e.clientY - rect.top) / zoom;
            const factor = e.deltaY < 0 ? 1.1 : 1 / 1.1;
            const newZoom = Math.min(3, Math.max(0.2, +(zoom * factor).toFixed(3)));
            if (newZoom === zoom) return;
            zoomAnchor.current = { cx, cy, clientX: e.clientX, clientY: e.clientY };
            setZoom(newZoom);
        };
        node.addEventListener('wheel', onWheel, { passive: false });
        return () => node.removeEventListener('wheel', onWheel);
    }, [zoom]);

    React.useLayoutEffect(() => {
        const a = zoomAnchor.current;
        if (!a || !paperRef.current || !canvasRef.current) return;
        const rect = paperRef.current.getBoundingClientRect();
        canvasRef.current.scrollLeft += a.cx * zoom - (a.clientX - rect.left);
        canvasRef.current.scrollTop += a.cy * zoom - (a.clientY - rect.top);
        zoomAnchor.current = null;
    }, [zoom]);

    React.useEffect(() => {
        els.filter((e): e is Img => e.type === 'image' && e.height == null).forEach((el) => {
            const probe = new Image();
            probe.onload = () => {
                const height = Math.round(el.width * (probe.naturalHeight / probe.naturalWidth));
                setEls((p) => p.map((e) => (e.id === el.id ? { ...e, height, lockAspect: (e as Img).lockAspect ?? true } : e)));
            };
            probe.src = el.src;
        });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [els]);

    return (
        <div className="flex flex-col h-[calc(100vh-4rem)]">
            {/* Header bar */}
            <div className="flex items-center gap-3 px-4 py-2.5 border-b border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 shrink-0">
                <Button variant="ghost" size="sm" onClick={() => router.visit('/settings/pdf-templates')} className="gap-1.5">
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Button>
                <div className="w-px h-5 bg-secondary-200 dark:bg-dark-600" />
                <span className="text-sm font-medium text-dark-900 dark:text-dark-50 truncate">{template.name}</span>
                <span className="text-xs text-dark-400 dark:text-dark-500 shrink-0">— Editor Template PDF</span>
            </div>

            {/* 3-column editor */}
            <div className="flex flex-1 overflow-hidden rounded-b-xl border-x border-b border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800">

                {/* ── KIRI: Layers ── */}
                <aside className="w-52 shrink-0 border-r border-secondary-200 dark:border-dark-600 flex flex-col">
                    <PanelHeader title="Layers" meta={els.length ? String(els.length) : undefined} />
                    <div className="flex-1 overflow-auto p-2 space-y-0.5">
                        {els.length === 0 && (
                            <p className="text-xs text-dark-400 dark:text-dark-500 px-2 py-3 text-center">Belum ada elemen.<br />Seret dari toolbar.</p>
                        )}
                        {[...els].reverse().map((el) => {
                            const active = selectedId === el.id;
                            const isOver = overLayerId === el.id;
                            return (
                                <div
                                    key={el.id}
                                    draggable
                                    onClick={() => setSelectedId(el.id)}
                                    onDragStart={(e) => {
                                        dragLayerId.current = el.id;
                                        e.dataTransfer.effectAllowed = 'move';
                                        e.stopPropagation();
                                    }}
                                    onDragOver={(e) => {
                                        e.preventDefault();
                                        if (dragLayerId.current != null && overLayerId !== el.id) setOverLayerId(el.id);
                                    }}
                                    onDrop={(e) => {
                                        e.preventDefault(); e.stopPropagation();
                                        if (dragLayerId.current != null) moveLayer(dragLayerId.current, el.id);
                                        dragLayerId.current = null; setOverLayerId(null);
                                    }}
                                    onDragEnd={() => { dragLayerId.current = null; setOverLayerId(null); }}
                                    className={`group flex items-center gap-1.5 rounded-lg pl-1 pr-1.5 py-1.5 cursor-pointer border-l-2 transition-colors ${
                                        active
                                            ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20'
                                            : 'border-transparent hover:bg-zinc-50 dark:hover:bg-dark-700'
                                    } ${isOver ? 'ring-1 ring-primary-400 ring-inset' : ''}`}
                                >
                                    <GripVertical className="w-3.5 h-3.5 shrink-0 text-dark-300 dark:text-dark-500 opacity-0 group-hover:opacity-100 cursor-grab active:cursor-grabbing" />
                                    <span className={`grid place-items-center h-6 w-6 rounded-md shrink-0 ${active ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-300' : 'bg-zinc-100 dark:bg-dark-700 text-dark-500 dark:text-dark-400'}`}>
                                        {el.type === 'text'
                                            ? <Type className="w-3.5 h-3.5" />
                                            : el.type === 'image'
                                                ? <ImageIcon className="w-3.5 h-3.5" />
                                                : el.type === 'grid'
                                                    ? <LayoutGrid className="w-3.5 h-3.5" />
                                                    : <Table2 className="w-3.5 h-3.5" />}
                                    </span>
                                    <span className={`flex-1 truncate text-sm ${active ? 'text-primary-700 dark:text-primary-200 font-medium' : 'text-dark-700 dark:text-dark-300'}`}>
                                        {el.type === 'text' ? el.content : el.type === 'image' ? 'Gambar' : el.type === 'grid' ? 'Grid' : 'Tabel Item'}
                                    </span>
                                    <button
                                        onClick={(e) => { e.stopPropagation(); remove(el.id); }}
                                        className="grid place-items-center h-6 w-6 rounded-md text-dark-400 opacity-0 group-hover:opacity-100 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 transition"
                                        title="Hapus"
                                    >
                                        <Trash2 className="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            );
                        })}
                    </div>
                </aside>

                {/* ── TENGAH: Kanvas ── */}
                <div className="relative flex-1 overflow-hidden">
                    <div ref={canvasRef} className="absolute inset-0 overflow-auto bg-zinc-200 dark:bg-dark-950">
                        <div className="min-h-full flex items-start justify-center p-10">
                            <div style={{ width: A4.w * zoom, height: A4.h * zoom }}>
                                <div
                                    ref={paperRef}
                                    onPointerDown={() => { setSelectedId(null); setSelectedCell(null); setEditingCell(null); }}
                                    onDragOver={(e) => { e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; if (!dragOver) setDragOver(true); }}
                                    onDragLeave={() => setDragOver(false)}
                                    onDrop={onDrop}
                                    className={`relative overflow-hidden bg-white shadow-xl origin-top-left ${dragOver ? 'ring-2 ring-primary-500' : ''}`}
                                    style={{ width: A4.w, height: A4.h, transform: `scale(${zoom})` }}
                                >
                                    {/* ── Flow-boundary indicator ── */}
                                    {(() => {
                                        const tableEl = els.find((e): e is TableEl => e.type === 'table');
                                        if (!tableEl) return null;
                                        return (
                                            <div
                                                key="flow-boundary"
                                                className="pointer-events-none absolute left-0 right-0 flex items-center"
                                                style={{ top: tableEl.y }}
                                            >
                                                <div className="flex-1 border-t border-dashed border-dark-400/40 dark:border-dark-500/40" />
                                                <span className="mx-2 shrink-0 rounded px-1.5 py-0.5 text-[9px] font-medium text-dark-400/70 dark:text-dark-500/70 bg-white dark:bg-dark-800 select-none">
                                                    Mengalir setelah tabel ↓
                                                </span>
                                                <div className="flex-1 border-t border-dashed border-dark-400/40 dark:border-dark-500/40" />
                                            </div>
                                        );
                                    })()}

                                    {els.map((el) => {
                                        const isSel = selectedId === el.id;
                                        const isEditing = editingId === el.id;

                                        if (el.type === 'table') {
                                            const height = preview
                                                ? tablePreviewHeight(el, sampleItems)
                                                : tableEditorHeight(el);
                                            const rows = preview ? sampleItems : null;
                                            return (
                                                <div
                                                    key={el.id}
                                                    onPointerDown={(e) => startDrag(e, el)}
                                                    className={`absolute cursor-move ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                                                    style={{ left: el.x, top: el.y, width: el.width, height, touchAction: 'none' }}
                                                >
                                                    <TablePreview el={el} rows={rows} />
                                                    {/* Right-edge resize handle */}
                                                    {isSel && !preview && (
                                                        <span
                                                            onPointerDown={(e) => startTableResize(e, el)}
                                                            className="absolute right-0 top-0 bottom-0 w-2 cursor-ew-resize flex items-center justify-center"
                                                            title="Geser untuk ubah lebar"
                                                        >
                                                            <span className="w-1 h-6 rounded-sm bg-primary-500 opacity-70" />
                                                        </span>
                                                    )}
                                                </div>
                                            );
                                        }

                                        if (el.type === 'grid') {
                                            const height = gridEditorHeight(el);
                                            return (
                                                <div
                                                    key={el.id}
                                                    onPointerDown={(e) => {
                                                        if (editingCell) return;
                                                        startDrag(e, el);
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
                                                            if (e.shiftKey && anchorCell && selectedId === el.id) {
                                                                setRangeEnd({ row: r, col: c });
                                                            } else {
                                                                setAnchorCell({ row: r, col: c });
                                                                setRangeEnd({ row: r, col: c });
                                                                setSelectedCell({ row: r, col: c });
                                                            }
                                                        }}
                                                        onCellDoubleClick={(r, c) => {
                                                            if (preview) return;
                                                            setSelectedId(el.id);
                                                            setSelectedCell({ row: r, col: c });
                                                            setEditingCell({ row: r, col: c });
                                                        }}
                                                        onCellCommit={(r, c, text) => {
                                                            if (text !== el.cells[r]?.[c]?.text) {
                                                                snapshot();
                                                                updateGridCell(el.id, r, c, { text });
                                                            }
                                                            setEditingCell(null);
                                                        }}
                                                        onCellEscape={(r, c) => {
                                                            setEditingCell(null);
                                                        }}
                                                        selectedRange={isSel ? selectedRange : null}
                                                        rangeAnchor={isSel ? anchorCell : null}
                                                    />
                                                    {/* Right-edge resize handle */}
                                                    {isSel && !preview && (
                                                        <span
                                                            onPointerDown={(e) => startTableResize(e, el as unknown as TableEl)}
                                                            className="absolute right-0 top-0 bottom-0 w-2 cursor-ew-resize flex items-center justify-center"
                                                            title="Geser untuk ubah lebar"
                                                        >
                                                            <span className="w-1 h-6 rounded-sm bg-primary-500 opacity-70" />
                                                        </span>
                                                    )}
                                                </div>
                                            );
                                        }

                                        return (
                                            <div
                                                key={el.id}
                                                onPointerDown={(e) => {
                                                    if (isEditing) { e.stopPropagation(); return; }
                                                    startDrag(e, el);
                                                }}
                                                className={`absolute select-none ${isEditing ? 'cursor-text' : 'cursor-move'} ${isSel && !preview ? 'outline-2 outline-primary-500' : ''}`}
                                                style={{
                                                    left: el.x, top: el.y, touchAction: 'none',
                                                    ...(el.type === 'image' ? { width: el.width, height: el.height } : {}),
                                                }}
                                            >
                                                {el.type === 'text' ? (
                                                    preview ? (
                                                        <span
                                                            className="whitespace-nowrap leading-none"
                                                            style={{ fontSize: el.fontSize, fontWeight: el.bold ? 700 : 400, color: el.color }}
                                                        >
                                                            {resolve(el.content)}
                                                        </span>
                                                    ) : (
                                                        <EditableText
                                                            el={el}
                                                            editing={isEditing}
                                                            onStartEdit={() => setEditingId(el.id)}
                                                            onCommit={(v) => {
                                                                if (v !== el.content) { snapshot(); update(el.id, { content: v }); }
                                                                setEditingId(null);
                                                            }}
                                                        />
                                                    )
                                                ) : (
                                                    <img
                                                        src={el.src}
                                                        alt=""
                                                        draggable={false}
                                                        style={{ width: '100%', height: '100%', maxWidth: 'none' }}
                                                        className="pointer-events-none block"
                                                    />
                                                )}

                                                {isSel && !preview && el.type === 'image' &&
                                                    (['nw', 'ne', 'sw', 'se'] as const).map((corner) => (
                                                        <span
                                                            key={corner}
                                                            onPointerDown={(e) => startResize(e, el, corner)}
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
                                    })}
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Floating toolbar */}
                    <div className="absolute bottom-5 left-1/2 -translate-x-1/2 flex items-center gap-1 px-2 py-1.5 rounded-xl bg-white dark:bg-dark-700 border border-secondary-200 dark:border-dark-600 shadow-lg">
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'text'); }} title="Seret ke kanvas, atau klik">
                            <Button variant="ghost" size="sm" className="cursor-grab active:cursor-grabbing" onClick={() => addText()}>
                                <Type className="w-4 h-4" /> Teks
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'image'); }} title="Seret ke kanvas, atau klik">
                            <Button variant="ghost" size="sm" className="cursor-grab active:cursor-grabbing" onClick={() => fileRef.current?.click()}>
                                <ImageIcon className="w-4 h-4" /> Gambar
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'table'); }} title="Seret ke kanvas, atau klik">
                            <Button variant="ghost" size="sm" className="cursor-grab active:cursor-grabbing" onClick={() => addTable()}>
                                <Table2 className="w-4 h-4" /> Tabel
                            </Button>
                        </div>
                        <div draggable onDragStart={(e) => { e.dataTransfer.effectAllowed = 'copy'; e.dataTransfer.setData('kind', 'grid'); }} title="Grid statis — seret ke kanvas atau klik">
                            <Button variant="ghost" size="sm" className="cursor-grab active:cursor-grabbing" onClick={() => addGrid()}>
                                <LayoutGrid className="w-4 h-4" /> Grid
                            </Button>
                        </div>
                        <div className="w-px h-6 bg-secondary-200 dark:bg-dark-600 mx-1" />
                        <Button
                            variant={preview ? 'primary' : 'ghost'}
                            size="sm"
                            onClick={() => { setPreview((p) => !p); setEditingId(null); }}
                            title="Lihat hasil dengan data contoh"
                        >
                            {preview ? <Pencil className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                            {preview ? 'Edit' : 'Preview'}
                        </Button>
                        <div className="w-px h-6 bg-secondary-200 dark:bg-dark-600 mx-1" />
                        <Button variant="ghost" size="icon" onClick={() => undo()} title="Undo (Ctrl+Z)">
                            <Undo2 className="w-4 h-4" />
                        </Button>
                        <Button variant="ghost" size="icon" onClick={() => redo()} title="Redo (Ctrl+Shift+Z)">
                            <Redo2 className="w-4 h-4" />
                        </Button>
                        <div className="w-px h-6 bg-secondary-200 dark:bg-dark-600 mx-1" />
                        <Button variant="ghost" size="icon" onClick={() => setZoom((z) => Math.max(0.2, +(z - 0.1).toFixed(2)))}>
                            <ZoomOut className="w-4 h-4" />
                        </Button>
                        <span className="text-xs tabular-nums text-dark-500 dark:text-dark-400 w-10 text-center">{Math.round(zoom * 100)}%</span>
                        <Button variant="ghost" size="icon" onClick={() => setZoom((z) => Math.min(3, +(z + 0.1).toFixed(2)))}>
                            <ZoomIn className="w-4 h-4" />
                        </Button>
                        <div className="w-px h-6 bg-secondary-200 dark:bg-dark-600 mx-1" />
                        <Button variant="ghost" size="sm" onClick={openPdf} title="Cetak PDF (data contoh)">
                            <FileDown className="w-4 h-4" /> PDF
                        </Button>
                        <Button variant="primary" size="sm" onClick={save} disabled={saving}>
                            <Save className="w-4 h-4" /> {saving ? 'Menyimpan…' : 'Simpan'}
                        </Button>
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
                                addImage(file, p?.x, p?.y);
                            }
                            e.target.value = '';
                        }}
                    />
                </div>

                {/* ── KANAN: Inspector ── */}
                <aside className="w-72 shrink-0 border-l border-secondary-200 dark:border-dark-600 flex flex-col">
                    <PanelHeader
                        title={
                            selected
                                ? selected.type === 'text' ? 'Teks'
                                    : selected.type === 'image' ? 'Gambar'
                                    : selected.type === 'grid' ? 'Grid'
                                    : 'Tabel Item'
                                : 'Properti'
                        }
                    />

                    {!selected ? (
                        <div className="flex-1 grid place-items-center p-6 text-center">
                            <p className="text-xs text-dark-400 dark:text-dark-500 leading-relaxed">
                                Pilih elemen di kanvas<br />untuk mengatur propertinya.
                            </p>
                        </div>
                    ) : (
                        <div
                            className="flex-1 overflow-auto px-3 divide-y divide-secondary-200 dark:divide-dark-600"
                            onFocusCapture={(e) => {
                                const t = e.target as HTMLElement;
                                if (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA') snapshot();
                            }}
                        >
                            {/* ── Text Inspector ── */}
                            {selected.type === 'text' && (
                                <>
                                    <Section title="Konten">
                                        <textarea
                                            ref={contentRef}
                                            value={selected.content}
                                            onChange={(e) => update(selected.id, { content: e.target.value })}
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
                                                            onClick={() => { insertToken(t.path); setFieldMenu(false); }}
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
                                    <Section title="Tampilan">
                                        <Row label="Ukuran">
                                            <NumField value={selected.fontSize} onChange={(v) => update(selected.id, { fontSize: v })} />
                                        </Row>
                                        <Row label="Warna">
                                            <Swatch value={selected.color} onChange={(v) => update(selected.id, { color: v })} />
                                        </Row>
                                        <Row label="Tebal">
                                            <button
                                                onClick={() => { snapshot(); update(selected.id, { bold: !(selected as Text).bold }); }}
                                                className={`grid place-items-center h-8 w-8 rounded-lg border transition-colors ${
                                                    (selected as Text).bold
                                                        ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-300'
                                                        : 'border-secondary-200 dark:border-dark-600 text-dark-500 dark:text-dark-400 hover:bg-zinc-50 dark:hover:bg-dark-700'
                                                }`}
                                                title="Bold"
                                            >
                                                <BoldIcon className="w-4 h-4" />
                                            </button>
                                        </Row>
                                    </Section>
                                </>
                            )}

                            {/* ── Image Inspector ── */}
                            {selected.type === 'image' && (
                                <Section title="Gambar">
                                    <Row label="Lebar">
                                        <NumField value={selected.width} onChange={(v) => setImgSize(selected, 'width', v)} />
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
                            )}

                            {/* ── Table Inspector ── */}
                            {selected.type === 'table' && (
                                <TableInspector
                                    el={selected as TableEl}
                                    catalog={itemColumnCatalog}
                                    onUpdate={(patch) => { snapshot(); update(selected.id, patch); }}
                                    onUpdateColumn={(idx, patch) => { snapshot(); updateTableColumn(selected.id, idx, patch); }}
                                    onMoveColumn={(idx, dir) => { snapshot(); moveTableColumn(selected.id, idx, dir); }}
                                    onRemoveColumn={(idx) => removeTableColumn(selected.id, idx)}
                                    onAddColumn={(key) => addTableColumn(selected.id, key)}
                                />
                            )}

                            {/* ── Grid Inspector ── */}
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

                            {/* ── Posisi (shared) ── */}
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
                            </Section>

                            <Section title="">
                                <Button variant="zinc" size="sm" className="w-full" onClick={() => duplicate(selected)}>
                                    <Copy className="w-4 h-4" /> Gandakan <span className="text-xs opacity-60">Ctrl+D</span>
                                </Button>
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

// ── Shared primitives ──────────────────────────────────────────────────────────

const inputCn =
    'h-8 w-full rounded-lg border border-secondary-200 dark:border-dark-600 bg-white dark:bg-dark-800 px-2.5 text-sm text-dark-900 dark:text-dark-50 tabular-nums focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500';

function PanelHeader({ title, meta }: { title: string; meta?: string }) {
    return (
        <div className="flex items-center justify-between px-3 h-9 shrink-0 border-b border-secondary-200 dark:border-dark-600 bg-zinc-50/60 dark:bg-dark-900/30">
            <span className="text-[11px] font-semibold uppercase tracking-wider text-dark-500 dark:text-dark-400">{title}</span>
            {meta && (
                <span className="text-[11px] tabular-nums text-dark-500 dark:text-dark-400 bg-zinc-100 dark:bg-dark-700 rounded-full px-1.5 py-0.5">{meta}</span>
            )}
        </div>
    );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <section className="py-3.5 first:pt-3 space-y-2.5">
            {title && <h4 className="text-[11px] font-semibold uppercase tracking-wider text-dark-400 dark:text-dark-500">{title}</h4>}
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
    el, editing, onStartEdit, onCommit,
}: { el: Text; editing: boolean; onStartEdit: () => void; onCommit: (v: string) => void }) {
    const ref = React.useRef<HTMLSpanElement>(null);
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

    return (
        <span
            ref={ref}
            contentEditable={editing}
            suppressContentEditableWarning
            onDoubleClick={onStartEdit}
            onBlur={(e) => onCommit(e.currentTarget.textContent ?? '')}
            onKeyDown={(e) => {
                if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); e.currentTarget.blur(); }
                else if (e.key === 'Escape') { e.currentTarget.textContent = el.content; e.currentTarget.blur(); }
            }}
            className={`whitespace-nowrap leading-none ${editing ? 'cursor-text outline-none' : ''}`}
            style={{ fontSize: el.fontSize, fontWeight: el.bold ? 700 : 400, color: el.color }}
        >
            {editing ? null : el.content}
        </span>
    );
}

PdfTemplateEdit.layout = (page: React.ReactNode) => <AppLayout>{page}</AppLayout>;
