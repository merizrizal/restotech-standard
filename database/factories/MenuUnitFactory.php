<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\MenuUnit;

class MenuUnitFactory extends Factory
{
    protected $model = MenuUnit::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('UNT-###')),
            'name' => $this->faker->randomElement(['Portion', 'Glass', 'Plate', 'Cup']),
            'symbol' => $this->faker->randomElement(['pc', 'glass', 'plate', 'cup']),
            'is_active' => true,
        ];
    }
}
