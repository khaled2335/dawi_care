<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use Hash;
use Auth;
class DocterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') { 
            $doctors = Doctor::all();  
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
           
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                     'national_id' => 'required|digits_between:10,20|unique:doctors,national_id',
                     'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:doctors,phone_number',
                     'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                     'union_registration' => 'required||max:255',
                     'specialty' => 'required|',
                     'scientific_degree' => 'required|max:255',
                     'worked_days' => 'required|integer|min:0',
                     'fixed_salary' => 'required|numeric|min:0',
                  
                ]
            );

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
            $doctor->worked_days = $request->worked_days;
            $doctor->specialty = $request->specialty;
            $doctor->fixed_salary = $request->fixed_salary;
            $res = $doctor->save();
            if ($res) {
                return response()->json(['message' => 'Doctor added successfully', 'doctor' => $doctor]);
            } else {
                return response()->json(['message' => 'Registration failed']);
            }
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    /**
     * Display the specified resource.
     */
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
    
            $request->validate(
                [
                    'name' => 'required|string|max:255',
                     'national_id' => 'required|digits_between:10,20|unique:doctors,national_id',
                     'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:doctors,phone_number',
                     'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                     'union_registration' => 'required||max:255|',
                     'scientific_degree' => 'required|max:255',
                     'worked_days' => 'required|integer|min:0',
                     'specialty' => 'required',
                     'fixed_salary' => 'required|numeric|min:0',
                ]
            );
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo
                if ($doctor->profile_photo) {
                    
                    $oldPhotoPath = public_path('/photos/doctor_photo/' . basename($doctor->profile_photo));
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                $union_registration_path = public_path('/photos/union_registration_file/' . basename($doctor->union_registration));
                
        
    
                $doctor_image_name = rand() . '.' . $request->profile_photo->getClientOriginalExtension();
                $request->profile_photo->move(public_path('/photos/doctor_photo'), $doctor_image_name);
                $doctor->profile_photo = asset('photos/doctor_photo/' . $doctor_image_name);
            }
    
            // Handle scientific degree file upload
            if ($request->hasFile('union_registration')) {
                // Delete old scientific degree file
                if ($doctor->union_registration) {
                    // $oldDegreePath = public_path(parse_url($doctor->scientific_degree, PHP_URL_PATH));
                    $oldDegreePath =  public_path('/photos/union_registration_file/' . basename($doctor->union_registration));;
                    if (file_exists($oldDegreePath)) {
                        unlink($oldDegreePath);
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
            $doctor->worked_days = $request->worked_days;
            $doctor->fixed_salary = $request->fixed_salary;
            $doctor->specialty = $request->specialty;
    
            $res = $doctor->save();
            if ($res) {
                return response()->json(['message' => 'Doctor updated successfully', 'doctor' => $doctor]);
            } else {
                return response()->json(['message' => 'Update failed']);
            }
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Auth::user();
     if ($admin && $admin->role == 'admin') {     // Find the doctor by ID
    $doctor = Doctor::find($id);

    if (!$doctor) {
        return response()->json([
            'status' => 'error',
            'message' => 'Doctor not found (ID is incorrect)',
        ], 404);
    }

    // Define paths to the files
    $profile_photo_path = public_path('/photos/doctor_photo/' . basename($doctor->profile_photo));
    $union_registration_path = public_path('/photos/union_registration_file/' . basename($doctor->union_registration));

    // Delete the profile photo if it exists
    if (file_exists($profile_photo_path)) {
        unlink($profile_photo_path);
    }

    // Delete the scientific degree file if it exists
    if (file_exists($union_registration_path)) {
        unlink($union_registration_path);
    }

    // Delete the doctor record
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











}