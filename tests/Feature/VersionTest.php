<?php

it('returns version when authenticated with organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-1')
        ->getJson('/api/version')
        ->assertOk()
        ->assertJsonPath('app', config('app.name'));
});

it('rejects unauthenticated access to version', function () {
    $this->getJson('/api/version')
        ->assertUnauthorized();
});

it('rejects version access without organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/version')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Organisation context is required.',
        ]);
});

it('rejects version access with invalid organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-999')
        ->getJson('/api/version')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Invalid organisation context.',
        ]);
});
