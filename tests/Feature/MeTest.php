<?php

it('returns the current actor from shunt', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser([
        'scopes' => ['profile.read'],
    ])
        ->withOrganisation('org-1')
        ->getJson('/api/me')
        ->assertOk()
        ->assertJsonPath('data.user_id', 'user-123')
        ->assertJsonPath('data.email', 'test@example.com')
        ->assertJsonPath('data.scopes.0', 'profile.read');
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/me')
        ->assertUnauthorized();
});
