# Laravel Inertia Permission

[![Latest Version on Packagist](https://img.shields.io/packagist/v/TwintyLabs/laravel-inertia-permission.svg?style=flat-square)](https://packagist.org/packages/TwintyLabs/laravel-inertia-permission)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/TwintyLabs/laravel-inertia-permission/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/TwintyLabs/laravel-inertia-permission/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/TwintyLabs/laravel-inertia-permission.svg?style=flat-square)](https://packagist.org/packages/TwintyLabs/laravel-inertia-permission)

Generate TypeScript types from [Spatie Laravel Permission](https://github.com/spatie/laravel-permission) for [Inertia.js](https://inertiajs.com) applications (React & Vue). This package automatically generates type-safe `Role` and `Permission` TypeScript types from your database, along with ready-to-use utils and hooks/composables for your frontend.

## Requirements

- PHP 8.4+
- Laravel 11.0+
- [spatie/laravel-permission](https://github.com/spatie/laravel-permission) ^6.0
- Inertia.js with React or Vue

## Installation

Install the package via composer:

```bash
composer require twintylabs/laravel-inertia-permission
```

Publish the config file:

```bash
php artisan vendor:publish --tag=inertia-permission-config
```

This is the contents of the published config file:

```php
<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    /*
     * The model used for roles.
     * Change this if you use a custom Role model.
     */
    'role_model' => Role::class,

    /*
     * The model used for permissions.
     * Change this if you use a custom Permission model.
     */
    'permission_model' => Permission::class,

    /*
     * The role name that bypasses all permission checks.
     */
    'super_admin_role' => 'Super Admin',

    /*
     * The frontend framework to generate stubs for.
     * Supported: "react", "vue"
     */
    'framework' => 'react',

    /*
     * Output path for the generated TypeScript types file.
     */
    'output_path' => resource_path('js/types/roles-permissions.ts'),

    /*
     * Output path for the generated utils stub.
     * Supported placeholders: {framework}
     */
    'stubs' => [
        'react' => [
            'utils' => resource_path('js/utils/role-permission.ts'),
            'hook' => resource_path('js/hooks/use-role-permission.ts'),
        ],
        'vue' => [
            'utils' => resource_path('js/utils/role-permission.ts'),
            'composable' => resource_path('js/composables/use-role-permission.ts'),
        ],
    ],
];

```

## Usage

### 1. Share roles and permissions via Inertia

In your `HandleInertiaRequests` middleware, share the roles and permissions to the frontend:

```php
// app/Http/Middleware/HandleInertiaRequests.php

public function share(Request $request): array
{
    $user = $request->user();

    return [
        ...parent::share($request),
        'roles'       => $user ? $user->roles->pluck('name')->values()->toArray() : [],
        'permissions' => $user ? $user->getPermissionsViaRoles()->pluck('name')->unique()->values()->toArray() : [],
    ];
}
```

### 2. Generate TypeScript types

Run the command to generate TypeScript types from your database:

```bash
php artisan inertia-permission:generate
```

This will generate `resources/js/types/roles-permissions.ts`:

```typescript
// ⚠️ AUTO-GENERATED — do not edit manually!
// Run: php artisan inertia-permission:generate

export type Role =
    | 'Super Admin'
    | 'Admin'
    | 'Manager'
    | 'Employee';

export type Permission =
    | 'view-user'
    | 'create-user'
    | 'edit-user'
    | 'delete-user';

export const SUPER_ADMIN_ROLE = 'Super Admin' as const;
```

### 3. Publish utils and hooks/composables

**React:**
```bash
php artisan inertia-permission:generate --with-stubs --framework=react
```

**Vue:**
```bash
php artisan inertia-permission:generate --with-stubs --framework=vue
```

Use `--force` to overwrite existing stubs:
```bash
php artisan inertia-permission:generate --with-stubs --force
```

### 4. Use in your frontend

**React:**
```tsx
import { useRolePermission } from '@/hooks/use-role-permission';

export default function MyComponent() {
    const { can, canAny, canAll, hasRole, hasAnyRole, hasEveryRole } = useRolePermission();

    return (
        <div>
            {/* Permission checks */}
            {can('edit-user') && <button>Edit User</button>}
            {canAny(['edit-user', 'delete-user']) && <DropdownMenu />}
            {canAll(['view-user', 'edit-user']) && <UserPanel />}

            {/* Role checks */}
            {hasRole('Admin') && <AdminPanel />}
            {hasAnyRole(['Admin', 'Manager']) && <ReportMenu />}
            {hasEveryRole(['Admin', 'Manager']) && <FullAccess />}
        </div>
    );
}
```

**Vue:**
```vue
<script setup lang="ts">
import { useRolePermission } from '@/composables/use-role-permission';

const { can, canAny, hasRole, hasAnyRole } = useRolePermission();
</script>

<template>
    <div>
        <button v-if="can('edit-user')">Edit User</button>
        <DropdownMenu v-if="canAny(['edit-user', 'delete-user'])" />
        <AdminPanel v-if="hasRole('Admin')" />
        <ReportMenu v-if="hasAnyRole(['Admin', 'Manager'])" />
    </div>
</template>
```

### Available methods

| Method | Description |
|---|---|
| `can(permission)` | Check if user has a specific permission |
| `canAny(permissions[])` | Check if user has at least one of the given permissions |
| `canAll(permissions[])` | Check if user has all of the given permissions |
| `hasRole(role)` | Check if user has a specific role |
| `hasAnyRole(roles[])` | Check if user has at least one of the given roles |
| `hasEveryRole(roles[])` | Check if user has all of the given roles |

> **Note:** All methods automatically return `true` for the `Super Admin` role (configurable via `super_admin_role` in config).

### Custom Role/Permission model

If you use a custom model, update the config:

```php
// config/inertia-permission.php

'role_model'       => \App\Models\Role::class,
'permission_model' => \App\Models\Permission::class,
```

### Re-generate types

Re-run the command every time you add or update roles and permissions in your database:

```bash
php artisan inertia-permission:generate
```

You can also automate this in your seeder:

```php
// database/seeders/PermissionSeeder.php

public function run(): void
{
    // ... create roles and permissions

    $this->command->call('inertia-permission:generate');
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Edi Kurniawan](https://github.com/edikurniawan-dev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.