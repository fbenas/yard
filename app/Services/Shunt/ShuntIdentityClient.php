<?php

namespace App\Services\Shunt;

use App\Support\Yard\CurrentActor;
use Illuminate\Support\Facades\Http;

class ShuntIdentityClient
{
    public function resolve(string $token, ?string $activeOrganisationId = null): CurrentActor
    {
        $response = Http::acceptJson()
            ->timeout(config('yard.shunt.timeout'))
            ->withToken($token)
            ->get(rtrim(config('yard.shunt.base_url'), '/') . '/api/oauth/me')
            ->throw();

        $data = $response->json('data');

        $scopes = $this->extractScopesFromToken($token);

        return CurrentActor::fromShunt(
            data: $data,
            scopes: $scopes,
            activeOrganisationId: $activeOrganisationId,
        );
    }

    protected function extractScopesFromToken(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) < 2) {
            return [];
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        if (! is_array($payload)) {
            return [];
        }

        return array_values($payload['scopes'] ?? []);
    }
}
