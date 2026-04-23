<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->disabled(),
                ]),

            Section::make('Access')
                ->visible(fn (string $operation) => $operation === 'edit')
                ->schema([
                    Hidden::make('access_organisation_id')
                        ->dehydrated(false)
                        ->default(fn () => static::getCurrentOrganisationId()),

                    Select::make('access_roles')
                        ->label('Roles')
                        ->multiple()
                        ->options(fn () => static::getRoleOptions())
                        ->preload()
                        ->dehydrated(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('current_org_roles')
                    ->label('Roles')
                    ->badge()
                    ->state(function (User $record) {
                        $organisationId = static::getCurrentOrganisationId();

                        if (! $organisationId) {
                            return [];
                        }

                        return $record->getRoleNamesInOrganisation($organisationId);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $organisationId = static::getCurrentOrganisationId();

        if (! $organisationId) {
            return $query->whereRaw('1 = 0');
        }

        $pivotTable = config('permission.table_names.model_has_roles');
        $roleTable = config('permission.table_names.roles');

        return $query->whereHas('roles', function (Builder $roleQuery) use ($pivotTable, $roleTable, $organisationId) {
            $roleQuery
                ->where("{$pivotTable}.organisation_id", $organisationId)
                ->where("{$roleTable}.organisation_id", $organisationId);
        });
    }

    protected static function getCurrentOrganisationId(): ?string
    {
        $adminUser = Auth::user();

        if (! $adminUser instanceof User) {
            return null;
        }

        return $adminUser->getCurrentOrganisationId();
    }

    protected static function getRoleOptions(): array
    {
        $organisationId = static::getCurrentOrganisationId();

        if (! $organisationId) {
            return [];
        }

        return Role::query()
            ->where('organisation_id', $organisationId)
            ->orderBy('name')
            ->pluck('name', 'name')
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
