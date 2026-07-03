import { usePage } from '@inertiajs/react';
import type { SharedProps } from '@/types';

/**
 * Permission helpers backed by the `auth.permissions` array shared from the server
 * (see HandleInertiaRequests). Frontend gating is UX only — the backend remains the
 * source of truth.
 */
export function useCan() {
    const { auth } = usePage<SharedProps>().props;
    const permissions = auth?.permissions ?? [];

    const can = (permission: string) => permissions.includes(permission);
    const canAny = (perms: string[]) => perms.some((p) => permissions.includes(p));

    return { can, canAny };
}
