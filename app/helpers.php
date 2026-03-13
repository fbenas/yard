<?php

use App\Support\Yard\CurrentActor;

if (! function_exists('current_actor')) {
    function current_actor(): ?CurrentActor
    {
        return request()->attributes->get('yard.actor');
    }
}

if (! function_exists('current_organisation_id')) {
    function current_organisation_id(): ?string
    {
        return current_actor()?->activeOrganisation();
    }
}
