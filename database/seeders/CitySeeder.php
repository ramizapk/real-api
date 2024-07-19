<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'الرياض',
            'جدة',
            'الدمام',
        ];
        foreach ($cities as $city) {
            City::create(['name' => $city]);
        }
    }
}
