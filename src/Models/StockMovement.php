<?php

namespace Restotech\Standard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends RestotechModel
{
    protected $table = 'restotech_stock_movements';

    public function inventorySku(): BelongsTo
    {
        return $this->belongsTo(InventorySku::class, 'inventory_sku_id');
    }

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }

    public function storageRack(): BelongsTo
    {
        return $this->belongsTo(StorageRack::class, 'storage_rack_id');
    }
}
