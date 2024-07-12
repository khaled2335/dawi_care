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
  
    
 public function create(Request $request, $id)
 {
     // Get the raw string data from the request
     $rawData = $request->input('data');

     // Parse the raw data into an array
     $elements = explode(',', $rawData); // Assuming the string is comma-separated

     // Ensure the array has an even number of elements
     if (count($elements) % 2 !== 0) {
         return response()->json(['error' => 'Data is not in valid pairs'], 400);
     }

     // Insert each pair into the database
     for ($i = 0; $i < count($elements); $i += 4) {
         // Insert the first pair
         $weekDay1 = new Week_day();
         $weekDay1->day = $elements[$i];
         $weekDay1->date = $elements[$i + 1];
         $weekDay1->doctor_id = $id;
         $weekDay1->save();

         // Insert the second pair, if available
         if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
             $weekDay2 = new Week_day();
             $weekDay2->day = $elements[$i + 2];
             $weekDay2->date = $elements[$i + 3];
             $weekDay2->doctor_id = $id;
             $weekDay2->save();
         }
     }

     return response()->json(['message' => 'Data inserted successfully']);
 }

  
 public function edit(Request $request, $doctorId)
 {
      // Check if the doctor exists
      $doctor = Doctor::find($doctorId);
      if (!$doctor) {
          return response()->json(['error' => 'Doctor not found'], 404);
      }

      // Get the raw string data from the request
      $rawData = $request->input('data');

      // Parse the raw data into an array
      $elements = explode(',', $rawData); // Assuming the string is comma-separated

      // Ensure the array has the correct number of elements
      if (count($elements) % 4 !== 0) {
          return response()->json(['error' => 'Data is not in valid pairs'], 400);
      }

      // Create a temporary array to store data for updates
      $updatedRows = [];

      // Iterate over the array and prepare data for updates
      for ($i = 0; $i < count($elements); $i += 4) {
          // Extract values
          $day1 = trim($elements[$i]);
          $date1 = trim($elements[$i + 1]);
          $day2 = isset($elements[$i + 2]) ? trim($elements[$i + 2]) : null;
          $date2 = isset($elements[$i + 3]) ? trim($elements[$i + 3]) : null;

          // Prepare data for the first pair
          $updatedRows[] = [
              'day' => $day1,
              'date' => $date1,
              'doctor_id' => $doctorId,
          ];

          // Prepare data for the second pair, if available
          if ($day2 && $date2) {
              $updatedRows[] = [
                  'day' => $day2,
                  'date' => $date2,
                  'doctor_id' => $doctorId,
              ];
          }
      }

      // Retrieve existing records to update
      $existingRecords = Week_day::where('doctor_id', $doctorId)
                                 ->whereIn('day', array_column($updatedRows, 'day'))
                                 ->whereIn('date', array_column($updatedRows, 'date'))
                                 ->get()
                                 ->keyBy(function($item) {
                                     return $item->day . '|' . $item->date;
                                 });

      // Update or create records based on the prepared data
      foreach ($updatedRows as $data) {
          $key = $data['day'] . '|' . $data['date'];
          if (isset($existingRecords[$key])) {
              // Update existing record
              $weekDay = $existingRecords[$key];
              $weekDay->day = $data['day'];
              $weekDay->date = $data['date'];
              $weekDay->save();
          } else {
              // Create a new record if it doesn't exist
              Week_day::create($data);
          }
      }

      return response()->json(['message' => 'Data updated successfully']);
  }

 







































}
 






       


    
       