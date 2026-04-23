<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\AdminUser;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $adminUser = Auth::user();

        $organisationId = null;

        if ($adminUser instanceof AdminUser) {
            $organisationId = $adminUser->getCurrentOrganisationId();
        }

        $data['access_organisation_id'] = $organisationId;
        $data['access_roles'] = $organisationId
            ? $this->record->getRoleNamesInOrganisation($organisationId)
            : [];

        return $data;
    }

    protected function afterSave(): void
    {
        $adminUser = Auth::user();

        if (! $adminUser instanceof AdminUser) {
            return;
        }

        $organisationId = $adminUser->getCurrentOrganisationId();

        if (! $organisationId) {
            return;
        }

        $roles = $this->data['access_roles'] ?? [];

        $this->record->syncRolesInOrganisation($roles, $organisationId);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
