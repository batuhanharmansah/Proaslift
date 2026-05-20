<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyId = \App\Models\Company::first()->id ?? 1;
        
        // Mevcut binaları ve personelleri al
        $buildings = \App\Models\Building::where('company_id', $companyId)->get();
        $employees = \App\Models\Employee::where('company_id', $companyId)->where('is_active', true)->get();
        
        if ($buildings->isEmpty() || $employees->isEmpty()) {
            \Log::warning('BuildingSeeder veya EmployeeSeeder çalıştırılmamış. Önce onları çalıştırın.');
            return;
        }
        
        $today = Carbon::today('Europe/Istanbul');
        $maintenances = [];
        
        // Her bina için çeşitli bakımlar oluştur
        foreach ($buildings as $building) {
            $buildingId = $building->id;
            $employeeIds = $employees->pluck('id')->toArray();
            
            // Bugün yapılacak işler
            $maintenances[] = [
                'company_id' => $companyId,
                'building_id' => $buildingId,
                'assigned_employee_id' => $employeeIds[0] ?? null,
                'maintenance_type' => 'rutin_bakim',
                'scheduled_date' => $today->format('Y-m-d'),
                'scheduled_time' => '09:00',
                'priority' => 'normal',
                'status' => 'atandi',
                'description' => 'Aylık rutin bakım - asansör motor kontrolü, fren sistemi kontrolü, yağlama işlemleri',
                'notes' => 'Düzenli bakım programı kapsamında',
                'estimated_cost' => 1500.00,
                'estimated_duration' => 180,
            ];
            
            // Yarın yapılacak işler
            $maintenances[] = [
                'company_id' => $companyId,
                'building_id' => $buildingId,
                'assigned_employee_id' => $employeeIds[1] ?? null,
                'maintenance_type' => 'rutin_bakim',
                'scheduled_date' => $today->copy()->addDay()->format('Y-m-d'),
                'scheduled_time' => '10:00',
                'priority' => 'normal',
                'status' => 'planli',
                'description' => 'Haftalık kontrol - kabin temizliği, düğme kontrolü, aydınlatma kontrolü',
                'notes' => 'Haftalık rutin kontrol',
                'estimated_cost' => 800.00,
                'estimated_duration' => 90,
            ];
            
            // Geçmişte tamamlanmış işler (son 30 gün)
            for ($i = 1; $i <= 3; $i++) {
                $maintenances[] = [
                    'company_id' => $companyId,
                    'building_id' => $buildingId,
                    'assigned_employee_id' => $employeeIds[array_rand($employeeIds)] ?? null,
                    'maintenance_type' => 'rutin_bakim',
                    'scheduled_date' => $today->copy()->subDays($i * 7)->format('Y-m-d'),
                    'scheduled_time' => '09:00',
                    'priority' => 'normal',
                    'status' => 'tamamlandi',
                    'description' => 'Aylık rutin bakım tamamlandı',
                    'notes' => 'Bakım başarıyla tamamlandı',
                    'estimated_cost' => 1500.00,
                    'estimated_duration' => 180,
                ];
            }
            
            // Gelecek hafta işler
            $maintenances[] = [
                'company_id' => $companyId,
                'building_id' => $buildingId,
                'assigned_employee_id' => $employeeIds[0] ?? null,
                'maintenance_type' => 'periyodik_kontrol',
                'scheduled_date' => $today->copy()->addWeek()->format('Y-m-d'),
                'scheduled_time' => '14:00',
                'priority' => 'normal',
                'status' => 'planli',
                'description' => '6 aylık periyodik güvenlik kontrolü - TSE belgesi yenileme',
                'notes' => 'Periyodik kontrol programı',
                'estimated_cost' => 2000.00,
                'estimated_duration' => 240,
            ];
            
            // Acil işler (bugün veya yarın)
            if ($buildingId <= 2) {
                $maintenances[] = [
                    'company_id' => $companyId,
                    'building_id' => $buildingId,
                    'assigned_employee_id' => $employeeIds[1] ?? null,
                    'maintenance_type' => 'ariza_onarim',
                    'scheduled_date' => $today->copy()->addDay()->format('Y-m-d'),
                    'scheduled_time' => '15:00',
                    'priority' => 'acil',
                    'status' => 'atandi',
                    'description' => 'Asansör kapı sensörü arızası - kapı açılıp kapanmıyor',
                    'notes' => 'Acil müdahale gerekiyor',
                    'estimated_cost' => 1200.00,
                    'estimated_duration' => 120,
                ];
            }
            
            // Gecikmiş işler (geçmiş tarih, henüz tamamlanmamış)
            if ($buildingId <= 2) {
                $maintenances[] = [
                    'company_id' => $companyId,
                    'building_id' => $buildingId,
                    'assigned_employee_id' => $employeeIds[0] ?? null,
                    'maintenance_type' => 'rutin_bakim',
                    'scheduled_date' => $today->copy()->subDays(3)->format('Y-m-d'),
                    'scheduled_time' => '09:00',
                    'priority' => 'normal',
                    'status' => 'atandi',
                    'description' => 'Gecikmiş rutin bakım',
                    'notes' => 'Tarih geçmiş, henüz tamamlanmadı',
                    'estimated_cost' => 1500.00,
                    'estimated_duration' => 180,
                ];
            }
            
            // Başlanmış işler
            if ($buildingId === 1) {
                $maintenances[] = [
                    'company_id' => $companyId,
                    'building_id' => $buildingId,
                    'assigned_employee_id' => $employeeIds[0] ?? null,
                    'maintenance_type' => 'ariza_onarim',
                    'scheduled_date' => $today->format('Y-m-d'),
                    'scheduled_time' => '11:00',
                    'priority' => 'yuksek',
                    'status' => 'baslandi',
                    'description' => 'Asansör motor gürültüsü - kontrol ve onarım',
                    'notes' => 'İş başlatıldı, devam ediyor',
                    'estimated_cost' => 2000.00,
                    'estimated_duration' => 180,
                ];
            }
        }

        foreach ($maintenances as $maintenance) {
            \App\Models\MaintenanceSchedule::create($maintenance);
        }
        
        \Log::info('MaintenanceSeeder: ' . count($maintenances) . ' bakım kaydı oluşturuldu.');
    }
}
