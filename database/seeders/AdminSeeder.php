<?php

namespace Database\Seeders;

use App\Models\admins;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Admin One',
                'username' => 'admin1',
                'phone' => '1234567890',
                'avatar' => null,
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Admin Two',
                'username' => 'admin2',
                'phone' => '0987654321',
                'avatar' => null,
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Admin Three',
                'username' => 'admin3',
                'phone' => '1122334455',
                'avatar' => null,
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($admins as $admin) {
            admins::create($admin);
        }
    }
}
