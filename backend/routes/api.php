<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\AuthController;
    use App\Http\Controllers\Api\ServiceController;
    use App\Http\Controllers\Api\SlotController;
    use App\Http\Controllers\Api\VehiculeController;
    use App\Http\Controllers\Api\ReservationController;
    use App\Http\Controllers\Api\PaymentController;

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

    // Webhook public (pas d'auth, appelé par les providers)
    Route::post('/payments/webhook/{provider}', [PaymentController::class, 'webhook']);

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

        Route::prefix('payments')->group(function () {
            Route::post('/reservations/{reservation}/initiate', [PaymentController::class, 'initiate']);
        });
    });





