<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    private $services = [
        'Perizinan' => [
            'Pengurusan SIUP',
            'Pengurusan TDP',
            'Pengurusan NPWP Perusahaan',
            'Pengurusan Izin Usaha',
        ],
        'Administrasi Perpajakan' => [
            'Konsultasi Pajak',
            'Pelaporan SPT Tahunan',
            'Pelaporan SPT Bulanan',
            'Audit Pajak',
        ],
        'Digital Marketing' => [
            'Social Media Management',
            'Google Ads Campaign',
            'SEO Optimization',
            'Content Marketing',
        ],
        'Sistem Digital' => [
            'Website Development',
            'Mobile App Development',
            'System Integration',
            'Database Design',
        ],
    ];

    public function definition(): array
    {
        $type = $this->faker->randomElement(array_keys($this->services));
        $serviceName = $this->faker->randomElement($this->services[$type]);

        return [
            'name' => $serviceName,
            'price' => $this->faker->randomFloat(2, 500000, 50000000), // 500K - 50M
            'type' => $type,
        ];
    }
}
