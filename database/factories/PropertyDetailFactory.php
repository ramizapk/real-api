<?php

namespace Database\Factories;

use App\Models\PropertyDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyDetail>
 */
class PropertyDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = PropertyDetail::class;

    public function definition()
    {
        return [
            'check_in_time' => $this->faker->time(),
            'check_out_time' => $this->faker->time(),
            'security_deposit' => $this->faker->randomFloat(2, 100, 1000),
            'additional_notes' => $this->faker->sentence(),
        ];
    }
}
