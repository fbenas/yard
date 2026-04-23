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

    protected $description = 'Sync permissions and roles';

    public function handle(): int
    {
        foreach (config('permissions.permissions', []) as $permissionName) {
            Permission::findOrCreate($permissionName);
            $this->line("Permission ready: {$permissionName}");
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Permission sync complete.');

        return self::SUCCESS;
    }
}
