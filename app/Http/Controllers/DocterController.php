<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\Week_day;
use Hash;
use Auth;
use Carbon\Carbon;

class DocterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
            $doctors = Doctor::with('weekDays','clinic')->get();   
            return response()->json($doctors);
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
           
          
            // Handle profile photo upload
            $doctor_image_name = rand() . '.' .$request->profile_photo->getClientOriginalExtension(); 
            $request->profile_photo->move(public_path('/photos/doctor_photo'),$doctor_image_name);

            $union_registration_file = rand() . '.' .$request->union_registration->getClientOriginalExtension(); 
            $request->union_registration->move(public_path('/photos/union_registration_file'),$union_registration_file);

            $doctor = new Doctor;
            $doctor->name = $request->name;
            $doctor->national_id = $request->national_id;
            $doctor->phone_number = $request->phone_number;
            $doctor->profile_photo = asset('photos/doctor_photo/' . $doctor_image_name); 
            $doctor->scientific_degree = $request->scientific_degree;
            $doctor->union_registration = asset('photos/union_registration_file/' . $union_registration_file); 
            $doctor->clinic_id = $request->clinic;
            $doctor->fixed_salary = $request->fixed_salary;
            $doctor->doctor_share = $request->doctor_share;
            $res = $doctor->save();
            if ($res) {
                
                $rawData = $request->input('data');
           
                $elements = explode(',', $rawData); 
                
                    if (count($elements) % 2 !== 0) {
                    return response()->json(['error' => 'Data is not in valid pairs'], 400);
                   }
                   
                   for ($i = 0; $i < count($elements); $i += 4) {
                 
                       $weekDay1 = new Week_day();
                       $weekDay1->day = $elements[$i];
                       $weekDay1->date = $elements[$i + 1];
                       $weekDay1->doctor_id = $doctor->id;
                       $weekDay1->save();
           
                       
                       if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
                           $weekDay2 = new Week_day();
                           $weekDay2->day = $elements[$i + 2];
                           $weekDay2->date = $elements[$i + 3];
                           $weekDay2->doctor_id = $doctor->id;
                           $weekDay2->save();
                       }
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
                
                    $workingDays = Week_day::where('doctor_id', $doctor->id)
                        ->pluck('day')
                        ->toArray();
            
                    $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);
            
                    // Update the doctor's num_working_days
                    $doctor->num_working_days = $monthlyWorkingDays;
                    $doctor->save();
            
                    $results['doctors'][] = [
                        'id' => $doctor->id,
                        'num_working_days' => $monthlyWorkingDays
                    ];
                $doctor->save();

            return response()->json(['message' => 'Doctor added successfully', 'doctor' => $doctor]);
            }     
                
            }
            return response()->json(['message' => 'Unauthorized'], 403);
    }

       public function show(string $id)
    {
        $doctor = Doctor::find($id);
        if ($doctor) {
            return response()->json([ $doctor ]);
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
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found'], 404);
        }

        // $request->validate(
        //     [
        //         'name' => 'required|string|max:255',
        //         'national_id' => 'required|digits_between:10,20|unique:doctors,national_id',
        //         'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:doctors,phone_number',
        //         'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        //         'union_registration' => 'required||max:255|',
        //         'scientific_degree' => 'required|max:255',
        //         'worked_days' => 'required|integer|min:0',
        //         'specialty' => 'required',
        //         'fixed_salary' => 'required|numeric|min:0',
        //     ]
        // );

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo
            if ($doctor->profile_photo) {
                $oldPhotoPath = public_path('/photos/doctor_photo/' . basename($doctor->profile_photo));
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }

            $doctor_image_name = rand() . '.' . $request->profile_photo->getClientOriginalExtension();
            $request->profile_photo->move(public_path('/photos/doctor_photo'), $doctor_image_name);
            $doctor->profile_photo = asset('photos/doctor_photo/' . $doctor_image_name);
        }

        // Handle union registration file upload
        if ($request->hasFile('union_registration')) {
            // Delete old union registration file
            if ($doctor->union_registration) {
                $oldUnionRegPath = public_path('/photos/union_registration_file/' . basename($doctor->union_registration));
                if (file_exists($oldUnionRegPath)) {
                    unlink($oldUnionRegPath);
                }
            }

            $union_registration_file = rand() . '.' . $request->union_registration->getClientOriginalExtension();
            $request->union_registration->move(public_path('/photos/union_registration_file'), $union_registration_file);
            $doctor->union_registration = asset('photos/union_registration_file/' . $union_registration_file);
        }

        $doctor->name = $request->name;
        $doctor->national_id = $request->national_id;
        $doctor->phone_number = $request->phone_number;
        $doctor->scientific_degree = $request->scientific_degree;
        $doctor->fixed_salary = $request->fixed_salary;
        $doctor->clinic_id = $request->clinic;
        $doctor->doctor_share = $request->doctor_share;
        $res = $doctor->save();
        if ($res) {
            $rawData = $request->input('data');
            $elements = explode(',', $rawData); 
    
            
                $doctorweekday = Week_day::where('doctor_id', $doctor->id)->get();
                foreach ($doctorweekday as $key => $dweekday) {
                     $dweekday->delete();
                }
               
               
                 if (count($elements) % 2 !== 0) {
                 return response()->json(['error' => 'Data is not in valid pairs'], 400);
                }
                
                for ($i = 0; $i < count($elements); $i += 4) {
              
                    $weekDay1 = new Week_day();
                    $weekDay1->day = $elements[$i];
                    $weekDay1->date = $elements[$i + 1];
                    $weekDay1->doctor_id = $doctor->id;
                    $weekDay1->save();
        
                    
                    if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
                        $weekDay2 = new Week_day();
                        $weekDay2->day = $elements[$i + 2];
                        $weekDay2->date = $elements[$i + 3];
                        $weekDay2->doctor_id = $doctor->id;
                        $weekDay2->save();
                    }
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
         
                 $workingDays = Week_day::where('doctor_id', $doctor->id)
                     ->pluck('day')
                     ->toArray();
         
                 $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);
               
                 // Update the doctor's num_working_days
                 $doctor->num_working_days = $monthlyWorkingDays;
                 $doctor->save();
         
                 $results['doctors'][] = [
                     'id' => $doctor->id,
                     'num_working_days' => $monthlyWorkingDays
                 ];
             
             
            return response()->json(['message' => 'Doctor updated successfully', 'doctor' => $doctor]);
        } else {
            return response()->json(['message' => 'Update failed']);
        }
    }
    return response()->json(['message' => 'Unauthorized'], 403);
}


    
    public function destroy(string $id)
    {
        $admin = Auth::user();
     if ($admin && $admin->role == 'admin') {   
    $doctor = Doctor::find($id);

    if (!$doctor) {
        return response()->json([
            'status' => 'error',
            'message' => 'Doctor not found (ID is incorrect)',
        ], 404);
    }

    
    $profile_photo_path = public_path('/photos/doctor_photo/' . basename($doctor->profile_photo));
    $union_registration_path = public_path('/photos/union_registration_file/' . basename($doctor->union_registration));

   
    if (file_exists($profile_photo_path)) {
        unlink($profile_photo_path);
    }

    
    if (file_exists($union_registration_path)) {
        unlink($union_registration_path);
    }


    if ($doctor->delete()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Doctor deleted successfully',
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete the doctor',
        ]);
    }
     }
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


    































    
























