<?php

namespace Database\Factories;

use App\Models\PropertyImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyImage>
 */
class PropertyImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = PropertyImage::class;
    public function definition(): array
    {
        // Generate random image using Faker
        $fakerImage = $this->faker->image(storage_path('app/public/property_images'), 600, 400, null, false);

        // Get the image filename from Faker
        $imageName = basename($fakerImage);

        // Define the path to save the processed image within the public disk
        $imagePath = 'property_images/' . $imageName;

        // Check if the 'property_images' directory exists in the public disk
        if (!Storage::disk('public')->exists('property_images')) {
            Storage::disk('public')->makeDirectory('property_images'); // Create the directory if not present
        }

        // Move the generated Faker image to the desired location
        $newImagePath = 'public/' . $imagePath;
        Storage::disk('public')->move($fakerImage, $newImagePath);

        return [
            'image_url' => $imagePath, // Use the same $imagePath here
        ];

    }
}
