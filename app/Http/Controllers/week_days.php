<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Week_day;
use App\Models\Attendance;
use App\Models\Employee;
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
        
         $rawData = $request->input('data');
         $type = $request->input('type');
    
         $elements = explode(',', $rawData); 
         
         
         if ($type === 'doctor') {
            
             if (count($elements) % 2 !== 0) {
             return response()->json(['error' => 'Data is not in valid pairs'], 400);
            }
            
            for ($i = 0; $i < count($elements); $i += 4) {
          
                $weekDay1 = new Week_day();
                $weekDay1->day = $elements[$i];
                $weekDay1->date = $elements[$i + 1];
                $weekDay1->doctor_id = $id;
                $weekDay1->save();
    
                
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
        if ($type === 'employee'){
            if (count($elements) == 0) {
                return response()->json(['error' => 'days empty'], 400);
            }
       
            for ($i=0 ; $i < count($elements); $i++) { 
                
                $e_weekdays = new Week_day;
                $e_weekdays->day = $elements[$i];
                $e_weekdays->emplyee_id = $id;
                $e_weekdays->save();
                
    
            }
            return response()->json(['message' => 'days inserted succssfully'], 200);
        }
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

        $doctorweekday = Week_day::where('doctor_id', $id)->all();
        $employeeweekday = Week_day::where('emplyee_id', $id)->all();

        if ($doctorweekday->exists() || $employeeweekday->exists()) {
            if ($doctorweekday->exists()) {
                $doctorweekday->delete();
            }
            if ($employeeweekday->exists()) {
                $employeeweekday->delete();
            }

            return $this->create($request, $id);
        }
    }

    return response()->json(['message' => 'Unauthorized or no weekdays found'], 403);
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
 
 public function calculateAllWorkingDaysForYear(Request $request)
 {
     $year = $request->input('year', now()->year);

     $dayMapping = [
         'الأحد' => 'Sunday',
         'الاثنين' => 'Monday',
         'الثلاثاء' => 'Tuesday',
         'الأربعاء' => 'Wednesday',
         'الخميس' => 'Thursday',
         'الجمعة' => 'Friday',
         'السبت' => 'Saturday',
     ];

     $results = [];

     // Calculate for employees
     $employees = Employee::all();
     foreach ($employees as $employee) {
         $workingDays = Week_day::where('emplyee_id', $employee->id)
             ->pluck('day')
             ->toArray();

         $yearlyWorkingDays = $this->countYearlyWorkingDays($year, $workingDays, $dayMapping);

         $results['employees'][] = [
             'id' => $employee->id,
             'num_working_days' => $yearlyWorkingDays
         ];
     }

     // Calculate for doctors
     $doctors = Doctor::all();
     foreach ($doctors as $doctor) {
         $workingDays = Week_day::where('doctor_id', $doctor->id)
             ->pluck('day')
             ->toArray();

         $yearlyWorkingDays = $this->countYearlyWorkingDays($year, $workingDays, $dayMapping);

         $results['doctors'][] = [
             'id' => $doctor->id,
             'num_working_days' => $yearlyWorkingDays
         ];
     }

     return response()->json([
         'year' => $year,
         'results' => $results
     ]);
 }

 private function countYearlyWorkingDays($year, $workingDays, $dayMapping)
 {
     $englishWorkingDays = array_map(function($day) use ($dayMapping) {
         return $dayMapping[$day] ?? $day;
     }, $workingDays);

     $yearlyWorkingDays = [];

     for ($month = 1; $month <= 12; $month++) {
         $date = Carbon::create($year, $month, 1);
         $daysInMonth = $date->daysInMonth;
         $workingDayCount = 0;

         for ($day = 1; $day <= $daysInMonth; $day++) {
             $currentDay = $date->format('l'); // Get the day name
             if (in_array($currentDay, $englishWorkingDays)) {
                 $workingDayCount++;
             }
             $date->addDay();
         }

         $yearlyWorkingDays[$month] = $workingDayCount;
     }

     return $yearlyWorkingDays;
 }




















}

 


 








































 






       


    
       