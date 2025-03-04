<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a superadmin user
        User::create([
            'name'              => 'Super Admin',
            'email'             => 'superadmin@example.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'role'              => 'superadmin',
        ]);

        // Create an admin user
        User::create([
            'name'              => 'Admin User',
            'email'             => 'admin@example.com',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'role'              => 'admin',
        ]);

        // Create some random admin users
        User::factory()
            ->count(3)
            ->create([
                'role' => 'admin',
            ]);
    }
}
