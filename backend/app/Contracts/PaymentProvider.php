<?php

namespace App\Contracts;

interface PaymentProvider
{
    public function initiate(array $data): array;
    public function verify(string $transactionId): array;
    public function handleWebhook(array $payload): array;
}