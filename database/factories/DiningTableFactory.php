<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Restotech\Standard\Models\DiningTable;

class DiningTableFactory extends Factory
{
    protected $model = DiningTable::class;

    public function definition(): array
    {
        return [
            'dining_area_id' => null,
            'code' => strtoupper($this->faker->unique()->bothify('TBL-###')),
            'name' => $this->faker->bothify('Table ##'),
            'capacity' => $this->faker->numberBetween(2, 8),
            'is_active' => true,
            'allow_tax_exemption' => false,
            'allow_service_charge_exemption' => false,
            'notes' => null,
        ];
    }
}
