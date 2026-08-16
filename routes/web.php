<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\IssueReportController;
use App\Http\Controllers\BuildingDocumentController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController as SuperAdminCompanyController;
use App\Http\Controllers\SuperAdmin\PaymentController as SuperAdminPaymentController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Employee\MaintenanceController as EmployeeMaintenanceController;
use App\Http\Controllers\Employee\DetailedMaintenanceController;
use App\Http\Controllers\ElevatorLabelController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MaintenanceApprovalController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PublicQuotationController;
use App\Http\Controllers\SystemMonitorController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\BulkMaintenanceController;
use App\Http\Controllers\RoutePlannerController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\CariController;
use App\Http\Controllers\Portal\PortalAuthController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\ComplianceDocumentController;
use App\Http\Controllers\HrFleetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Ana sayfa - Landing page
Route::get('/', function () {
    return view('welcome');
});

// Yasal sayfalar (public)
Route::get('/kvkk', function () {
    return view('legal.kvkk');
})->name('kvkk');
Route::get('/kullanim-sartlari', function () {
    return view('legal.kullanim-sartlari');
})->name('kullanim-sartlari');
Route::get('/hesap-silme', function () {
    return view('legal.hesap-silme');
})->name('hesap-silme');

// Bina yöneticisi bakım onayı (public, token ile)
Route::get('/onay/{token}', [MaintenanceApprovalController::class, 'show'])
    ->name('maintenance-approval.show');
Route::post('/onay/{token}', [MaintenanceApprovalController::class, 'submit'])
    ->middleware('throttle:10,1')
    ->name('maintenance-approval.submit');

// Müşteri teklif onayı (public, token ile)
Route::get('/teklif/onay/{token}', [PublicQuotationController::class, 'show'])
    ->name('quotations.public.show');
Route::post('/teklif/onay/{token}', [PublicQuotationController::class, 'respond'])
    ->middleware('throttle:10,1')
    ->name('quotations.public.respond');

// Dil değiştirme (EN/TR)
Route::get('/locale/{locale}', [LocaleController::class, 'set'])->name('locale.set');

// Müşteri Portalı — ana auth sisteminden bağımsız, izole giriş akışı
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/giris', [PortalAuthController::class, 'showLogin'])->name('login');
    Route::post('/giris', [PortalAuthController::class, 'login'])->middleware('throttle:10,1')->name('login.submit');
    Route::post('/cikis', [PortalAuthController::class, 'logout'])->name('logout');

    Route::middleware('portal.auth')->group(function () {
        Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    });
});

// SUPER ADMIN ROUTES
Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    // Firma yönetimi
    Route::resource('companies', SuperAdminCompanyController::class);
    Route::post('companies/{company}/suspend', [SuperAdminCompanyController::class, 'suspend'])->name('companies.suspend');
    Route::post('companies/{company}/activate', [SuperAdminCompanyController::class, 'activate'])->name('companies.activate');
    Route::get('companies/{company}/details', [SuperAdminCompanyController::class, 'details'])->name('companies.details');
    Route::post('companies/clear-admin-info', [SuperAdminCompanyController::class, 'clearAdminInfo'])->name('companies.clear-admin-info');

    // Ödeme yönetimi
    Route::resource('payments', SuperAdminPaymentController::class);
    Route::post('payments/{payment}/mark-paid', [SuperAdminPaymentController::class, 'markAsPaid'])->name('payments.mark-paid');
    Route::post('payments/{payment}/mark-overdue', [SuperAdminPaymentController::class, 'markAsOverdue'])->name('payments.mark-overdue');
    Route::post('payments/bulk-create', [SuperAdminPaymentController::class, 'bulkCreate'])->name('payments.bulk-create');
});

// SİSTEM SAĞLIĞI İZLEME (sadece SYSTEM_MONITOR_EMAILS içindeki kullanıcılar)
Route::middleware(['auth', 'system.monitor'])->prefix('system-monitor')->name('system-monitor.')->group(function () {
    Route::get('/', [SystemMonitorController::class, 'index'])->name('index');
    Route::post('/import-history', [SystemMonitorController::class, 'importHistory'])->name('import-history');
    Route::post('/categorize', [SystemMonitorController::class, 'categorize'])->name('categorize');
    Route::post('/cleanup-load-test-data', [SystemMonitorController::class, 'cleanupLoadTestData'])->name('cleanup-load-test-data');
    Route::get('/{event}', [SystemMonitorController::class, 'show'])->name('show');
});

// COMPANY ADMIN DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:company_admin', 'company.active'])
    ->name('dashboard');

// Dashboard API endpoints
Route::middleware(['auth', 'role:company_admin', 'company.active'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/real-time-data', [DashboardController::class, 'getRealTimeData'])->name('real-time-data');
    Route::post('/update-widgets', [DashboardController::class, 'updateWidgets'])->name('update-widgets');
    Route::get('/export', [DashboardController::class, 'exportData'])->name('export');
    Route::post('/import', [DashboardController::class, 'importData'])->name('import');
    Route::post('/backup', [DashboardController::class, 'createBackup'])->name('backup');
    Route::post('/restore', [DashboardController::class, 'restoreBackup'])->name('restore');
});

// EMPLOYEE ROUTES
Route::middleware(['auth', 'role:employee', 'company.active'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // Bakım işleri
    Route::get('/maintenance', [EmployeeMaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/{maintenance}', [EmployeeMaintenanceController::class, 'show'])->name('maintenance.show');
    Route::post('/maintenance/{maintenance}/start', [EmployeeMaintenanceController::class, 'startWork'])->name('maintenance.start');

    // Bakım raporları
    Route::get('/maintenance/{maintenance}/report/create', [EmployeeMaintenanceController::class, 'createReport'])->name('maintenance.create-report');
    Route::post('/maintenance/{maintenance}/report', [EmployeeMaintenanceController::class, 'storeReport'])->name('maintenance.store-report');
    Route::get('/maintenance/{maintenance}/report/edit', [EmployeeMaintenanceController::class, 'editReport'])->name('maintenance.edit-report');
    Route::put('/maintenance/{maintenance}/report', [EmployeeMaintenanceController::class, 'updateReport'])->name('maintenance.update-report');

    // Detaylı bakım formu
    Route::get('/maintenance/{maintenance}/detailed-form', [DetailedMaintenanceController::class, 'show'])->name('maintenance.detailed-form');
    Route::post('/maintenance/{maintenance}/detailed-store', [DetailedMaintenanceController::class, 'store'])->name('maintenance.detailed-store');
    Route::put('/maintenance/{maintenance}/detailed-update', [DetailedMaintenanceController::class, 'update'])->name('maintenance.detailed-update');
    Route::get('/maintenance/{maintenance}/detailed-report', [DetailedMaintenanceController::class, 'show'])->name('maintenance.detailed-report');

    // Kendi profilim (maaş/pozisyon dahil değil)
    Route::get('/profile', [EmployeeController::class, 'selfProfile'])->name('profile');
    Route::patch('/profile', [EmployeeController::class, 'updateSelfProfile'])->name('profile.update');
});

// COMPANY ROUTES (Asansör firması)
Route::middleware(['auth', 'role:company_admin', 'company.active', 'company.scope'])->group(function () {
    // Personel yönetimi
    Route::resource('personeller', EmployeeController::class)->names('employees');

    // Bina yönetimi
    Route::resource('binalar', BuildingController::class)->names('buildings');
    Route::post('binalar/{building}/portal-ac', [BuildingController::class, 'enablePortal'])->name('buildings.portal.enable');
    Route::post('binalar/{building}/portal-kapat', [BuildingController::class, 'disablePortal'])->name('buildings.portal.disable');

    // Payment Receipts (specific routes first)
    Route::post('building-documents/mark-as-paid', [BuildingDocumentController::class, 'markAsPaid'])->name('building.documents.mark-as-paid');
    Route::post('building-documents/payment-receipt', [BuildingDocumentController::class, 'uploadPaymentReceipt'])->name('building.documents.upload-payment-receipt');

    // Bina belgeleri
    Route::post('building-documents', [BuildingDocumentController::class, 'store'])->name('building.documents.store');
    Route::get('building-documents/{document}/download', [BuildingDocumentController::class, 'download'])->name('building.documents.download');
    Route::delete('building-documents/{document}', [BuildingDocumentController::class, 'destroy'])->name('building.documents.destroy');
    Route::get('building-documents/{buildingId}/payment-receipts', [BuildingDocumentController::class, 'getPaymentReceipts'])->name('building.documents.get-payment-receipts');
    Route::get('building-documents/{buildingId}/payment-status/{year}/{month}', [BuildingDocumentController::class, 'checkPaymentStatus'])->name('building.documents.check-payment-status');

    // Kullanım Kılavuzu
    Route::get('kullanim-kilavuzu', [GuideController::class, 'index'])->name('guide.index');

    // Arıza bildirimi — stats ÖNCE tanımlanmalı, yoksa resource'un {issueReport} wildcard'ı yakalar
    Route::get('ariza-bildirimi/stats', [IssueReportController::class, 'getStats'])->name('issue-reports.stats');
    Route::resource('ariza-bildirimi', IssueReportController::class)->names('issue-reports');
    Route::post('ariza-bildirimi/{issueReport}/assign-employee', [IssueReportController::class, 'assignEmployee'])->name('issue-reports.assign-employee');
    Route::post('ariza-bildirimi/{issueReport}/start-work', [IssueReportController::class, 'startWork'])->name('issue-reports.start-work');
    Route::post('ariza-bildirimi/{issueReport}/create-maintenance', [IssueReportController::class, 'createMaintenanceFromIssue'])->name('issue-reports.create-maintenance');
    Route::post('ariza-bildirimi/{issueReport}/complete', [IssueReportController::class, 'complete'])->name('issue-reports.complete');

    // Bakım takibi
    Route::resource('bakim-takibi', MaintenanceController::class)->names('maintenance');
    Route::get('bakim-takibi/{maintenance}/report', [MaintenanceController::class, 'showReport'])->name('maintenance.report');
    Route::get('bakim-takibi/{maintenance}/report/download', [MaintenanceController::class, 'downloadReport'])->name('maintenance.report.download');
    Route::post('bakim-takibi/{maintenance}/resend-approval-sms', [MaintenanceController::class, 'resendApprovalSms'])->name('maintenance.resend-approval-sms');

    // Depo (eski URL yönlendirmeleri)
    Route::redirect('urun-katalogu', 'depo', 301);
    Route::redirect('urun-katalogu/toplu-stok', 'depo/toplu-stok', 301);
    Route::redirect('urun-katalogu/toplu-stok/pdf', 'depo/toplu-stok/pdf', 301);
    Route::redirect('urun-katalogu/toplu-stok/excel', 'depo/toplu-stok/excel', 301);
    Route::get('urun-katalogu/create', fn () => redirect()->route('products.create', [], 301));
    Route::get('urun-katalogu/{id}/edit', fn ($id) => redirect()->route('products.edit', ['product' => $id], 301))->whereNumber('id');
    Route::get('urun-katalogu/{id}', fn ($id) => redirect()->route('products.show', ['product' => $id], 301))->whereNumber('id');

    // Depo
    Route::get('depo/toplu-stok', [ProductController::class, 'bulkStockPage'])->name('products.bulk-stock');
    Route::post('depo/toplu-stok-guncelle', [ProductController::class, 'bulkUpdateStock'])->name('products.bulk-update-stock');
    Route::get('depo/toplu-stok/pdf', [ProductController::class, 'exportStockPdf'])->name('products.bulk-stock.export-pdf');
    Route::get('depo/toplu-stok/excel', [ProductController::class, 'exportStockExcel'])->name('products.bulk-stock.export-excel');
    Route::resource('depo', ProductController::class)->names('products')->parameters(['depo' => 'product']);

    // Finansal Yönetim (Tek Modül) — spesifik path'ler önce (404 önlemek için)
    Route::get('finansal/islemler', [FinancialController::class, 'unifiedIndex'])->name('financial.unified');
    Route::get('finansal/gun-sonu', [FinancialController::class, 'dayEnd'])->name('financial.day-end');
    Route::get('finansal/gun-sonu/excel', [FinancialController::class, 'dayEndExportExcel'])->name('financial.day-end.excel');
    Route::get('finansal/gun-sonu/pdf', [FinancialController::class, 'dayEndExportPdf'])->name('financial.day-end.pdf');
    Route::get('finansal/gecmis', [FinancialController::class, 'history'])->name('financial.history');
    Route::get('finansal/rapor', [FinancialController::class, 'report'])->name('financial.report');
    Route::get('finansal/kar-maliyet-raporu', [FinancialController::class, 'karMaliyetRaporu'])->name('financial.kar-maliyet');
    Route::get('finansal', [FinancialController::class, 'index'])->name('financial.index');
    Route::post('finansal/quick-transaction', [FinancialController::class, 'quickTransaction'])->name('financial.quick-transaction');
    Route::post('finansal/add-account', [FinancialController::class, 'addAccount'])->name('financial.add-account');
    Route::post('finansal/update-balance', [FinancialController::class, 'updateAccountBalance'])->name('financial.update-balance');

    // Eski ayrı sayfalar → birleşik sayfaya yönlendir
    Route::get('finansal/alacaklar', function () {
        return redirect('finansal/islemler?type=alacak');
    })->name('financial.receivables');
    Route::get('finansal/borclar', function () {
        return redirect('finansal/islemler?type=borc');
    })->name('financial.payables');
    Route::get('finansal/duzenli-odemeler', function () {
        return redirect('finansal/islemler?type=duzenli');
    })->name('financial.recurring-payments');

    // Alacak/Borç İşlemleri
    Route::post('finansal/add-receivable', [FinancialController::class, 'addReceivable'])->name('financial.add-receivable');
    Route::post('finansal/receive-payment', [FinancialController::class, 'receivePayment'])->name('financial.receive-payment');
    Route::post('finansal/add-payable', [FinancialController::class, 'addPayable'])->name('financial.add-payable');
    Route::post('finansal/make-payment', [FinancialController::class, 'makePayment'])->name('financial.make-payment');

    // Düzenli Ödemeler
    Route::post('finansal/add-recurring-payment', [FinancialController::class, 'addRecurringPayment'])->name('financial.add-recurring-payment');
    Route::post('finansal/process-recurring-payments', [FinancialController::class, 'processRecurringPayments'])->name('financial.process-recurring-payments');

    // Teklif Yönetimi
    Route::prefix('teklifler')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'index'])->name('index');
        Route::get('/create', [QuotationController::class, 'create'])->name('create');
        Route::post('/', [QuotationController::class, 'store'])->name('store');
        Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
        Route::post('/{quotation}/send', [QuotationController::class, 'markAsSent'])->name('send');
        Route::post('/{quotation}/cancel', [QuotationController::class, 'cancel'])->name('cancel');
        Route::post('/{quotation}/convert-to-receivable', [QuotationController::class, 'convertToReceivable'])->name('convert-to-receivable');
        Route::get('/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('pdf');
    });

    // İş takibi
    Route::get('/is-takibi', function () {
        $employees = \App\Models\Employee::where('is_active', true)->get();
        return view('maintenance.track', compact('employees'));
    })->name('maintenance.track');

    // Konum takibi haritası
    Route::get('/konum-takibi', [App\Http\Controllers\LocationMapController::class, 'index'])->name('location-map.index');
    
    // Location map API routes (for AJAX calls from web - using web middleware)
    // COST-OPTIMIZED: Rate limited geocoding endpoint
    Route::prefix('api/location-map')->middleware(['auth', 'role:company_admin', 'company.active'])->group(function () {
        Route::get('/data', [App\Http\Controllers\Api\LocationMapController::class, 'getMapData'])->name('api.location-map.data');
        Route::get('/maintenance/{maintenanceScheduleId}/checks', [App\Http\Controllers\Api\LocationMapController::class, 'getLocationChecks'])->name('api.location-map.checks');
        Route::post('/maintenance/{maintenanceScheduleId}/create-checks', [App\Http\Controllers\Api\LocationMapController::class, 'createLocationChecks'])->name('api.location-map.create-checks');
        Route::post('/building/{buildingId}/update-coordinates', [App\Http\Controllers\Api\LocationMapController::class, 'updateBuildingCoordinates'])->name('api.location-map.update-coordinates');
        // Rate limited: 10 requests per minute (preview only, actual geocoding on save)
        Route::post('/geocode', [App\Http\Controllers\Api\LocationMapController::class, 'geocodeAddress'])->middleware('throttle:10,1')->name('api.location-map.geocode');
    });

    // Raporlar
    Route::prefix('raporlar')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/finansal', [App\Http\Controllers\ReportController::class, 'financial'])->name('financial');
        Route::get('/bakim', [App\Http\Controllers\ReportController::class, 'maintenance'])->name('maintenance');
        Route::get('/personel', [App\Http\Controllers\ReportController::class, 'employee'])->name('employee');
        Route::get('/musteri', [App\Http\Controllers\ReportController::class, 'customer'])->name('customer');
        Route::get('/export', [App\Http\Controllers\ReportController::class, 'export'])->name('export');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Firma profil
    Route::get('/company-profile', [CompanyController::class, 'profile'])->name('company.profile');
    Route::patch('/company-profile', [CompanyController::class, 'updateProfile'])->name('company.profile.update');

    // Bakım kontrol listesi - firma özel maddeler
    Route::get('/settings/checklist-items', [ChecklistItemController::class, 'index'])->name('settings.checklist-items');
    Route::post('/settings/checklist-items', [ChecklistItemController::class, 'store'])->name('settings.checklist-items.store');
    Route::delete('/settings/checklist-items/{checklistItem}', [ChecklistItemController::class, 'destroy'])->name('settings.checklist-items.destroy');

    // Aylık Toplu Bakım Sihirbazı
    Route::prefix('bakim/toplu')->name('maintenance.bulk.')->group(function () {
        Route::get('/', [BulkMaintenanceController::class, 'create'])->name('create');
        Route::post('/onizle', [BulkMaintenanceController::class, 'preview'])->name('preview');
        Route::post('/olustur', [BulkMaintenanceController::class, 'store'])->name('store');
    });

    // Rota Planlayıcı
    Route::prefix('bakim/rota-planlayici')->name('maintenance.route-planner.')->group(function () {
        Route::get('/', [RoutePlannerController::class, 'index'])->name('index');
        Route::get('/binalar', [RoutePlannerController::class, 'buildings'])->name('buildings');
    });

    // Bildirim Tercihleri
    Route::get('/settings/bildirim-tercihleri', [NotificationPreferenceController::class, 'index'])->name('settings.notification-preferences');
    Route::post('/settings/bildirim-tercihleri', [NotificationPreferenceController::class, 'update'])->name('settings.notification-preferences.update');

    // Cariler (birleşik müşteri/tedarikçi/personel bakiye görünümü)
    Route::get('/cariler', [CariController::class, 'index'])->name('cariler.index');

    // Çek & Senet
    Route::prefix('cek-senet')->name('checks.')->group(function () {
        Route::get('/', [CheckController::class, 'index'])->name('index');
        Route::post('/', [CheckController::class, 'store'])->name('store');
        Route::patch('/{check}', [CheckController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{check}', [CheckController::class, 'destroy'])->name('destroy');
    });

    // DTR + Kurtarma Formu
    Route::prefix('belgeler/{type}')->name('compliance-documents.')->group(function () {
        Route::get('/', [ComplianceDocumentController::class, 'index'])->name('index');
        Route::post('/', [ComplianceDocumentController::class, 'store'])->name('store');
    });
    Route::patch('/belgeler/kayit/{complianceDocument}', [ComplianceDocumentController::class, 'updateStatus'])->name('compliance-documents.update-status');
    Route::delete('/belgeler/kayit/{complianceDocument}', [ComplianceDocumentController::class, 'destroy'])->name('compliance-documents.destroy');

    // Hakediş + Araç Takip + Devamsızlık
    Route::prefix('hakedis-arac-takip')->name('hr-fleet.')->group(function () {
        Route::get('/', [HrFleetController::class, 'index'])->name('index');
        Route::post('/hakedis', [HrFleetController::class, 'storeBonus'])->name('bonus.store');
        Route::delete('/hakedis/{bonus}', [HrFleetController::class, 'destroyBonus'])->name('bonus.destroy');
        Route::post('/arac', [HrFleetController::class, 'storeVehicle'])->name('vehicle.store');
        Route::delete('/arac/{vehicle}', [HrFleetController::class, 'destroyVehicle'])->name('vehicle.destroy');
        Route::post('/devamsizlik', [HrFleetController::class, 'storeAbsence'])->name('absence.store');
        Route::delete('/devamsizlik/{absence}', [HrFleetController::class, 'destroyAbsence'])->name('absence.destroy');
    });

    // Çalışan profil
    Route::get('/employees/{employee}/profile', [EmployeeController::class, 'profile'])->name('employees.profile');
    Route::patch('/employees/{employee}/profile', [EmployeeController::class, 'updateProfile'])->name('employees.profile.update');

    // Asansör Etiket Sistemi
    Route::prefix('elevator-labels')->name('elevator-labels.')->group(function () {
        Route::get('/', [ElevatorLabelController::class, 'index'])->name('index');
        Route::get('/create', [ElevatorLabelController::class, 'create'])->name('create');
        Route::post('/', [ElevatorLabelController::class, 'store'])->name('store');
        Route::get('/stats', [ElevatorLabelController::class, 'stats'])->name('stats');
        Route::get('/report', [ElevatorLabelController::class, 'report'])->name('report');

        Route::get('/{elevatorLabel}', [ElevatorLabelController::class, 'show'])->name('show');
        Route::get('/{elevatorLabel}/edit', [ElevatorLabelController::class, 'edit'])->name('edit');
        Route::put('/{elevatorLabel}', [ElevatorLabelController::class, 'update'])->name('update');
        Route::post('/{elevatorLabel}/complete', [ElevatorLabelController::class, 'complete'])->name('complete');
        Route::post('/{elevatorLabel}/seal', [ElevatorLabelController::class, 'seal'])->name('seal');
        Route::post('/{elevatorLabel}/cancel', [ElevatorLabelController::class, 'cancel'])->name('cancel');
        Route::get('/{elevatorLabel}/notifications', [ElevatorLabelController::class, 'notifications'])->name('notifications');
        Route::get('/{elevatorLabel}/escalations', [ElevatorLabelController::class, 'escalations'])->name('escalations');

        Route::get('/building/{building}/history', [ElevatorLabelController::class, 'history'])->name('history');
    });

});

require __DIR__.'/auth.php';
