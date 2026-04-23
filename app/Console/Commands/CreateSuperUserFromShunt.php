<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class CreateSuperUserFromShunt extends Command
{
    protected $signature = 'yard:super-user
        {--shunt-user-id= : The Shunt user ID}
        {--email= : The user email}
        {--organisation= : The organisation ID}
        {--name= : The user name}';

    protected $description = 'Create or update a Yard super user for a specific organisation';

    public function handle(): int
    {
        $shuntUserId = $this->option('shunt-user-id');
        $email = $this->option('email');
        $organisationId = $this->option('organisation');
        $name = $this->option('name');

        if (! $shuntUserId || ! $email || ! $organisationId) {
            $this->error('You must provide --shunt-user-id, --email, and --organisation.');

            return self::INVALID;
        }

        return DB::transaction(function () use ($shuntUserId, $email, $organisationId, $name) {
            $user = User::query()
                ->where('auth_user_id', $shuntUserId)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                if (! $name) {
                    $this->error('You must provide --name when creating a new user.');

                    return self::INVALID;
                }

                $user = new User();
                $user->auth_user_id = $shuntUserId;
                $user->email = $email;
                $user->name = $name;
                $user->save();

                $this->info("Created Yard user [{$user->id}] for [{$email}].");
            } else {
                $dirty = false;

                if (! $user->auth_user_id) {
                    $user->auth_user_id = $shuntUserId;
                    $dirty = true;
                }

                if ($user->auth_user_id !== $shuntUserId) {
                    $this->error("Existing user [{$user->id}] has a different auth_user_id [{$user->auth_user_id}].");

                    return self::FAILURE;
                }

                if ($user->email !== $email) {
                    $this->error("Existing user [{$user->id}] has a different email [{$user->email}].");

                    return self::FAILURE;
                }

                if ($name && $user->name !== $name) {
                    $user->name = $name;
                    $dirty = true;
                }

                if ($dirty) {
                    $user->save();
                    $this->line("Updated existing Yard user [{$user->id}].");
                } else {
                    $this->line("Using existing Yard user [{$user->id}].");
                }
            }

            $role = Role::query()->firstOrCreate(
                [
                    'name' => 'super_admin',
                    'guard_name' => 'web',
                    'organisation_id' => $organisationId,
                ]
            );

            $allPermissions = Permission::query()
                ->where('guard_name', 'web')
                ->pluck('name')
                ->all();

            $role->syncPermissions($allPermissions);

            $this->line('Ensured role [super_admin] exists and has all current permissions.');

            $alreadyAssigned = $user->roles()
                ->where('roles.id', $role->id)
                ->wherePivot('organisation_id', $organisationId)
                ->exists();

            if ($alreadyAssigned) {
                $this->info("User [{$user->id}] already has [super_admin] in organisation [{$organisationId}].");

                app(PermissionRegistrar::class)->forgetCachedPermissions();

                return self::SUCCESS;
            }

            $user->roles()->syncWithoutDetaching([
                $role->id => [
                    'organisation_id' => $organisationId,
                ],
            ]);

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            $this->info("Assigned role [super_admin] to user [{$user->id}] in organisation [{$organisationId}].");

            return self::SUCCESS;
        });
    }
}
