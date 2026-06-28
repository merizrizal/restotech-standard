<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends RestotechModel
{
    protected $table = 'restotech_order_items';

    protected $casts = [
        'quantity' => 'decimal:3',
        'is_free' => 'bool',
        'is_void' => 'bool',
        'free_at' => 'datetime',
        'void_at' => 'datetime',
        'discounted_at' => 'datetime',
    ];

    public function tableSession(): BelongsTo
    {
        return $this->belongsTo(TableSession::class, 'table_session_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
