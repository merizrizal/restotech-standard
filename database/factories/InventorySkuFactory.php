<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\InventorySku;

class InventorySkuFactory extends Factory
{
    protected $model = InventorySku::class;

    public function definition(): array
    {
        return [
            'inventory_item_id' => null,
            'sku_code' => strtoupper($this->faker->unique()->bothify('SKU-###')),
            'sku_name' => $this->faker->words(2, true),
            'barcode' => null,
            'minimum_stock_quantity' => 0,
            'is_active' => true,
            'notes' => null,
        ];
    }
}
