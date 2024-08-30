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
        
        $weekdays = Week_day::with(['attendanceofweekday', 'doctor','employee'])->get();

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
        $result = DB::table('week_days')
            ->join('attendances', 'week_days.id', '=', 'attendances.day_id')
            ->join('doctors', 'week_days.doctor_id', '=', 'doctors.id')
            ->select(
                'doctors.name',
                'week_days.day',
                'attendances.attedance as attendance',
                DB::raw('DATE(attendances.created_at) as attendance_date')
            )
            ->where('doctors.id', $doctorId)
            ->get()
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group[0]->name,
                    'attendance_data' => $group->map(function ($item) {
                        return [
                            'day' => $item->day,
                            'switch_day' => $item->switch_day,
                            'attendance' => $item->attendance,
                            'date' => $item->attendance_date
                        ];
                    })->values()->toArray()
                ];
            })
            ->values()
            ->first();
    
        return response()->json($result);
    }
    public function showemployee($employeeId)
    {
        $result = DB::table('week_days')
            ->join('attendances', 'week_days.id', '=', 'attendances.day_id')
            ->join('employees', 'week_days.emplyee_id', '=', 'employees.id')
            ->select(
                'employees.name',
                'week_days.day',
                'attendances.attedance as attendance',
                DB::raw('DATE(attendances.created_at) as attendance_date')
            )
            ->where('employees.id', $employeeId)
            ->get()
            ->groupBy('name')
            ->map(function ($group) {
                return [
                    'name' => $group[0]->name,
                    'attendance_data' => $group->map(function ($item) {
                        return [
                            'day' => $item->day,
                            'switch_day' => $item->switch_day,
                            'attendance' => $item->attendance,
                            'date' => $item->attendance_date
                        ];
                    })->values()->toArray()
                ];
            })
            ->values()
            ->first();
    
        return response()->json($result);
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
            $existingAttendance->delete();
        } else {
            return response()->json(['message' => 'Attendance already taken'], 200);
        }
    }
    
        $attendance = new Attendance;
        $attendance->day_id = $id;
        $attendance->attedance = 0;
        
        if ($request->created_at) {
            $attendance->created_at = $request->created_at;
        }
        
        $attendance->save();
    
        return response()->json(['message' => 'take attendance'], 200);
    }

    public function deleteattendence(Request $request, $id)
    {
        $targetDate = Carbon::day($request->date)->startOfDay();
        
        $attendance = Attendance::whereDate('created_at', $targetDate)
            ->where('day_id', $id)
            ->first();
        
        if (!$attendance) {
            return response()->json(['message' => 'No attendance record found for the given date and ID'], 404);
        }
        
        $attendance->delete();
        
        return response()->json(['message' => 'Attendance record deleted successfully'], 200);
    }
    public function takeattedence()
{
   Carbon::setLocale('ar');
    $today = Carbon::now()->translatedFormat('l');
    $weekdays = Week_day::get();
    $attendanceTaken = false;

    foreach ($weekdays as $weekday) {
        if ($today == $weekday->day) {
            
            $existingAttendance = Attendance::where('day_id', $weekday->id)
                ->whereDate('created_at', Carbon::today()->toDateString())
                ->first();

            if (!$existingAttendance) {
                
                $attendance = new Attendance;
                $attendance->attedance = 1;
                $attendance->day_id = $weekday->id;
                $attendance->save();
                $attendanceTaken = true;
            } else {
                $attendanceTaken = 'already_taken';
            }
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