<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->prefix(config('restotech-standard.route_prefixes.back_office', 'restotech/admin'))
    ->name('restotech.standard.back_office.')
    ->group(function (): void {
        Route::get('/_info', function () {
            return response()->json([
                'group' => 'back_office',
                'package' => config('restotech-standard.package.name'),
                'namespace' => config('restotech-standard.package.namespace'),
                'version' => config('restotech-standard.package.version'),
                'prefix' => config('restotech-standard.route_prefixes.back_office', 'restotech/admin'),
                'laravel' => app()->version(),
            ]);
        })->name('info');
    });
