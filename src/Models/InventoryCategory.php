<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCategory extends RestotechModel
{
    protected $table = 'restotech_inventory_categories';

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class, 'inventory_category_id');
    }
}
