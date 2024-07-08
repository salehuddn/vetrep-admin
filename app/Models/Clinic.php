<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'phone_no',
        'address',
        'latitude',
        'longitude',
        'working_hours',
    ];

    protected function casts(): array
    {
        return [
            'working_hours' => 'array',
        ];
    }

    public function timeslots ()
    {
        return $this->hasMany(ClinicTimeslot::class);
    }
}
