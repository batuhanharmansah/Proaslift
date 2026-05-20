<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Employee;

class AuthController extends Controller
{
        public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Web'deki gibi Auth::attempt kullan
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Employee bilgilerini al
            $employee = Employee::where('email', $user->email)->first();

            // Eğer employee bulunamazsa, user_id ile dene
            if (!$employee) {
                $employee = Employee::where('user_id', $user->id)->first();
            }

            if (!$employee) {
                // Employee yoksa User bilgilerini kullan
                $token = $user->createToken('mobile-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'first_name' => $user->name,
                            'last_name' => '',
                            'email' => $user->email,
                            'phone' => '',
                            'position' => 'personel',
                            'role' => 'employee',
                            'is_active' => true,
                        ],
                        'token' => $token,
                    ],
                    'message' => 'Giriş başarılı',
                ]);
            }

            $token = $user->createToken('mobile-token')->plainTextToken;

            // Position'a göre role belirle
            $role = ($employee->position === 'admin' || $employee->position === 'yonetici' || $employee->email === 'admin@asd.com') ? 'company_admin' : 'employee';

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $employee->id,
                        'first_name' => $employee->first_name,
                        'last_name' => $employee->last_name,
                        'email' => $employee->email,
                        'phone' => $employee->phone,
                        'position' => $employee->position,
                        'role' => $role,
                        'is_active' => $employee->is_active,
                    ],
                    'token' => $token,
                ],
                'message' => 'Giriş başarılı',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Email veya şifre hatalı.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı',
        ]);
    }
}
