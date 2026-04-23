<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\AdminUser;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $adminUser = Auth::user();

        if (! $adminUser instanceof AdminUser) {
            abort(403);
        }

        $organisationId = $adminUser->getCurrentOrganisationId();

        if (! $organisationId) {
            abort(403);
        }

        $data['organisation_id'] = $organisationId;
        $data['guard_name'] = 'web';

        return $data;
    }
}
