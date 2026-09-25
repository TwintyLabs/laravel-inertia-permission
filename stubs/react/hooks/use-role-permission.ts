// ⚠️ AUTO-GENERATED !
// Run: php artisan inertia-permission:generate --with-stubs

import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';

import { can, canAll, canAny, hasAnyRole, hasEveryRole, hasRole } from '@/utils/role-permission';

import type { Permission, Role } from '@/types/roles-permissions';

interface PagePropsWithAuth {
    roles: string[];
    permissions: string[];
    [key: string]: unknown;
}

export function useRolePermission() {
    const { roles, permissions } = usePage<PagePropsWithAuth>().props;

    const roleSet = useMemo(() => new Set<string>(roles), [roles]);
    const permissionSet = useMemo(() => new Set<string>(permissions), [permissions]);

    return {
        // Permission checks
        can: (permission: Permission) => can(roleSet, permissionSet, permission),
        canAny: (permissions: Permission[]) => canAny(roleSet, permissionSet, permissions),
        canAll: (permissions: Permission[]) => canAll(roleSet, permissionSet, permissions),

        // Role checks
        hasRole: (role: Role) => hasRole(roleSet, role),
        hasAnyRole: (roles: Role[]) => hasAnyRole(roleSet, roles),
        hasEveryRole: (roles: Role[]) => hasEveryRole(roleSet, roles),
    };
}
