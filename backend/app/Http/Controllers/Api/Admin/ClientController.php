<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filter = $request->get('filter', 'all');

        $query = User::where('role', 'client')
            ->withCount('reservations')
            ->withSum(['reservations' => function ($q) {
                $q->where('payment_status', 'paid');
            }], 'price');

        $query = match($filter) {
            'vip'      => $query->having('reservations_count', '>=', 10),
            'inactive' => $query->whereDoesntHave('reservations', function ($q) {
                $q->where('scheduled_date', '>=', now()->subMonths(3));
            }),
            'new'      => $query->whereMonth('created_at', now()->month),
            default    => $query,
        };

        $clients = $query->orderByDesc('reservations_count')->paginate(20);

        $data = $clients->map(function ($client) {
            return [
                'id'                => $client->id,
                'name'              => $client->first_name . ' ' . $client->last_name,
                'phone'             => $client->phone,
                'email'             => $client->email,
                'total_reservations'=> $client->reservations_count,
                'total_spent'       => $client->reservations_sum_price ?? 0,
                'is_vip'            => $client->reservations_count >= 10,
                'joined_at'         => $client->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'total'        => $clients->total(),
                'current_page' => $clients->currentPage(),
                'last_page'    => $clients->lastPage(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        $reservations = $user->reservations()
            ->with(['service', 'vehicule'])
            ->orderByDesc('scheduled_date')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => [
                'client' => [
                    'id'    => $user->id,
                    'name'  => $user->first_name . ' ' . $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ],
                'stats' => [
                    'total_reservations' => $user->reservations()->count(),
                    'total_spent'        => $user->reservations()->where('payment_status', 'paid')->sum('price'),
                    'last_visit'         => $user->reservations()->latest('scheduled_date')->value('scheduled_date'),
                ],
                'reservations' => $reservations,
            ],
        ]);
    }
}
