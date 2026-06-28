<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\MenuCategory;

class MenuCategoryFactory extends Factory
{
    protected $model = MenuCategory::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('CAT-###')),
            'name' => $this->faker->words(2, true),
            'is_active' => true,
            'allow_discount' => true,
            'supports_queue' => false,
            'notes' => null,
        ];
    }
}
