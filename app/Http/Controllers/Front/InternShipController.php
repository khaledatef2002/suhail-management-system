<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\InternshipRequestSentMail;
use App\Models\InternshipRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InternShipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('front.internship-apply');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_of_birth' => 'required|date',
            'country_code' => 'required|string|size:2',
            'phone_number' => ['required', 'numeric', 'unique:users,phone_number', 'phone:' . $request->input('country_code')],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200']
        ]);

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $originalExtension = $file->getClientOriginalExtension();
            $randomName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $originalExtension;
            $file->move(public_path('storage/internship_requests'), $randomName);

            $data['cv'] = 'internship_requests/' . $randomName;
        }

        $internship = InternshipRequest::create($data);

        Mail::to($internship->email)->send(new InternshipRequestSentMail($internship));

        return response()->json(['message' => __('dashboard.internship_request_sent')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
