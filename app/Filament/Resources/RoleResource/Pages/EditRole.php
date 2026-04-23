<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\AdminUser;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $adminUser = Auth::user();

        if (! $adminUser instanceof AdminUser) {
            abort(403);
        }

        $organisationId = $adminUser->getCurrentOrganisationId();

        if (! $organisationId) {
            abort(403);
        }

        if ($this->record->organisation_id !== $organisationId) {
            abort(403);
        }

        $data['organisation_id'] = $this->record->organisation_id;
        $data['guard_name'] = $this->record->guard_name ?? 'web';

        return $data;
    }
}
