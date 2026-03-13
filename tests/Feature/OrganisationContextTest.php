<?php

it('returns organisation context with valid organisation header', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-1')
        ->getJson('/api/context')
        ->assertOk()
        ->assertJsonPath('data.organisation_id', 'org-1');
});

it('rejects organisation context without organisation header', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->getJson('/api/context')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Organisation context is required.',
        ]);
});

it('rejects organisation context with invalid organisation header', function () {
    $this->fakeShuntActor();

    $this->actingAsShuntUser()
        ->withOrganisation('org-999')
        ->getJson('/api/context')
        ->assertForbidden()
        ->assertJson([
            'message' => 'Invalid organisation context.',
        ]);
});
