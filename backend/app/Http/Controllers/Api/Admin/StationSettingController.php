<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StationSetting;
use Illuminate\Http\JsonResponse;

class StationSettingController extends Controller
{
    /**
     * Récupérer tous les paramètres
     */
    public function index(): JsonResponse
    {
        $settings = StationSetting::all()->mapWithKeys(fn($s) => [$s->key => $s->value]);

        return response()->json([
            'success' => true,
            'data'    => $settings,
        ]);
    }

    /**
     * Mettre à jour les horaires d'ouverture
     */
    public function updateOpeningHours(Request $request): JsonResponse
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        $request->validate([
            'hours'           => ['required', 'array'],
            'hours.*.is_open' => ['required', 'boolean'],
            'hours.*.open'    => ['nullable', 'date_format:H:i'],
            'hours.*.close'   => ['nullable', 'date_format:H:i'],
        ]);

        $current = StationSetting::where('key', 'opening_hours')->first();
        $currentValue = $current ? $current->value : [];

        foreach ($request->hours as $day => $hours) {
            if (!in_array($day, $days)) continue;

            if (!$hours['is_open']) {
                $currentValue[$day] = ['is_open' => false, 'open' => null, 'close' => null];
                continue;
            }

            // Vérifier que l'heure de fermeture est après l'ouverture
            if ($hours['open'] >= $hours['close']) {
                return response()->json([
                    'success' => false,
                    'message' => "Pour {$day} : l'heure de fermeture doit être après l'ouverture.",
                ], 422);
            }

            $currentValue[$day] = [
                'is_open' => true,
                'open'    => $hours['open'],
                'close'   => $hours['close'],
            ];
        }

        StationSetting::updateOrCreate(
            ['key' => 'opening_hours'],
            ['value' => $currentValue]
        );

        return response()->json([
            'success' => true,
            'message' => 'Horaires mis à jour.',
            'data'    => $currentValue,
        ]);
    }

    /**
     * Mettre à jour la capacité
     */
    public function updateCapacity(Request $request): JsonResponse
    {
        $request->validate([
            'total_capacity'    => ['required', 'integer', 'min:1', 'max:20'],
            'online_capacity'   => ['required', 'integer', 'min:1'],
            'walk_in_capacity'  => ['required', 'integer', 'min:0'],
        ]);

        // online + walk_in ne peut pas dépasser total
        if ($request->online_capacity + $request->walk_in_capacity > $request->total_capacity) {
            return response()->json([
                'success' => false,
                'message' => 'La capacité en ligne + walk-in ne peut pas dépasser la capacité totale.',
            ], 422);
        }

        $value = [
            'total_capacity'   => $request->total_capacity,
            'online_capacity'  => $request->online_capacity,
            'walk_in_capacity' => $request->walk_in_capacity,
        ];

        StationSetting::updateOrCreate(
            ['key' => 'capacity_per_slot'],
            ['value' => $value]
        );

        return response()->json([
            'success' => true,
            'message' => 'Capacité mise à jour.',
            'data'    => $value,
        ]);
    }

    /**
     * Mettre à jour les infos station
     */
    public function updateStationInfo(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'address'   => ['required', 'string', 'max:255'],
            'phone'     => ['required', 'string', 'max:20'],
            'email'     => ['required', 'email'],
            'latitude'  => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $value = $request->only(['name', 'address', 'phone', 'email', 'latitude', 'longitude']);

        StationSetting::updateOrCreate(
            ['key' => 'station_info'],
            ['value' => $value]
        );

        return response()->json([
            'success' => true,
            'message' => 'Informations station mises à jour.',
            'data'    => $value,
        ]);
    }

    /**
     * Mettre à jour la politique de retard
     */
    public function updateLatePolicy(Request $request): JsonResponse
    {
        $request->validate([
            'tolerance_minutes'          => ['required', 'integer', 'min:0', 'max:60'],
            'no_show_threshold_minutes'  => ['required', 'integer', 'min:0', 'max:120'],
            'penalty_points'             => ['required', 'integer', 'min:0'],
            'max_no_shows_before_block'  => ['required', 'integer', 'min:1'],
            'block_duration_days'        => ['required', 'integer', 'min:1'],
        ]);

        $value = $request->only([
            'tolerance_minutes',
            'no_show_threshold_minutes',
            'penalty_points',
            'max_no_shows_before_block',
            'block_duration_days',
        ]);

        StationSetting::updateOrCreate(
            ['key' => 'late_policy'],
            ['value' => $value]
        );

        return response()->json([
            'success' => true,
            'message' => 'Politique de retard mise à jour.',
            'data'    => $value,
        ]);
    }

    /**
     * Mettre à jour les règles de fidélité
     */
    public function updateLoyaltyRules(Request $request): JsonResponse
    {
        $request->validate([
            'points_per_1000_fcfa'   => ['required', 'integer', 'min:1'],
            'review_bonus_points'    => ['required', 'integer', 'min:0'],
            'referral_bonus_points'  => ['required', 'integer', 'min:0'],
            'rewards'                => ['required', 'array'],
            'rewards.*.points'       => ['required', 'integer', 'min:1'],
            'rewards.*.type'         => ['required', 'in:discount,free_wash'],
            'rewards.*.value'        => ['required', 'integer', 'min:0'],
        ]);

        $value = $request->only([
            'points_per_1000_fcfa',
            'review_bonus_points',
            'referral_bonus_points',
            'rewards',
        ]);

        StationSetting::updateOrCreate(
            ['key' => 'loyalty_rules'],
            ['value' => $value]
        );

        return response()->json([
            'success' => true,
            'message' => 'Règles de fidélité mises à jour.',
            'data'    => $value,
        ]);
    }
}
