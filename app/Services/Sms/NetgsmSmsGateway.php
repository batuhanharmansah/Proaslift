<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Http;

class NetgsmSmsGateway implements SmsGateway
{
    public function send(string $phone, string $message): array
    {
        $user = config('sms.netgsm.user');
        $password = config('sms.netgsm.password');

        if (!$user || !$password) {
            return [
                'success' => false,
                'provider' => 'netgsm',
                'error' => 'Netgsm credentials not configured',
            ];
        }

        try {
            $response = Http::timeout(15)->get(config('sms.netgsm.endpoint'), [
                'usercode' => $user,
                'password' => $password,
                'gsmno' => $phone,
                'message' => $message,
                'msgheader' => config('sms.originator'),
            ]);

            $body = trim((string) $response->body());
            $success = (bool) preg_match('/^00\s+\d+$/', $body);

            return [
                'success' => $success,
                'provider' => 'netgsm',
                'error' => $success ? null : $body,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'provider' => 'netgsm',
                'error' => $e->getMessage(),
            ];
        }
    }
}
