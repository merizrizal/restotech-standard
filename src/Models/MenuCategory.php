<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuCategory extends RestotechModel
{
    protected $table = 'restotech_menu_categories';

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_category_id');
    }
}
