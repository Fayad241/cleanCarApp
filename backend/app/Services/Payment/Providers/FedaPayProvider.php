<?php

namespace App\Services\Payment\Providers;

use App\Contracts\PaymentProvider;
use Illuminate\Support\Facades\Http;

class FedaPayProvider implements PaymentProvider
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey  = config('payment.fedapay.secret_key');
        $this->baseUrl = config('payment.fedapay.sandbox')
            ? 'https://sandbox-api.fedapay.com/v1'
            : 'https://api.fedapay.com/v1';
    }

    public function initiate(array $data): array
    {
        if (config('app.env') !== 'production') {
            return [
                'provider'       => 'fedapay',
                'transaction_id' => 'sim_feda_' . uniqid(),
                'payment_url'    => 'https://sandbox.fedapay.com/pay/sim_' . uniqid(),
                'status'         => 'simulated',
            ];
        }

        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/transactions", [
                'description'  => $data['description'],
                'amount'       => $data['amount'],
                'currency'     => ['iso' => $data['currency'] ?? 'XOF'],
                'callback_url' => $data['callback_url'],
                'customer'     => [
                    'firstname' => $data['customer_firstname'],
                    'lastname'  => $data['customer_lastname'],
                    'email'     => $data['customer_email'],
                    'phone_number' => [
                        'number'  => $data['customer_phone'],
                        'country' => 'BJ',
                    ],
                ],
            ]);

        $transaction = $response->json();

        return [
            'provider'       => 'fedapay',
            'transaction_id' => $transaction['v1/transaction']['id'],
            'payment_url'    => $transaction['v1/transaction']['links']['payment_url'] ?? null,
            'status'         => 'pending',
        ];
    }

    public function verify(string $transactionId): array
    {
        if (config('app.env') !== 'production') {
            return ['status' => 'completed', 'transaction_id' => $transactionId];
        }

        $response    = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/transactions/{$transactionId}");
        $transaction = $response->json()['v1/transaction'];

        return [
            'status'         => $transaction['status'] === 'approved' ? 'completed' : $transaction['status'],
            'transaction_id' => $transactionId,
        ];
    }

    public function handleWebhook(array $payload): array
    {
        if (($payload['name'] ?? '') === 'transaction.approved') {
            return [
                'status'         => 'completed',
                'transaction_id' => (string) $payload['entity']['id'],
                'reservation_id' => $payload['entity']['description'] ?? null,
            ];
        }

        return ['status' => 'ignored'];
    }
}