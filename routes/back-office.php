<?php

use Illuminate\Support\Facades\Route;
use Restotech\Standard\Http\Controllers\BackOffice\DiningAreaController;
use Restotech\Standard\Http\Middleware\BackOfficeAuthenticate;

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

        Route::view('/login', 'restotech-standard::back-office.login')->name('login');

        Route::middleware(BackOfficeAuthenticate::class)->group(function (): void {
            Route::get('/', function () {
                return redirect()->route('restotech.standard.back_office.dining-areas.index');
            })->name('home');

            Route::resource('dining-areas', DiningAreaController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        });
    });
