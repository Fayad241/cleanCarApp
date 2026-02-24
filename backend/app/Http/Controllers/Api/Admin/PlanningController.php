<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Admin\StoreWalkInRequest;

class PlanningController extends Controller
{
    /**
     * Planning d'une journée
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['sometimes', 'date'],
        ]);

        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $reservations = Reservation::with(['user', 'vehicule', 'service'])
            ->whereDate('scheduled_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('scheduled_time', 'asc')
            ->get();

        // Grouper par créneau horaire
        $grouped = $reservations->groupBy('scheduled_time')
            ->map(function ($slotReservations, $time) {
                return [
                    'time'         => $time,
                    'reservations' => ReservationResource::collection($slotReservations),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'date'    => $date,
                'summary' => [
                    'total'       => $reservations->count(),
                    'pending'     => $reservations->where('status', 'pending')->count(),
                    'confirmed'   => $reservations->where('status', 'confirmed')->count(),
                    'in_progress' => $reservations->where('status', 'in_progress')->count(),
                    'completed'   => $reservations->where('status', 'completed')->count(),
                ],
                'slots'   => $grouped,
            ],
        ]);
    }

    /**
     * Planning de la semaine
     */
    public function week(Request $request): JsonResponse
    {
        $startDate = Carbon::parse($request->get('start', Carbon::today()->format('Y-m-d')));
        $endDate   = $startDate->copy()->addDays(6);

        $reservations = Reservation::with(['service'])
            ->whereBetween('scheduled_date', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $date          = $startDate->copy()->addDays($i);
            $dayReservations = $reservations->filter(
                fn($r) => $r->scheduled_date->format('Y-m-d') === $date->format('Y-m-d')
            );

            $week[] = [
                'date'      => $date->format('Y-m-d'),
                'label'     => $date->locale('fr')->isoFormat('ddd D MMM'),
                'total'     => $dayReservations->count(),
                'completed' => $dayReservations->where('status', 'completed')->count(),
                'revenue'   => $dayReservations->where('payment_status', 'paid')->sum('price'),
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $week,
        ]);
    }

    /**
     * Ajouter une réservation manuelle (walk-in)
     */
    public function storeWalkIn(StoreWalkInRequest $request): JsonResponse
    {
        $config = \App\Models\ServiceVehiculeDuration::where('service_id', $request->service_id)
            ->where('vehicule_size', $request->vehicule_size)
            ->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Ce service n\'est pas disponible pour cette taille de véhicule.',
            ], 422);
        }

        $reservation = Reservation::create([
            'reservation_number'   => 'WA' . strtoupper(\Illuminate\Support\Str::random(5)),
            'user_id'              => auth()->id(),
            'vehicule_id'          => null,
            'service_id'           => $request->service_id,
            'scheduled_date'       => $request->scheduled_date,
            'scheduled_time'       => $request->scheduled_time,
            'duration_minutes'     => $config->duration_minutes,
            'slots_occupied'       => $config->slot_size,
            'price'                => $config->price,
            'payment_method'       => $request->payment_method,
            'special_instructions' => $request->special_instructions,
            'source'               => 'walk_in',
            'status'               => 'confirmed',
            'payment_status'       => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation manuelle créée.',
            'data'    => new ReservationResource($reservation->load(['service'])),
        ], 201);
    }
}
