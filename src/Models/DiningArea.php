<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningArea extends RestotechModel
{
    protected $table = 'restotech_dining_areas';

    public function diningTables(): HasMany
    {
        return $this->hasMany(DiningTable::class, 'dining_area_id');
    }
}
