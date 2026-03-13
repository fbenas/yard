<?php

it('returns version when authenticated with organisation context', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/version')
        ->assertOk()
        ->assertJsonPath('app', config('app.name'));
});

it('rejects unauthenticated access to version', function () {
    $this->getJson('/api/version')
        ->assertUnauthorized();
});
