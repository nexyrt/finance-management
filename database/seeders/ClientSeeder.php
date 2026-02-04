<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Seed the clients table with test data
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'PT. Teknologi Maju Indonesia',
                'type' => 'company',
                'email' => 'info@tekno-maju.co.id',
                'NPWP' => '01.234.567.8-901.000',
                'status' => 'Active',
                'ar_phone_number' => '021-55667788',
                'address' => 'Gedung Cyber, Jl. Kuningan, Jakarta Selatan',
                'person_in_charge' => 'Andi Wijaya',
            ],
            [
                'name' => 'CV. Karya Bersama',
                'type' => 'company',
                'email' => 'hrd@karyabersama.com',
                'NPWP' => '55.666.777.8-555.000',
                'status' => 'Active',
                'ar_phone_number' => '021-99887766',
                'address' => 'Jl. Gatot Subroto No. 88, Jakarta',
                'person_in_charge' => 'Dewi Lestari',
            ],
            [
                'name' => 'PT. Global Solusi Prima',
                'type' => 'company',
                'email' => 'finance@globalsolusi.id',
                'NPWP' => '11.222.333.4-111.000',
                'status' => 'Active',
                'ar_phone_number' => '021-77665544',
                'address' => 'Plaza Indonesia Lt. 15, Jakarta',
                'person_in_charge' => 'Rudi Hartono',
            ],
            [
                'name' => 'Budi Santoso',
                'type' => 'individual',
                'email' => 'budi.santoso@email.com',
                'NPWP' => '12.345.678.9-012.000',
                'status' => 'Active',
                'ar_phone_number' => '081234567890',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'type' => 'individual',
                'email' => 'siti.nurhaliza@email.com',
                'NPWP' => '98.765.432.1-098.000',
                'status' => 'Active',
                'ar_phone_number' => '082345678901',
                'address' => 'Jl. Thamrin No. 45, Jakarta Selatan',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }

        $this->command->info('✓ Seeded clients table: 5 clients (3 companies, 2 individuals)');
    }
}
