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


class AttendanceController extends Controller
{

    public function add_attendance()
    {
        Carbon::setLocale('ar'); 
        $today = Carbon::now()->locale('ar')->isoFormat('dddd');//الاحد
     
        $weekdays = Week_day::where('day', $today)->get();//107
        
        foreach ($weekdays as $weekday) {
            $attendance = new Attendance;
            $attendance->day_id = $weekday->id;
            $attendance->save();
        }
        return response()->json(['message' => 'take attendance'], 200); 
    }

    public function index()
    {
        $weekdays = Week_day::with('attendanceofweekday')->get();
        return response()->json($weekdays);
    }

    public function attendencezero($id)
    {
        $row = Attendance::find($id);
        $row->attedance = 0;
        $row->save();
    }











}
