<?php

namespace App\Http\Middleware;

use App\Services\Auth\AuthIdentityClient;
use App\Services\Auth\SyncAuthUser;
use App\Support\CurrentActor;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResolveAuthActor
{
    public function __construct(
        protected AuthIdentityClient $authIdentityClient,
        protected SyncAuthUser $syncAuthUser,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        try {
            $resolved = $this->authIdentityClient->resolve(
                token: $token,
                activeOrganisationId: null,
            );
        } catch (Throwable) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = $this->syncAuthUser->handle([
            'id' => $resolved->userId,
            'email' => $resolved->email,
            'name' => $resolved->name,
            'status' => $resolved->status,
        ]);

        $actor = CurrentActor::fromAuth(
            data: [
                'id' => $resolved->userId,
                'email' => $resolved->email,
                'name' => $resolved->name,
                'status' => $resolved->status,
                'memberships' => $resolved->memberships,
            ],
            scopes: $resolved->scopes,
            activeOrganisationId: null,
            user: $user,
        );

        $request->attributes->set('api.actor', $actor);

        return $next($request);
    }
}
