<?php

namespace App\Services;

use App\Models\MobileDeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private const EXPO_PUSH_ENDPOINT = 'https://exp.host/--/api/v2/push/send';

    public function registerDeviceToken(User $user, string $token, ?string $platform = null): void
    {
        MobileDeviceToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'platform' => $platform,
                'is_active' => true,
                'last_seen_at' => now(),
            ]
        );
    }

    public function unregisterDeviceToken(User $user, ?string $token = null): void
    {
        MobileDeviceToken::query()
            ->where('user_id', $user->id)
            ->when($token, fn ($query) => $query->where('token', $token))
            ->update([
                'is_active' => false,
                'last_seen_at' => now(),
            ]);
    }

    public function sendToUsers(array $userIds, array $payload): void
    {
        if (empty($userIds)) {
            return;
        }

        $tokens = MobileDeviceToken::query()
            ->whereIn('user_id', array_values(array_unique($userIds)))
            ->where('is_active', true)
            ->pluck('token')
            ->all();

        if (empty($tokens)) {
            return;
        }

        $messages = array_map(function (string $token) use ($payload) {
            return [
                'to' => $token,
                'sound' => 'default',
                'title' => $payload['title'] ?? 'Yeni Bildirim',
                'body' => $payload['body'] ?? '',
                'data' => $payload['data'] ?? [],
                'badge' => $payload['badge'] ?? null,
                'priority' => 'high',
            ];
        }, $tokens);

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->post(self::EXPO_PUSH_ENDPOINT, $messages);

            if (!$response->successful()) {
                Log::warning('Expo push request failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return;
            }

            $results = $response->json('data', []);
            foreach ($results as $index => $result) {
                if (($result['status'] ?? null) === 'error') {
                    $details = $result['details'] ?? [];
                    if (($details['error'] ?? null) === 'DeviceNotRegistered' && isset($tokens[$index])) {
                        MobileDeviceToken::where('token', $tokens[$index])->update(['is_active' => false]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Expo push send exception', [
                'error' => $e->getMessage(),
                'user_ids' => $userIds,
            ]);
        }
    }
}
