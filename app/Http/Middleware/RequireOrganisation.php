<?php

namespace App\Http\Middleware;

use App\Support\Yard\CurrentActor;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireOrganisation
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var CurrentActor|null $actor */
        $actor = $request->attributes->get('yard.actor');

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

        $request->attributes->set('yard.actor', CurrentActor::fromShunt(
            data: [
                'id' => $actor->userId,
                'email' => $actor->email,
                'name' => $actor->name,
                'status' => $actor->status,
                'memberships' => $actor->memberships,
            ],
            scopes: $actor->scopes,
            activeOrganisationId: $organisationId,
        ));

        return $next($request);
    }
}
