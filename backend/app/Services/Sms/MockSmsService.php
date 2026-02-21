<?php

namespace App\Services\Sms;

use App\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\Log;

class MockSmsService implements SmsServiceInterface
{
    public function sendOtp(string $phone, string $code): bool
    {
        Log::info("📲 SMS OTP envoyé", [
            'phone' => $phone,
            'code' => $code,
            'message' => "Votre code CleanCar : {$code}. Valide 5 minutes."
        ]);

        return true;
    }

    public function sendReservationConfirmation(string $phone, string $reservationNumber, string $dateTime): bool
    {
        Log::info("📲 SMS Confirmation envoyé", [
            'phone' => $phone,
            'message' => "Réservation confirmée #{$reservationNumber}. RDV le {$dateTime}. Clean Car vous remercie !"
        ]);

        return true;
    }

    public function sendReminder(string $phone, string $reservationNumber, string $dateTime): bool
    {
        Log::info("📲 SMS Rappel envoyé", [
            'phone' => $phone,
            'message' => "⏰ Rappel : Votre lavage dans 30 min ({$dateTime}). #{$reservationNumber}"
        ]);

        return true;
    }

    public function sendCompletion(string $phone, int $pointsEarned): bool
    {
        Log::info("📲 SMS Terminé envoyé", [
            'phone' => $phone,
            'message' => "✅ Lavage terminé ! +{$pointsEarned} points gagnés ⭐. Merci et à bientôt ! 😊"
        ]);

        return true;
    }

    public function sendCancellation(string $phone, string $reservationNumber, string $reason): bool
    {
        Log::info("📲 SMS Annulation envoyé", [
            'phone' => $phone,
            'message' => "❌ Réservation #{$reservationNumber} annulée. Raison : {$reason}"
        ]);

        return true;
    }
}