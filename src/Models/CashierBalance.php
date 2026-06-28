<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierBalance extends RestotechModel
{
    protected $table = 'restotech_cashier_balances';

    public function transactionDay(): BelongsTo
    {
        return $this->belongsTo(TransactionDay::class, 'transaction_day_id');
    }

    public function tableSessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'cashier_balance_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')->whereNull('closed_at');
    }
}
