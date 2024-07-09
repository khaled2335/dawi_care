<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Week_day;
use Hash;
use Auth;
class week_days extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $weekdays = Week_day::get();
        return response()->json($weekdays);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request ,$id  )
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 

        //    $docter = Doctor::find($id);
           $week_day = new Week_day;
           $week_day->doctor_id = $id;
           $week_day->day = $request->day;
           $week_day->date = $request->date;
           $res  = $week_day->save();
           if ($res) {
            return response()->json(['message' => 'Week_day added successfully']);
           } else {
            return response()->json(['message' => 'Week_day added  failed']);
           }
 }






       


    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
