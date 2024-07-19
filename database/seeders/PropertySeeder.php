<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Property::factory()
            ->count(5)
            ->hasPools(3) // إنشاء 3 مسابح لكل عقار
            ->hasImages(5) // إنشاء 5 صور لكل عقار
            ->hasSessions(2) // إنشاء 2 جلسات لكل عقار
            ->hasDetails(1) // إنشاء تفاصيل لكل عقار
            ->create();

    }
}
