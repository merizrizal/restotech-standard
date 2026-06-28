<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')
    ->prefix(config('restotech-standard.route_prefixes.pos', 'restotech/pos'))
    ->name('restotech.standard.pos.')
    ->group(function (): void {
        Route::get('/_info', function () {
            return response()->json([
                'group' => 'pos',
                'package' => config('restotech-standard.package.name'),
                'namespace' => config('restotech-standard.package.namespace'),
                'version' => config('restotech-standard.package.version'),
                'prefix' => config('restotech-standard.route_prefixes.pos', 'restotech/pos'),
                'laravel' => app()->version(),
            ]);
        })->name('info');
    });
