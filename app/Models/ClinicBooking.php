<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'user_phone_no',
        'reference_no',
        'booking_date',
        'is_confirmed',
        'pet_name',
        'pet_gender',
        'pet_age',

    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($clinicBooking) {
            $clinicBooking->reference_no = Str::random(20);
        });
    }

    public function timeslot()
    {
        return $this->belongsTo(ClinicTimeslot::class, 'slot_id');
    }
}
