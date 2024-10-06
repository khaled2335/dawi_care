<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\Attendance;
use App\Models\Week_day;
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
 
public function all_salary(){

    $salarys = Salary::get();
    return response()->json($salarys );

}






}
