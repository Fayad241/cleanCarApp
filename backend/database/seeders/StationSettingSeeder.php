<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StationSetting;

class StationSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'opening_hours',
                'value' => [
                    'monday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'tuesday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'wednesday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'thursday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'friday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'saturday' => ['open' => '08:00', 'close' => '19:00', 'is_open' => true],
                    'sunday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
                ],
            ],
            [
                'key' => 'capacity_per_slot',
                'value' => [
                    'total_capacity' => 3,
                    'online_capacity' => 2,
                    'walk_in_capacity' => 1,
                ],
            ],
            [
                'key' => 'station_info',
                'value' => [
                    'name' => 'Clean Car Pro',
                    'address' => 'Avenue 123, Cotonou, Bénin',
                    'phone' => '+229 01 57 XX XX XX',
                    'email' => 'contact@cleancar.bj',
                    'latitude' => 6.3703,
                    'longitude' => 2.3912,
                ],
            ],
            [
                'key' => 'loyalty_rules',
                'value' => [
                    'points_per_1000_fcfa' => 10,
                    'review_bonus_points' => 20,
                    'referral_bonus_points' => 100,
                    'rewards' => [
                        ['points' => 200, 'type' => 'discount', 'value' => 1000],
                        ['points' => 500, 'type' => 'discount', 'value' => 2000],
                        ['points' => 1000, 'type' => 'free_wash', 'value' => 0],
                    ],
                ],
            ],
            [
                'key' => 'late_policy',
                'value' => [
                    'tolerance_minutes' => 15,
                    'no_show_threshold_minutes' => 30,
                    'penalty_points' => 50,
                    'max_no_shows_before_block' => 3,
                    'block_duration_days' => 7,
                ],
            ],
        ];

        foreach ($settings as $setting) {
            StationSetting::create($setting);
        }
    }
}
