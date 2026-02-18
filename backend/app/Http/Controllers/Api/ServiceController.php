<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Liste des services actifs (publique)
     */
    public function index(Request $request)
    {
        $vehiculeType = $request->query('vehicule_type'); // 'car' ou 'motorcycle'
        $vehiculeSize = $request->query('vehicule_size'); // 'small', 'medium', 'large', 'xlarge'

        // Récupérer services actifs
        $services = Service::where('is_active', true)
            ->when($vehiculeType, function ($query, $vehiculeType) {
                return $query->where('vehicule_type', $vehiculeType);
            })
            ->with(['durations' => function ($query) use ($vehiculeSize) {
                if ($vehiculeSize) {
                    $query->where('vehicule_size', $vehiculeSize);
                }
            }])
            ->orderBy('order')
            ->get();

        // Formatter la réponse
        $formattedServices = $services->map(function ($service) use ($vehiculeSize) {
            $data = [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'vehicule_type' => $service->vehicule_type,
                'is_popular' => $service->is_popular,
            ];

            // Si vehicule_size spécifié, retourner prix/durée pour cette taille
            if ($vehiculeSize && $service->durations->isNotEmpty()) {
                $duration = $service->durations->first();
                $data['price'] = $duration->price;
                $data['duration_minutes'] = $duration->duration_minutes;
                $data['slot_size'] = $duration->slot_size;
            } else {
                // Sinon, retourner toutes les durées/prix par taille
                $data['pricing'] = $service->durations->map(function ($duration) {
                    return [
                        'vehicule_size' => $duration->vehicule_size,
                        'price' => $duration->price,
                        'duration_minutes' => $duration->duration_minutes,
                        'slot_size' => $duration->slot_size,
                    ];
                });
            }

            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $formattedServices
            ]
        ], 200);
    }

    /**
     * Détail d'un service
     */
    public function show($id)
    {
        $service = Service::with('durations')
            ->where('is_active', true)
            ->find($id);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service non trouvé.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'vehicule_type' => $service->vehicule_type,
                    'is_popular' => $service->is_popular,
                    'pricing' => $service->durations->map(function ($duration) {
                        return [
                            'vehicule_size' => $duration->vehicule_size,
                            'price' => $duration->price,
                            'duration_minutes' => $duration->duration_minutes,
                            'slot_size' => $duration->slot_size,
                        ];
                    }),
                ]
            ]
        ], 200);
    }
}
