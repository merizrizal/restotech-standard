<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends RestotechModel
{
    protected $table = 'restotech_menu_items';

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function menuUnit(): BelongsTo
    {
        return $this->belongsTo(MenuUnit::class, 'menu_unit_id');
    }

    public function menuCondiments(): HasMany
    {
        return $this->hasMany(MenuCondiment::class, 'menu_item_id');
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(MenuRecipeItem::class, 'menu_item_id');
    }
}
