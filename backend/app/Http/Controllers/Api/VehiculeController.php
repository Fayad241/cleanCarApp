<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicule;
use Illuminate\Support\Facades\Validator;

class VehiculeController extends Controller
{
    /**
     * Liste des véhicules de l'utilisateur connecté
     */
    public function index()
    {
        $vehicules = auth()->user()->vehicules()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'vehicules' => $vehicules->map(function ($vehicule) {
                    return [
                        'id' => $vehicule->id,
                        'type' => $vehicule->type,
                        'brand' => $vehicule->brand,
                        'model' => $vehicule->model,
                        'size' => $vehicule->size,
                        'color' => $vehicule->color,
                        'plate_number' => $vehicule->plate_number,
                        'is_default' => $vehicule->is_default,
                        'created_at' => $vehicule->created_at,
                    ];
                })
            ]
        ], 200);
    }

    /**
     * Créer un nouveau véhicule
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:car,motorcycle',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'size' => 'required|in:small,medium,large,xlarge,motorcycle',
            'color' => 'required|string|max:100',
            'plate_number' => 'nullable|string|max:50',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Si c'est le premier véhicule, le définir par défaut automatiquement
        $isFirstVehicule = auth()->user()->vehicules()->count() === 0;
        $isDefault = $request->is_default ?? $isFirstVehicule;

        // Si is_default = true, retirer le défaut des autres véhicules
        if ($isDefault) {
            auth()->user()->vehicules()->update(['is_default' => false]);
        }

        // Créer le véhicule
        $vehicule = auth()->user()->vehicules()->create([
            'type' => $request->type,
            'brand' => $request->brand,
            'model' => $request->model,
            'size' => $request->size,
            'color' => $request->color,
            'plate_number' => $request->plate_number,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Véhicule ajouté avec succès.',
            'data' => [
                'vehicule' => [
                    'id' => $vehicule->id,
                    'type' => $vehicule->type,
                    'brand' => $vehicule->brand,
                    'model' => $vehicule->model,
                    'size' => $vehicule->size,
                    'color' => $vehicule->color,
                    'plate_number' => $vehicule->plate_number,
                    'is_default' => $vehicule->is_default,
                ]
            ]
        ], 201);
    }

    /**
     * Afficher un véhicule
     */
    public function show($id)
    {
        $vehicule = auth()->user()->vehicules()->find($id);

        if (!$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'vehicule' => [
                    'id' => $vehicule->id,
                    'type' => $vehicule->type,
                    'brand' => $vehicule->brand,
                    'model' => $vehicule->model,
                    'size' => $vehicule->size,
                    'color' => $vehicule->color,
                    'plate_number' => $vehicule->plate_number,
                    'is_default' => $vehicule->is_default,
                    'created_at' => $vehicule->created_at,
                ]
            ]
        ], 200);
    }

    /**
     * Modifier un véhicule
     */
    public function update(Request $request, $id)
    {
        $vehicule = auth()->user()->vehicules()->find($id);

        if (!$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|in:car,motorcycle',
            'brand' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'size' => 'sometimes|in:small,medium,large,xlarge,motorcycle',
            'color' => 'sometimes|string|max:100',
            'plate_number' => 'nullable|string|max:50',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Si is_default = true, retirer le défaut des autres
        if ($request->has('is_default') && $request->is_default) {
            auth()->user()->vehicules()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $vehicule->update($request->only([
            'type', 'brand', 'model', 'size', 'color', 'plate_number', 'is_default'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Véhicule modifié avec succès.',
            'data' => [
                'vehicule' => [
                    'id' => $vehicule->id,
                    'type' => $vehicule->type,
                    'brand' => $vehicule->brand,
                    'model' => $vehicule->model,
                    'size' => $vehicule->size,
                    'color' => $vehicule->color,
                    'plate_number' => $vehicule->plate_number,
                    'is_default' => $vehicule->is_default,
                ]
            ]
        ], 200);
    }

    /**
     * Supprimer un véhicule
     */
    public function destroy($id)
    {
        $vehicule = auth()->user()->vehicules()->find($id);

        if (!$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé.'
            ], 404);
        }

        // Empêcher suppression si c'est le seul véhicule
        if (auth()->user()->vehicules()->count() === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez avoir au moins un véhicule.'
            ], 400);
        }

        // Si c'était le véhicule par défaut, définir un autre par défaut
        $wasDefault = $vehicule->is_default;
        $vehicule->delete();

        if ($wasDefault) {
            $firstVehicule = auth()->user()->vehicules()->first();
            if ($firstVehicule) {
                $firstVehicule->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Véhicule supprimé avec succès.'
        ], 200);
    }

    /**
     * Définir un véhicule par défaut
     */
    public function setDefault($id)
    {
        $vehicule = auth()->user()->vehicules()->find($id);

        if (!$vehicule) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé.'
            ], 404);
        }

        // Retirer le défaut des autres
        auth()->user()->vehicules()->update(['is_default' => false]);

        // Définir ce véhicule par défaut
        $vehicule->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Véhicule défini par défaut.',
            'data' => [
                'vehicule' => [
                    'id' => $vehicule->id,
                    'brand' => $vehicule->brand,
                    'model' => $vehicule->model,
                    'is_default' => true,
                ]
            ]
        ], 200);
    }
}
