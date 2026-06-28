<?php

it('registers the back office info route', function () {
    $this->getJson('/restotech/admin/_info')
        ->assertOk()
        ->assertJsonPath('group', 'back_office')
        ->assertJsonPath('package', 'restotech/standard')
        ->assertJsonPath('prefix', 'restotech/admin');
});

it('registers the pos info route', function () {
    $this->getJson('/restotech/pos/_info')
        ->assertOk()
        ->assertJsonPath('group', 'pos')
        ->assertJsonPath('package', 'restotech/standard')
        ->assertJsonPath('prefix', 'restotech/pos');
});

it('registers the api info route', function () {
    $this->getJson('/api/restotech/v1/_info')
        ->assertOk()
        ->assertJsonPath('group', 'api')
        ->assertJsonPath('package', 'restotech/standard')
        ->assertJsonPath('prefix', 'api/restotech/v1');
});
