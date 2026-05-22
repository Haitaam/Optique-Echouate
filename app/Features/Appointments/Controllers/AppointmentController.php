<?php

namespace App\Features\Appointments\Controllers;

use App\Features\Appointments\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('pages.appointments');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'service_type' => 'required|in:eye_exam,consultation,repair,fitting',
            'appointment_date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:500',
        ]);

        Appointment::create($data);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Appointment booked successfully! We will contact you shortly.']);
        }

        return redirect()->route('appointments')->with('success', 'Appointment booked successfully! We will contact you shortly.');
    }
}
