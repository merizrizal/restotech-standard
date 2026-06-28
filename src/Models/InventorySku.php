<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventorySku extends RestotechModel
{
    protected $table = 'restotech_inventory_skus';

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'inventory_sku_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'inventory_sku_id');
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(MenuRecipeItem::class, 'inventory_sku_id');
    }
}
