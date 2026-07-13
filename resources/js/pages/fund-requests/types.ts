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

export interface FundRequestRow {
    id: number;
    request_number: string;
    title: string;
    purpose: string;
    total_amount: number;
    priority: 'low' | 'medium' | 'high' | 'urgent';
    needed_by_date: string;
    status: 'draft' | 'pending' | 'approved' | 'rejected' | 'disbursed';
    user_name: string | null;
    user_id: number;
    reviewed_by_name: string | null;
    reviewed_at: string | null;
    review_notes: string | null;
    disbursed_by_name: string | null;
    disbursement_date: string | null;
    disbursement_notes: string | null;
    attachment_url: string | null;
    attachment_name: string | null;
    items_count: number;
    items: {
        id: number;
        description: string;
        category_label: string | null;
        quantity: number;
        unit_price: number;
        amount: number;
    }[];
    disbursement_account_name: string | null;
    disbursement_attachment_url: string | null;
    disbursement_attachment_name: string | null;
    can_edit: boolean;
    can_delete: boolean;
    can_submit: boolean;
    can_review: boolean;
    can_disburse: boolean;
    created_at: string;
}

export interface FundRequestFilters {
    tab: string;
    search: string | null;
    status: string | null;
    priority: string | null;
    user_id: string | null;
    month: string | null;
    per_page: number;
    page: number;
}

export interface FundRequestStats {
    total: number;
    total_amount: number;
    pending_count: number;
    approved_count: number;
    disbursed_count: number;
}

export interface FundRequestItem {
    id?: number;
    description: string;
    category_id: number | null;
    quantity: number;
    unit_price: number;
    amount: number;
    notes: string;
}
