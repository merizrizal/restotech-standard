<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuRecipeItem extends RestotechModel
{
    protected $table = 'restotech_menu_recipe_items';

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function inventorySku(): BelongsTo
    {
        return $this->belongsTo(InventorySku::class, 'inventory_sku_id');
    }
}
