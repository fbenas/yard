<?php

it('returns health', function () {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJsonPath('status', 'ok');
});

it('returns enabled modules when authenticated with organisation context', function () {
    config()->set('yard.modules', ['catalog', 'inventory']);

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-1')
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson([
            'data' => ['catalog', 'inventory'],
        ]);
});

it('rejects modules access without organisation context', function () {
    config()->set('yard.modules', ['catalog', 'inventory']);

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/modules')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Organisation context is required.',
        ]);
});

it('rejects modules access with invalid organisation context', function () {
    config()->set('yard.modules', ['catalog', 'inventory']);

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-999')
        ->getJson('/api/modules')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Invalid organisation context.',
        ]);
});
