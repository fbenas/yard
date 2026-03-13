<?php

use App\Support\CurrentActor;

if (! function_exists('current_actor')) {
    function current_actor(): ?CurrentActor
    {
        return request()->attributes->get('api.actor');
    }
}

if (! function_exists('current_organisation_id')) {
    function current_organisation_id(): ?string
    {
        return current_actor()?->activeOrganisation();
    }
}

if (! function_exists('actor_can')) {
    function actor_can(string $permission): bool
    {
        return current_actor()?->can($permission) ?? false;
    }
}
