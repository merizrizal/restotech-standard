<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

it('migrates the core package tables on mariadb without creating host users', function () {
    Artisan::call('migrate:fresh', [
        '--force' => true,
    ]);

    foreach ([
        'restotech_user_profiles',
        'restotech_roles',
        'restotech_permissions',
        'restotech_role_permissions',
        'restotech_settings',
        'restotech_number_sequences',
        'restotech_transaction_days',
        'restotech_shifts',
        'restotech_cashier_balances',
    ] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
    }

    expect(Schema::hasTable('users'))->toBeFalse();
});
