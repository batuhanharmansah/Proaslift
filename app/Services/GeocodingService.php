<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Cost-Optimized Geocoding Service
 * 
 * Rules:
 * - Geocoding only happens on SAVE/UPDATE, not on page load or listing
 * - Coordinates are cached and reused
 * - Google Maps API is primary, OSM is free fallback
 * - Minimal API calls, maximum cache usage
 */
class GeocodingService
{
    private const NOMINATIM_API_URL = 'https://nominatim.openstreetmap.org/search';
    private const GOOGLE_GEOCODING_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    
    // Cache duration: 30 days (addresses rarely change)
    private const CACHE_DURATION_DAYS = 30;
    
    /**
     * Check if Google Maps API should be used
     */
    private function useGoogleMaps(): bool
    {
        return config('services.google.geocoding_enabled', false) 
            && !empty(config('services.google.maps_api_key'));
    }

    /**
     * Geocode address ONLY when saving/updating
     * 
     * @param string $address Full address
     * @param string|null $city Optional city
     * @param string|null $district Optional district
     * @return array|null ['lat' => float, 'lng' => float] or null
     */
    public function geocodeAddress(string $address, ?string $city = null, ?string $district = null): ?array
    {
        if (empty(trim($address))) {
            return null;
        }

        // 1. Check cache FIRST (most cost-effective)
        $cacheKey = $this->getCacheKey($address, $city, $district);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            Log::debug('Geocoding cache hit', ['address' => $address]);
            return $cached;
        }

        // 2. Try Google Maps API (if enabled and coordinates missing)
        if ($this->useGoogleMaps()) {
            $result = $this->geocodeWithGoogle($address, $city, $district);
            if ($result) {
                Cache::put($cacheKey, $result, now()->addDays(self::CACHE_DURATION_DAYS));
                Log::info('Google Geocoding successful (cached)', ['address' => $address]);
                return $result;
            }
        }

        // 3. Fallback to OpenStreetMap (FREE, no cost)
        $result = $this->geocodeWithOSM($address, $city, $district);
        if ($result) {
            Cache::put($cacheKey, $result, now()->addDays(self::CACHE_DURATION_DAYS));
            Log::info('OSM Geocoding successful (cached)', ['address' => $address]);
            return $result;
        }

        Log::warning('Tüm sağlayıcılar için adres eşlemesi başarısız', ['address' => $address]);
        return null;
    }

    /**
     * Google Maps Geocoding (PAID, use sparingly)
     */
    private function geocodeWithGoogle(string $address, ?string $city, ?string $district): ?array
    {
        try {
            $fullAddress = $this->buildAddressString($address, $city, $district);
            $apiKey = config('services.google.maps_api_key');

            $response = Http::timeout(5) // Short timeout to avoid long waits
                ->get(self::GOOGLE_GEOCODING_API_URL, [
                    'address' => $fullAddress,
                    'key' => $apiKey,
                    'region' => 'tr',
                    'language' => 'tr',
                    'components' => 'country:tr', // Restrict to Turkey (more accurate)
                ]);

            if (!$response->successful()) {
                Log::warning('Google Geocoding API error', [
                    'status' => $response->status(),
                    'address' => $fullAddress
                ]);
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['results'])) {
                Log::debug('Google Geocoding no results', [
                    'status' => $data['status'] ?? 'UNKNOWN',
                    'address' => $fullAddress
                ]);
                return null;
            }

            $location = $data['results'][0]['geometry']['location'];
            return [
                'lat' => (float) $location['lat'],
                'lng' => (float) $location['lng']
            ];

        } catch (\Exception $e) {
            Log::error('Google Geocoding exception', [
                'error' => $e->getMessage(),
                'address' => $address
            ]);
            return null;
        }
    }

    /**
     * OpenStreetMap Geocoding (FREE fallback)
     */
    private function geocodeWithOSM(string $address, ?string $city, ?string $district): ?array
    {
        try {
            $fullAddress = $this->buildAddressString($address, $city, $district);
            
            // Rate limiting: OSM requires max 1 request per second
            sleep(1);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => config('app.name') . '/1.0', // Required by OSM
                ])
                ->get(self::NOMINATIM_API_URL, [
                    'q' => $fullAddress,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'tr',
                ]);

            if (!$response->successful() || empty($response->json())) {
                return null;
            }

            $data = $response->json();
            if (empty($data) || !isset($data[0]['lat']) || !isset($data[0]['lon'])) {
                return null;
            }

            return [
                'lat' => (float) $data[0]['lat'],
                'lng' => (float) $data[0]['lon']
            ];

        } catch (\Exception $e) {
            Log::debug('OSM Geocoding exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build normalized address string for geocoding
     */
    private function buildAddressString(string $address, ?string $city, ?string $district): string
    {
        $parts = array_filter([trim($address), trim($district), trim($city), 'Turkey']);
        return implode(', ', $parts);
    }

    /**
     * Generate cache key from address components
     */
    private function getCacheKey(string $address, ?string $city, ?string $district): string
    {
        $normalized = strtolower(trim($address . ' ' . ($district ?? '') . ' ' . ($city ?? '')));
        return 'geocode:' . md5($normalized);
    }

    /**
     * Clear cache for an address (useful when address is corrected)
     */
    public function clearCache(string $address, ?string $city = null, ?string $district = null): void
    {
        $cacheKey = $this->getCacheKey($address, $city, $district);
        Cache::forget($cacheKey);
        Log::debug('Geocoding cache cleared', ['address' => $address]);
    }
}
