<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicTimeslot extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'time',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function bookings()
    {
        return $this->hasMany(ClinicBooking::class, 'slot_id');
    }
}
