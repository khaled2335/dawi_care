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
        
        $weekdays = Week_day::with(['attendanceofweekday', 'doctor'])->get();

        $result = $weekdays->map(function ($weekday) {
            return [
                'id' => $weekday->id,
                'day' => $weekday->day,
                'revenue' => $weekday->revenue,
                'doctor_id' => $weekday->doctor_id,
                'doctor_name' => $weekday->doctor ? $weekday->doctor->name : null,
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
      

     public function attendencezero($id , Request $request)
    {
        Carbon::setLocale('ar'); 
        $today = Carbon::now()->locale('ar')->isoFormat('dddd');//الاحد
     
            $attendance = new Attendance;
            $attendance->day_id = $id;
            $attendance->attedance = 0;
            if ($request->created_at) {
                $attendance->created_at = $request->created_at;
            }
            $attendance->save();
        
        return response()->json(['message' => 'take attendance'], 200); 
    }

        public function deleteattendence(Request $request)
    {
        $targetDate = Carbon::parse($request->date)->startOfDay();
        
        $attendances = Attendance::whereDate('created_at', $targetDate)->get();
        
        if ($attendances->isEmpty()) {
            return response()->json(['message' => 'No attendance records found for the given date'], 404);
        }
        
        foreach ($attendances as $attendance) {
            $attendance->delete();
        }
        
        return response()->json(['message' => 'Attendance records deleted successfully'], 200);
    }













}