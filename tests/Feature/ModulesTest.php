<?php

it('returns modules when authenticated', function () {

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
