<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\InventoryItem;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'inventory_category_id' => null,
            'code' => strtoupper($this->faker->unique()->bothify('ITEM-###')),
            'name' => $this->faker->words(2, true),
            'base_unit' => 'pcs',
            'minimum_stock_quantity' => 0,
            'is_active' => true,
            'notes' => null,
        ];
    }
}
