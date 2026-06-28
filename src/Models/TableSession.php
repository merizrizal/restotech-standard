<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TableSession extends RestotechModel
{
    protected $table = 'restotech_table_sessions';

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'bill_printed_at' => 'datetime',
        'bill_printed' => 'bool',
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'table_session_id');
    }

    public function salesInvoice(): HasOne
    {
        return $this->hasOne(SalesInvoice::class, 'table_session_id');
    }

    public function isBillLocked(): bool
    {
        return (bool) $this->bill_printed;
    }

    public function markBillPrinted(array $totals): self
    {
        $this->fill([
            'subtotal_amount' => $totals['subtotal_amount'] ?? 0,
            'discount_amount' => $totals['discount_amount'] ?? 0,
            'net_amount' => $totals['net_amount'] ?? 0,
            'tax_amount' => $totals['tax_amount'] ?? 0,
            'service_charge_amount' => $totals['service_charge_amount'] ?? 0,
            'grand_total_amount' => $totals['grand_total_amount'] ?? 0,
            'bill_printed' => true,
            'bill_printed_at' => now(),
        ]);

        return $this;
    }

    public function unlockBill(): self
    {
        $this->fill([
            'subtotal_amount' => 0,
            'discount_amount' => 0,
            'net_amount' => 0,
            'tax_amount' => 0,
            'service_charge_amount' => 0,
            'grand_total_amount' => 0,
            'bill_printed' => false,
            'bill_printed_at' => null,
        ]);

        return $this;
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
