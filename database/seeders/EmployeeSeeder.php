<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'first_name' => 'Ahmet',
                'last_name' => 'Yılmaz',
                'phone' => '0532 123 45 67',
                'email' => 'ahmet.yilmaz@harmansah.com',
                'address' => 'Kadıköy, İstanbul',
                'position' => 'usta',
                'salary' => 18000.00,
                'hire_date' => '2020-01-15',
                'is_active' => true,
                'notes' => 'Deneyimli usta, asansör montajında uzman.'
            ],
            [
                'first_name' => 'Mehmet',
                'last_name' => 'Demir',
                'phone' => '0533 234 56 78',
                'email' => 'mehmet.demir@harmansah.com',
                'address' => 'Şişli, İstanbul',
                'position' => 'teknisyen',
                'salary' => 15000.00,
                'hire_date' => '2021-03-10',
                'is_active' => true,
                'notes' => 'Elektronik sistemlerde uzman teknisyen.'
            ],
            [
                'first_name' => 'Ayşe',
                'last_name' => 'Kaya',
                'phone' => '0534 345 67 89',
                'email' => 'ayse.kaya@harmansah.com',
                'address' => 'Beylikdüzü, İstanbul',
                'position' => 'muhendis',
                'salary' => 25000.00,
                'hire_date' => '2019-09-01',
                'is_active' => true,
                'notes' => 'Makine mühendisi, proje yönetimi deneyimi var.'
            ],
            [
                'first_name' => 'Fatma',
                'last_name' => 'Özkan',
                'phone' => '0535 456 78 90',
                'email' => 'fatma.ozkan@harmansah.com',
                'address' => 'Ümraniye, İstanbul',
                'position' => 'muhasebe',
                'salary' => 16000.00,
                'hire_date' => '2020-06-01',
                'is_active' => true,
                'notes' => 'Mali işler ve bordro uzmanı.'
            ],
            [
                'first_name' => 'Can',
                'last_name' => 'Arslan',
                'phone' => '0536 567 89 01',
                'email' => 'can.arslan@harmansah.com',
                'address' => 'Maltepe, İstanbul',
                'position' => 'teknisyen',
                'salary' => 14500.00,
                'hire_date' => '2022-01-15',
                'is_active' => true,
                'notes' => 'Genç ve hevesli teknisyen.'
            ],
            [
                'first_name' => 'Emre',
                'last_name' => 'Çelik',
                'phone' => '0537 678 90 12',
                'email' => 'emre.celik@harmansah.com',
                'address' => 'Pendik, İstanbul',
                'position' => 'yonetici',
                'salary' => 30000.00,
                'hire_date' => '2018-05-01',
                'is_active' => true,
                'notes' => 'Saha operasyon yöneticisi.'
            ]
        ];

        foreach ($employees as $employee) {
            \App\Models\Employee::create($employee);
        }
    }
}
