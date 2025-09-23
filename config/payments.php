<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Manual Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for manual payment methods including bank transfer
    | and mobile wallet payment options.
    |
    */

    'manual' => [
        'bank_transfer' => [
            'enabled' => true,
            'bank_name' => env('PAYMENT_BANK_NAME', 'ABC Bank Limited'),
            'account_name' => env('PAYMENT_ACCOUNT_NAME', 'Learning Management System'),
            'account_number' => env('PAYMENT_ACCOUNT_NUMBER', '1234567890123'),
            'routing_number' => env('PAYMENT_ROUTING_NUMBER', '123456789'),
            'swift_code' => env('PAYMENT_SWIFT_CODE', 'ABCBBD23'),
        ],

        'mobile_wallet' => [
            'enabled' => true,
            'providers' => [
                'bkash' => [
                    'name' => 'bKash',
                    'number' => env('PAYMENT_BKASH_NUMBER', '01700-123456'),
                ],
                'nagad' => [
                    'name' => 'Nagad',
                    'number' => env('PAYMENT_NAGAD_NUMBER', '01700-123456'),
                ],
                'rocket' => [
                    'name' => 'Rocket',
                    'number' => env('PAYMENT_ROCKET_NUMBER', '01700-1234567'),
                ],
                'upay' => [
                    'name' => 'Upay',
                    'number' => env('PAYMENT_UPAY_NUMBER', '01700-123456'),
                ],
            ],
        ],

        // Processing times
        'processing_time' => '24 hours',
        'business_hours' => 'Monday to Friday, 9 AM to 6 PM',

        // Support contact
        'support' => [
            'email' => env('PAYMENT_SUPPORT_EMAIL', 'support@yoursite.com'),
            'phone' => env('PAYMENT_SUPPORT_PHONE', '+880-1700-123456'),
        ],

        // Instructions
        'instructions' => [
            'bank_transfer' => [
                'Send exactly the amount shown',
                'Keep your transaction receipt/screenshot',
                'Fill the form with correct transaction details',
                'Your purchase will be activated after admin approval',
            ],
            'mobile_wallet' => [
                'Use Send Money option in your mobile wallet',
                'Send exactly the amount shown',
                'Keep transaction screenshot',
                'Enter correct transaction ID in the form',
            ],
        ],
    ],
];
