<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SyncEmployeeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mevcut çalışanlar için User hesapları oluştur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mevcut çalışanlar için User hesapları oluşturuluyor...');

        // User hesabı olmayan çalışanları bul
        $employeesWithoutUsers = Employee::whereNull('user_id')
            ->orWhereDoesntHave('user')
            ->get();

        $this->info("User hesabı olmayan {$employeesWithoutUsers->count()} çalışan bulundu.");

        $created = 0;
        $errors = 0;

        foreach ($employeesWithoutUsers as $employee) {
            try {
                // Aynı email ile User var mı kontrol et
                $existingUser = User::where('email', $employee->email)->first();

                if ($existingUser) {
                    // Mevcut user'ı employee ile bağla
                    $employee->update(['user_id' => $existingUser->id]);
                    $this->line("✓ Mevcut user bağlandı: {$employee->full_name} ({$employee->email})");
                } else {
                    // Yeni user oluştur - rastgele geçici şifre
                    $tempPassword = Str::random(12);
                    $user = User::create([
                        'name' => $employee->full_name,
                        'email' => $employee->email,
                        'password' => $tempPassword,
                        'company_id' => $employee->company_id,
                        'email_verified_at' => now(),
                    ]);

                    // Employee rolü ata
                    $user->assignRole('employee', $employee->company_id);

                    // Employee'ye user_id'yi kaydet
                    $employee->update(['user_id' => $user->id]);

                    $this->line("✓ Yeni user oluşturuldu: {$employee->full_name} ({$employee->email}) - Geçici şifre: {$tempPassword}");
                    $created++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Hata: {$employee->full_name} - {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("\n--- Özet ---");
        $this->info("✓ Oluşturulan user hesabı: {$created}");
        if ($errors > 0) {
            $this->error("✗ Hata sayısı: {$errors}");
        }
        $this->info("Oluşturulan hesaplar için geçici şifreler yukarıda gösterildi. Personeli bilgilendirin.");

        return Command::SUCCESS;
    }
}
