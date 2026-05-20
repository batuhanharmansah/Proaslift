<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Employee;
use App\Models\EmployeeLocation;
use App\Models\MaintenanceSchedule;
use App\Models\LocationCheck;
use App\Services\LocationCheckService;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LocationMapController extends Controller
{
    private function resolveCompanyId($user): ?int
    {
        $companyId = $user->company_id;

        if (!$companyId) {
            $employee = $user->employee ?? Employee::where('email', $user->email)->first();
            $companyId = $employee?->company_id;
        }

        return $companyId ? (int) $companyId : null;
    }

    private function resolveEmployee($user, ?int $companyId): ?Employee
    {
        return $user->employee
            ?? Employee::where('email', $user->email)
                ->when($companyId, fn ($query) => $query->where('company_id', $companyId))
                ->first();
    }

    /**
     * Get map data for admin: buildings and field employees with their locations
     */
    public function getMapData(Request $request)
    {
        $user = Auth::user();
        $companyId = $this->resolveCompanyId($user);

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bilgisi bulunamadı',
            ], 403);
        }

        // Get all buildings (including those without coordinates)
        $buildingsQuery = Building::where('company_id', $companyId)
            ->where('status', 'aktif')
            ->select('id', 'name', 'address', 'latitude', 'longitude', 'city', 'district');

        $buildings = $buildingsQuery->get()
            ->map(function ($building) {
                return [
                    'id' => $building->id,
                    'name' => $building->name,
                    'address' => $building->address,
                    'city' => $building->city,
                    'district' => $building->district,
                    'has_coordinates' => !is_null($building->latitude) && !is_null($building->longitude),
                    'coordinates' => ($building->latitude && $building->longitude) ? [
                        'lat' => (float) $building->latitude,
                        'lng' => (float) $building->longitude,
                    ] : null,
                ];
            });

        // Separate buildings with and without coordinates
        $buildingsWithCoordinates = $buildings->filter(function ($building) {
            return $building['has_coordinates'];
        })->values();

        $buildingsWithoutCoordinates = $buildings->filter(function ($building) {
            return !$building['has_coordinates'];
        })->values();

        // Get active employees with recent locations
        // Only show employees with location updates in the last 1 hour (online/active)
        // This prevents showing employees who uninstalled the app
        $recentLocationThreshold = Carbon::now()->subHours(1);

        // Get recent locations first, then get employees
        $recentLocations = EmployeeLocation::where('company_id', $companyId)
            ->where('recorded_at', '>=', $recentLocationThreshold)
            ->with(['employee' => function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->where('is_active', true);
            }])
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->filter(function ($location) {
                return $location->employee !== null;
            });

        // Group by employee_id to get latest location per employee
        $employeeLocationsMap = $recentLocations->groupBy('employee_id')->map(function ($locations) {
            return $locations->first(); // Get most recent location
        });

        // Get unique employee IDs
        $employeeIds = $employeeLocationsMap->keys()->toArray();

        // Get employees with their data
        $activeEmployees = Employee::whereIn('id', $employeeIds)
            ->with(['maintenanceSchedules' => function ($query) {
                $query->whereIn('status', ['atandi', 'baslandi'])
                    ->orderBy('scheduled_date', 'desc')
                    ->orderBy('scheduled_time', 'desc')
                    ->with(['building', 'arrivalCheck', 'departureCheck']);
            }])
            ->get()
            ->map(function ($employee) use ($employeeLocationsMap) {
                $latestLocation = $employeeLocationsMap->get($employee->id);
                $activeMaintenance = $employee->maintenanceSchedules->first();

                $locationCheckService = new LocationCheckService();
                $checkSummary = $activeMaintenance
                    ? $locationCheckService->getLocationCheckSummary($activeMaintenance)
                    : null;

                // Check if location is recent (within last 1 hour) - online status
                $isOnline = $latestLocation && $latestLocation->recorded_at->isAfter(Carbon::now()->subHour(1));
                $minutesSinceUpdate = $latestLocation
                    ? $latestLocation->recorded_at->diffInMinutes(Carbon::now())
                    : null;

                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'position' => $employee->position_label,
                    'phone' => $employee->phone,
                    'coordinates' => $latestLocation ? [
                        'lat' => (float) $latestLocation->latitude,
                        'lng' => (float) $latestLocation->longitude,
                    ] : null,
                    'last_update' => $latestLocation?->recorded_at?->format('Y-m-d H:i:s'),
                    'is_online' => $isOnline,
                    'minutes_since_update' => $minutesSinceUpdate,
                    'active_maintenance' => $activeMaintenance ? [
                        'id' => $activeMaintenance->id,
                        'building_id' => $activeMaintenance->building_id,
                        'building_name' => $activeMaintenance->building->name,
                        'building_address' => $activeMaintenance->building->address,
                        'scheduled_time' => Carbon::parse($activeMaintenance->scheduled_date->format('Y-m-d') . ' ' . $activeMaintenance->scheduled_time)->format('Y-m-d H:i:s'),
                        'estimated_duration' => $activeMaintenance->estimated_duration,
                        'status' => $activeMaintenance->status,
                        'status_label' => $activeMaintenance->status_label,
                    ] : null,
                    'location_checks' => $checkSummary,
                ];
            })
            ->filter(function ($employee) {
                // Only return employees with valid coordinates AND online status (last update within 1 hour)
                // This prevents showing employees who uninstalled the app
                return $employee['coordinates'] !== null && $employee['is_online'] === true;
            })
            ->values();

        // Get maintenance schedules for selected date (default: today)
        // Each job shows on its scheduled_date (not created_at)
        $selectedDate = $request->input('date', today()->format('Y-m-d'));
        $selectedDateCarbon = Carbon::parse($selectedDate);

        $todaySchedules = MaintenanceSchedule::where('company_id', $companyId)
            ->whereDate('scheduled_date', $selectedDateCarbon) // Use scheduled_date, not created_at
            ->where('status', '!=', 'iptal') // Exclude cancelled
            ->with(['building', 'assignedEmployee'])
            ->get()
            ->map(function ($schedule) {
                // Only include if building has coordinates
                if (!$schedule->building || !$schedule->building->latitude || !$schedule->building->longitude) {
                    return null;
                }

                // Format scheduled time (handle null)
                $scheduledDateTime = null;
                if ($schedule->scheduled_time) {
                    try {
                        $scheduledDateTime = Carbon::parse($schedule->scheduled_date->format('Y-m-d') . ' ' . $schedule->scheduled_time)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $scheduledDateTime = $schedule->scheduled_date->format('Y-m-d');
                    }
                } else {
                    $scheduledDateTime = $schedule->scheduled_date->format('Y-m-d');
                }

                // Calculate estimated end time
                $estimatedEndTime = null;
                if ($schedule->scheduled_time && $schedule->estimated_duration) {
                    try {
                        $startTime = Carbon::parse($schedule->scheduled_date->format('Y-m-d') . ' ' . $schedule->scheduled_time);
                        $estimatedEndTime = $startTime->copy()->addMinutes($schedule->estimated_duration)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        // Ignore if time parsing fails
                    }
                }

                // Format scheduled_time for display (H:i format)
                $scheduledTimeDisplay = null;
                if ($schedule->scheduled_time) {
                    try {
                        $timeParts = explode(':', $schedule->scheduled_time);
                        if (count($timeParts) >= 2) {
                            $scheduledTimeDisplay = $timeParts[0] . ':' . $timeParts[1]; // H:i format
                        }
                    } catch (\Exception $e) {
                        // Ignore if parsing fails
                    }
                }

                return [
                    'id' => $schedule->id,
                    'maintenance_type' => $schedule->maintenance_type,
                    'maintenance_type_label' => $schedule->maintenance_type_label,
                    'priority' => $schedule->priority,
                    'priority_label' => $schedule->priority_label,
                    'status' => $schedule->status,
                    'status_label' => $schedule->status_label,
                    'scheduled_date' => $schedule->scheduled_date->format('Y-m-d'),
                    'scheduled_time' => $scheduledDateTime, // Full datetime for calculations
                    'scheduled_time_display' => $scheduledTimeDisplay, // H:i format for display (e.g. "14:30")
                    'estimated_duration' => $schedule->estimated_duration,
                    'estimated_end_time' => $estimatedEndTime,
                    'description' => $schedule->description,
                    'building' => [
                        'id' => $schedule->building->id,
                        'name' => $schedule->building->name,
                        'address' => $schedule->building->address,
                        'district' => $schedule->building->district,
                        'city' => $schedule->building->city,
                        'coordinates' => [
                            'lat' => (float) $schedule->building->latitude,
                            'lng' => (float) $schedule->building->longitude,
                        ],
                    ],
                    'assigned_employee' => $schedule->assignedEmployee ? [
                        'id' => $schedule->assignedEmployee->id,
                        'name' => $schedule->assignedEmployee->full_name,
                        'phone' => $schedule->assignedEmployee->phone,
                        'position' => $schedule->assignedEmployee->position_label,
                    ] : null,
                ];
            })
            ->filter() // Remove nulls (buildings without coordinates)
            ->values();

        // Get location checks for selected date (based on scheduled_time)
        $todayLocationChecks = LocationCheck::where('company_id', $companyId)
            ->whereDate('scheduled_time', $selectedDateCarbon) // Use selected date, not today()
            ->with(['employee', 'building', 'maintenanceSchedule'])
            ->get()
            ->map(function ($check) {
                return [
                    'id' => $check->id,
                    'check_type' => $check->check_type,
                    'check_type_label' => $check->check_type === 'arrival' ? 'Geliş' : 'Ayrılış',
                    'employee_id' => $check->employee_id,
                    'employee_name' => $check->employee->full_name,
                    'building_id' => $check->building_id,
                    'building_name' => $check->building->name,
                    'scheduled_time' => $check->scheduled_time->format('Y-m-d H:i:s'),
                    'actual_time' => $check->actual_time?->format('Y-m-d H:i:s'),
                    'status' => $check->status,
                    'status_label' => $this->getStatusLabel($check->status),
                    'is_on_time' => $check->is_on_time,
                    'time_difference_minutes' => $check->time_difference_minutes,
                    'distance_from_building' => $check->distance_from_building,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'buildings' => $buildingsWithCoordinates,
                'buildings_without_coordinates' => $buildingsWithoutCoordinates,
                'employees' => $activeEmployees,
                'today_schedules' => $todaySchedules, // Maintenance jobs for selected date (based on scheduled_date)
                'selected_date' => $selectedDate, // The date being viewed
                'location_checks' => $todayLocationChecks,
            ],
        ]);
    }

    /**
     * Get location check details for a maintenance schedule
     */
    public function getLocationChecks($maintenanceScheduleId)
    {
        $user = Auth::user();
        $companyId = $this->resolveCompanyId($user);
        $employee = $this->resolveEmployee($user, $companyId);

        if (!$companyId) {
            return response()->json([
                'success' => false,
                'message' => 'Firma bilgisi bulunamadı',
            ], 403);
        }

        $maintenanceQuery = MaintenanceSchedule::where('id', $maintenanceScheduleId)
            ->where(function ($query) use ($companyId) {
                $query->where('company_id', $companyId)
                    ->orWhereHas('building', fn ($buildingQuery) => $buildingQuery->where('company_id', $companyId));
            })
            ->with(['building', 'assignedEmployee', 'arrivalCheck', 'departureCheck']);

        if ($user->hasRole('employee')) {
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personel kaydı bulunamadı',
                ], 403);
            }

            $maintenanceQuery->where('assigned_employee_id', $employee->id);
        }

        $maintenanceSchedule = $maintenanceQuery->firstOrFail();

        $locationCheckService = new LocationCheckService();
        $summary = $locationCheckService->getLocationCheckSummary($maintenanceSchedule);

        // Get employee locations for this maintenance
        $employeeLocations = EmployeeLocation::where('maintenance_schedule_id', $maintenanceScheduleId)
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'coordinates' => [
                        'lat' => (float) $location->latitude,
                        'lng' => (float) $location->longitude,
                    ],
                    'accuracy' => $location->accuracy,
                    'speed' => $location->speed,
                    'heading' => $location->heading,
                    'recorded_at' => $location->recorded_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'maintenance' => [
                    'id' => $maintenanceSchedule->id,
                    'building' => [
                        'id' => $maintenanceSchedule->building->id,
                        'name' => $maintenanceSchedule->building->name,
                        'address' => $maintenanceSchedule->building->address,
                        'coordinates' => [
                            'lat' => (float) $maintenanceSchedule->building->latitude,
                            'lng' => (float) $maintenanceSchedule->building->longitude,
                        ],
                    ],
                    'employee' => [
                        'id' => $maintenanceSchedule->assignedEmployee->id,
                        'name' => $maintenanceSchedule->assignedEmployee->full_name,
                    ],
                    'scheduled_time' => Carbon::parse($maintenanceSchedule->scheduled_date->format('Y-m-d') . ' ' . $maintenanceSchedule->scheduled_time)->format('Y-m-d H:i:s'),
                    'estimated_duration' => $maintenanceSchedule->estimated_duration,
                ],
                'location_checks' => $summary,
                'employee_locations' => $employeeLocations,
            ],
        ]);
    }

    /**
     * Manually trigger location check creation for a maintenance schedule
     */
    public function createLocationChecks(Request $request, $maintenanceScheduleId)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $maintenanceSchedule = MaintenanceSchedule::where('id', $maintenanceScheduleId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $locationCheckService = new LocationCheckService();
        $locationCheckService->createLocationChecks($maintenanceSchedule);

        return response()->json([
            'success' => true,
            'message' => 'Konum kontrolü kayıtları oluşturuldu',
        ]);
    }

    /**
     * Update building coordinates manually or via geocoding
     */
    public function updateBuildingCoordinates(Request $request, $buildingId)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $building = Building::where('id', $buildingId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'geocode_address' => 'nullable|boolean', // If true, geocode the address
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz koordinat değerleri',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If geocode_address is true, geocode the building's address
        if ($request->has('geocode_address') && $request->geocode_address) {
            $geocodingService = new GeocodingService();
            $coordinates = $geocodingService->geocodeAddress(
                $building->address,
                $building->city,
                $building->district
            );

            if ($coordinates) {
                $building->update([
                    'latitude' => $coordinates['lat'],
                    'longitude' => $coordinates['lng'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Bina koordinatları adresten başarıyla alındı',
                    'data' => [
                        'id' => $building->id,
                        'name' => $building->name,
                        'coordinates' => [
                            'lat' => (float) $building->latitude,
                            'lng' => (float) $building->longitude,
                        ],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Adres için koordinat bulunamadı. Lütfen adresi kontrol edin veya koordinatları manuel olarak girin.',
                ], 404);
            }
        }

        // Manual coordinate update
        if ($request->has('latitude') && $request->has('longitude')) {
            $building->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bina koordinatları güncellendi',
                'data' => [
                    'id' => $building->id,
                    'name' => $building->name,
                    'coordinates' => [
                        'lat' => (float) $building->latitude,
                        'lng' => (float) $building->longitude,
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Koordinat değerleri veya geocoding parametresi gereklidir',
        ], 400);
    }

    /**
     * COST-OPTIMIZED: Geocode an address (ONLY for preview, geocoding happens on save)
     *
     * Rate limited: 10 requests per minute per user
     * This is OPTIONAL preview - actual geocoding happens on form submit (backend)
     */
    public function geocodeAddress(Request $request)
    {
        // Rate limiting: Prevent abuse (max 10 requests per minute)
        $rateLimitKey = 'geocode_preview:' . Auth::id();
        $rateLimitCount = Cache::get($rateLimitKey, 0);

        if ($rateLimitCount >= 10) {
            return response()->json([
                'success' => false,
                'message' => 'Çok fazla istek. Lütfen bir dakika bekleyin. Koordinatlar form kaydedilirken otomatik alınacaktır.',
            ], 429);
        }

        Cache::put($rateLimitKey, $rateLimitCount + 1, now()->addMinute());

        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz adres bilgisi',
                'errors' => $validator->errors(),
            ], 422);
        }

        // COST-OPTIMIZED: Check cache first (no API call if cached)
        $geocodingService = new GeocodingService();
        $coordinates = $geocodingService->geocodeAddress(
            $request->address,
            $request->city,
            $request->district
        );

        if ($coordinates) {
            Log::info('Geocode preview (will be cached for form submit)', [
                'user_id' => Auth::id(),
                'address' => $request->address
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Koordinatlar önizleme için alındı (cache\'lendi, form kaydedilirken tekrar alınmayacak)',
                'data' => [
                    'coordinates' => [
                        'lat' => $coordinates['lat'],
                        'lng' => $coordinates['lng'],
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Koordinat bulunamadı. Koordinatlar form kaydedilirken tekrar denenilecek veya haritadan seçebilirsiniz.',
        ], 404);
    }

    /**
     * Get status label in Turkish
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Beklemede',
            'on_time' => 'Zamanında',
            'late' => 'Geç',
            'early' => 'Erken',
            'missed' => 'Kaçırıldı',
        ];

        return $labels[$status] ?? $status;
    }
}
