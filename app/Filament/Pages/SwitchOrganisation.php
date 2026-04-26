<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SwitchOrganisation extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.switch-organisation';

    protected static ?string $title = 'Switch organisation';

    public array $organisations = [];

    public ?string $currentOrganisationId = null;

    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);
        $this->organisations = $user->getAvailableOrganisations();
        $this->currentOrganisationId = $user->current_organisation_id;
    }

    public function switchOrganisation(string $organisationId)
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        abort_unless(
            $user->hasOrganisation($organisationId),
            403
        );

        $user->current_organisation_id = $organisationId;
        $user->save();

        Notification::make()
            ->title('Organisation switched')
            ->success()
            ->send();

        return redirect(static::getUrl());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back to dashboard')
                ->url(url('/admin')),
        ];
    }
}
