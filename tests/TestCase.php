<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Restotech\Standard\StandardServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('r', 32)));
        $app['config']->set('database.default', env('DB_CONNECTION', 'mysql'));
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3307'),
            'database' => env('DB_DATABASE', 'restotech_standard_test'),
            'username' => env('DB_USERNAME', 'restotech'),
            'password' => env('DB_PASSWORD', 'restotech'),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([]) : [],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            StandardServiceProvider::class,
        ];
    }
}
