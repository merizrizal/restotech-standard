<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiningTable extends RestotechModel
{
    protected $table = 'restotech_dining_tables';

    public function diningArea(): BelongsTo
    {
        return $this->belongsTo(DiningArea::class, 'dining_area_id');
    }

    public function tableSessions(): HasMany
    {
        return $this->hasMany(TableSession::class, 'dining_table_id');
    }
}
