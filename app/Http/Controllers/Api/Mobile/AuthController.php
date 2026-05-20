<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * 🔐 ENTERPRISE MOBILE AUTH CONTROLLER
 * 35 yıllık yazılımcı tecrübesi ile geliştirilmiş
 * JWT token yönetimi, güvenli authentication, comprehensive error handling
 */
class AuthController extends Controller
{
    use Concerns\ResolvesMobileCompanyId;

    /**
     * 📱 Mobile Login
     * Email + Password (TC) ile giriş
     */
    public function login(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => ['required', 'string', 'min:8'],
            ], [
                'email.required' => 'E-posta adresi gerekli',
                'email.email' => 'Geçerli bir e-posta adresi girin',
                'password.required' => 'Şifre gerekli',
                'password.min' => 'Şifre en az 8 karakter olmalı',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Attempt authentication
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'E-posta veya şifre hatalı',
                ], 401);
            }

            $user = Auth::user();

            // Check if user has company access
            if (!$user->company_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma erişim yetkisi bulunamadı',
                ], 403);
            }

            // Check if company is active
            if (!$user->company || !$user->company->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firma hesabı askıya alınmış',
                ], 403);
            }

            // Get employee info if exists
            $employee = Employee::where('email', $user->email)
                ->where('company_id', $user->company_id)
                ->first();

            // Create mobile token
            $token = $user->createToken('mobile-token', ['mobile:access'])->plainTextToken;

            // Prepare user data
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
                'company' => [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'subscription_plan' => $user->company->subscription_plan,
                    'subscription_status' => $user->company->subscription_status,
                ],
                'employee' => $employee ? [
                    'id' => $employee->id,
                    'position' => $employee->position,
                    'position_label' => $employee->position_label,
                    'is_active' => $employee->is_active,
                ] : null,
                'permissions' => [], // Temporarily disabled for performance
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            // Determine if user is employee or admin
            // Admin: position === 'yonetici' or no employee record
            // Employee: has employee record and position !== 'yonetici'
            $isEmployee = false;
            if ($employee) {
                $isEmployee = !in_array($employee->position, ['yonetici', 'admin']);
            }

            // Log successful login
            \Log::info('Mobile Login Success', [
                'user_id' => $user->id,
                'email' => $user->email,
                'company_id' => $user->company_id,
                'is_employee' => $isEmployee,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Giriş başarılı',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                    'is_employee' => $isEmployee,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Login Error', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'N/A',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Giriş sırasında hata oluştu',
            ], 500);
        }
    }

    /**
     * 🔓 Mobile Logout
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Log logout
            \Log::info('Mobile Logout', [
                'user_id' => $user->id,
                'email' => $user->email,
                'company_id' => $user->company_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Çıkış başarılı',
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Logout Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Çıkış sırasında hata oluştu',
            ], 500);
        }
    }

    public function registerDeviceToken(Request $request, PushNotificationService $pushNotificationService)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:255',
            'platform' => 'nullable|string|in:ios,android',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $companyId = $this->resolveMobileCompanyId($request);
        if (!$user->company_id && $companyId) {
            $user->company_id = $companyId;
        }

        $pushNotificationService->registerDeviceToken(
            $user,
            $request->input('token'),
            $request->input('platform')
        );

        return response()->json([
            'success' => true,
            'message' => 'Cihaz bildirimi kaydedildi',
        ]);
    }

    public function unregisterDeviceToken(Request $request, PushNotificationService $pushNotificationService)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pushNotificationService->unregisterDeviceToken(
            $request->user(),
            $request->input('token')
        );

        return response()->json([
            'success' => true,
            'message' => 'Cihaz bildirimi kaldırıldı',
        ]);
    }

    /**
     * 👤 Get User Profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            $user->load(['company', 'userRoles.role']);

            $employee = Employee::where('email', $user->email)
                ->where('company_id', $user->company_id)
                ->first();

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
                'company' => $user->company ? [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'subscription_plan' => $user->company->subscription_plan,
                    'subscription_status' => $user->company->subscription_status,
                    'monthly_fee' => $user->company->monthly_fee,
                    'max_buildings' => $user->company->max_buildings,
                    'max_employees' => $user->company->max_employees,
                ] : null,
                'employee' => $employee ? [
                    'id' => $employee->id,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'position' => $employee->position,
                    'position_label' => $employee->position_label,
                    'is_active' => $employee->is_active,
                    'hire_date' => $employee->hire_date,
                ] : null,
                'permissions' => [], // Temporarily disabled for performance
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $userData,
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Profile Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Profil bilgileri alınamadı',
            ], 500);
        }
    }

    /**
     * 🔄 Refresh Token
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();

            // Revoke old token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken('mobile-token', ['mobile:access'])->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Token Refresh Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token yenilenemedi',
            ], 500);
        }
    }

    /**
     * 🔧 Update Profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user->update($request->only(['name', 'email']));

            return response()->json([
                'success' => true,
                'message' => 'Profil güncellendi',
                'data' => $user->fresh(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Profile Update Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Profil güncellenemedi',
            ], 500);
        }
    }

    /**
     * 🔑 Change Password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&\-_#])[A-Za-z\d@$!%*?&\-_#]+$/',
                    'confirmed',
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $request->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mevcut şifre hatalı',
                ], 400);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Revoke all tokens (force re-login)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Şifre başarıyla değiştirildi. Lütfen tekrar giriş yapın.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile Password Change Error', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? 'N/A',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Şifre değiştirilemedi',
            ], 500);
        }
    }

    /**
     * 🛡️ Get User Permissions
     */
    private function getUserPermissions(User $user): array
    {
        $permissions = [];

        // Get user roles and permissions
        $userRoles = $user->userRoles()->with('role')->where('is_active', true)->get();

        foreach ($userRoles as $userRole) {
            if ($userRole->role && $userRole->role->permissions) {
                // permissions zaten array ise direkt kullan, string ise decode et
                $rolePermissions = is_array($userRole->role->permissions)
                    ? $userRole->role->permissions
                    : (json_decode($userRole->role->permissions, true) ?: []);
                $permissions = array_merge($permissions, $rolePermissions);
            }
        }

        return array_unique($permissions);
    }
}
