<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

class LogSmsGateway implements SmsGateway
{
    public function send(string $phone, string $message): array
    {
        Log::info('SMS (log driver)', [
            'phone' => $phone,
            'message' => $message,
        ]);

        return [
            'success' => true,
            'provider' => 'log',
        ];
    }
}
