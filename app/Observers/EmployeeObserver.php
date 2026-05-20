<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        // Controller request'e _employee_temp_password koyar; yoksa geçici şifre (Controller sonra kesin güncelleyecek)
        $password = request()->get('_employee_temp_password') ?? $this->generateSecurePassword();

        // Personel için otomatik kullanıcı hesabı (User model 'password' => 'hashed' cast ile plain alır)
        $user = User::create([
            'name' => $employee->full_name,
            'email' => $employee->email,
            'password' => $password,
            'company_id' => $employee->company_id,
        ]);

        // Employee role'unu bul
        $employeeRole = Role::where('slug', 'employee')->first();

        if ($employeeRole) {
            // Kullanıcıya employee rolünü ata
            $user->userRoles()->create([
                'role_id' => $employeeRole->id,
                'company_id' => $employee->company_id,
                'is_active' => true,
            ]);
        }

        // Employee'ye user_id'yi kaydet
        $employee->update(['user_id' => $user->id]);
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        // Email değişmişse user'ı da güncelle
        if ($employee->wasChanged('email') && $employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $user->update([
                    'name' => $employee->full_name,
                    'email' => $employee->email,
                ]);
            }
        }
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // Employee silinirse user'ı da sil
        if ($employee->user_id) {
            $user = User::find($employee->user_id);
            if ($user) {
                $user->delete();
            }
        }
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }

    /**
     * Güvenli şifre oluştur
     */
    private function generateSecurePassword(): string
    {
        return Str::random(12);
    }
}
