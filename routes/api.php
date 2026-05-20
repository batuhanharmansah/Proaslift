<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==================== MOBILE API ROUTES ====================
// Mobile API routes are included here
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\IssueReportController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\LocationMapController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Dashboard routes
    Route::get('/dashboard/stats', [CompanyController::class, 'getDashboardStats']);

    // Employee routes
    Route::prefix('employee')->group(function () {
        Route::get('/assignments', [EmployeeController::class, 'getAssignments']);
        Route::get('/assignments/{id}', [EmployeeController::class, 'getAssignment']);
        Route::post('/assignments/{id}/start', [EmployeeController::class, 'startWork']);
        Route::post('/assignments/{id}/complete', [EmployeeController::class, 'completeWork']);
        Route::post('/forms/submit', [EmployeeController::class, 'submitForm']);
        Route::post('/location/update', [EmployeeController::class, 'updateLocation']);
        Route::get('/materials', [EmployeeController::class, 'getMaterials']);
    });

    // Company admin routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [CompanyController::class, 'getEmployees']);
        Route::get('/{id}', [CompanyController::class, 'getEmployee']);
        Route::post('/', [CompanyController::class, 'createEmployee']);
        Route::put('/{id}', [CompanyController::class, 'updateEmployee']);
    });

    Route::prefix('buildings')->group(function () {
        Route::get('/', [BuildingController::class, 'index']);
        Route::get('/{id}', [BuildingController::class, 'show']);
        Route::post('/', [BuildingController::class, 'store']);
        Route::put('/{id}', [BuildingController::class, 'update']);
    });

    Route::prefix('maintenance')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index']);
        Route::get('/track', [MaintenanceController::class, 'track']);
        Route::get('/{id}', [MaintenanceController::class, 'show']);
        Route::post('/', [MaintenanceController::class, 'store']);
        Route::put('/{id}', [MaintenanceController::class, 'update']);
        Route::post('/{id}/assign', [MaintenanceController::class, 'assignEmployee']);
        Route::post('/{id}/report', [MaintenanceController::class, 'storeReport']);
    });

    Route::prefix('issue-reports')->group(function () {
        Route::get('/', [IssueReportController::class, 'index']);
        Route::get('/{id}', [IssueReportController::class, 'show']);
        Route::post('/', [IssueReportController::class, 'store']);
        Route::put('/{id}', [IssueReportController::class, 'update']);
        Route::post('/{id}/assign-employee', [IssueReportController::class, 'assignEmployee']);
    });

    Route::prefix('financial')->group(function () {
        Route::get('/transactions', [FinancialController::class, 'getTransactions']);
        Route::post('/transactions', [FinancialController::class, 'createTransaction']);
        Route::get('/stats', [FinancialController::class, 'getStats']);
    });

    Route::prefix('company')->group(function () {
        Route::get('/profile', [CompanyController::class, 'getProfile']);
        Route::put('/profile', [CompanyController::class, 'updateProfile']);
    });

    // Report routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('/{id}', [ReportController::class, 'show']);
        Route::post('/', [ReportController::class, 'store']);
        Route::put('/{id}', [ReportController::class, 'update']);
        Route::delete('/{id}', [ReportController::class, 'destroy']);
    });

    // Location map routes (Admin only - requires company_admin role)
    Route::middleware('role:company_admin')->prefix('location-map')->group(function () {
        Route::get('/data', [LocationMapController::class, 'getMapData']);
        Route::get('/maintenance/{maintenanceScheduleId}/checks', [LocationMapController::class, 'getLocationChecks']);
        Route::post('/maintenance/{maintenanceScheduleId}/create-checks', [LocationMapController::class, 'createLocationChecks']);
        Route::post('/building/{buildingId}/update-coordinates', [LocationMapController::class, 'updateBuildingCoordinates']);
    });
});

