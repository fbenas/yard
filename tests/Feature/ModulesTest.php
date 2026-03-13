<?php

it('returns modules when authenticated', function () {

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-1')
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson(config('yard.modules'));
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/modules')
        ->assertUnauthorized();
});

it('rejects modules access without organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/modules')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Organisation context is required.',
        ]);
});

it('rejects modules access with invalid organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-999')
        ->getJson('/api/modules')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Invalid organisation context.',
        ]);
});
