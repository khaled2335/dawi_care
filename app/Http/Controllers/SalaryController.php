<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\Attendance;
use App\Models\Week_day;
use App\Models\Doctor;
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
    $doctorId = Week_day::where('id', $day->day_id)->first();
    $weekDays = Week_day::where('doctor_id', $doctorId->doctor_id)->get();
    $countDays = 0;
    foreach ($weekDays as $weekDay) {
        $countDays += Attendance::where('day_id', $weekDay->id)->where('attedance',1)->count();
    }
    $salary = Salary::where('doctor_id', $doctorId->doctor_id)
                    ->where('month', date('m'))
                    ->where('year', date('Y'))
                    ->first();

    if ($salary) {
        $salary->total_salary += $services;
        $salary->num_worked_days = $countDays;
        $salary->save();
    } else {
        $salary = new Salary();
        $salary->doctor_id = $doctorId->doctor_id;
        $salary->total_salary = $services;	
        $salary->num_worked_days = $countDays;
        $salary->month = date('m');
        $salary->year = date('Y');
        $salary->save();
    }
    return response()->json(['salary' => $salary, 'day' => $day, 'total_attendance' => $countDays]);
}
 
public function add_salaryEmployee(Request $request, $employeeId){
        $dayIds = Week_day::where('emplyee_id',$employeeId)->pluck('id');
        $employee = Employee::findOrFail($employeeId);
        $fixedsalary = $employee->fixed_salary;
        $absenteeismCount = Attendance::whereIn('day_id', $dayIds)->where('attedance',0)->get()->count();
        $attendanceCount = Attendance::whereIn('day_id', $dayIds)->where('attedance',1)->get()->count();
        $deduction = $request->deduction; //per day
        $salary = new Salary();
        $salary->employee_id = $employeeId;
        if($attendanceCount>0)
        $salary->total_salary =  $fixedsalary - ($deduction * $absenteeismCount);
        $salary->num_worked_days = $attendanceCount ;
        $salary->is_payed = $request->is_payed ?? 1;
        $salary->month = date('m');
        $salary->year = date('Y');
        $salary->save();
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







}
