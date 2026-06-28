<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')
    ->prefix(config('restotech-standard.route_prefixes.api', 'api/restotech/v1'))
    ->name('restotech.standard.api.')
    ->group(function (): void {
        Route::get('/_info', function () {
            return response()->json([
                'group' => 'api',
                'package' => config('restotech-standard.package.name'),
                'namespace' => config('restotech-standard.package.namespace'),
                'version' => config('restotech-standard.package.version'),
                'prefix' => config('restotech-standard.route_prefixes.api', 'api/restotech/v1'),
                'laravel' => app()->version(),
            ]);
        })->name('info');
    });
