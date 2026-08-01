<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SystemEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * 📟 Mobil hata/crash raporlama uç noktası.
 * Kendisi asla hata üretip tekrar buraya rapor göndermemeli — bu yüzden
 * her şey try/catch içinde ve daima 200 döner.
 */
class MonitoringController extends Controller
{
    public function logError(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'nullable|string|max:50',
                'severity' => 'nullable|string|in:critical,warning,info',
                'message' => 'required|string|max:2000',
                'stack_trace' => 'nullable|string',
                'screen' => 'nullable|string|max:150',
                'app_version' => 'nullable|string|max:30',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false], 200);
            }

            $data = $validator->validated();

            SystemEvent::log(
                source: 'mobile',
                type: $data['type'] ?? 'mobile_api_error',
                severity: $data['severity'] ?? 'warning',
                message: $data['message'],
                stackTrace: $data['stack_trace'] ?? null,
                context: [
                    'screen' => $data['screen'] ?? null,
                    'app_version' => $data['app_version'] ?? null,
                    'user_id' => $request->user()?->id,
                    'company_id' => $request->user()?->company_id,
                ]
            );

            return response()->json(['success' => true], 200);
        } catch (Throwable $e) {
            return response()->json(['success' => false], 200);
        }
    }
}
