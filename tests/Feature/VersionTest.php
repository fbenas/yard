<?php

use Illuminate\Support\Facades\Http;

it('returns version when authenticated', function () {

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/version')
        ->assertOk()
        ->assertJsonPath('app', config('app.name'));
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/version')
        ->assertUnauthorized();
});
