# 💰 Google Maps API Cost Optimization Guide

## 🎯 Optimization Strategy

### ✅ İmplemented Optimizations

1. **Geocoding ONLY on Save/Update**
   - ❌ NOT on page load
   - ❌ NOT on listing/search
   - ❌ NOT on every keystroke
   - ✅ ONLY when form is submitted

2. **30-Day Cache**
   - Addresses are cached for 30 days
   - Same address = ZERO API calls (cached)
   - Cache key: `geocode:md5(address+city+district)`

3. **On-Demand API Loading**
   - Google Places Autocomplete: Loaded ONLY when user clicks button
   - NOT loaded on page load (saves unnecessary API calls)
   - User must explicitly enable autocomplete

4. **Free Fallback**
   - OpenStreetMap (OSM) is used as FREE fallback
   - Google Maps API only used if OSM fails (optional)
   - Set `GOOGLE_USE_OSM_FIRST=true` to prefer OSM

5. **Manual Coordinate Input**
   - Users can mark location on map (FREE, no API call)
   - Users can select from existing buildings (FREE, no API call)
   - Geocoding is last resort

## 💵 Cost Breakdown

### Google Maps API Pricing (2024)

| Service | Cost | When Used |
|---------|------|-----------|
| **Geocoding API** | $5 per 1,000 requests | Only on SAVE/UPDATE |
| **Places Autocomplete** | $2.83 per 1,000 requests | On-demand (button click) |
| **Maps JavaScript API** | FREE (up to 28,000 loads/month) | Map display only |

### Estimated Monthly Cost

**Scenario: 100 new/updated buildings per month**

- **Geocoding API**: 100 requests × $0.005 = **$0.50/month**
- **Places Autocomplete**: ~50 uses × $0.00283 = **$0.14/month**
- **Maps JavaScript**: FREE (under limit)
- **Total: ~$0.64/month** (with cache: **$0.30-0.40/month**)

**With 30-day cache**: Most addresses are cached → **~70% cost reduction**

## 🔒 Security Best Practices

### 1. API Key Restrictions (CRITICAL)

In Google Cloud Console:

```
1. Go to APIs & Services > Credentials
2. Select your API key
3. Set "API restrictions":
   ✅ Restrict key → Select "Geocoding API" only
4. Set "Application restrictions":
   ✅ HTTP referrers → Add: https://yourdomain.com/*
```

### 2. Billing Alerts

```
1. Go to Billing > Budgets & alerts
2. Create budget: $50/month
3. Alert threshold: 80% ($40)
4. Action: Email notification
```

### 3. Environment Variables

```env
# .env file
GOOGLE_MAPS_API_KEY=your_api_key_here
GOOGLE_GEOCODING_ENABLED=true

# OPTIONAL: Use free OSM first (saves money)
GOOGLE_USE_OSM_FIRST=false
```

## 📊 Usage Monitoring

### Check API Usage in Google Cloud Console

```
1. Go to APIs & Services > Dashboard
2. Select "Geocoding API"
3. View "Requests" chart
4. Set date range to monitor daily/weekly usage
```

### Laravel Log Monitoring

All geocoding requests are logged:

```bash
# Check geocoding logs
tail -f storage/logs/laravel.log | grep "Geocoding"

# Count successful geocoding requests
grep "Geocoding successful" storage/logs/laravel.log | wc -l

# Count cache hits (FREE, no API call)
grep "cache hit" storage/logs/laravel.log | wc -l
```

## 🚀 Performance Optimizations

### Cache Hit Rate Monitoring

```php
// Check cache statistics
Cache::remember('geocoding_stats', 3600, function() {
    // Log cache hit rate
});
```

### Optimize Cache Duration

Current: 30 days (addresses rarely change)

To reduce more:
```php
// In GeocodingService.php
private const CACHE_DURATION_DAYS = 60; // Increase to 60 days
```

## 🎛️ Configuration Options

### Option 1: Free Only (OSM)

```env
GOOGLE_GEOCODING_ENABLED=false
GOOGLE_USE_OSM_FIRST=true
```

**Cost: $0/month** (100% free)

### Option 2: Google Primary (Recommended)

```env
GOOGLE_GEOCODING_ENABLED=true
GOOGLE_USE_OSM_FIRST=false
```

**Cost: ~$0.30-0.64/month** (with cache)

### Option 3: Hybrid (OSM First)

```env
GOOGLE_GEOCODING_ENABLED=true
GOOGLE_USE_OSM_FIRST=true
```

**Cost: ~$0.10-0.20/month** (Google only as fallback)

## ⚠️ Cost Warnings

### DO NOT:
- ❌ Load Places Autocomplete on every page
- ❌ Geocode on page load/listing
- ❌ Make API calls without cache check
- ❌ Use unlimited API key (always restrict)

### DO:
- ✅ Cache all geocoding results
- ✅ Use manual coordinate input (FREE)
- ✅ Load APIs on-demand only
- ✅ Monitor usage daily
- ✅ Set billing alerts

## 📈 Cost Reduction Tips

1. **Enable Cache**: Already implemented (30 days)
2. **Use OSM First**: Set `GOOGLE_USE_OSM_FIRST=true`
3. **Manual Input**: Encourage users to mark on map
4. **Batch Updates**: Update multiple addresses at once (shared cache)
5. **Address Validation**: Validate addresses before geocoding

## 🔍 Troubleshooting

### High API Usage?

```bash
# Check recent geocoding requests
grep "Geocoding successful" storage/logs/laravel.log | tail -20

# Clear cache if needed (will cause re-geocoding)
php artisan cache:clear
```

### API Key Errors?

```bash
# Check API key configuration
php artisan config:clear
php artisan config:cache

# Verify API key in Google Cloud Console
```

## 📝 Summary

**Current Implementation:**
- ✅ Geocoding: Only on SAVE/UPDATE
- ✅ Cache: 30 days
- ✅ On-demand API loading
- ✅ Free OSM fallback
- ✅ Manual coordinate input

**Monthly Cost (100 buildings):**
- With cache: **~$0.30-0.40**
- Without cache: **~$0.64**
- With OSM first: **~$0.10-0.20**

**Cost Reduction:**
- 30-day cache: **~70% reduction**
- OSM first: **~85% reduction**
- Manual input: **~95% reduction** (when used)
