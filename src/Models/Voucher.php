<?php

namespace Restotech\Standard\Models;

class Voucher extends RestotechModel
{
    protected $table = 'restotech_vouchers';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'used_at' => 'datetime',
        'is_active' => 'bool',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
