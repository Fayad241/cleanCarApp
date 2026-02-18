<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\SmsServiceInterface;
use App\Helpers\OtpHelper;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Inscription - Étape 1 : Créer compte
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|unique:users,phone|regex:/^\+229\d{8}$/',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'phone.regex' => 'Le numéro doit être au format béninois (+229XXXXXXXX)',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Créer l'utilisateur (non vérifié)
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client',
        ]);

        // Générer et envoyer OTP
        $otpCode = OtpHelper::generate($user->phone);
        $this->smsService->sendOtp($user->phone, $otpCode);

        return response()->json([
            'success' => true,
            'message' => 'Compte créé. Un code de vérification a été envoyé par SMS.',
            'data' => [
                'user_id' => $user->id,
                'phone' => $user->phone,
            ]
        ], 201);
    }

    /**
     * Inscription - Étape 2 : Vérifier OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
            'otp_code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier OTP
        if (!OtpHelper::verify($request->phone, $request->otp_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Code OTP invalide ou expiré.'
            ], 400);
        }

        // Marquer téléphone comme vérifié
        $user = User::where('phone', $request->phone)->first();
        $user->update(['phone_verified_at' => now()]);

        // Générer token JWT
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Téléphone vérifié avec succès.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Renvoyer un code OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Générer nouveau code
        $otpCode = OtpHelper::generate($request->phone);
        $this->smsService->sendOtp($request->phone, $otpCode);

        return response()->json([
            'success' => true,
            'message' => 'Nouveau code envoyé par SMS.',
        ], 200);
    }

    /**
     * Connexion
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Peut être phone ou email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Déterminer si login est email ou phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginField => $request->login,
            'password' => $request->password,
        ];

        // Vérifier credentials
        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.'
            ], 401);
        }

        // Vérifier téléphone vérifié
        if (!$user->phone_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez vérifier votre numéro de téléphone.',
                'requires_verification' => true,
                'phone' => $user->phone,
            ], 403);
        }

        // Générer token
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie.'
        ], 200);
    }

    /**
     * Rafraîchir token
     */
    public function refresh()
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $newToken,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Profil utilisateur connecté
     */
    public function me()
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'role' => $user->role,
                    'loyalty_points' => $user->loyalty_points,
                    'phone_verified_at' => $user->phone_verified_at,
                    'created_at' => $user->created_at,
                ]
            ]
        ], 200);
    }
}
