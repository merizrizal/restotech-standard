<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends RestotechModel
{
    protected $table = 'restotech_inventory_items';

    public function inventoryCategory(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function inventorySkus(): HasMany
    {
        return $this->hasMany(InventorySku::class, 'inventory_item_id');
    }
}
