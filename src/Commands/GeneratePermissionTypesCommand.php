<?php

namespace TwintyLabs\InertiaPermission\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;

class GeneratePermissionTypesCommand extends Command
{
    protected $signature = 'inertia-permission:generate
                            {--framework= : Frontend framework to generate stubs for (react/vue)}
                            {--with-stubs : Also publish utils and hook/composable stubs}
                            {--force : Overwrite existing stubs}';

    protected $description = 'Generate TypeScript types from roles and permissions in database';

    public function handle(): int
    {
        $this->info('Generating permission types...');

        $roleModel = config('inertia-permission.role_model');
        $permissionModel = config('inertia-permission.permission_model');

        if (! is_string($roleModel) || ! class_exists($roleModel)) {
            $this->error("Role model [{$roleModel}] not found.");

            return self::FAILURE;
        }

        if (! is_string($permissionModel) || ! class_exists($permissionModel)) {
            $this->error("Permission model [{$permissionModel}] not found.");

            return self::FAILURE;
        }

        $roles = $roleModel::pluck('name');
        $permissions = $permissionModel::pluck('name');

        if ($roles->isEmpty() && $permissions->isEmpty()) {
            $this->warn('No roles or permissions found in database.');

            return self::FAILURE;
        }

        $this->generateTypes($roles, $permissions);

        if ($this->option('with-stubs')) {
            $this->publishStubs();
        }

        $this->newLine();

        $this->info(
            "✅ Generated {$roles->count()} roles and {$permissions->count()} permissions."
        );

        return self::SUCCESS;
    }

    private function generateTypes($roles, $permissions): void
    {
        $superAdminRole = config(
            'inertia-permission.super_admin_role',
            'Super Admin'
        );

        $roleTypes = $roles->isEmpty()
            ? '    never'
            : $roles->map(
                fn ($role) => "    | '{$role}'"
            )->join("\n");

        $permissionTypes = $permissions->isEmpty()
            ? '    never'
            : $permissions->map(
                fn ($permission) => "    | '{$permission}'"
            )->join("\n");

        $content = <<<TS
        // ⚠️ AUTO-GENERATED — do not edit manually!
        // Run: php artisan inertia-permission:generate

        export type Role =
        {$roleTypes};

        export type Permission =
        {$permissionTypes};

        export const SUPER_ADMIN_ROLE = '{$superAdminRole}' as const;
        TS;

        $content = collect(explode("\n", $content))
            ->map(fn ($line) => ltrim($line))
            ->join("\n");

        $outputPath = config('inertia-permission.output_path');

        if (! is_string($outputPath)) {
            throw new InvalidArgumentException(
                'The inertia-permission.output_path configuration must be a string.'
            );
        }

        $outputDirectory = dirname($outputPath);

        if (! is_dir($outputDirectory)) {
            mkdir($outputDirectory, 0755, true);
        }

        file_put_contents($outputPath, $content);

        $this->line("  📄 Types    → {$outputPath}");
    }

    private function publishStubs(): void
    {
        $framework = $this->option('framework')
            ?? config('inertia-permission.framework', 'react');

        if (! is_string($framework)) {
            $this->error('Framework configuration must be a string.');

            return;
        }

        if (! in_array($framework, ['react', 'vue'], true)) {
            $this->error(
                "Framework [{$framework}] not supported. Use 'react' or 'vue'."
            );

            return;
        }

        $stubs = config("inertia-permission.stubs.{$framework}");

        if (! is_array($stubs)) {
            $this->error(
                "Stub configuration for framework [{$framework}] is invalid."
            );

            return;
        }

        $stubsPath = __DIR__."/../../stubs/{$framework}";
        $force = (bool) $this->option('force');

        foreach ($stubs as $type => $destination) {
            if (! is_string($type) || ! is_string($destination)) {
                $this->error('Invalid stub configuration.');

                return;
            }

            $source = match ($type) {
                'utils' => "{$stubsPath}/utils/role-permission.ts",
                'hook' => "{$stubsPath}/hooks/use-role-permission.ts",
                'composable' => "{$stubsPath}/composables/use-role-permission.ts",
                default => throw new InvalidArgumentException(
                    "Unsupported stub type [{$type}]."
                ),
            };

            $destinationDirectory = dirname($destination);

            if (! is_dir($destinationDirectory)) {
                mkdir($destinationDirectory, 0755, true);
            }

            if (! $force && file_exists($destination)) {
                $this->warn(
                    "  ⚠️  Skipped  → {$destination} ".
                    '(already exists, use --force to overwrite)'
                );

                continue;
            }

            copy($source, $destination);

            $this->line("  📄 Stub     → {$destination}");
        }
    }
}
