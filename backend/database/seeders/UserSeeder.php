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
            'last_name' => 'Système',
            'phone' => '+22990111111',
            'email' => 'admin@cleancar.bj',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Manager
        User::create([
            'first_name' => 'Rodrigue',
            'last_name' => 'Ahouandjinou',
            'phone' => '+22990222222',
            'email' => 'manager@cleancar.bj',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ]);

        // Employee
        User::create([
            'first_name' => 'Yvette',
            'last_name' => 'Dossou',
            'phone' => '+22990333333',
            'email' => 'employee@cleancar.bj',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'phone_verified_at' => now(),
        ]);

        // Clients
        User::create([
            'first_name' => 'Nadège',
            'last_name' => 'Kpomassè',
            'phone' => '+22990444444',
            'email' => 'nadege@example.bj',
            'password' => Hash::make('password'),
            'role' => 'client',
            'loyalty_points' => 450,
            'phone_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Hermann',
            'last_name' => 'Houndonougbo',
            'phone' => '+22990555555',
            'email' => 'hermann@example.bj',
            'password' => Hash::make('password'),
            'role' => 'client',
            'loyalty_points' => 820,
            'phone_verified_at' => now(),
        ]);
    }
}
