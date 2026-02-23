<?php

namespace App\Services\Payment\Providers;

use App\Contracts\PaymentProvider;
use Illuminate\Support\Facades\Http;

class PaystackProvider implements PaymentProvider
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('payment.paystack.secret_key');
    }

    public function initiate(array $data): array
    {
        if (config('app.env') !== 'production') {
            return [
                'provider'       => 'paystack',
                'transaction_id' => 'sim_paystack_' . uniqid(),
                'payment_url'    => null,
                'status'         => 'simulated',
            ];
        }

        $response = Http::withToken($this->secretKey)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'      => $data['customer_email'],
                'amount'     => $data['amount'] * 100,
                'currency'   => $data['currency'] ?? 'NGN',
                'reference'  => $data['reservation_id'],
                'callback_url' => $data['callback_url'],
            ]);

        $result = $response->json()['data'];

        return [
            'provider'       => 'paystack',
            'transaction_id' => $result['reference'],
            'payment_url'    => $result['authorization_url'],
            'status'         => 'pending',
        ];
    }

    public function verify(string $transactionId): array
    {
        if (config('app.env') !== 'production') {
            return ['status' => 'completed', 'transaction_id' => $transactionId];
        }

        $response = Http::withToken($this->secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$transactionId}");
        $data     = $response->json()['data'];

        return [
            'status'         => $data['status'] === 'success' ? 'completed' : 'failed',
            'transaction_id' => $transactionId,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        if (($payload['event'] ?? '') === 'charge.success') {
            return [
                'status'         => 'completed',
                'transaction_id' => $payload['data']['reference'],
                'reservation_id' => $payload['data']['reference'],
            ];
        }

        return ['status' => 'ignored'];
    }
}