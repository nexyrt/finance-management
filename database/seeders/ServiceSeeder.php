<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceClient;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Create services categorized by type with realistic Rupiah pricing
        $services = [
            // Perizinan Services
            ['name' => 'Izin Usaha (NIB)', 'price' => 3500000, 'type' => 'Perizinan'],
            ['name' => 'Izin Edar Produk', 'price' => 8500000, 'type' => 'Perizinan'],
            ['name' => 'Pendaftaran NPWP Badan', 'price' => 1500000, 'type' => 'Perizinan'],
            ['name' => 'Sertifikasi Halal', 'price' => 6500000, 'type' => 'Perizinan'],
            ['name' => 'Perizinan Industri', 'price' => 7500000, 'type' => 'Perizinan'],
            
            // Administrasi Perpajakan Services
            ['name' => 'SPT Tahunan Badan', 'price' => 4500000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'SPT Tahunan Pribadi', 'price' => 1200000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'Konsultasi Pajak Bulanan', 'price' => 3000000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'Restitusi Pajak', 'price' => 10000000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'Pendampingan Audit Pajak', 'price' => 15000000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'Tax Planning', 'price' => 8000000, 'type' => 'Administrasi Perpajakan'],
            ['name' => 'Pembuatan Faktur Pajak', 'price' => 1000000, 'type' => 'Administrasi Perpajakan'],
            
            // Digital Marketing Services
            ['name' => 'Pembuatan Website Perusahaan', 'price' => 12000000, 'type' => 'Digital Marketing'],
            ['name' => 'Pengelolaan Media Sosial (Bulanan)', 'price' => 5000000, 'type' => 'Digital Marketing'],
            ['name' => 'Periklanan Google Ads', 'price' => 4500000, 'type' => 'Digital Marketing'],
            ['name' => 'SEO Optimization', 'price' => 6000000, 'type' => 'Digital Marketing'],
            
            // Sistem Digital Services
            ['name' => 'Implementasi Sistem Akuntansi', 'price' => 20000000, 'type' => 'Sistem Digital'],
            ['name' => 'Sistem Pelaporan Pajak Digital', 'price' => 9500000, 'type' => 'Sistem Digital'],
            ['name' => 'Integrasi E-Faktur', 'price' => 7500000, 'type' => 'Sistem Digital'],
            ['name' => 'Training Penggunaan Aplikasi Perpajakan', 'price' => 4000000, 'type' => 'Sistem Digital'],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
        
        // Create some service client records for seeding
        ServiceClient::factory()->count(40)->create();
    }
}
