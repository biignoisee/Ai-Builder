<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\ConsultationType;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::query()->inRandomOrder()->value('id')
                ?? Doctor::factory(),
            'patient_id' => Patient::query()->inRandomOrder()->value('id')
                ?? Patient::factory(),
            'appointment_at' => fake()->dateTimeBetween('now', '+7 days'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed']),
            'consultation_type_id' => ConsultationType::query()->inRandomOrder()->value('id'),
            'ai_notes' => null,
        ];
    }
}
