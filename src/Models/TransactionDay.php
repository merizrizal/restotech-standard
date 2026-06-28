<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionDay extends RestotechModel
{
    protected $table = 'restotech_transaction_days';

    public function cashierBalances(): HasMany
    {
        return $this->hasMany(CashierBalance::class, 'transaction_day_id');
    }

    public function tableSessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'transaction_day_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open')->whereNull('ended_at');
    }
}
