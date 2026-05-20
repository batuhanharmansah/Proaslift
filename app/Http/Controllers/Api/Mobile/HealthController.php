<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 🛡️ ENTERPRISE HEALTH CHECK CONTROLLER
 * Sistem durumu, veritabanı bağlantısı, servis kontrolü
 */
class HealthController extends Controller
{
    /**
     * 🏥 System Health Check
     */
    public function check(Request $request)
    {
        try {
            $startTime = microtime(true);

            // Database connection check
            $dbStatus = $this->checkDatabase();

            // Cache check
            $cacheStatus = $this->checkCache();

            // Storage check
            $storageStatus = $this->checkStorage();

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            $overallStatus = $dbStatus && $cacheStatus && $storageStatus ? 'healthy' : 'degraded';

            return response()->json([
                'success' => true,
                'status' => $overallStatus,
                'timestamp' => now()->toISOString(),
                'response_time_ms' => $responseTime,
                'version' => '1.0.0',
                'environment' => app()->environment(),
                'checks' => [
                    'database' => $dbStatus ? 'ok' : 'error',
                    'cache' => $cacheStatus ? 'ok' : 'error',
                    'storage' => $storageStatus ? 'ok' : 'error',
                ],
                'system_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                    'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
                ],
            ], $overallStatus === 'healthy' ? 200 : 503);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Sağlık kontrolü başarısız',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 503);
        }
    }

    /**
     * 🗄️ Database Health Check
     */
    private function checkDatabase(): bool
    {
        try {
            DB::select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            \Log::error('Veritabanı sağlık kontrolü başarısız', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 💾 Cache Health Check
     */
    private function checkCache(): bool
    {
        try {
            $key = 'health_check_' . time();
            $value = 'test';

            cache()->put($key, $value, 60);
            $retrieved = cache()->get($key);
            cache()->forget($key);

            return $retrieved === $value;
        } catch (\Exception $e) {
            \Log::error('Önbellek sağlık kontrolü başarısız', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 📁 Storage Health Check
     */
    private function checkStorage(): bool
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'health check test';

            \Storage::disk('public')->put($testFile, $testContent);
            $retrieved = \Storage::disk('public')->get($testFile);
            \Storage::disk('public')->delete($testFile);

            return $retrieved === $testContent;
        } catch (\Exception $e) {
            \Log::error('Depolama sağlık kontrolü başarısız', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
