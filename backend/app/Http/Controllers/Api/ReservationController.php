<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    public function index(Request $request): JsonResponse
    {
        $tab = $request->get('tab', 'upcoming');

        $query = Reservation::with(['vehicule', 'service'])
            ->where('user_id', auth()->id())
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc');

        if ($tab === 'upcoming') {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        } else {
            $query->whereIn('status', ['completed', 'cancelled'])
                  ->orderBy('scheduled_date', 'desc');
        }

        $reservations = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => ReservationResource::collection($reservations),
            'meta'    => [
                'current_page' => $reservations->currentPage(),
                'last_page'    => $reservations->lastPage(),
                'total'        => $reservations->total(),
            ],
        ]);
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->reservationService->create(
                $request->validated(),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès.',
                'data'    => new ReservationResource($reservation),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Reservation $reservation): JsonResponse
    {
        if (auth()->user()->role === 'client' && $reservation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        $reservation->load(['user', 'vehicule', 'service']);

        return response()->json([
            'success' => true,
            'data'    => new ReservationResource($reservation),
        ]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        try {
            $updated = $this->reservationService->update($reservation, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Réservation modifiée.',
                'data'    => new ReservationResource($updated),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        try {
            $this->reservationService->cancel($reservation, $request->input('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function updateStatus(Request $request, Reservation $reservation): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'in_progress', 'completed', 'cancelled'])],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            if ($request->status === 'cancelled') {
                $updated = $this->reservationService->cancel($reservation, $request->reason);
            } else {
                $updated = $this->reservationService->updateStatus($reservation, $request->status);
            }

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour.',
                'data'    => new ReservationResource($updated),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
