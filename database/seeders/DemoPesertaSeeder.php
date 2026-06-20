<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoPesertaSeeder extends Seeder
{
    public function run(): void
    {
        $demoData = [
            [
                'name'       => 'Ahmad Fauzi',
                'email'      => 'ahmad@demo.com',
                'no_peserta' => 'SKD-2024-001',
                'no_hp'      => '081234567890',
                'is_active'  => true,
            ],
            [
                'name'       => 'Siti Rahayu',
                'email'      => 'siti@demo.com',
                'no_peserta' => 'SKD-2024-002',
                'no_hp'      => '082345678901',
                'is_active'  => true,
            ],
            [
                'name'       => 'Budi Santoso',
                'email'      => 'budi@demo.com',
                'no_peserta' => 'SKD-2024-003',
                'no_hp'      => '083456789012',
                'is_active'  => true,
            ],
            [
                'name'       => 'Dewi Lestari',
                'email'      => 'dewi@demo.com',
                'no_peserta' => 'SKD-2024-004',
                'no_hp'      => '084567890123',
                'is_active'  => false, // Demo: nonaktif
            ],
            [
                'name'       => 'Rizky Pratama',
                'email'      => 'rizky@demo.com',
                'no_peserta' => 'SKD-2024-005',
                'no_hp'      => '085678901234',
                'is_active'  => true,
            ],
        ];

        foreach ($demoData as $data) {
            if (!User::where('email', $data['email'])->exists()) {
                User::create(array_merge($data, [
                    'password' => Hash::make('password123'),
                    'role'     => 'peserta',
                ]));
                $this->command->info("Created peserta: {$data['name']}");
            }
        }

        $this->command->info('Demo peserta seeder complete. Total: ' . User::where('role', 'peserta')->count());
    }
}
