// ⚠️ AUTO-GENERATED !
// Run: php artisan inertia-permission:generate --with-stubs

import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

import { can, canAll, canAny, hasAnyRole, hasEveryRole, hasRole } from '@/utils/role-permission';

import type { Permission, Role } from '@/types/roles-permissions';

export function useRolePermission() {
    const page = usePage();

    const roleSet = computed(() => new Set<string>(page.props.roles as string[]));
    const permissionSet = computed(() => new Set<string>(page.props.permissions as string[]));

    return {
        // Permission checks
        can: (permission: Permission) => can(roleSet.value, permissionSet.value, permission),
        canAny: (permissions: Permission[]) => canAny(roleSet.value, permissionSet.value, permissions),
        canAll: (permissions: Permission[]) => canAll(roleSet.value, permissionSet.value, permissions),

        // Role checks
        hasRole: (role: Role) => hasRole(roleSet.value, role),
        hasAnyRole: (roles: Role[]) => hasAnyRole(roleSet.value, roles),
        hasEveryRole: (roles: Role[]) => hasEveryRole(roleSet.value, roles),
    };
}
