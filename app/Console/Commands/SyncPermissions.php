<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    protected $signature = 'api:sync-permissions';

    protected $description = 'Sync permissions and roles from core and enabled API modules';

    public function handle(): int
    {
        foreach (config('permissions.permissions', []) as $permissionName) {
            Permission::findOrCreate($permissionName);
            $this->line("Permission ready: {$permissionName}");
        }

        $modules = config('api.modules', []);

        if ($modules === []) {
            $this->warn('No enabled modules configured.');
        }

        foreach ($modules as $module => $value) {
            $path = base_path("modules/" . ucfirst($module) . "/config/permissions.php");
            if (! File::exists($path)) {
                $this->warn("No permissions config found for module [{$module}] at [{$path}]");

                continue;
            }

            $definition = require $path;

            if (! is_array($definition)) {
                $this->warn("Permissions config for module [{$module}] did not return an array.");

                continue;
            }

            $this->info("Syncing module [{$module}]");

            foreach ($definition['permissions'] ?? [] as $permissionName) {
                Permission::findOrCreate($permissionName, 'web');

                $this->line("  Permission synced: {$permissionName}");
            }

            foreach ($definition['roles'] ?? [] as $roleName => $permissions) {
                $role = Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                    'organisation_id' => null,
                ]);

                $role->syncPermissions($permissions);

                $this->line("  Role synced: {$roleName}");
            }
        }

        $allPermissions = Permission::query()->pluck('name')->all();

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
            'organisation_id' => null,
        ]);

        $superAdmin->syncPermissions($allPermissions);

        $this->info('Super admin role synced with all permissions.');

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Permission sync complete.');

        return self::SUCCESS;
    }
}
