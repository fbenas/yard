<?php

namespace Tests\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Http;

trait Auth
{
    protected function fakeAuthActor(array $overrides = []): array
    {
        $actor = array_merge([
            'id' => 'user-123',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'active',
            'memberships' => [
                [
                    'id' => 'membership-1',
                    'role' => 'owner',
                    'status' => 'active',
                    'organisation' => [
                        'id' => 'org-1',
                        'name' => 'Test Org',
                        'slug' => 'test-org',
                        'status' => 'active',
                    ],
                ],
            ],
        ], $overrides);

        Http::fake([
            rtrim(config('api.auth.base_url'), '/') . '/api/oauth/me' => Http::response([
                'data' => $actor,
            ]),
        ]);

        return $actor;
    }

    protected function createLocalUser(array $overrides = []): \App\Models\User
    {
        $data = array_merge([
            'auth_user_id' => 'user-123',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'status' => 'active',
        ], $overrides);

        return \App\Models\User::query()->updateOrCreate(
            [
                'auth_user_id' => $data['auth_user_id'],
            ],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'status' => $data['status'],
            ],
        );
    }

    protected function actingAsAuthUser(array $jwtPayload = [])
    {
        $token = $this->makeFakeJwt(array_merge([
            'scopes' => ['profile.read'],
        ], $jwtPayload));

        return $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    protected function withOrganisation(string $organisationId)
    {
        return $this->withHeader('X-Organisation-Id', $organisationId);
    }

    protected function makeFakeJwt(array $payload): string
    {
        $header = base64_encode(json_encode([
            'alg' => 'none',
            'typ' => 'JWT',
        ]));

        $body = base64_encode(json_encode($payload));

        $header = rtrim(strtr($header, '+/', '-_'), '=');
        $body = rtrim(strtr($body, '+/', '-_'), '=');

        return $header . '.' . $body . '.';
    }
}
