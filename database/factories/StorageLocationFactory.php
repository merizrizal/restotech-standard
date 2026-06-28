<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\StorageLocation;

class StorageLocationFactory extends Factory
{
    protected $model = StorageLocation::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('LOC-###')),
            'name' => $this->faker->words(2, true),
            'is_active' => true,
            'notes' => null,
        ];
    }
}
