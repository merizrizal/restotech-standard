<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningArea;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\MenuItem;
use Restotech\Standard\Models\TransactionDay;

class DemoDashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'currentUser' => Auth::user(),
            'demoEmail' => config('restotech-standard.demo.user_email', 'demo@restotech.test'),
            'demoPassword' => config('restotech-standard.demo.user_password', 'password'),
            'demoTableId' => config('restotech-standard.demo.table_id', 1),
            'openTransactionDay' => TransactionDay::query()->open()->latest('business_date')->first(),
            'openCashierBalance' => CashierBalance::query()->open()->latest('opened_at')->first(),
            'diningAreasCount' => DiningArea::query()->count(),
            'diningTablesCount' => DiningTable::query()->count(),
            'menuItemsCount' => MenuItem::query()->count(),
        ]);
    }
}
