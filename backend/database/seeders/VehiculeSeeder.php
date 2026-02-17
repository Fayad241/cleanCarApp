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
        $fatou = User::where('phone', '+2290196444444')->first();
        $jean = User::where('phone', '+2290196555555')->first();

        // Véhicules Fatou
        Vehicule::create([
            'user_id' => $fatou->id,
            'type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Yaris',
            'size' => 'small',
            'color' => 'Gris',
            'plate_number' => 'CT 1234 AB',
            'is_default' => true,
        ]);

        // Véhicules Jean
        Vehicule::create([
            'user_id' => $jean->id,
            'type' => 'car',
            'brand' => 'Mercedes',
            'model' => 'C200',
            'size' => 'medium',
            'color' => 'Noir',
            'plate_number' => 'CT 5678 CD',
            'is_default' => true,
        ]);

        Vehicule::create([
            'user_id' => $jean->id,
            'type' => 'car',
            'brand' => 'Range Rover',
            'model' => 'Sport',
            'size' => 'xlarge',
            'color' => 'Blanc',
            'plate_number' => null,
            'is_default' => false,
        ]);
    }
}
