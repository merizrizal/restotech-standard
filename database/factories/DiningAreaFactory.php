<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\DiningArea;

class DiningAreaFactory extends Factory
{
    protected $model = DiningArea::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('AREA-###')),
            'name' => $this->faker->words(2, true),
            'is_active' => true,
            'sort_order' => 0,
            'notes' => null,
        ];
    }
}
