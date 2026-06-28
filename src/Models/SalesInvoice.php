<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesInvoice extends RestotechModel
{
    protected $table = 'restotech_sales_invoices';

    protected $casts = [
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'closed_at' => 'datetime',
        'tax_rate' => 'decimal:3',
        'service_charge_rate' => 'decimal:3',
    ];

    public function tableSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class, 'table_session_id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class, 'sales_invoice_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesInvoicePayment::class, 'sales_invoice_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' && $this->paid_at !== null;
    }
}
