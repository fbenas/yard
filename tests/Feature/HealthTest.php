<?php

use Illuminate\Support\Facades\Http;

it('returns health', function () {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJsonPath('status', 'ok');
});

it('returns enabled modules when authenticated', function () {

    config()->set('yard.modules', ['catalog', 'inventory']);

    Http::fake([
        'http://localhost:8000/api/oauth/me' => Http::response([
            'data' => [
                'id' => 'user-123',
                'name' => 'Phil',
                'email' => 'phil@example.com',
                'status' => 'active',
                'memberships' => [],
            ],
        ]),
    ]);

    $token = makeFakeJwt([
        'scopes' => ['profile.read'],
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson([
            'data' => ['catalog', 'inventory'],
        ]);
});
