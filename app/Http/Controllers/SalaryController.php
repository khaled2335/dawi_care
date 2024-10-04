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
    public function add_salary(Request $request, $weekdayid)
{
    $services = $request->services;
    $day = Week_day::findOrFail($weekdayid);
    $day->daily_earnings += $services;
    $day->save();
    
    $weekDays = Week_day::where('doctor_id', $day->doctor_id)->get();
    
    $countDays = 0;
    foreach ($weekDays as $weekDay) {
        $countDays += Attendance::where('day_id', $weekDay->id)->where('attedance',1)->count();
    }
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

    return response()->json(['salary' => $salary, 'day' => $day, 'total_attendance' => $countDays]);
}
   

    
    






}
