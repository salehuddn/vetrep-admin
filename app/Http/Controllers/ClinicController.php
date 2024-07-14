<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Illuminate\Http\Request;
use App\Models\ClinicBooking;
use App\Models\ClinicTimeslot;

class ClinicController extends Controller
{
    public function store(Request $request)
    {
        $clinic = Clinic::create($request->all());

        // create timeslots based on working hours
        $start = new \DateTime('10:00');
        $end = new \DateTime('18:00');
        $interval = new \DateInterval('PT1H');
        $period = new \DatePeriod($start, $interval, $end);

        foreach ($period as $time) {
            ClinicTimeslot::create([
                'clinic_id' => $clinic->id,
                'time' => $time->format('H:i'),
            ]);
        }

        return response()->json($clinic, 201);
    }

    public function updateClinic(Request $request, Clinic $clinic)
    {
        // dd($request->all(), $clinic);
        $clinic->update($request->all());
        return response()->json($clinic, 200);
    }

    public function viewBookingByDate(Request $request)
    {
        $bookings = ClinicBooking::with(['timeslot'])
            ->whereHas('timeslot', function ($query) use ($request) {
                $query->where('clinic_id', $request->clinic_id);
            })
            ->when($request->filled('phone_no'), function ($query) use ($request) {
                $query->where('user_phone_no', $request->phone_no);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->where('booking_date', $request->date);
            })
            
            ->get();

        if ($bookings->isNotEmpty()) {
            return response()->json($bookings, 200);
        }

        return response()->json("No available bookings", 200);
    }


    public function clinicAvailability(Request $request)
    {
        $clinic_id = $request->clinic_id;
        $year = $request->year;
        $month = $request->month;
        $day = $request->day;

        $clinic = Clinic::findOrFail($clinic_id);

        // Query bookings based on provided date parameters
        $bookingsQuery = ClinicBooking::whereHas('timeslot', function ($query) use ($clinic_id) {
            $query->where('clinic_id', $clinic_id);
        })
        ->whereYear('booking_date', $year)
        ->whereMonth('booking_date', $month);

        if ($request->filled('day')) {
            $bookingsQuery->whereDay('booking_date', $day);
        }

        $bookings = $bookingsQuery->get();

        // Generate availability for the specified day or month
        $availability = [];
        if ($request->filled('day')) {
            $date = sprintf('%s-%02d-%02d', $year, $month, $day);
            $dailyBookings = $bookings->where('booking_date', $date);
            $times = $this->generateTimeSlots($clinic_id, $dailyBookings);
            $availability[$date] = $times;
        } else {
            for ($day = 1; $day <= 31; $day++) {
                $date = sprintf('%s-%02d-%02d', $year, $month, $day);
                $dailyBookings = $bookings->where('booking_date', $date);
                $times = $this->generateTimeSlots($clinic_id, $dailyBookings);
                $availability[$date] = $times;
            }
        }

        return response()->json($availability, 200);
    }

    private function generateTimeSlots($clinic_id, $bookings)
    {
        $timeslots = ClinicTimeslot::where('clinic_id', $clinic_id)->get();
        $slots = [];

        foreach ($timeslots as $timeslot) {
            $isBooked = $bookings->contains('slot_id', $timeslot->id);
            $slots[] = [
                'slot_id' => $timeslot->id,
                'time' => $timeslot->time,
                'status' => $isBooked ? 'booked' : 'available'
            ];
        }

        return $slots;
    }

    public function book(Request $request)
    {
        $existingBooking = ClinicBooking::where('slot_id', $request->slot_id)
            ->where('booking_date', $request->booking_date)
            ->first();

        if ($existingBooking) {
            return response()->json(['message' => 'Time slot already booked'], 409);
        }

        // Retrieve the timeslot to get the clinic_id
        $timeslot = ClinicTimeslot::find($request->slot_id);
        if (!$timeslot) {
            return response()->json(['message' => 'Invalid slot ID'], 400);
        }

        $booking = ClinicBooking::create([
            'slot_id' => $request->slot_id,
            'user_phone_no' => $request->user_phone_no,
            'booking_date' => $request->booking_date,
            'is_confirmed' => 1,
            'pet_name' => $request->pet_name,
            'pet_gender' => $request->pet_gender,
            'pet_age' => $request->pet_age,
        ]);

        return response()->json($booking, 201);
    }


    public function updateBooking(Request $request, ClinicBooking $booking)
    {
        $booking->update($request->all());
        return response()->json($booking, 200);
    }

    public function allClinics(Request $request){

        $clinics = Clinic::all();
        return response()->json($clinics, 200);

    }

    public function show($id)
    {
        $clinic = Clinic::findOrFail($id);
        return response()->json($clinic, 200);
    }

    public function allBooking(Request $request)
    {
        $bookings = ClinicBooking::all();
        return response()->json($bookings, 200);
    }

    public function showBookings($id)
    {
        $bookings = ClinicBooking::whereHas('timeslot', function ($query) use ($id) {
            $query->where('clinic_id', $id);
        })
        ->with('timeslot')
        ->get();
        return response()->json($bookings, 200);
    }

    public function getBookingsByPhoneNumber($phoneNumber)
    {
        // Eager load the 'timeslot' relationship
        $bookings = ClinicBooking::where('user_phone_no', $phoneNumber)
                                  ->with('timeslot')  // Use the correct relationship method name
                                  ->get();

        return response()->json($bookings);
    }
}
