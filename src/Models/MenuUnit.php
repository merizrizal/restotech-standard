<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuUnit extends RestotechModel
{
    protected $table = 'restotech_menu_units';

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'menu_unit_id');
    }
}
