<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Week_day;
use App\Models\Attendance;
use Hash;
use Auth;
use Carbon\Carbon;


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

  
 public function edit(Request $request, $id)
 {
    $admin = Auth::user();
    if ($admin && $admin->role == 'admin') {
    $weekday = Week_day::find($id);
    if ($weekday) {
        $weekday->day = $request->day; 
        $weekday->date = $request->date; 
        $weekday->save();
        return response()->json(['massege'=>'weekday updated successfully']);
    }
    return response()->json([ 'message' => 'weekday not found (id is wrong)']);
}
   return response()->json(['message' => 'Unauthorized'], 403); 

 }
 public function editall(Request $request, $id)
 {
    $admin = Auth::user();
    if ($admin && $admin->role == 'admin') {
    $weekday = Week_day::where('doctor_id' , $id )->delete();//doc_id
    
    return $this->create($request, $id);

 }
}
 

 public function destroy(Request $request, $id)
 {
    
    $admin = Auth::user();
    if ($admin && $admin->role == 'admin') {
    $weekday = Week_day::find($id);
    if($weekday){
     $weekday->delete();
     return response()->json(['massege'=>'weekday deleted successfully']);
    }
    return response()->json([ 'message' => 'weekday not found (id is wrong)']);
}
   return response()->json(['message' => 'Unauthorized'], 403); 

 }

 


 







































}
 






       


    
       