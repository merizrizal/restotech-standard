<?php

use Illuminate\Support\Facades\Artisan;
use Restotech\Standard\Actions\ManageTableSessionOrders;
use Restotech\Standard\Actions\OpenTableSession;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\MenuItem;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\TransactionDay;

beforeEach(function (): void {
    config()->set('restotech-standard.pos.tax_rate', 11.5);
    config()->set('restotech-standard.pos.service_charge_rate', 5.0);
});

function createBillableTableSession(): TableSession
{
    $transactionDay = TransactionDay::query()->create([
        'business_date' => now()->toDateString(),
        'started_at' => now(),
        'status' => 'open',
    ]);

    CashierBalance::query()->create([
        'transaction_day_id' => $transactionDay->id,
        'user_id' => null,
        'opened_at' => now(),
        'opening_balance_amount' => 100000,
        'closing_balance_amount' => 0,
        'status' => 'open',
    ]);

    $table = DiningTable::factory()->create();

    return app(OpenTableSession::class)->handle($table, 15);
}

it('adds items and calculates bill totals from server-side pricing', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);

    $tableSession = createBillableTableSession();
    $menuItem = MenuItem::factory()->create([
        'sale_price_amount' => 10000,
    ]);

    $orders = app(ManageTableSessionOrders::class);
    $orderItem = $orders->addItem($tableSession, $menuItem, 2, 'Extra spicy');
    $discountedItem = $orders->applyDiscount($orderItem, 'Percent', 10);
    $bill = $orders->generateBill($tableSession->refresh());

    expect($discountedItem->line_subtotal_amount)->toBe(20000);
    expect($discountedItem->discount_amount)->toBe(2000);
    expect($discountedItem->line_total_amount)->toBe(18000);
    expect($bill->bill_printed)->toBeTrue();
    expect($bill->subtotal_amount)->toBe(20000);
    expect($bill->discount_amount)->toBe(2000);
    expect($bill->net_amount)->toBe(18000);
    expect($bill->tax_amount)->toBe(2070);
    expect($bill->service_charge_amount)->toBe(900);
    expect($bill->grand_total_amount)->toBe(20970);
    expect($bill->orderItems)->toHaveCount(1);
});

it('rejects mutations after bill generation and allows them after unlock', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);

    $tableSession = createBillableTableSession();
    $menuItem = MenuItem::factory()->create([
        'sale_price_amount' => 10000,
    ]);

    $orders = app(ManageTableSessionOrders::class);
    $orderItem = $orders->addItem($tableSession, $menuItem, 1, 'No onions');
    $orders->generateBill($tableSession->refresh());

    expect(fn () => $orders->changeQuantity($orderItem->refresh(), 3))->toThrow(DomainException::class, 'TABLE_SESSION_BILL_LOCKED');

    $unlockedSession = $orders->unlockBill($tableSession->refresh());
    $updatedItem = $orders->changeQuantity($orderItem->refresh(), 3);
    $rebuiltBill = $orders->generateBill($unlockedSession->refresh());

    expect($updatedItem->line_subtotal_amount)->toBe(30000);
    expect($updatedItem->line_total_amount)->toBe(30000);
    expect($rebuiltBill->bill_printed)->toBeTrue();
    expect($rebuiltBill->grand_total_amount)->toBe(34950);
});
