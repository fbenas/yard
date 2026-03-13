<?php

use Illuminate\Support\Facades\Http;

it('returns version when authenticated', function () {

    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson(config('yard.modules'));
});

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/modules')
        ->assertUnauthorized();
});
