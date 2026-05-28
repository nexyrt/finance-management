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
    value: number | string;
}

export interface ReimbursementRow {
    id: number;
    title: string;
    description: string | null;
    amount: number;
    amount_paid: number;
    amount_remaining: number;
    expense_date: string;
    category_input: string;
    category_label: string;
    category_id: number | null;
    status: 'draft' | 'pending' | 'approved' | 'rejected' | 'paid';
    payment_status: 'unpaid' | 'partial' | 'paid';
    user_name: string | null;
    user_id: number;
    reviewed_by_name: string | null;
    reviewed_at: string | null;
    review_notes: string | null;
    attachment_url: string | null;
    attachment_name: string | null;
    can_edit: boolean;
    can_delete: boolean;
    can_submit: boolean;
    can_review: boolean;
    can_pay: boolean;
    created_at: string;
}

export interface ReimbursementFilters {
    tab: string;
    search: string | null;
    status: string | null;
    category: string | null;
    date_from: string | null;
    date_to: string | null;
    per_page: number;
    page: number;
}

export interface ReimbursementStats {
    total: number;
    total_amount: number;
    pending_count: number;
    approved_count: number;
    total_paid: number;
}
