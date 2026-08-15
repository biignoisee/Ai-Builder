<?php

namespace Database\Seeders;

use App\Models\ConsultationType;
use Illuminate\Database\Seeder;

class ConsultationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'consultation_name' => 'Refracción (Medida de lentes)',
                'price' => 50.00,
            ],
            [
                'consultation_name' => 'Consulta Oftalmológica',
                'price' => 80.00,
            ],
            [
                'consultation_name' => 'Consulta Oculoplástica',
                'price' => 120.00,
            ],
            [
                'consultation_name' => 'Cirugía',
                'price' => 200.00,
            ],
        ];

        foreach ($types as $type) {
            ConsultationType::firstOrCreate(
                ['consultation_name' => $type['consultation_name']],
                $type
            );
        }
    }
}
