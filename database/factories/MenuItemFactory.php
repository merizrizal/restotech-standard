<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\MenuItem;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'menu_category_id' => null,
            'menu_unit_id' => null,
            'code' => strtoupper($this->faker->unique()->bothify('MENU-###')),
            'name' => $this->faker->words(3, true),
            'sale_price_amount' => $this->faker->numberBetween(10000, 75000),
            'cost_amount' => $this->faker->numberBetween(5000, 40000),
            'image_path' => null,
            'is_active' => true,
            'allow_discount' => true,
            'notes' => null,
        ];
    }
}
