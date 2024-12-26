<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\deduction;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\Attendance;
use App\Models\Week_day;
use App\Models\Doctor;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function add_salary(Request $request, $dayid)
{
    $services = $request->services;
    $day = Attendance::findOrFail($dayid);
    if ($day->attedance == 0) {
        return response()->json(['message'=>'cant add revenue on zero attendance']);
    }
    $day->revenue += $services;
    $day->save();
    
    $countDays = 0;
    $countDays += Attendance::where('doctor_id', $day->doctor_id)->where('attedance',1)->count();
    
    $salary = Salary::where('doctor_id', $day->doctor_id)
                    ->where('month', date('m'))
                    ->where('year', date('Y'))
                    ->first();

    if ($salary) {
        $salary->total_salary += $services;
        $salary->num_worked_days = $countDays;
        $salary->save();
    } else {
        $salary = new Salary();
        $salary->doctor_id = $day->doctor_id;
        $salary->total_salary = $services;	
        $salary->num_worked_days = $countDays;
        $salary->month = date('m');
        $salary->year = date('Y');
        $salary->save();
    }
    $this->totalsalaryequation($salary->id);
    return response()->json(['salary' => $salary, 'day' => $day, 'total_attendance' => $countDays]);
}



 
// public function add_salaryEmployee(Request $request, $employeeId){
//         $dayIds = Week_day::where('emplyee_id',$employeeId)->pluck('id');
//         $employee = Employee::findOrFail($employeeId);
//         $fixedsalary = $employee->fixed_salary;
//         $absenteeismCount = Attendance::whereIn('day_id', $dayIds)->where('attedance',0)->get()->count();
//         $attendanceCount = Attendance::whereIn('day_id', $dayIds)->where('attedance',1)->get()->count();
//         $deduction = $request->deduction; //per day
//         $salary = Salary::where('employee_id', $employeeId)
//         ->where('month', date('m'))
//         ->where('year', date('Y'))
//         ->first();
//         //customDeduction
//         if($request->customdeduction && $salary){
//         $customDeduction  = new deduction();
//         $customDeduction->deduction = $request->customdeduction;
//         $customDeduction->description = $request->description;
//         $customDeduction->salary_id = $salary->id;
//         if ($request->created_at) {
//             $customDeduction->created_at = $request->created_at;
//         }
//         $customDeduction->save();
//         $salary->total_salary -= $customDeduction->deduction;
//         $salary->save();
//         }
        
//         if ($deduction && $salary->is_payed == 0) {
//         $salary = new Salary();
//         $salary->employee_id = $employeeId;
//         if($attendanceCount>0)
//         $salary->total_salary =  $fixedsalary - ($deduction * $absenteeismCount);
//         else
//         $salary->total_salary =  $fixedsalary;
//         $salary->num_worked_days = $attendanceCount;
//         $salary->is_payed = $request->is_payed ?? 1;
//         $salary->month = date('m');
//         $salary->year = date('Y');
//         $salary->save();
//         }
//         elseif ($salary->is_payed == 1) {
//             return response()->json('the salary has been paid before');
//         }
//         return response()->json(['salary' => $salary]);
// }

public function add_salaryEmployee(Request $request, $employeeId){
    $employee = Employee::findOrFail($employeeId);
    $fixedsalary = $employee->fixed_salary;
    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;

    $absenteeismCount = Attendance::where('employee_id', $employeeId)
    ->where('attedance',0)
    ->whereMonth('created_at', $currentMonth)    
    ->whereYear('created_at', $currentYear)
    ->count();

    $attendanceCount = Attendance::where('day_id', $employeeId)
    ->where('attedance',1)
    ->whereMonth('created_at', $currentMonth)    
    ->whereYear('created_at', $currentYear)
    ->count();
    $deduction = $request->deduction; //per day
    $salary = Salary::where('employee_id', $employeeId)
    ->where('month', date('m'))
    ->where('year', date('Y'))
    ->first();

    //customDeduction
    if($request->customdeduction && $salary){
        // Get the month and year from created_at if provided, otherwise use current date
        $deductionDate = $request->created_at ? Carbon::parse($request->created_at) : Carbon::now();
        
        // Find the salary record for the month of the deduction
        $targetSalary = Salary::where('employee_id', $employeeId)
            ->where('month', $deductionDate->format('m'))
            ->where('year', $deductionDate->format('Y'))
            ->first();
        
        if($targetSalary) {
            $customDeduction  = new deduction();
            $customDeduction->deduction = $request->customdeduction;
            $customDeduction->description = $request->description;
            $customDeduction->salary_id = $targetSalary->id;
            if ($request->created_at) {
                $customDeduction->created_at = $request->created_at;
            }
            $customDeduction->save();
            
            $targetSalary->total_salary -= $customDeduction->deduction;
            $targetSalary->save();
        }
        return response()->json(['salary' => $salary]);
    }
    
    if ($deduction) {
        $salary = new Salary();
        $salary->employee_id = $employeeId;
        if($absenteeismCount>0)
            $salary->total_salary =  $fixedsalary - ($deduction * $absenteeismCount);
        else
            $salary->total_salary =  $fixedsalary;
        $salary->num_worked_days = $attendanceCount;
        $salary->is_payed = $request->is_payed ?? 1;
        $salary->month = date('m');
        $salary->year = date('Y');
        $salary->save();
    }
    elseif ($salary->is_payed == 1) {
        return response()->json('the salary has been paid before');
    }
    return response()->json(['salary' => $salary]);
}



public function all_salary(){
    $salarys = Salary::get();
    return response()->json($salarys );
}
public function show_salary($id,Request $request){

    if ($request->type == 'doctor') {
       $salary= Salary::where('doctor_id' ,$id )->get();
    }
    else{
    $salary= Salary::where('employee_id' ,$id )->get();
    }
    return response()->json($salary);
}
public function totalsalaryequation($id)
{
    $salary = Salary::findOrFail($id);
    $doctor = Doctor::findOrFail($salary->doctor_id);
    $percentage = $doctor->doctor_share / 100;
    $doctorShares = $salary->total_salary * $percentage;
    if ($doctor->fixed_salary > $doctorShares) {
        $salary->doctor_salary = $doctor->fixed_salary;
    } else {
        $salary->doctor_salary  = $doctorShares;
    }
    $salary->clinic_salary  = $salary->total_salary - $salary->doctor_salary ;
    $salary->save();

    return response()->json($salary);
}

public function getPayed($id,Request $request)
{
    $salary = Salary::findOrFail($id);
    $value = $request->value;
    if ($salary->is_payed == 1 &&  $value == 1) {
        return response()->json('sorry.this doctor get payed before');
    }
    else {
        $salary->is_payed = $value;
        $salary->save();
        return response()->json('this doctor get payed');
    }
}

public function show_deduction($salaryid)
{
        $deduction  = deduction::where('salary_id' ,$salaryid )->get();
        return response()->json($deduction);
}





}
