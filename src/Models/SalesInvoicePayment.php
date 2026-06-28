<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoicePayment extends RestotechModel
{
    protected $table = 'restotech_sales_invoice_payments';

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class, 'sales_invoice_id');
    }
}
