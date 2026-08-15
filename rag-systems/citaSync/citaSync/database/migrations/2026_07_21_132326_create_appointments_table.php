<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();

            // Datos de la cita
            $table->dateTime('appointment_at'); // fecha y hora
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed
            $table->text('ai_notes')->nullable(); // Para el resumen que genere la IA
            $table->foreignId('consultation_type_id')
                ->constrained('consultation_types')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
