<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        return [
            'session_type' => $this->faker->randomElement(['main_hall', 'outdoor_session', 'dining_table', 'external_annex', 'outdoor_seating']),
            'capacity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
