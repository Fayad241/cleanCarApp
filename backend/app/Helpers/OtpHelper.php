<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class OtpHelper
{
    const OTP_EXPIRY = 15; // minutes
    const MAX_ATTEMPTS = 3;
    const RESEND_COOLDOWN = 60; // secondes

    public static function generate(string $phone): string
    {
        // Vérifier si déjà envoyé récemment
        if (Cache::has("otp_sent_{$phone}")) {
            throw new \Exception("Code déjà envoyé. Attendez 1 minute.");
        }

        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        Cache::put("otp_{$phone}", $code, now()->addMinutes(self::OTP_EXPIRY));
        Cache::put("otp_sent_{$phone}", true, self::RESEND_COOLDOWN);
        Cache::put("otp_attempts_{$phone}", 0, now()->addMinutes(self::OTP_EXPIRY));
        
        return $code;
    }

    public static function verify(string $phone, string $code): bool
    {
        // Vérifier nombre de tentatives
        $attempts = Cache::get("otp_attempts_{$phone}", 0);
        
        if ($attempts >= self::MAX_ATTEMPTS) {
            throw new \Exception("Trop de tentatives. Réessayez dans 5 minutes.");
        }

        $storedCode = Cache::get("otp_{$phone}");
        
        if (!$storedCode) {
            throw new \Exception("Code expiré ou invalide.");
        }

        // Incrémenter tentatives
        Cache::increment("otp_attempts_{$phone}");

        if ($storedCode !== $code) {
            return false;
        }
        
        // Supprimer tout après succès
        Cache::forget("otp_{$phone}");
        Cache::forget("otp_attempts_{$phone}");
        Cache::forget("otp_sent_{$phone}");
        
        return true;
    }
}