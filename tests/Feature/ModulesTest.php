<?php

it('returns modules when authenticated', function () {

    $this->fakeAuthActor();

    $this->actingAsAuthUser()
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJsonPath('data.0', 'products');
    });

it('rejects requests without a bearer token', function () {
    $this->getJson('/api/modules')
        ->assertUnauthorized();
});
