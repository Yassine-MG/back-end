<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FreelancerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(Auth::user()->role == 'Freelancer'){
            return response()->json(['message' => 'You are already a freelancer']);
        }
           // Define validation rules for incoming data
            $rules = [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'displayed_name' => 'required|string',
                'description' => 'required|string',
                'cv' => 'required|string',
                'occupation' => 'required|string',
                'skills' => 'required|string',
                'education' => 'required|string',
                'certification' => 'required|string',
            ];

            // Validate incoming data
            $validatedData = $request->validate($rules);

            // Create a new Freelancer instance with the validated data
            $freelancer = new Freelancer($validatedData);
            
            // Set the user_id attribute of the new freelancer to the current user's ID
            if (Auth::check()) {
                $freelancer->user_id = auth()->user()->id;

            }
            // Save the new freelancer to the database
            $freelancer->save();
            $user = Auth::user();
            $user->role = 'Freelancer';
            $user->save();

            // Return a success response
            return response()->json(['message' => 'Freelancer created successfully', 'user'=>$user]);
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
