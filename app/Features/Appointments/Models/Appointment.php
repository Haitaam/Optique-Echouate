<?php

namespace App\Features\Appointments\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'service_type',
        'appointment_date', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
        ];
    }
}
