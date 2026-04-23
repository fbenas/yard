<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentOrganisation
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user instanceof User && $user->current_organisation_id) {
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($user->current_organisation_id);
        }

        return $next($request);
    }
}
