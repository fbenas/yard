<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

it('returns products for authorised users', function () {
    $this->fakeAuthActor();

    $user = $this->createLocalUser();

    app(PermissionRegistrar::class)->setPermissionsTeamId('org-1');

    Permission::findOrCreate('products.read', 'web');

    $user->givePermissionTo('products.read');

    $this->actingAsAuthUser()
        ->withOrganisation('org-1')
        ->getJson('/api/products/list')
        ->assertOk()
        ->assertJsonPath('data.module', 'products')
        ->assertJsonPath('data.organisation_id', 'org-1');
});
