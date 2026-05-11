import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string | null;
}

export interface Auth {
    user: User | null;
    permissions: string[];
    roles: string[];
}

export interface Flash {
    success?: string | null;
    error?: string | null;
    warning?: string | null;
    info?: string | null;
}

export type SharedProps = {
    auth: Auth;
    locale: string;
    flash: Flash;
    ziggy: Config & { location: string };
    [key: string]: unknown;
};
