<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SlotService;
use Illuminate\Support\Facades\Validator;

class SlotController extends Controller
{
    protected $slotService;

    public function __construct(SlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    /**
     * Récupérer les créneaux disponibles
     */
    public function available(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|uuid|exists:services,id',
            'vehicule_size' => 'required|in:small,medium,large,xlarge,motorcycle',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $slots = $this->slotService->getAvailableSlots(
                $request->date,
                $request->service_id,
                $request->vehicule_size
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'date' => $request->date,
                    'service_id' => $request->service_id,
                    'vehicule_size' => $request->vehicule_size,
                    'slots' => $slots,
                    'total_available' => count($slots),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([ 
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
