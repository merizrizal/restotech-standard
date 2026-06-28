<?php

namespace Restotech\Standard\Actions;

use DomainException;
use Restotech\Standard\Models\CashierBalance;
use Restotech\Standard\Models\DiningTable;
use Restotech\Standard\Models\TableSession;
use Restotech\Standard\Models\TransactionDay;

class OpenTableSession
{
    public function handle(DiningTable $diningTable, ?int $openedByUserId = null): TableSession
    {
        $transactionDay = TransactionDay::query()->open()->latest('business_date')->first();

        if (! $transactionDay) {
            throw new DomainException('TRANSACTION_DAY_CLOSED');
        }

        $cashierBalance = CashierBalance::query()
            ->open()
            ->where('transaction_day_id', $transactionDay->id)
            ->latest('opened_at')
            ->first();

        if (! $cashierBalance) {
            throw new DomainException('CASHIER_BALANCE_REQUIRED');
        }

        $existingSession = TableSession::query()
            ->open()
            ->where('dining_table_id', $diningTable->id)
            ->where('transaction_day_id', $transactionDay->id)
            ->first();

        if ($existingSession) {
            return $existingSession;
        }

        return TableSession::create([
            'dining_table_id' => $diningTable->id,
            'transaction_day_id' => $transactionDay->id,
            'cashier_balance_id' => $cashierBalance->id,
            'opened_by_user_id' => $openedByUserId,
            'status' => 'open',
            'opened_at' => now(),
            'closed_at' => null,
            'tax_rate' => (float) config('restotech-standard.pos.tax_rate', 0),
            'service_charge_rate' => (float) config('restotech-standard.pos.service_charge_rate', 0),
        ]);
    }
}
