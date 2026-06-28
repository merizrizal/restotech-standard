<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\MenuRecipeItem;

class MenuRecipeItemFactory extends Factory
{
    protected $model = MenuRecipeItem::class;

    public function definition(): array
    {
        return [
            'menu_item_id' => null,
            'inventory_sku_id' => null,
            'quantity' => $this->faker->randomFloat(3, 0.100, 5.000),
            'is_optional' => false,
            'notes' => null,
        ];
    }
}
