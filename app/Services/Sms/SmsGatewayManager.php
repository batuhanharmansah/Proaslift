<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;

class SmsGatewayManager
{
    public function driver(?string $driver = null): SmsGateway
    {
        $driver = $driver ?? config('sms.driver', 'log');

        return match ($driver) {
            'netgsm' => app(NetgsmSmsGateway::class),
            default => app(LogSmsGateway::class),
        };
    }
}
