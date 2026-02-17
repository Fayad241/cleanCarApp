<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'phone' => '+2290157967900',
            'email' => 'admin@cleancar.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Manager
        User::create([
            'first_name' => 'Amadou',
            'last_name' => 'Diallo',
            'phone' => '+2290190222222',
            'email' => 'manager@cleancar.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Employee
        User::create([
            'first_name' => 'Moussa',
            'last_name' => 'Jean',
            'phone' => '+2290145333333',
            'email' => 'employee@cleancar.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone_verified_at' => now(),
        ]);

        // Clients
        User::create([
            'first_name' => 'Fatou',
            'last_name' => 'Badarou',
            'phone' => '+2290196444444',
            'email' => 'fatou@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'loyalty_points' => 450,
            'phone_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Jean',
            'last_name' => 'Sagna',
            'phone' => '+2290196555555',
            'email' => 'jean@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'loyalty_points' => 820,
            'phone_verified_at' => now(),
        ]);
    }
}
