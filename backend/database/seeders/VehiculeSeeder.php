<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiculeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nadege = User::where('phone', '+22990444444')->first();
        $hermann = User::where('phone', '+22990555555')->first();

        // Véhicules Nadège
        Vehicule::create([
            'user_id' => $nadege->id,
            'type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'size' => 'medium',
            'color' => 'Blanc',
            'plate_number' => 'BJ 1234 AB',
            'is_default' => true,
        ]);

        // Véhicules Hermann
        Vehicule::create([
            'user_id' => $hermann->id,
            'type' => 'car',
            'brand' => 'Mercedes',
            'model' => 'C200',
            'size' => 'medium',
            'color' => 'Noir',
            'plate_number' => 'BJ 5678 CD',
            'is_default' => true,
        ]);

        Vehicule::create([
            'user_id' => $hermann->id,
            'type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Land Cruiser',
            'size' => 'xlarge',
            'color' => 'Gris',
            'plate_number' => null,
            'is_default' => false,
        ]);
    }
}
