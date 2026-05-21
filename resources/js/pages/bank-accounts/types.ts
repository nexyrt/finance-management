/* Shared types for the bank-accounts page family */

export interface AccountListItem {
    id: number;
    account_name: string;
    account_number: string;
    bank_name: string;
    branch: string | null;
    initial_balance: number;
    balance: number;
    trend: 'up' | 'down';
}

export interface OverallSummary {
    total_balance: number;
    income: number;
    expense: number;
}

export interface AccountStats {
    total_income: number;
    total_expense: number;
    net_cashflow: number;
    transaction_count: number;
}

export interface ChartMonth {
    month: string;
    income: number;
    expense: number;
}

export interface CategoryBreakdown {
    name: string;
    total: number;
}

export interface PeriodInfo {
    start: string | null;
    end: string | null;
    label: string;
    is_all_time: boolean;
}

export interface AccountDetail {
    period: PeriodInfo;
    stats: AccountStats;
    chart_months: ChartMonth[];
    category_breakdown: CategoryBreakdown[];
}

export interface TransactionRow {
    id: number;
    description: string;
    transaction_type: 'credit' | 'debit';
    transaction_date: string;
    amount: number;
    reference_number: string | null;
    category: {
        id: number;
        label: string;
        parent_label: string | null;
    } | null;
    attachment_url: string | null;
    attachment_name: string | null;
}

export interface PaymentRow {
    id: number;
    payment_date: string;
    amount: number;
    payment_method: 'cash' | 'bank_transfer' | string;
    reference_number: string | null;
    invoice_number: string | null;
    invoice_status: string;
    client_name: string;
    client_type: string;
    attachment_url: string | null;
    attachment_name: string | null;
}

export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface CategoryOption {
    label: string;
    value: number;
    disabled?: boolean;
}

export interface AccountOption {
    label: string;
    value: number;
}
