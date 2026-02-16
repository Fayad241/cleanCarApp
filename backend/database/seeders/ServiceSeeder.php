<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceVehiculeDuration;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Lavage Extérieur',
                'description' => 'Carrosserie + Vitres + Pneus',
                'vehicule_type' => 'car',
                'is_popular' => false,
                'order' => 3,
                'durations' => [
                    'small' => ['duration' => 15, 'slots' => 1, 'price' => 5000],
                    'medium' => ['duration' => 20, 'slots' => 1, 'price' => 5000],
                    'large' => ['duration' => 25, 'slots' => 1, 'price' => 6000],
                    'xlarge' => ['duration' => 30, 'slots' => 1, 'price' => 7000],
                ],
            ],
            [
                'name' => 'Lavage Complet',
                'description' => 'Extérieur + Intérieur + Tapis + Aspirateur',
                'vehicule_type' => 'car',
                'is_popular' => true,
                'order' => 1,
                'durations' => [
                    'small' => ['duration' => 25, 'slots' => 1, 'price' => 7000],
                    'medium' => ['duration' => 35, 'slots' => 2, 'price' => 8000],
                    'large' => ['duration' => 45, 'slots' => 2, 'price' => 10000],
                    'xlarge' => ['duration' => 50, 'slots' => 2, 'price' => 12000],
                ],
            ],
            [
                'name' => 'Premium',
                'description' => 'Complet + Lustrage + Polish phares + Nettoyage moteur',
                'vehicule_type' => 'car',
                'is_popular' => false,
                'order' => 2,
                'durations' => [
                    'small' => ['duration' => 40, 'slots' => 2, 'price' => 10000],
                    'medium' => ['duration' => 50, 'slots' => 2, 'price' => 12000],
                    'large' => ['duration' => 60, 'slots' => 2, 'price' => 15000],
                    'xlarge' => ['duration' => 70, 'slots' => 3, 'price' => 18000],
                ],
            ],
            [
                'name' => 'Lavage Moto',
                'description' => 'Nettoyage complet moto',
                'vehicule_type' => 'motorcycle',
                'is_popular' => false,
                'order' => 4,
                'durations' => [
                    'motorcycle' => ['duration' => 20, 'slots' => 1, 'price' => 3000],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $durations = $serviceData['durations'];
            unset($serviceData['durations']);

            $service = Service::create($serviceData);

            foreach ($durations as $size => $config) {
                ServiceVehiculeDuration::create([
                    'service_id' => $service->id,
                    'vehicule_size' => $size,
                    'duration_minutes' => $config['duration'],
                    'slot_size' => $config['slots'],
                    'price' => $config['price'],
                ]);
            }
        }
    } 
}
