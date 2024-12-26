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
use Illuminate\Support\Facades\DB;



class AttendanceController extends Controller
{

    // public function add_attendance()
    // {
    //     Carbon::setLocale('ar'); 
    //     $today = Carbon::now()->locale('ar')->isoFormat('dddd');//الاحد

    //     $weekdays = Week_day::where('day', $today)->get();//107

    //     foreach ($weekdays as $weekday) {
    //         $attendance = new Attendance;
    //         $attendance->day_id = $weekday->id;
    //         $attendance->attedance = 0;
    //         $attendance->save();
    //     }
    //     return response()->json(['message' => 'take attendance'], 200); 
    // }

    public function index()
    {

        $weekdays = Week_day::with(['attendanceofweekday', 'doctor', 'employee'])->get();

        $result = $weekdays->map(function ($weekday) {
            return [
                'id' => $weekday->id,
                'day' => $weekday->day,
                'switch_day' => $weekday->switch_day,
                'revenue' => $weekday->revenue,
                'doctor_id' => $weekday->doctor_id,
                'emplyee_id' => $weekday->emplyee_id,
                'name' => $weekday->doctor_id ? ($weekday->doctor ? $weekday->doctor->name : null) : ($weekday->employee ? $weekday->employee->name : null),
                'created_at' => $weekday->created_at,
                'attendanceofweekday' => $weekday->attendanceofweekday->map(function ($attendance) {
                    return [
                        'id' => $attendance->id,
                        'attendance' => $attendance->attedance,
                        'day_id' => $attendance->day_id,
                        'created_at' => $attendance->created_at,
                    ];
                }),
            ];
        });

        return response()->json($result);
    }

    public function show($doctorId)
    {
        $docattendence = Attendance::where('doctor_id',$doctorId)->get();

        return response()->json($docattendence);
    }
    public function showemployee($employeeId)
    {
        
        $employeettendence = Attendance::where('employee_id',$employeeId)->get();
        return response()->json($employeettendence);
    }

    public function attendencezero($id, Request $request)
    {
        Carbon::setLocale('ar');
        $today = Carbon::now()->locale('ar')->isoFormat('dddd');
        $query = Attendance::where('day_id', $id);
        if ($request->created_at) {
            $query->whereDate('created_at', $request->created_at);
        }
        $existingAttendance = $query->first();

        if ($existingAttendance) {
            if ($existingAttendance->attedance == 1) {
                $existingAttendance->attedance = 0;
                $existingAttendance->save();
                return response()->json(['message' => 'take attendance'], 200);
            } else {
                return response()->json(['message' => 'Attendance already taken'], 200);
            }

        }
        
        return response()->json(['message' => 'not found'], 200);
    }

    public function deleteattendence($id)
    {
        
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json(['message' => 'No attendance record found for the given date and ID'], 404);
        }
            $attendance->attedance = 1;
            $attendance->save();
            return response()->json(['message' => 'Attendance updating successfully'], 200);
    }
    public function takeattedence()
    {
        Carbon::setLocale('ar');
        date_default_timezone_set('Africa/Cairo'); 
        $today = now()->translatedFormat('l');
        $weekdays = Week_day::where('day',  $today)
        ->orWhere('switch_day', $today)
        ->get();
        $attendanceTaken = false;

        foreach ($weekdays as $weekday) {
                $existingAttendance = Attendance::where('day_id', $weekday->id)
                    ->whereDate('created_at', now()->toDateString())
                    ->first();

                if (!$existingAttendance) {
                    $attendance = new Attendance;
                    $attendance->attedance = 1;
                    $attendance->day_id = $weekday->id;
                    $attendance->doctor_id = $weekday->doctor_id;
                    $attendance->employee_id = $weekday->emplyee_id;
                    $attendance->created_at = now();
                    $attendance->save();
                    $attendanceTaken = true;
                } else {
                    $attendanceTaken = 'already_taken';
                }
            
        }

        if ($attendanceTaken === true) {
            return response()->json(['message' => 'Attendance taken successfully'], 200);
        } elseif ($attendanceTaken === 'already_taken') {
            return response()->json(['message' => 'Attendance already taken for today'], 400);
        } else {
            return response()->json(['message' => 'No attendance taken'], 404);
        }
    }
}
