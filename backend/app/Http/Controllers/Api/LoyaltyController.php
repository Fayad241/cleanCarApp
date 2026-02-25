<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LoyaltyService;
use Illuminate\Http\JsonResponse;

class LoyaltyController extends Controller
{
    public function __construct(private LoyaltyService $loyaltyService) {}

    /**
     * Résumé du compte fidélité du client connecté
     */
    public function summary(): JsonResponse
    {
        $user    = auth()->user();
        $summary = $this->loyaltyService->getSummary($user);

        return response()->json([
            'success' => true,
            'data'    => $summary,
        ]);
    }

    /**
     * Historique des transactions
     */
    public function history(Request $request): JsonResponse
    {
        $transactions = auth()->user()
            ->loyaltyTransactions()
            ->with('reservation:id,reservation_number,scheduled_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $transactions->items(),
            'meta'    => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }

    /**
     * Utiliser des points pour une récompense
     */
    public function redeem(Request $request): JsonResponse
    {
        $request->validate([
            'reward_index' => ['required', 'integer', 'min:0'],
        ]);

        try {
            $reward = $this->loyaltyService->redeemReward(
                auth()->user(),
                $request->reward_index
            );

            return response()->json([
                'success' => true,
                'message' => 'Récompense obtenue avec succès.',
                'data'    => [
                    'reward'         => $reward,
                    'points_remaining' => auth()->user()->fresh()->loyalty_points,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
