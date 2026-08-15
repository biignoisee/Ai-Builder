<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => 'doctor',
            ]),
            'dni' => fake()->unique()->numerify('########'),
            'nombres' => fake()->name(),
            'ubicacion' => fake()->city(),
            'speciality' => 'Oftalmología',
            'disponibilidad' => [
                'lunes' => ['08:00-12:00', '14:00-18:00'],
                'martes' => ['08:00-12:00'],
                'miercoles' => ['14:00-18:00'],
                'jueves' => ['08:00-12:00'],
                'viernes' => ['08:00-13:00'],
                'sabado' => ['08:00-13:00'],
            ],
        ];
    }
}
