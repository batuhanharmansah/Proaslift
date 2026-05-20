<?php

return [
    'driver' => env('SMS_DRIVER', 'log'),
    'originator' => env('SMS_ORIGINATOR', 'ASANSOR'),

    'maintenance_approval' => [
        'token_days' => (int) env('MAINTENANCE_APPROVAL_TOKEN_DAYS', 30),
        'sms_cooldown_hours' => (int) env('MAINTENANCE_APPROVAL_SMS_COOLDOWN_HOURS', 24),
    ],

    'netgsm' => [
        'user' => env('NETGSM_USER'),
        'password' => env('NETGSM_PASSWORD'),
        'endpoint' => env('NETGSM_ENDPOINT', 'https://api.netgsm.com.tr/sms/send/get'),
    ],
];
