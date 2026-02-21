<?php

namespace App\Contracts;

interface SmsServiceInterface
{
    public function sendOtp(string $phone, string $code): bool;
    
    public function sendReservationConfirmation(string $phone, string $reservationNumber, string $dateTime): bool;
    
    public function sendReminder(string $phone, string $reservationNumber, string $dateTime): bool;
    
    public function sendCompletion(string $phone, int $pointsEarned): bool;
    
    public function sendCancellation(string $phone, string $reservationNumber, string $reason): bool;
}