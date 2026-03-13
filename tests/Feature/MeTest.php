<?php

it('returns the current actor from shunt', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withHeader('X-Organisation-Id', 'org-1')
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.user_id', 'user-123')
        ->assertJsonPath('data.email', 'test@example.com')
        ->assertJsonPath('data.active_organisation_id', 'org-1')
        ->assertJsonPath('data.scopes.0', 'profile.read');
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/me')
        ->assertUnauthorized();
});

function makeFakeJwt(array $payload): string
{
    $header = base64_encode(json_encode(['alg' => 'none', 'typ' => 'JWT']));
    $body = base64_encode(json_encode($payload));

    $header = rtrim(strtr($header, '+/', '-_'), '=');
    $body = rtrim(strtr($body, '+/', '-_'), '=');

    return $header . '.' . $body . '.';
}
