/* Shared types for cash-flow pages */

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface FilterOption {
    label: string;
    value: number;
    disabled?: boolean;
}

export interface CashFlowStats {
    total_amount: number;
    total_count: number;
}

/* ─── Income page ──────────────────────────────────── */

export interface IncomeRow {
    uid: string;
    id: number;
    source_type: 'payment' | 'transaction' | string;
    date: string;
    amount: number;
    reference_number: string | null;
    invoice_number: string | null;
    client_name: string | null;
    bank_name: string;
    category_id: number | null;
    category_label: string | null;
    description: string | null;
    attachment_url: string | null;
    attachment_name: string | null;
}

export interface IncomeFilters {
    date_from: string | null;
    date_to: string | null;
    clients: number[];
    categories: number[];
    search: string | null;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
    page: number;
}

/* ─── Expenses page ────────────────────────────────── */

export interface ExpenseRow {
    id: number;
    transaction_date: string;
    amount: number;
    description: string | null;
    reference_number: string | null;
    category_id: number;
    category_label: string | null;
    bank_name: string;
    account_name: string;
    attachment_url: string | null;
    attachment_name: string | null;
}

export interface ExpenseFilters {
    date_from: string | null;
    date_to: string | null;
    categories: number[];
    bank_accounts: number[];
    search: string | null;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
    page: number;
}

/* ─── Transfers page ───────────────────────────────── */

export interface TransferRow {
    id: number;
    debit_id: number | null;
    transaction_date: string;
    reference_number: string | null;
    amount: number;
    total_debit: number;
    admin_fee: number;
    description: string | null;
    from_account: {
        id: number;
        account_name: string;
        bank_name: string;
    } | null;
    to_account: {
        id: number;
        account_name: string;
        bank_name: string;
    };
    attachment_url: string | null;
    attachment_name: string | null;
}

export interface TransferFilters {
    date_from: string | null;
    date_to: string | null;
    bank_accounts: number[];
    search: string | null;
    per_page: number;
    page: number;
}
