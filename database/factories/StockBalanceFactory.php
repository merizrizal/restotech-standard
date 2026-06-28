<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\StockBalance;

class StockBalanceFactory extends Factory
{
    protected $model = StockBalance::class;

    public function definition(): array
    {
        return [
            'inventory_sku_id' => null,
            'storage_location_id' => null,
            'storage_rack_id' => null,
            'on_hand_quantity' => $this->faker->randomFloat(3, 0, 100),
            'reserved_quantity' => 0,
            'minimum_stock_quantity' => 0,
            'is_active' => true,
        ];
    }
}
