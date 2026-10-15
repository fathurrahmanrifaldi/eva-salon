<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\BusinessHour;
use App\Models\SalonProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Profil Salon (PRD 6.1 & DESIGN.md 1)
        SalonProfile::create([
            'name' => 'Eva Salon',
            'description' => 'Perawatan rambut, wajah, tubuh, dan kuku yang bersih, hangat, dan terjangkau di Bekasi Timur.',
            'address' => 'Perumnas 3 Ruko No.3, RT.004/RW.014, Aren Jaya, Kec. Bekasi Timur, Kota Bekasi, Jawa Barat 17111',
            'phone' => '+6281234567890',
            'wa_number' => '+6281234567890',
            'operating_note' => 'Buka setiap hari 09.00 – 20.30 WIB',
            'cancel_limit_minutes' => 30,
            'hold_default_minutes' => 30,
            'hold_max_minutes' => 120,
            'pending_alert_hours' => 24,
            'require_payment_to_complete' => false,
        ]);

        // 2. Jam Operasional (DESIGN.md 10: Setiap hari 09.00 - 20.30)
        foreach (range(0, 6) as $day) {
            BusinessHour::create([
                'day_of_week' => $day,
                'open_time' => '09:00:00',
                'close_time' => '20:30:00',
                'is_closed' => false,
            ]);
        }

        // 3. Pengguna (Owner & Kasir)
        User::create([
            'name' => 'Owner Eva Salon',
            'email' => 'owner@evasalon.test',
            'password' => Hash::make('murahmeriah'),
            'role' => Role::Owner,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        User::create([
            'name' => 'Kasir Eva Salon',
            'email' => 'kasir@evasalon.test',
            'password' => Hash::make('password'),
            'role' => Role::Kasir,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        // 4. Kategori & Layanan (DESIGN.md bagian 10)
        $categoriesData = [
            [
                'name' => 'Terapi & Refleksi',
                'sort_order' => 1,
                'services' => [
                    ['name' => 'Refleksi Seluruh Tubuh', 'price' => 80000, 'duration' => 60, 'buffer' => 10, 'featured' => true],
                    ['name' => 'Kop Masuk Angin', 'price' => 40000, 'duration' => 30, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Kerik / Kerok', 'price' => 40000, 'duration' => 30, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Bekam', 'price' => 70000, 'duration' => 45, 'buffer' => 10, 'featured' => false],
                ],
            ],
            [
                'name' => 'Kuku & Rias Wajah',
                'sort_order' => 2,
                'services' => [
                    ['name' => 'Nail Art / Gel', 'price' => 150000, 'duration' => 90, 'buffer' => 10, 'featured' => true],
                    ['name' => 'Manicure', 'price' => 85000, 'duration' => 45, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Pedicure', 'price' => 90000, 'duration' => 60, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Make Up', 'price' => 150000, 'price_max' => 250000, 'duration' => 90, 'buffer' => 15, 'featured' => false],
                    ['name' => 'Lash Lift', 'price' => 70000, 'duration' => 45, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Eyelash Extension', 'price' => 150000, 'price_max' => 250000, 'duration' => 90, 'buffer' => 15, 'featured' => true],
                ],
            ],
            [
                'name' => 'Perawatan Wajah & Tubuh',
                'sort_order' => 3,
                'services' => [
                    ['name' => 'Totok Wajah', 'price' => 40000, 'price_max' => 85000, 'duration' => 45, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Terapi Telinga', 'price' => 60000, 'duration' => 30, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Slimming Body', 'price' => 100000, 'duration' => 60, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Whitening Ketiak', 'price' => 60000, 'duration' => 30, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Pengencangan Payudara', 'price' => 100000, 'duration' => 60, 'buffer' => 10, 'featured' => false, 'sensitive' => true],
                    ['name' => 'Spa Kewanitaan', 'price' => 60000, 'duration' => 45, 'buffer' => 10, 'featured' => false, 'sensitive' => true],
                    ['name' => 'Waxing', 'price' => 50000, 'price_max' => 300000, 'duration' => 45, 'buffer' => 10, 'featured' => false],
                    ['name' => 'Massage Full Body', 'price' => 80000, 'duration' => 60, 'buffer' => 10, 'featured' => true],
                ],
            ],
            [
                'name' => 'Paket Khusus',
                'sort_order' => 4,
                'services' => [
                    [
                        'name' => 'Paket Lulur Susu',
                        'price' => 200000,
                        'duration' => 120,
                        'buffer' => 15,
                        'featured' => true,
                        'description' => "Scrub lulur\nMandi susu\nMassage susu\nSteam\nMasker susu",
                    ],
                    [
                        'name' => 'Paket Lulur Susu + Bleaching',
                        'price' => 275000,
                        'duration' => 150,
                        'buffer' => 15,
                        'featured' => true,
                        'description' => "Scrub lulur\nMasker\nMandi susu\nSteam\nMassage\nBleaching",
                    ],
                    [
                        'name' => 'Paket Bleaching',
                        'price' => 200000,
                        'duration' => 90,
                        'buffer' => 15,
                        'featured' => false,
                        'description' => "Scrub lulur\nMasker\nMandi susu\nBleaching",
                    ],
                ],
            ],
        ];

        $allServices = collect();

        foreach ($categoriesData as $cData) {
            $cat = ServiceCategory::create([
                'name' => $cData['name'],
                'sort_order' => $cData['sort_order'],
            ]);

            foreach ($cData['services'] as $idx => $sData) {
                $service = Service::create([
                    'category_id' => $cat->id,
                    'name' => $sData['name'],
                    'description' => $sData['description'] ?? null,
                    'price' => $sData['price'],
                    'price_max' => $sData['price_max'] ?? null,
                    'duration_minutes' => $sData['duration'],
                    'buffer_minutes' => $sData['buffer'] ?? 10,
                    'is_available' => true,
                    'is_featured' => $sData['featured'] ?? false,
                    'is_sensitive' => $sData['sensitive'] ?? false,
                    'sort_order' => $idx + 1,
                ]);

                $allServices->push($service);
            }
        }

        // 5. 20 Staf Dummy (PRD 1 & agent.md 11)
        $staffNames = [
            ['name' => 'Rina Marlina', 'gender' => 'female', 'position' => 'Senior Kapster'],
            ['name' => 'Sari Handayani', 'gender' => 'female', 'position' => 'Terapis Spa'],
            ['name' => 'Dewi Anggraini', 'gender' => 'female', 'position' => 'Nail & Make Up Artist'],
            ['name' => 'Dimas Prasetyo', 'gender' => 'male', 'position' => 'Terapis Refleksi'],
        ];

        foreach ($staffNames as $idx => $s) {
            $staff = Staff::create([
                'name' => $s['name'],
                'position' => $s['position'],
                'gender' => $s['gender'],
                'phone' => '+62812'.str_pad((string) ($idx + 1000), 8, '0', STR_PAD_LEFT),
                'is_active' => true,
                'sort_order' => $idx + 1,
            ]);

            // Jadwal mingguan: Semua buka 09:00 - 20:30
            foreach (range(0, 6) as $day) {
                // Beri libur bergilir (misal staf libur pada hari (id % 7))
                if ($day === ($staff->id % 7)) {
                    continue;
                }
                $staff->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '20:30:00',
                ]);
            }

            // Tetapkan kemampuan layanan:
            // Staf perempuan mendapat layanan wanita/sensitif, pria mendapat refleksi/bekam/kop.
            $serviceIds = $allServices->filter(function ($srv) use ($s) {
                if ($srv->is_sensitive && $s['gender'] !== 'female') {
                    return false;
                }
                return true;
            })->pluck('id');

            $staff->services()->attach($serviceIds);
        }
    }
}
