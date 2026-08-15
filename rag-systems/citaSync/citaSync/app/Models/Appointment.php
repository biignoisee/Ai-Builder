<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<Appointment> */
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_at',
        'status',
        'ai_notes',
        'consultation_type_id',
    ];

    // Indicar es una fecha
    protected function casts(): array
    {
        return [
            'appointment_at' => 'datetime',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultationType(): BelongsTo
    {
        return $this->belongsTo(ConsultationType::class);
    }
}
