<?php

namespace App\Http\Middleware;

use App\Support\CurrentActor;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class RequireOrganisation
{
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $actor = $request->attributes->get('api.actor');

        if (! $actor) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $organisationId = $request->header('X-Organisation-Id');

        if (! $organisationId) {
            return new JsonResponse([
                'message' => 'Organisation context is required.',
            ], 403);
        }

        if (! $actor->hasOrganisation($organisationId)) {
            return new JsonResponse([
                'message' => 'Invalid organisation context.',
            ], 403);
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($organisationId);

        if ($actor->user) {
            $actor->user->unsetRelation('roles');
            $actor->user->unsetRelation('permissions');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $request->attributes->set('api.actor', CurrentActor::fromAuth(
            data: [
                'id' => $actor->userId,
                'email' => $actor->email,
                'name' => $actor->name,
                'status' => $actor->status,
                'memberships' => $actor->memberships,
            ],
            scopes: $actor->scopes,
            activeOrganisationId: $organisationId,
            user: $actor->user,
        ));

        return $next($request);
    }
}
