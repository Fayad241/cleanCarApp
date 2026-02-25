<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceVehiculeDuration;
use Illuminate\Http\JsonResponse;

class AdminServiceController extends Controller
{
    /**
     * Liste tous les services (actifs et inactifs)
     */
    public function index(): JsonResponse
    {
        $services = Service::withTrashed()
            ->with('durations')
            ->orderBy('order')
            ->get()
            ->map(fn($service) => [
                'id'           => $service->id,
                'name'         => $service->name,
                'description'  => $service->description,
                'vehicule_type'=> $service->vehicule_type,
                'is_active'    => $service->is_active,
                'is_popular'   => $service->is_popular,
                'order'        => $service->order,
                'deleted_at'   => $service->deleted_at,
                'pricing'      => $service->durations->map(fn($d) => [
                    'id'               => $d->id,
                    'vehicule_size'    => $d->vehicule_size,
                    'duration_minutes' => $d->duration_minutes,
                    'slot_size'        => $d->slot_size,
                    'price'            => $d->price,
                ]),
            ]);

        return response()->json(['success' => true, 'data' => $services]);
    }

    /**
     * Créer un service
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'vehicule_type' => ['required', 'in:car,motorcycle,truck'],
            'is_active'     => ['boolean'],
            'is_popular'    => ['boolean'],
            'order'         => ['integer', 'min:0'],
            'pricing'       => ['required', 'array', 'min:1'],
            'pricing.*.vehicule_size'    => ['required', 'in:small,medium,large,xlarge,motorcycle'],
            'pricing.*.duration_minutes' => ['required', 'integer', 'min:15'],
            'pricing.*.price'            => ['required', 'numeric', 'min:0'],
        ]);

        $service = Service::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'vehicule_type' => $request->vehicule_type,
            'is_active'     => $request->boolean('is_active', true),
            'is_popular'    => $request->boolean('is_popular', false),
            'order'         => $request->integer('order', 0),
        ]);

        foreach ($request->pricing as $pricing) {
            $slotSize = (int) ceil($pricing['duration_minutes'] / 30);
            ServiceVehiculeDuration::create([
                'service_id'       => $service->id,
                'vehicule_size'    => $pricing['vehicule_size'],
                'duration_minutes' => $pricing['duration_minutes'],
                'slot_size'        => $slotSize,
                'price'            => $pricing['price'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Service créé avec succès.',
            'data'    => $service->load('durations'),
        ], 201);
    }

    /**
     * Modifier un service
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $request->validate([
            'name'          => ['sometimes', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'vehicule_type' => ['sometimes', 'in:car,motorcycle,truck'],
            'is_active'     => ['sometimes', 'boolean'],
            'is_popular'    => ['sometimes', 'boolean'],
            'order'         => ['sometimes', 'integer', 'min:0'],
        ]);

        $service->update($request->only([
            'name', 'description', 'vehicule_type',
            'is_active', 'is_popular', 'order',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Service mis à jour.',
            'data'    => $service->load('durations'),
        ]);
    }

    /**
     * Supprimer un service (soft delete)
     */
    public function destroy(Service $service): JsonResponse
    {
        $activeReservations = $service->reservations()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        if ($activeReservations > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer : {$activeReservations} réservation(s) active(s) pour ce service.",
            ], 422);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service supprimé.',
        ]);
    }

    /**
     * Restaurer un service supprimé
     */
    public function restore(string $id): JsonResponse
    {
        $service = Service::withTrashed()->findOrFail($id);
        $service->restore();

        return response()->json([
            'success' => true,
            'message' => 'Service restauré.',
            'data'    => $service->load('durations'),
        ]);
    }

    /**
     * Ajouter ou modifier un tarif par taille véhicule
     */
    public function upsertPricing(Request $request, Service $service): JsonResponse
    {
        $request->validate([
            'vehicule_size'    => ['required', 'in:small,medium,large,xlarge,motorcycle'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'price'            => ['required', 'numeric', 'min:0'],
        ]);

        $slotSize = (int) ceil($request->duration_minutes / 30);

        $pricing = ServiceVehiculeDuration::updateOrCreate(
            [
                'service_id'    => $service->id,
                'vehicule_size' => $request->vehicule_size,
            ],
            [
                'duration_minutes' => $request->duration_minutes,
                'slot_size'        => $slotSize,
                'price'            => $request->price,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tarif mis à jour.',
            'data'    => $pricing,
        ]);
    }

    /**
     * Supprimer un tarif par taille véhicule
     */
    public function destroyPricing(Service $service, string $vehiculeSize): JsonResponse
    {
        $pricing = ServiceVehiculeDuration::where('service_id', $service->id)
            ->where('vehicule_size', $vehiculeSize)
            ->first();

        if (!$pricing) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif non trouvé.',
            ], 404);
        }

        $pricing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarif supprimé.',
        ]);
    }

    /**
     * Activer / désactiver un service
     */
    public function toggleActive(Service $service): JsonResponse
    {
        $service->update(['is_active' => !$service->is_active]);

        return response()->json([
            'success' => true,
            'message' => $service->is_active ? 'Service activé.' : 'Service désactivé.',
            'data'    => ['is_active' => $service->is_active],
        ]);
    }
}
