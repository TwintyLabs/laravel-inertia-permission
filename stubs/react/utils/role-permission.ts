// ⚠️ AUTO-GENERATED !
// Run: php artisan inertia-permission:generate --with-stubs

import type { Permission, Role } from '@/types/roles-permissions';
import { SUPER_ADMIN_ROLE } from '@/types/roles-permissions';

/** Check if the user has a specific permission. */
export function can(userRoles: Set<string>, userPermissions: Set<string>, allowedPermission: Permission): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return userPermissions.has(allowedPermission);
}

/** Check if the user has at least one of the given permissions. */
export function canAny(
    userRoles: Set<string>,
    userPermissions: Set<string>,
    allowedPermissions: Permission[],
): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return allowedPermissions.some((p) => userPermissions.has(p));
}

/** Check if the user has all of the given permissions. */
export function canAll(
    userRoles: Set<string>,
    userPermissions: Set<string>,
    allowedPermissions: Permission[],
): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return allowedPermissions.every((p) => userPermissions.has(p));
}

/** Check if the user has a specific role. */
export function hasRole(userRoles: Set<string>, allowedRole: Role): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return userRoles.has(allowedRole);
}

/** Check if the user has at least one of the given roles. */
export function hasAnyRole(userRoles: Set<string>, allowedRoles: Role[]): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return allowedRoles.some((r) => userRoles.has(r));
}

/** Check if the user has all of the given roles. */
export function hasEveryRole(userRoles: Set<string>, allowedRoles: Role[]): boolean {
    if (userRoles.has(SUPER_ADMIN_ROLE)) return true;
    return allowedRoles.every((r) => userRoles.has(r));
}
