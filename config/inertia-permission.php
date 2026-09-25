<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return [
    /*
    |--------------------------------------------------------------------------
    | Role Model
    |--------------------------------------------------------------------------
    | The model used for roles. Change this if you use a custom Role model.
    |
    */
    'role_model' => Role::class,

    /*
    |--------------------------------------------------------------------------
    | Permission Model
    |--------------------------------------------------------------------------
    | The model used for permissions. Change this if you use a custom
    | Permission model.
    |
    */
    'permission_model' => Permission::class,

    /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    | The role name that bypasses all permission checks.
    |
    */
    'super_admin_role' => 'Super Admin',

    /*
    |--------------------------------------------------------------------------
    | Frontend Framework
    |--------------------------------------------------------------------------
    | The frontend framework to generate stubs for.
    | Supported: "react", "vue"
    |
    */
    'framework' => 'react',

    /*
    |--------------------------------------------------------------------------
    | TypeScript Output Path
    |--------------------------------------------------------------------------
    | Output path for the generated TypeScript types file.
    |
    */
    'output_path' => resource_path('js/types/roles-permissions.ts'),

    /*
    |--------------------------------------------------------------------------
    | Stub Output Paths
    |--------------------------------------------------------------------------
    | Output paths for the generated framework stubs.
    | Supported placeholders: {framework}
    |
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
