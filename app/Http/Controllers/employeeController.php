<?php

namespace App\Http\Controllers;
use App\Http\Controllers\week_days;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Week_day;
use Hash;
use Auth;
use Carbon\Carbon;

use Illuminate\Http\Request;

class employeeController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
             $employee = Employee::with('weekdays')->get();    
            return response()->json($employee);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
           
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|max:1000', // Description must be a string and max length of 1000 characters
                'national_id' => 'required|numeric', // National ID is required, must be numeric and exactly 10 digits
                'phone_number' => 'required|string|regex:/^\+?[0-9]{7,15}$/', // Phone number is required, should be a string, and match the regex pattern
                'fixed_salary' => 'required|numeric|min:0', // Fixed salary is required, must be numeric, and at least 0
               
            ]);

        
            $employee = new Employee;
            $employee->name = $request->name;
            $employee->national_id = $request->national_id;
            $employee->phone_number = $request->phone_number;
            $employee->description = $request->description;
            $employee->fixed_salary = $request->fixed_salary;
            
            $res = $employee->save();
            if ($res) {
                
                $rawData = $request->input('data');
           
                $elements = explode(',', $rawData); 
                
                if (count($elements) == 0) {
                    return response()->json(['error' => 'days empty'], 400);
                }
           
                for ($i=0 ; $i < count($elements); $i++) { 
                    
                    $e_weekdays = new Week_day;
                    $e_weekdays->day = $elements[$i];
                    $e_weekdays->emplyee_id = $employee->id;
                    $e_weekdays->save();
                    
                }

            $now = now();
            $year = $request->input('year', $now->year);
            $month = $request->input('month', $now->month);

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

            // Calculate and update for employees
           
            
                $workingDays = Week_day::where('emplyee_id', $employee->id)
                    ->pluck('day')
                    ->toArray();

                $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);

                // Update the employee's num_working_days
                $employee->num_working_days = $monthlyWorkingDays;
                $employee->save();

                $results['employees'][] = [
                    'id' => $employee->id,
                    'num_working_days' => $monthlyWorkingDays
                ];
            

               
        
                //   $employees = Employee::all();
    
                //   foreach ($employees as $employee) {         
                //     if (isset($employee->worked_days)) {
                //         // Convert comma-separated string to an array
                //         $workedDaysArray = array_map('trim', explode(',', $employee->worked_days));
                //         // Filter out empty values and count the number of working days
                //         $employee->num_working_days = count(array_filter($workedDaysArray));
                //     } else {
                //         $employee->num_working_days = 0;
                //     }
    
                //     // Save the updated doctor record
                //     $employee->save();
                // }      
           } 
                return response()->json(['message' => 'Employee added successfully', 'employee' => $employee]);
           
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            return response()->json([ $employee ]);
        }
        else {
            return response()->json(['message'=>'user not found']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
   
   
     public function edit(string $id, Request $request)
     {
         $admin = Auth::user();
         if ($admin && $admin->role == 'admin') {
             $employee = Employee::find($id);
             if (!$employee) {
                 return response()->json(['message' => 'Employee not found'], 404);
             }
 
             $request->validate([
                 'name' => 'required|string|max:255',
                 'description' => 'nullable|string|max:1000', // Description is optional, must be a string and max length of 1000 characters
                 'national_id' => 'required|numeric', // National ID is required, must be numeric
                 'phone_number' => 'required|string|regex:/^\+?[0-9]{7,15}$/', // Phone number is required, should be a string, and match the regex pattern
                 'fixed_salary' => 'required|numeric|min:0', // Fixed salary is required, must be numeric, and at least 0
             ]);
 
             // Update employee attributes
             $employee->name = $request->name;
             $employee->national_id = $request->national_id;
             $employee->phone_number = $request->phone_number;
             $employee->description = $request->description;
             $employee->fixed_salary = $request->fixed_salary;
             
             $res = $employee->save();
             if ($res) {
                $rawData = $request->input('data');
                $elements = explode(',', $rawData); 
                $employeeweekdays = Week_day::where('emplyee_id', $employee->id)->get();
                foreach ($employeeweekdays as $key => $employeeweekday) {
                    $employeeweekday->delete();
                }
              
                if (count($elements) == 0) {
                             return response()->json(['error' => 'days empty'], 400);
                         }
                       
                         for ($i=0 ; $i < count($elements); $i++) { 
                                
                               $e_weekdays = new Week_day;
                               $e_weekdays->day = $elements[$i];
                               $e_weekdays->emplyee_id = $employee->id;
                               $e_weekdays->save();
                                
                    
                          }
                          $now = now();
            $year = $request->input('year', $now->year);
            $month = $request->input('month', $now->month);

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

            // Calculate and update for employees
           
            
                $workingDays = Week_day::where('emplyee_id', $employee->id)
                    ->pluck('day')
                    ->toArray();

                $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);

                // Update the employee's num_working_days
                $employee->num_working_days = $monthlyWorkingDays;
                $employee->save();

                $results['employees'][] = [
                    'id' => $employee->id,
                    'num_working_days' => $monthlyWorkingDays
                ];
            

 
                 return response()->json(['message' => 'Employee updated successfully', 'employee' => $employee]);
             }
 
            
         }
 
         return response()->json(['message' => 'Unauthorized'], 403);
     }
 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
           // Find the employee by ID
           $admin = Auth::user();
           if ($admin && $admin->role == 'admin') {
           $employee = Employee::find($id);
           if($employee){
            $employee->delete();
            return response()->json(['massege'=>'employee deleted successfully']);
           }
           return response()->json([ 'message' => 'employee not found (id is wrong)']);
       }
          return response()->json(['message' => 'Unauthorized'], 403); 
    }
private function countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping)
{
    $englishWorkingDays = array_map(function($day) use ($dayMapping) {
        return $dayMapping[$day] ?? $day;
    }, $workingDays);

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

    return $workingDayCount;
}










}