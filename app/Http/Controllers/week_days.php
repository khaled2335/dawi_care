<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Week_day;
use App\Models\Attendance;
use App\Models\Employee;
use Hash;
use Auth;
use Carbon\Carbon;


class week_days extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $weekdays = Week_day::get();
        return response()->json($weekdays);
    }

    /**
     * Show the form for creating a new resource.
     */


    //  public function switchday(Request $request, $id)
    //  {

    //      $rawData = $request->input('data');
    //      $type = $request->input('type');
    //      $sDayDate = $request->input('sdaydate');
    //      if (!$sDayDate) {

    //         $sDayDate = Carbon::now()->toDateString();
    //     }

    //      $elements = explode(',', $rawData); 

    //      if ($type === 'doctor') {

    //         for ($i = 0; $i < count($elements); $i += 4) {

    //             $weekDay1 = new Week_day();
    //             $weekDay1->switch_day = $elements[$i];
    //             $weekDay1->date = $elements[$i + 1];
    //             $weekDay1->doctor_id = $id;
    //             $weekDay1->switched_day_date = $sDayDate;
    //             $weekDay1->save();



    //             if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
    //                 $weekDay2 = new Week_day();
    //                 $weekDay2->switch_day = $elements[$i + 2];
    //                 $weekDay2->date = $elements[$i + 3];
    //                 $weekDay2->doctor_id = $id;
    //                 $weekDay2->switched_day_date = $sDayDate;
    //                 $weekDay2->save();
    //             }

    //             $attendance = new Attendance;
    //             $attendance->day_id = $weekDay1->id;
    //             $attendance->attedance = 1;
    //             $attendance->created_at = $request->created_at;
    //             $attendance->save();  

    //      }
    //          return response()->json(['message' => 'Data inserted successfully']);
    //     }
    //     if ($type === 'employee'){
    //         if (count($elements) == 0) {
    //             return response()->json(['error' => 'days empty'], 400);
    //         }

    //         for ($i=0 ; $i < count($elements); $i++) { 

    //             $e_weekdays = new Week_day;
    //             $e_weekdays->switch_day = $elements[$i];
    //             $e_weekdays->emplyee_id = $id;
    //             $e_weekdays->switched_day_date = $sDayDate;
    //             $e_weekdays->save();


    //         }
    //         $attendance = new Attendance;
    //         $attendance->day_id = $e_weekdays->id;
    //         $attendance->attedance = 1;
    //         $attendance->created_at = $request->created_at;
    //         $attendance->save();      
    //         return response()->json(['message' => 'Data inserted successfully']);

    //     }

    //  }

    public function switchday(Request $request, $id)
    {
        $day = $request->input('day');
        $type = $request->input('type');
        $switchedDayDate = $request->input('switchedDayDate');
        $switchDayDate = $request->input('switchDayDate');
        $date = $request->input('date');
        if ($type === 'doctor') {
            $existingSwitch = Week_day::where('doctor_id', $id)
                ->where('switched_day_date', $switchedDayDate)
                ->first();

            if ($existingSwitch) {
                return response()->json([
                    'error' => 'This day has already been submitted for switching',
                    'existing_switch' => [
                        'switch_day' => $existingSwitch->switch_day,
                        'switched_date' => $existingSwitch->switched_day_date
                    ]
                ], 400);
            }
                $weekDay= new Week_day();
                $weekDay->switch_day = $day;
                $weekDay->date = $date;
                $weekDay->doctor_id = $id;
                $weekDay->switched_day_date = $switchedDayDate;
                $weekDay->switch_day_date = $switchDayDate;
                $weekDay->save();
            return response()->json(['message' => 'Data inserted successfully']);
        }
        if ($type === 'employee') {
            $existingSwitch = Week_day::where('emplyee_id', $id)
                ->where('switched_day_date', $switchedDayDate)
                ->first();

            if ($existingSwitch) {
                return response()->json([
                    'error' => 'This day has already been submitted for switching',
                    'existing_switch' => [
                        'switch_day' => $existingSwitch->switch_day,
                        'switched_date' => $existingSwitch->switched_day_date
                    ]
                ], 400);
            }
                $weekDay= new Week_day();
                $weekDay->switch_day = $day;
                $weekDay->date = $date;
                $weekDay->emplyee_id = $id;
                $weekDay->switched_day_date = $switchedDayDate;
                $weekDay->switch_day_date = $switchDayDate;
                $weekDay->save();
            return response()->json(['message' => 'Data inserted successfully']);
        }

    }



    
    public function editall(Request $request, $id)
    {
        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') {
            $rawData = $request->input('data');
            $type = $request->input('type');
            $elements = explode(',', $rawData);

            if ($type == 'doctor') {
                $doctorweekday = Week_day::where('doctor_id', $id)->get();
                foreach ($doctorweekday as $key => $dweekday) {
                    $dweekday->delete();
                }


                if (count($elements) % 2 !== 0) {
                    return response()->json(['error' => 'Data is not in valid pairs'], 400);
                }

                for ($i = 0; $i < count($elements); $i += 4) {

                    $weekDay1 = new Week_day();
                    $weekDay1->day = $elements[$i];
                    $weekDay1->date = $elements[$i + 1];
                    $weekDay1->doctor_id = $id;
                    $weekDay1->save();


                    if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
                        $weekDay2 = new Week_day();
                        $weekDay2->day = $elements[$i + 2];
                        $weekDay2->date = $elements[$i + 3];
                        $weekDay2->doctor_id = $id;
                        $weekDay2->save();
                    }
                }

                return response()->json(['message' => 'doctor days updated successfully']);
            } else {

                $employeeweekdays = Week_day::where('emplyee_id', $id)->get();
                foreach ($employeeweekdays as $key => $employeeweekday) {
                    $employeeweekday->delete();
                }

                if (count($elements) == 0) {
                    return response()->json(['error' => 'days empty'], 400);
                }

                for ($i = 0; $i < count($elements); $i++) {

                    $e_weekdays = new Week_day;
                    $e_weekdays->day = $elements[$i];
                    $e_weekdays->emplyee_id = $id;
                    $e_weekdays->save();
                }
                return response()->json(['message' => ' employee days updated succssfully'], 200);
            }
        }

        return response()->json(['message' => 'Unauthorized or no weekdays found'], 403);
    }


    public function destroy(Request $request, $id)
    {

        $admin = Auth::user();
        if ($admin && $admin->role == 'admin') {
            $weekday = Week_day::find($id);
            if ($weekday) {
                $weekday->delete();
                return response()->json(['massege' => 'weekday deleted successfully']);
            }
            return response()->json(['message' => 'weekday not found (id is wrong)']);
        }
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function calculateAllWorkingDaysForYear(Request $request)
    {
        $now = now();
        $year = $request->input('year', $now->year);
        $month = $request->input('month', $now->month);

        $dayMapping = [
            'الأحد' => 'Sunday',
            'الاثنين' => 'Monday',
            'الثلاثاء' => 'Tuesday',
            'الأربعاء' => 'Wednesday',
            'الخميس' => 'Thursday',
            'الجمعة' => 'Friday',
            'السبت' => 'Saturday',
        ];

        $results = [];

        // Calculate and update for employees
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $workingDays = Week_day::where('emplyee_id', $employee->id)
                ->pluck('day')
                ->toArray();

            $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);

            // Update the employee's num_working_days
            $employee->num_working_days = $monthlyWorkingDays;
            $employee->save();

            $results['employees'][] = [
                'id' => $employee->id,
                'num_working_days' => $monthlyWorkingDays
            ];
        }

        // Calculate and update for doctors
        $doctors = Doctor::all();
        foreach ($doctors as $doctor) {
            $workingDays = Week_day::where('doctor_id', $doctor->id)
                ->pluck('day')
                ->toArray();

            $monthlyWorkingDays = $this->countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping);

            // Update the doctor's num_working_days
            $doctor->num_working_days = $monthlyWorkingDays;
            $doctor->save();

            $results['doctors'][] = [
                'id' => $doctor->id,
                'num_working_days' => $monthlyWorkingDays
            ];
        }

        return response()->json([
            'year' => $year,
            'month' => $month,
            'results' => $results
        ]);
    }

    private function countMonthlyWorkingDays($year, $month, $workingDays, $dayMapping)
    {
        $englishWorkingDays = array_map(function ($day) use ($dayMapping) {
            return $dayMapping[$day] ?? $day;
        }, $workingDays);

        $date = Carbon::create($year, $month, 1);
        $daysInMonth = $date->daysInMonth;
        $workingDayCount = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDay = $date->format('l'); // Get the day name
            if (in_array($currentDay, $englishWorkingDays)) {
                $workingDayCount++;
            }
            $date->addDay();
        }

        return $workingDayCount;
    }
}
