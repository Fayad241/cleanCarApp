<?php

namespace App\Services\Payment\Providers;

use App\Contracts\PaymentProvider;

class StripeProvider implements PaymentProvider
{
    public function initiate(array $data): array
    {
        // En dev : simulation
        if (config('app.env') !== 'production') {
            return [
                'provider'       => 'stripe',
                'transaction_id' => 'sim_stripe_' . uniqid(),
                'payment_url'    => null,
                'status'         => 'simulated',
            ];
        }

        // Production
        $stripe = new \Stripe\StripeClient(config('payment.stripe.secret_key'));

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items'           => [[
                'price_data' => [
                    'currency'     => $data['currency'] ?? 'eur',
                    'product_data' => ['name' => $data['description']],
                    'unit_amount'  => (int) ($data['amount'] * 100),
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => $data['success_url'],
            'cancel_url'  => $data['cancel_url'],
            'metadata'    => ['reservation_id' => $data['reservation_id']],
        ]);

        return [
            'provider'       => 'stripe',
            'transaction_id' => $session->id,
            'payment_url'    => $session->url,
            'status'         => 'pending',
        ];
    }

    public function verify(string $transactionId): array
    {
        if (config('app.env') !== 'production') {
            return ['status' => 'completed', 'transaction_id' => $transactionId];
        }

        $stripe  = new \Stripe\StripeClient(config('payment.stripe.secret_key'));
        $session = $stripe->checkout->sessions->retrieve($transactionId);

        return [
            'status'         => $session->payment_status === 'paid' ? 'completed' : 'pending',
            'transaction_id' => $transactionId,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        $event = $payload['type'] ?? null;

        if ($event === 'checkout.session.completed') {
            return [
                'status'         => 'completed',
                'transaction_id' => $payload['data']['object']['id'],
                'reservation_id' => $payload['data']['object']['metadata']['reservation_id'],
            ];
        }

        return ['status' => 'ignored'];
    }
}