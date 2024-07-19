<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            'فيلا',
            'استوديو',
            'شقة',
            'مزرعة',
            'إستراحة',
            'برج',
            'مكتب',
            'مستودع',
            'كشك',
            'فندق',
            'مجمع سكني',
            'بيت ريفي',
            'كابينة',
            'محطة',
            'مصنع',
            'مدرسة',
            'مستشفى',
            'محل تجاري',
            'أرض'
        ];
        foreach ($propertyTypes as $type) {
            PropertyType::create(['name' => $type]);
        }
    }
}