<?php

return [
    'currency'     => env('PAYMENT_CURRENCY', 'XOF'),
    'success_url'  => env('PAYMENT_SUCCESS_URL', 'http://localhost:3000/payment/success'),
    'cancel_url'   => env('PAYMENT_CANCEL_URL', 'http://localhost:3000/payment/cancel'),
    'callback_url' => env('PAYMENT_CALLBACK_URL', 'http://localhost:8000/api/payments/webhook'),

    'stripe' => [
        'secret_key'      => env('STRIPE_SECRET_KEY'),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'fedapay' => [
        'secret_key' => env('FEDAPAY_SECRET_KEY'),
        'sandbox'    => env('FEDAPAY_SANDBOX', true),
    ],

    'paystack' => [
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
    ],
];