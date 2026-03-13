<?php

it('returns health', function () {
    $this->getJson('/api/health')
        ->assertOk()
        ->assertJsonPath('status', 'ok');
});

it('returns enabled modules when authenticated with organisation context', function () {
    config()->set('api.modules', ['catalog', 'inventory']);

    $this->fakeAuthActor();

    $this->actingAsAuthUser()
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson([
            'data' => ['catalog', 'inventory'],
        ]);
});
