<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageLocation extends RestotechModel
{
    protected $table = 'restotech_storage_locations';

    public function storageRacks(): HasMany
    {
        return $this->hasMany(StorageRack::class, 'storage_location_id');
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'storage_location_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'storage_location_id');
    }
}
