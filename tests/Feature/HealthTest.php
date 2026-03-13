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
        ->getJson('/api/modules')
        ->assertOk()
        ->assertJson([
            'data' => ['catalog', 'inventory'],
        ]);
});
