<?php

/**
 * 📱 ENTERPRISE MOBILE API ROUTES
 * 35 yıllık yazılımcı tecrübesi ile tasarlanmış
 * RESTful API, güvenlik, performans, error handling
 *
 * ÖNEMLİ (rate limiting mimarisi):
 * Laravel'in temel `throttle:maxAttempts,decayMinutes` middleware'i, giriş yapmış
 * bir kullanıcı için sayaç anahtarını SADECE kullanıcı ID'sine göre oluşturur
 * (üçüncü bir "prefix" parametresi verilmedikçe route'tan bağımsızdır).
 * Bu yüzden her mantıksal grup (`throttle:N,1,BENZERSIZ-PREFIX` şeklinde) kendi
 * bağımsız sayacına sahip olacak şekilde AYRI bir prefix ile tanımlanmıştır.
 * Aksi halde örn. Binalar listesini görüntülemek (okuma) ile Arıza Bildirimi
 * göndermek (yazma) AYNI ortak sayacı paylaşır ve normal gezinme bile yazma
 * işlemlerini "too many attempts" (429) hatasına düşürebilir.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\DashboardController;
use App\Http\Controllers\Api\Mobile\BuildingController;
use App\Http\Controllers\Api\Mobile\EmployeeController;
use App\Http\Controllers\Api\Mobile\MaintenanceController;
use App\Http\Controllers\Api\Mobile\IssueReportController;
use App\Http\Controllers\Api\Mobile\FinancialController;
use App\Http\Controllers\Api\Mobile\NotificationController;
use App\Http\Controllers\Api\Mobile\ElevatorLabelController;
use App\Http\Controllers\Api\Mobile\MonitoringController;
use App\Http\Controllers\Api\Mobile\ProductController;
use App\Http\Controllers\Api\Mobile\ParityController;
use App\Http\Controllers\Api\LocationMapController;
use App\Http\Controllers\Api\EmployeeController as LegacyEmployeeController;

// ==================== PUBLIC ROUTES ====================
Route::prefix('mobile')->group(function () {

    // Health check (rate limit: 30/dk)
    Route::middleware('throttle:30,1,mobile-health')->get('/health', function () {
        return response()->json([
            'success' => true,
            'message' => 'Mobile API is running',
            'timestamp' => now(),
            'version' => '1.0.0',
        ]);
    });

    // Authentication routes: 20 attempts per minute (brute-force koruması, paylaşımlı ofis/saha WiFi'lerinde
    // birden fazla çalışanın aynı IP'den giriş yapabilmesi için 10'dan 20'ye çıkarıldı)
    Route::prefix('auth')->middleware('throttle:20,1,mobile-auth-login')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });
});

// ==================== PROTECTED ROUTES ====================
// Yazma (POST/PUT/PATCH/DELETE) işlemleri: 30/dk
// Okuma (GET) işlemleri: 120/dk
// Her grup kendi bağımsız throttle prefix'ine sahiptir (yukarıdaki notu okuyun).
Route::prefix('mobile')->middleware(['auth:sanctum'])->group(function () {

    // ==================== AUTH ROUTES (refresh: 60/dk, diğer yazma: 10/dk, okuma: 60/dk) ====================
    Route::prefix('auth')->group(function () {
        // Token yenileme — client tarafından otomatik tetiklenir, daha yüksek limit
        Route::middleware('throttle:60,1,mobile-auth-refresh')->group(function () {
            Route::post('/refresh', [AuthController::class, 'refresh']);
        });
        Route::middleware('throttle:10,1,mobile-auth-write')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/device-token', [AuthController::class, 'registerDeviceToken']);
            Route::delete('/device-token', [AuthController::class, 'unregisterDeviceToken']);
        });
        Route::middleware('throttle:60,1,mobile-auth-read')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
        });
    });

    // ==================== DASHBOARD ROUTES (GET: 120/dk) ====================
    Route::prefix('dashboard')->middleware('throttle:120,1,mobile-dashboard-read')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/charts', [DashboardController::class, 'getCharts']);
        Route::get('/quick-actions', [DashboardController::class, 'getQuickActions']);
    });

    // ==================== STOCK WARNINGS ROUTE (GET: 120/dk) ====================
    Route::middleware('throttle:120,1,mobile-stock-warnings-read')->get('/stock-warnings', [DashboardController::class, 'getStockWarnings']);

    // ==================== BUILDINGS ROUTES ====================
    Route::prefix('buildings')->group(function () {
        // Okuma: 120/dk
        Route::middleware('throttle:120,1,mobile-buildings-read')->group(function () {
            Route::get('/', [BuildingController::class, 'index']);
            Route::get('/statistics', [BuildingController::class, 'getStatistics']);
            Route::prefix('{id}')->group(function () {
                Route::get('/', [BuildingController::class, 'show']);
                Route::get('/contacts', [BuildingController::class, 'getContacts']);
                Route::get('/documents', [BuildingController::class, 'getDocuments']);
                Route::get('/maintenance-history', [BuildingController::class, 'getMaintenanceHistory']);
            });
        });
        // Yazma: 30/dk
        Route::middleware('throttle:30,1,mobile-buildings-write')->group(function () {
            Route::post('/', [BuildingController::class, 'store']);
            Route::put('/{id}', [BuildingController::class, 'update']);
            Route::delete('/{id}', [BuildingController::class, 'destroy']);
            Route::post('/search-qr', [BuildingController::class, 'searchByQR']);
        });
    });

    // ==================== EMPLOYEES ROUTES ====================
    Route::prefix('employees')->group(function () {
        // Okuma: 120/dk
        Route::middleware('throttle:120,1,mobile-employees-read')->group(function () {
            Route::get('/', [EmployeeController::class, 'index']);
            Route::prefix('{id}')->group(function () {
                Route::get('/', [EmployeeController::class, 'show']);
                Route::get('/performance', [EmployeeController::class, 'getPerformance']);
            });
        });
        // Yazma: 30/dk
        Route::middleware('throttle:30,1,mobile-employees-write')->group(function () {
            Route::put('/{id}', [EmployeeController::class, 'update']);
        });
    });

    // ==================== MAINTENANCE ROUTES ====================
    Route::prefix('maintenance')->group(function () {
        // Okuma: 120/dk
        Route::middleware('throttle:120,1,mobile-maintenance-read')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index']);
            Route::get('/calendar', [MaintenanceController::class, 'getCalendar']);
            Route::get('/products', [MaintenanceController::class, 'products']);
            Route::get('/checklist-items', [MaintenanceController::class, 'checklistItems']);
            Route::get('/{id}/report/download', [MaintenanceController::class, 'downloadReport']);
            Route::get('/{id}', [MaintenanceController::class, 'show']);
        });
        // Yazma: 30/dk
        Route::middleware('throttle:30,1,mobile-maintenance-write')->group(function () {
            Route::post('/', [MaintenanceController::class, 'store']);
            Route::post('/{id}/start', [MaintenanceController::class, 'start']);
            Route::put('/{id}', [MaintenanceController::class, 'update']);
            Route::post('/{id}/complete', [MaintenanceController::class, 'complete']);
            Route::post('/{id}/store-report', [MaintenanceController::class, 'storeReport']);
            Route::post('/{id}/resend-approval-sms', [MaintenanceController::class, 'resendApprovalSms']);
        });
    });

    // ==================== ISSUE REPORTS ROUTES ====================
    Route::prefix('issues')->group(function () {
        Route::middleware('throttle:120,1,mobile-issues-read')->group(function () {
            Route::get('/', [IssueReportController::class, 'index']);
            Route::get('/stats', [IssueReportController::class, 'getStats']);
            Route::get('/{id}', [IssueReportController::class, 'show']);
        });
        Route::middleware('throttle:30,1,mobile-issues-write')->group(function () {
            Route::post('/', [IssueReportController::class, 'store']);
            Route::put('/{id}/status', [IssueReportController::class, 'updateStatus']);
            Route::post('/{id}/assign', [IssueReportController::class, 'assign']);
            Route::post('/{id}/start-work', [IssueReportController::class, 'startWork']);
            Route::post('/{id}/complete', [IssueReportController::class, 'complete']);
            Route::post('/{id}/create-maintenance', [IssueReportController::class, 'createMaintenance']);
        });
    });

    // ==================== FINANCIAL ROUTES ====================
    Route::prefix('financial')->group(function () {
        // Okuma: 120/dk
        Route::middleware('throttle:120,1,mobile-financial-read')->group(function () {
            Route::get('/summary', [FinancialController::class, 'getSummary']);
            Route::get('/day-end', [FinancialController::class, 'getDayEnd']);
            Route::get('/day-end/export/excel', [FinancialController::class, 'dayEndExportExcel']);
            Route::get('/day-end/export/pdf', [FinancialController::class, 'dayEndExportPdf']);
            Route::get('/accounts', [FinancialController::class, 'getAccounts']);
            Route::get('/transactions', [FinancialController::class, 'getTransactions']);
            Route::get('/transactions/{id}', [FinancialController::class, 'getTransaction']);
            Route::get('/receivables', [FinancialController::class, 'getReceivables']);
            Route::get('/receivables/{id}', [FinancialController::class, 'getReceivable']);
            Route::get('/payables', [FinancialController::class, 'getPayables']);
            Route::get('/payables/{id}', [FinancialController::class, 'getPayable']);
            Route::get('/recurring-payments', [FinancialController::class, 'getRecurringPayments']);
        });
        // Yazma: 30/dk
        Route::middleware('throttle:30,1,mobile-financial-write')->group(function () {
            Route::post('/accounts', [FinancialController::class, 'createAccount']);
            Route::put('/accounts/{id}', [FinancialController::class, 'updateAccount']);
            Route::post('/accounts/update-balance', [FinancialController::class, 'updateAccountBalance']);
            Route::delete('/accounts/{id}', [FinancialController::class, 'deleteAccount']);
            Route::post('/transactions', [FinancialController::class, 'createTransaction']);
            Route::put('/transactions/{id}', [FinancialController::class, 'updateTransaction']);
            Route::delete('/transactions/{id}', [FinancialController::class, 'deleteTransaction']);
            Route::post('/receivables', [FinancialController::class, 'createReceivable']);
            Route::put('/receivables/{id}', [FinancialController::class, 'updateReceivable']);
            Route::post('/receivables/receive-payment', [FinancialController::class, 'receivePayment']);
            Route::delete('/receivables/{id}', [FinancialController::class, 'deleteReceivable']);
            Route::post('/payables', [FinancialController::class, 'createPayable']);
            Route::put('/payables/{id}', [FinancialController::class, 'updatePayable']);
            Route::post('/payables/make-payment', [FinancialController::class, 'makePayment']);
            Route::delete('/payables/{id}', [FinancialController::class, 'deletePayable']);
            Route::post('/recurring-payments', [FinancialController::class, 'createRecurringPayment']);
            Route::put('/recurring-payments/{id}', [FinancialController::class, 'updateRecurringPayment']);
            Route::delete('/recurring-payments/{id}', [FinancialController::class, 'deleteRecurringPayment']);
        });
    });

    // ==================== NOTIFICATIONS ROUTES ====================
    Route::prefix('notifications')->group(function () {
        // Okuma: 120/dk
        Route::middleware('throttle:120,1,mobile-notifications-read')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        });
        // Yazma: 30/dk
        Route::middleware('throttle:30,1,mobile-notifications-write')->group(function () {
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::post('/read-by-type', [NotificationController::class, 'markByTypeAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
            Route::delete('/read', [NotificationController::class, 'deleteRead']);
            Route::delete('/old', [NotificationController::class, 'deleteOld']);
        });
    });

    // ==================== REPORTS ROUTES (GET: 60/dk — ağır sorgular) ====================
    Route::prefix('reports')->middleware('throttle:60,1,mobile-reports-read')->group(function () {
        Route::get('/hub', [ParityController::class, 'reportsHub']);
        Route::get('/financial', [ParityController::class, 'reportsFinancial']);
        Route::get('/maintenance', [ParityController::class, 'reportsMaintenance']);
        Route::get('/employee', [ParityController::class, 'reportsEmployee']);
        Route::get('/summary', function (Request $request) {
            try {
                $companyId = $request->user()->company_id;

                $summary = [
                    'buildings_count' => \App\Models\Building::where('company_id', $companyId)->count(),
                    'employees_count' => \App\Models\Employee::where('company_id', $companyId)->where('is_active', true)->count(),
                    'maintenance_this_month' => \App\Models\MaintenanceSchedule::whereHas('building', function ($q) use ($companyId) {
                        $q->where('company_id', $companyId);
                    })->whereMonth('scheduled_date', now()->month)->count(),
                    'issues_this_month' => \App\Models\IssueReport::where('company_id', $companyId)
                        ->whereMonth('created_at', now()->month)->count(),
                ];

                return response()->json([
                    'success' => true,
                    'data' => $summary,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rapor özeti yüklenemedi',
                ], 500);
            }
        });
    });

    // ==================== ELEVATOR LABEL ROUTES ====================
    Route::prefix('elevator-labels')->group(function () {
        Route::middleware('throttle:120,1,mobile-elevator-labels-read')->group(function () {
            Route::get('/', [ElevatorLabelController::class, 'index']);
            Route::get('/stats', [ElevatorLabelController::class, 'stats']);
            Route::get('/building/{buildingId}/history', [ElevatorLabelController::class, 'buildingHistory']);
            Route::get('/{id}', [ElevatorLabelController::class, 'show']);
        });

        Route::middleware('throttle:30,1,mobile-elevator-labels-write')->group(function () {
            Route::post('/{id}/complete', [ElevatorLabelController::class, 'complete']);
            Route::post('/{id}/seal', [ElevatorLabelController::class, 'seal']);
            Route::post('/{id}/cancel', [ElevatorLabelController::class, 'cancel']);
        });
    });

    // ==================== PRODUCTS / DEPO ROUTES ====================
    Route::prefix('products')->group(function () {
        Route::middleware('throttle:120,1,mobile-products-read')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
        });
        Route::middleware('throttle:30,1,mobile-products-write')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('/{id}', [ProductController::class, 'update']);
            Route::delete('/{id}', [ProductController::class, 'destroy']);
        });
    });

    // ==================== WEB PARITY MODULES ====================
    Route::middleware('throttle:120,1,mobile-parity-read')->group(function () {
        Route::get('/quotations', [ParityController::class, 'quotations']);
        Route::get('/quotations/{id}', [ParityController::class, 'quotationShow']);
        Route::get('/checks', [ParityController::class, 'checks']);
        Route::get('/compliance', [ParityController::class, 'compliance']);
        Route::get('/hr-fleet', [ParityController::class, 'hrFleet']);
        Route::get('/checklist-settings', [ParityController::class, 'checklistSettings']);
        Route::get('/guide', [ParityController::class, 'guide']);
        Route::get('/route-planner', [ParityController::class, 'routePlanner']);
        Route::post('/bulk-maintenance/preview', [ParityController::class, 'bulkMaintenancePreview']);
    });
    Route::middleware('throttle:30,1,mobile-parity-write')->group(function () {
        Route::post('/checklist-settings', [ParityController::class, 'storeChecklistItem']);
        Route::delete('/checklist-settings/{id}', [ParityController::class, 'destroyChecklistItem']);
        Route::post('/bulk-maintenance', [ParityController::class, 'bulkMaintenanceStore']);
    });

    // ==================== LOCATION TRACKING ROUTES ====================
    Route::prefix('location-map')->group(function () {
        Route::middleware('throttle:120,1,mobile-location-map-read')->group(function () {
            Route::get('/data', [LocationMapController::class, 'getMapData']);
            Route::get('/maintenance/{maintenanceScheduleId}/checks', [LocationMapController::class, 'getLocationChecks']);
        });
    });

    Route::prefix('employee')->middleware('throttle:30,1,mobile-employee-location-write')->group(function () {
        Route::post('/location/update', [LegacyEmployeeController::class, 'updateLocation']);
    });

    // ==================== MONITORING ROUTES (crash/hata raporlama: 60/dk) ====================
    Route::prefix('monitoring')->middleware('throttle:60,1,mobile-monitoring-write')->group(function () {
        Route::post('/log-error', [MonitoringController::class, 'logError']);
    });
});
