<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\Api\VehiculeController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\PlanningController;
use App\Http\Controllers\Api\Admin\ClientController;
use App\Http\Controllers\Api\Admin\StationSettingController;
use App\Http\Controllers\Api\LoyaltyController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Routes publiques (sans auth)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Services (publique)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

// Créneaux disponibles (publique)
Route::get('/available-slots', [SlotController::class, 'available']);

// Routes protégées (avec auth JWT)
Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // Véhicules
    Route::prefix('vehicules')->group(function () {
        Route::get('/', [VehiculeController::class, 'index']);
        Route::post('/', [VehiculeController::class, 'store']);
        Route::get('/{id}', [VehiculeController::class, 'show']);
        Route::patch('/{id}', [VehiculeController::class, 'update']);
        Route::delete('/{id}', [VehiculeController::class, 'destroy']);
        Route::patch('/{id}/set-default', [VehiculeController::class, 'setDefault']);
    });

    // Réservations
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index']);
        Route::post('/', [ReservationController::class, 'store']);
        Route::get('/{reservation}', [ReservationController::class, 'show']);
        Route::patch('/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/{reservation}', [ReservationController::class, 'destroy']);
        Route::patch('/{reservation}/status', [ReservationController::class, 'updateStatus'])
            ->middleware('role:employee,manager,admin');
    });

    // Loyauté
    Route::prefix('loyalty')->group(function () {
      Route::get('/summary', [LoyaltyController::class, 'summary']);
      Route::get('/history', [LoyaltyController::class, 'history']);
      Route::post('/redeem', [LoyaltyController::class, 'redeem']);
    });

});


Route::middleware(['auth:api', 'role:employee,manager,admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/revenue', [DashboardController::class, 'revenue']);
        Route::get('/services', [DashboardController::class, 'services']);
    });

    // Planning
    Route::prefix('planning')->group(function () {
        Route::get('/', [PlanningController::class, 'index']);
        Route::get('/week', [PlanningController::class, 'week']);
        Route::post('/walk-in', [PlanningController::class, 'storeWalkIn']);
    });

    // Clients
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::get('/{user}', [ClientController::class, 'show']);
    });

    Route::get('/settings', [StationSettingController::class, 'index']);

});   

Route::middleware(['auth:api', 'role:manager,admin'])->prefix('admin')->group(function () {
   
    // Paramètres station
    Route::prefix('settings')->group(function () {
        Route::put('/opening-hours', [StationSettingController::class, 'updateOpeningHours']);
        Route::put('/capacity', [StationSettingController::class, 'updateCapacity']);
        Route::put('/station-info', [StationSettingController::class, 'updateStationInfo']);
        Route::put('/late-policy', [StationSettingController::class, 'updateLatePolicy']);
        Route::put('/loyalty-rules', [StationSettingController::class, 'updateLoyaltyRules']);
    });
});
