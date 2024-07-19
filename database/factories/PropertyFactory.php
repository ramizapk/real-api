<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\admins;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Property::class;
    public function definition(): array
    {
        // اختيار مالك عشوائي (إما مستخدم أو مدير)
        if ($this->faker->boolean) {
            $owner = User::inRandomOrder()->first();
            $ownerType = User::class;
        } else {
            $owner = admins::inRandomOrder()->first();
            $ownerType = admins::class;
        }
        return [
            'property_name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10000, 1000000),
            'type_id' => 1, // تأكد من وجود نوع مع id = 1 في جدول property_types
            'city_id' => 1, // تأكد من وجود مدينة مع id = 1 في جدول cities
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'capacity' => $this->faker->numberBetween(1, 10),
            'amenities' => json_encode(['wifi', 'parking']),
            'kitchen_amenities' => json_encode(['oven', 'microwave']),
            'property_status' => $this->faker->randomElement(['sale', 'rent']),
            'availability_status' => $this->faker->randomElement(['available', 'unavailable', 'rented', 'sold']),
            'owner_id' => $owner->id,
            'owner_type' => $ownerType,
        ];
    }
}
