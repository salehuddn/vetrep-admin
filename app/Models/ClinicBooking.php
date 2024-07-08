<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'user_phone_no',
        'booking_date',
        'booking_time',
        'is_confirmed',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function bookings()
    {
        return $this->hasMany(ClinicBooking::class);
    }
}
