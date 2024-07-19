<?php

namespace Database\Factories;

use App\Models\Pool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pool>
 */
class PoolFactory extends Factory
{
    protected $model = Pool::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['indoor', 'outdoor', 'water_park', 'heated']),
            'fence' => $this->faker->randomElement(['with_fence', 'without_fence']),
            'is_graduated' => $this->faker->boolean,
            'depth' => $this->faker->numberBetween(1, 5),
            'length' => $this->faker->numberBetween(5, 20),
            'width' => $this->faker->numberBetween(3, 10),
        ];
    }
}
