<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Employee;
use Hash;
use Auth;
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
            $employee = Employee::all();  
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
                'national_id' => 'ؤ|numeric', // National ID is required, must be numeric and exactly 10 digits
                'phone_number' => 'required|string|regex:/^\+?[0-9]{7,15}$/', // Phone number is required, should be a string, and match the regex pattern
                'total_salary' => 'required|numeric|min:0', // Total salary is required, must be numeric, and at least 0
                'fixed_salary' => 'required|numeric|min:0', // Fixed salary is required, must be numeric, and at least 0
                'overtime_salary' => 'numeric|min:0', // Overtime salary must be numeric, and at least 0
               
            ]);

        
            $employee = new Employee;
            $employee->name = $request->name;
            $employee->national_id = $request->national_id;
            $employee->phone_number = $request->phone_number;
            $employee->description = $request->description;
            $employee->total_salary = $request->total_salary;
            $employee->overtime_salary = $request->overtime_salary;
            $employee->fixed_salary = $request->fixed_salary;
            $res = $employee->save();
            if ($res) {
                return response()->json(['message' => 'Employee added successfully', 'doctor' => $employee]);
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
                return response()->json(['message' => 'employee not found'], 404);
            }
    
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'string|max:1000', // Description must be a string and max length of 1000 characters
                'national_id' => 'required|numeric', // National ID is required, must be numeric and exactly 10 digits
                'phone_number' => 'required|string|regex:/^\+?[0-9]{7,15}$/', // Phone number is required, should be a string, and match the regex pattern
                'total_salary' => 'required|numeric|min:0', // Total salary is required, must be numeric, and at least 0
                'fixed_salary' => 'required|numeric|min:0', // Fixed salary is required, must be numeric, and at least 0
                'overtime_salary' => 'numeric|min:0', // Overtime salary must be numeric, and at least 0
            ]);

    
            
            $employee->name = $request->name;
            $employee->national_id = $request->national_id;
            $employee->phone_number = $request->phone_number;
            $employee->description = $request->description;
            $employee->total_salary = $request->total_salary;
            $employee->overtime_salary = $request->overtime_salary;
            $employee->fixed_salary = $request->fixed_salary;
         
            $res = $employee->save();
            if ($res) {
                return response()->json(['message' => 'employee updated successfully', 'employee' => $employee]);
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











}