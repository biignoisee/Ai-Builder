<?php

namespace Database\Factories;

use App\Models\ConsultationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsultationType>
 */
class ConsultationTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consultation_name' => fake()->randomElement([
                'Refracción (Medida de lentes)',
                'Consulta Oftalmológica',
                'Consulta Oculoplástica',
                'Cirugía',
            ]),
            'price' => fake()->randomFloat(2, 20, 100), // Precio entre 20 y 100
        ];
    }
}
