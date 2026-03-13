<?php

namespace App\Http\Middleware;

use App\Services\Shunt\ShuntIdentityClient;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResolveShuntActor
{
    public function __construct(
        protected ShuntIdentityClient $shuntIdentityClient,
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
            $actor = $this->shuntIdentityClient->resolve(
                token: $token
            );
        } catch (Throwable) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $request->attributes->set('yard.actor', $actor);

        return $next($request);
    }
}
