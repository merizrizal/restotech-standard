<?php

use Illuminate\Support\Facades\Artisan;
use Restotech\Standard\Actions\CheckoutTableSession;
use Restotech\Standard\Actions\ManageTableSessionOrders;
use Restotech\Standard\Actions\OpenTableSession;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\Employee;
use Restotech\Standard\Models\InventoryCategory;
use Restotech\Standard\Models\InventoryItem;
use Restotech\Standard\Models\InventorySku;
use Restotech\Standard\Models\MenuItem;
use Restotech\Standard\Models\MenuRecipeItem;
use Restotech\Standard\Models\SalesInvoice;
use Restotech\Standard\Models\SalesInvoiceItem;
use Restotech\Standard\Models\SalesInvoicePayment;
use Restotech\Standard\Models\StockBalance;
use Restotech\Standard\Models\StockMovement;
use Restotech\Standard\Models\StorageLocation;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\TransactionDay;
use Restotech\Standard\Models\Voucher;

beforeEach(function (): void {
    config()->set('restotech-standard.pos.tax_rate', 11.5);
    config()->set('restotech-standard.pos.service_charge_rate', 5.0);
    config()->set('restotech-standard.stock.allow_negative_stock', false);
});

function createCheckoutFixture(array $overrides = []): array
{
    Artisan::call('migrate:fresh', ['--force' => true]);

    $options = array_merge([
        'sale_price_amount' => 10000,
        'order_quantity' => 2,
        'recipe_quantity' => 1,
        'stock_quantity' => 10,
        'employee_remaining' => 50000,
        'employee_limit' => 50000,
        'voucher_type' => 'Value',
        'voucher_value_amount' => 5000,
        'voucher_start_date' => now()->subDay()->toDateString(),
        'voucher_end_date' => now()->addDay()->toDateString(),
        'voucher_is_active' => true,
    ], $overrides);

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
    $tableSession = app(OpenTableSession::class)->handle($table, 33);

    $menuCategory = InventoryCategory::factory()->create();
    $inventoryItem = InventoryItem::factory()->create([
        'inventory_category_id' => $menuCategory->id,
    ]);
    $inventorySku = InventorySku::factory()->create([
        'inventory_item_id' => $inventoryItem->id,
    ]);

    $storageLocation = StorageLocation::factory()->create();
    $stockBalance = StockBalance::factory()->create([
        'inventory_sku_id' => $inventorySku->id,
        'storage_location_id' => $storageLocation->id,
        'storage_rack_id' => null,
        'on_hand_quantity' => $options['stock_quantity'],
    ]);

    $menuItem = MenuItem::factory()->create([
        'sale_price_amount' => $options['sale_price_amount'],
    ]);

    MenuRecipeItem::query()->create([
        'menu_item_id' => $menuItem->id,
        'inventory_sku_id' => $inventorySku->id,
        'quantity' => $options['recipe_quantity'],
        'is_optional' => false,
        'notes' => null,
    ]);

    $employee = Employee::query()->create([
        'employee_code' => 'EMP-001',
        'name' => 'Tony Stark',
        'credit_limit_amount' => $options['employee_limit'],
        'remaining_credit_amount' => $options['employee_remaining'],
        'is_active' => true,
        'notes' => null,
    ]);

    $voucher = Voucher::query()->create([
        'voucher_code' => 'VC-001',
        'voucher_type' => $options['voucher_type'],
        'voucher_value_amount' => $options['voucher_value_amount'],
        'start_date' => $options['voucher_start_date'],
        'end_date' => $options['voucher_end_date'],
        'is_active' => $options['voucher_is_active'],
        'used_at' => null,
        'used_by_user_id' => null,
        'used_sales_invoice_id' => null,
        'notes' => null,
    ]);

    $orders = app(ManageTableSessionOrders::class);
    $orders->addItem($tableSession, $menuItem, $options['order_quantity'], 'No ice');
    $orders->generateBill($tableSession->refresh());

    return [
        'tableSession' => $tableSession->refresh(),
        'menuItem' => $menuItem,
        'stockBalance' => $stockBalance,
        'employee' => $employee,
        'voucher' => $voucher,
        'grandTotalAmount' => (int) $tableSession->fresh()->grand_total_amount,
    ];
}

it('creates a sales invoice, invoice items, and cash payment when checkout is ready', function () {
    $fixture = createCheckoutFixture();
    $tableSession = $fixture['tableSession'];
    $tenderedAmount = $fixture['grandTotalAmount'];

    $invoice = app(CheckoutTableSession::class)->handle($tableSession, $tenderedAmount, 77);

    expect($invoice->invoice_number)->toBe('SI-' . $tableSession->id);
    expect($invoice->isPaid())->toBeTrue();
    expect($invoice->grand_total_amount)->toBe($tenderedAmount);
    expect($invoice->paid_amount)->toBe($tenderedAmount);
    expect($invoice->change_amount)->toBe(0);
    expect($invoice->tableSession->is($tableSession))->toBeTrue();
    expect($invoice->invoiceItems)->toHaveCount(1);
    expect($invoice->payments)->toHaveCount(1);
    expect($invoice->invoiceItems->first())->toBeInstanceOf(SalesInvoiceItem::class);
    expect($invoice->payments->first())->toBeInstanceOf(SalesInvoicePayment::class);
    expect(SalesInvoice::query()->count())->toBe(1);
    expect(SalesInvoiceItem::query()->count())->toBe(1);
    expect(SalesInvoicePayment::query()->count())->toBe(1);
    expect(StockMovement::query()->count())->toBe(1);
    expect($fixture['stockBalance']->fresh()->on_hand_quantity)->toBe('8.000');
    expect($tableSession->fresh()->status)->toBe('closed');
    expect($tableSession->fresh()->closed_at)->not->toBeNull();
});

it('rejects duplicate checkout for the same table session', function () {
    $fixture = createCheckoutFixture();
    $tableSession = $fixture['tableSession'];
    $tenderedAmount = $fixture['grandTotalAmount'];

    $checkout = app(CheckoutTableSession::class);
    $checkout->handle($tableSession, $tenderedAmount, 77);

    expect(fn () => $checkout->handle($tableSession->fresh(), $tenderedAmount, 77))
        ->toThrow(DomainException::class, 'TABLE_SESSION_ALREADY_CHECKED_OUT');
});

it('rejects checkout when employee credit is insufficient', function () {
    $fixture = createCheckoutFixture(['employee_remaining' => 5000]);
    $tableSession = $fixture['tableSession'];
    $employee = $fixture['employee'];
    $voucher = $fixture['voucher'];
    $grandTotalAmount = $fixture['grandTotalAmount'];

    expect(fn () => app(CheckoutTableSession::class)->handle($tableSession, [
        [
            'method' => 'employee_credit',
            'employee_id' => $employee->id,
            'amount' => 10000,
            'notes' => 'Employee credit',
        ],
        [
            'method' => 'voucher',
            'voucher_id' => $voucher->id,
            'amount' => 5000,
            'notes' => 'Voucher',
        ],
        [
            'method' => 'cash',
            'amount' => $grandTotalAmount - 15000,
            'notes' => 'Cash remainder',
        ],
    ], 77))->toThrow(DomainException::class, 'EMPLOYEE_CREDIT_INSUFFICIENT');
});

it('rejects checkout when voucher is invalid', function () {
    $fixture = createCheckoutFixture(['voucher_end_date' => now()->subDay()->toDateString()]);
    $tableSession = $fixture['tableSession'];
    $employee = $fixture['employee'];
    $voucher = $fixture['voucher'];
    $grandTotalAmount = $fixture['grandTotalAmount'];

    expect(fn () => app(CheckoutTableSession::class)->handle($tableSession, [
        [
            'method' => 'employee_credit',
            'employee_id' => $employee->id,
            'amount' => 10000,
            'notes' => 'Employee credit',
        ],
        [
            'method' => 'voucher',
            'voucher_id' => $voucher->id,
            'amount' => 5000,
            'notes' => 'Voucher',
        ],
        [
            'method' => 'cash',
            'amount' => $grandTotalAmount - 15000,
            'notes' => 'Cash remainder',
        ],
    ], 77))->toThrow(DomainException::class, 'VOUCHER_INVALID');
});

it('rejects checkout when stock is insufficient', function () {
    $fixture = createCheckoutFixture([
        'order_quantity' => 1,
        'recipe_quantity' => 2,
        'stock_quantity' => 1,
    ]);
    $tableSession = $fixture['tableSession'];
    $grandTotalAmount = $fixture['grandTotalAmount'];

    expect(fn () => app(CheckoutTableSession::class)->handle($tableSession, $grandTotalAmount, 77))
        ->toThrow(DomainException::class, 'STOCK_INSUFFICIENT');

    expect(SalesInvoice::query()->count())->toBe(0);
    expect(StockMovement::query()->count())->toBe(0);
});

it('processes mixed employee credit, voucher, and cash payments while consuming stock', function () {
    $fixture = createCheckoutFixture();
    $tableSession = $fixture['tableSession'];
    $employee = $fixture['employee'];
    $voucher = $fixture['voucher'];
    $grandTotalAmount = $fixture['grandTotalAmount'];
    $employeeAmount = 10000;
    $voucherAmount = 5000;
    $cashAmount = $grandTotalAmount - $employeeAmount - $voucherAmount;

    $invoice = app(CheckoutTableSession::class)->handle($tableSession, [
        [
            'method' => 'employee_credit',
            'employee_id' => $employee->id,
            'amount' => $employeeAmount,
            'notes' => 'Employee credit',
        ],
        [
            'method' => 'voucher',
            'voucher_id' => $voucher->id,
            'amount' => $voucherAmount,
            'notes' => 'Voucher',
        ],
        [
            'method' => 'cash',
            'amount' => $cashAmount,
            'notes' => 'Cash remainder',
        ],
    ], 77);

    expect($invoice->grand_total_amount)->toBe($grandTotalAmount);
    expect($invoice->paid_amount)->toBe($grandTotalAmount);
    expect($invoice->change_amount)->toBe(0);
    expect($invoice->payments)->toHaveCount(3);
    expect($employee->fresh()->remaining_credit_amount)->toBe(40000);
    expect($voucher->fresh()->used_at)->not->toBeNull();
    expect($voucher->fresh()->used_sales_invoice_id)->toBe($invoice->id);
    expect($fixture['stockBalance']->fresh()->on_hand_quantity)->toBe('8.000');
    expect(StockMovement::query()->count())->toBe(1);
    expect($tableSession->fresh()->status)->toBe('closed');
});
