<?php

it('loads the package configuration', function () {
    expect(config('restotech-standard.package.name'))->toBe('restotech/standard');
    expect(config('restotech-standard.route_prefixes.back_office'))->toBe('restotech/admin');
    expect(config('restotech-standard.views.theme'))->toBe('minimal');
});
