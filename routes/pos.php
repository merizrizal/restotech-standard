<?php

use Illuminate\Support\Facades\Route;
use Restotech\Standard\Http\Controllers\Pos\TableSessionController;
use Restotech\Standard\Http\Middleware\PosAuthenticate;

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

        Route::view('/login', 'restotech-standard::back-office.login')->name('login');

        Route::middleware(PosAuthenticate::class)->group(function (): void {
            Route::get('/', function () {
                return view('restotech-standard::pos.shell', [
                    'openTableSessionEndpoint' => route('restotech.standard.pos.internal.table-sessions.open'),
                ]);
            })->name('shell');

            Route::post('/internal/table-sessions/open', [TableSessionController::class, 'store'])
                ->name('internal.table-sessions.open');
        });
    });
