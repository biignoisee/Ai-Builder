<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ConsultationTypeSeeder::class,
        ]);

        Doctor::factory()->count(4)->create();
        Patient::factory()->count(40)->create();
        Appointment::factory()->count(100)->create();
    }
}
