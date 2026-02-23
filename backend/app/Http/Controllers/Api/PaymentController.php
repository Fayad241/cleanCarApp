<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    /**
     * Initier un paiement
     */
    public function initiate(Request $request, Reservation $reservation): JsonResponse
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
        }

        if ($reservation->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Cette réservation est déjà payée.'], 422);
        }

        $request->validate([
            'method' => ['required', 'in:stripe,fedapay,paystack,cash'],
        ]);

        try {
            $result = $this->paymentService->initiate(
                $reservation->load('user'),
                $request->method
            );

            return response()->json([
                'success' => true,
                'message' => 'Paiement initié.',
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Webhook — appelé par le provider
     */
    public function webhook(Request $request, string $provider): JsonResponse
    {
        try {
            $this->paymentService->handleWebhook($provider, $request->all());
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
