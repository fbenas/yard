<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource as FilamentResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class Resource extends FilamentResource
{
    protected static function getPrefix(): string
    {
        return Str::plural(Str::snake(Str::before(class_basename(static::class), 'Resource')));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo(static::getPrefix() . '.list') ?? false;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->hasPermissionTo(static::getPrefix() . '.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo(static::getPrefix() . '.create') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasPermissionTo(static::getPrefix() . '.update') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return $record->organisation_id === auth()->user()?->getCurrentOrganisationId() &&
            auth()->user()?->hasPermissionTo(static::getPrefix() . '.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasPermissionTo(static::getPrefix() . '.delete') ?? false;
    }
}
