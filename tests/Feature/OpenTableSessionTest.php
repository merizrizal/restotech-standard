<?php

use Illuminate\Support\Facades\Artisan;
use Restotech\Standard\Actions\OpenTableSession;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningArea;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\TransactionDay;

beforeEach(function (): void {
    config()->set('restotech-standard.pos.tax_rate', 11.5);
    config()->set('restotech-standard.pos.service_charge_rate', 5.0);
});

it('rejects opening a table session when no transaction day is open', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);

    $table = DiningTable::factory()->create();

    expect(fn () => app(OpenTableSession::class)->handle($table))->toThrow(DomainException::class, 'TRANSACTION_DAY_CLOSED');
});

it('rejects opening a table session when cashier balance is missing', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);

    TransactionDay::query()->create([
        'business_date' => now()->toDateString(),
        'started_at' => now(),
        'status' => 'open',
    ]);

    $table = DiningTable::factory()->create();

    expect(fn () => app(OpenTableSession::class)->handle($table))->toThrow(DomainException::class, 'CASHIER_BALANCE_REQUIRED');
});

it('opens and reuses a table session when gates are satisfied', function () {
    Artisan::call('migrate:fresh', ['--force' => true]);

    $transactionDay = TransactionDay::query()->create([
        'business_date' => now()->toDateString(),
        'started_at' => now(),
        'status' => 'open',
    ]);

    $cashierBalance = CashierBalance::query()->create([
        'transaction_day_id' => $transactionDay->id,
        'user_id' => null,
        'opened_at' => now(),
        'opening_balance_amount' => 100000,
        'closing_balance_amount' => 0,
        'status' => 'open',
    ]);

    $diningArea = DiningArea::factory()->create();
    $table = DiningTable::factory()->create([
        'dining_area_id' => $diningArea->id,
    ]);

    $action = app(OpenTableSession::class);
    $firstSession = $action->handle($table, 15);
    $secondSession = $action->handle($table, 15);

    expect($firstSession->is($secondSession))->toBeTrue();
    expect($firstSession->diningTable->is($table))->toBeTrue();
    expect($firstSession->transactionDay->is($transactionDay))->toBeTrue();
    expect($firstSession->cashierBalance->is($cashierBalance))->toBeTrue();
    expect($firstSession->status)->toBe('open');
    expect($firstSession->tax_rate)->toBe('11.500');
    expect($firstSession->service_charge_rate)->toBe('5.000');
    expect($firstSession->opened_by_user_id)->toBe(15);
    expect(TableSession::query()->count())->toBe(1);
});
