<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableSession extends RestotechModel
{
    protected $table = 'restotech_table_sessions';

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'tax_rate' => 'decimal:3',
        'service_charge_rate' => 'decimal:3',
    ];

    public function diningTable(): BelongsTo
    {
        return $this->belongsTo(DiningTable::class, 'dining_table_id');
    }

    public function transactionDay(): BelongsTo
    {
        return $this->belongsTo(TransactionDay::class, 'transaction_day_id');
    }

    public function cashierBalance(): BelongsTo
    {
        return $this->belongsTo(CashierBalance::class, 'cashier_balance_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open' && $this->closed_at === null;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')->whereNull('closed_at');
    }
}
