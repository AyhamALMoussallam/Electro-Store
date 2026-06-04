<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@electro.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 1,
                'phone' => 912345678,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@electro.com'],
            [
                'name' => 'Demo Customer',
                'password' => Hash::make('password'),
                'role' => 0,
                'phone' => 998877665,
                'email_verified_at' => now(),
            ]
        );

        if (User::where('role', 0)->count() < 11) {
            User::factory(8)->create([
                'role' => 0,
                'email_verified_at' => now(),
                'phone' => (int) fake()->numberBetween(900000000, 999999999),
            ]);
        }
    }
}
