<?php

namespace App\Services;

use App\Contracts\PaymentProvider;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\Payment\Providers\StripeProvider;
use App\Services\Payment\Providers\FedaPayProvider;
use App\Services\Payment\Providers\PaystackProvider;

class PaymentService
{
    /**
     * Résoudre le bon provider selon la méthode choisie
     */
    public function resolveProvider(string $method): PaymentProvider
    {
        return match($method) {
            'stripe'       => new StripeProvider(),
            'fedapay'      => new FedaPayProvider(),
            'paystack'     => new PaystackProvider(),
            default        => throw new \Exception("Provider de paiement inconnu : {$method}"),
        };
    }

    /**
     * Initier un paiement
     */
    public function initiate(Reservation $reservation, string $method, array $customerData = []): array
    {
        $provider = $this->resolveProvider($method);

        $result = $provider->initiate([
            'reservation_id'     => $reservation->id,
            'amount'             => $reservation->price,
            'currency'           => config('payment.currency', 'XOF'),
            'description'        => "Réservation {$reservation->reservation_number}",
            'customer_firstname' => $customerData['firstname'] ?? $reservation->user->first_name,
            'customer_lastname'  => $customerData['lastname'] ?? $reservation->user->last_name,
            'customer_email'     => $customerData['email'] ?? $reservation->user->email,
            'customer_phone'     => $customerData['phone'] ?? $reservation->user->phone,
            'success_url'        => config('payment.success_url'),
            'cancel_url'         => config('payment.cancel_url'),
            'callback_url'       => config('payment.callback_url'),
        ]);

        // Créer l'enregistrement Payment
        Payment::create([
            'reservation_id' => $reservation->id,
            'amount'         => $reservation->price,
            'method'         => $method,
            'status'         => 'pending',
            'transaction_id' => $result['transaction_id'],
            'metadata'       => $result,
        ]);

        return $result;
    }

    /**
     * Traiter un webhook entrant
     */
    public function handleWebhook(string $method, array $payload): void
    {
        $provider = $this->resolveProvider($method);
        $result   = $provider->handleWebhook($payload);

        if ($result['status'] === 'ignored') {
            return;
        }

        $payment = Payment::where('transaction_id', $result['transaction_id'])->first();

        if (!$payment) {
            return;
        }

        $payment->update(['status' => $result['status']]);

        if ($result['status'] === 'completed') {
            $payment->reservation->update(['payment_status' => 'paid']);
        } elseif ($result['status'] === 'failed') {
            $payment->reservation->update(['payment_status' => 'pending']);
        }
    }
}