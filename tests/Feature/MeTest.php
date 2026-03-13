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
        ->assertJsonPath('data.active_organisation_id', 'org-1')
        ->assertJsonPath('data.scopes.0', 'profile.read');
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/me')
        ->assertUnauthorized();
});

it('rejects requests without organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/me')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Organisation context is required.',
        ]);
});

it('rejects requests with an organisation the actor does not belong to', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-999')
        ->getJson('/api/me')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Invalid organisation context.',
        ]);
});
