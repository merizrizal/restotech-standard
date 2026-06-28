<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\InventoryCategory;

class InventoryCategoryFactory extends Factory
{
    protected $model = InventoryCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('INVCAT-###')),
            'name' => $this->faker->words(2, true),
            'is_active' => true,
            'notes' => null,
        ];
    }
}
