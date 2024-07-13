<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Week_day;
use App\Models\Emlpoyee_week_day;
use Hash;
use Auth;
use Illuminate\Http\Request;

class employee_weekdays_controller extends Controller
{
    public function create(Request $request , $id){
        $admin = Auth::user();
    if ($admin && $admin->role == 'admin') {
        $rawData = $request->input('data');

        $elements = explode(',' ,$rawData);

        if (count($elements) == 0) {
            return response()->json(['error' => 'days empty'], 400);
        }
   
        for ($i=0 ; $i < count($elements); $i++) { 
            
            $e_weekdays = new Emlpoyee_week_day;
            $e_weekdays->day = $elements[$i];
            $e_weekdays->employee_id = $id;
            $e_weekdays->save();
            

        }
        return response()->json(['message' => 'days inserted succssfully'], 200);
    }
      return response()->json(['error' => 'Unauthorized'], 400);

    }
    public function editall(Request $request, $id)
    {
       $admin = Auth::user();
       if ($admin && $admin->role == 'admin') {
       $weekday = Emlpoyee_week_day::where('employee_id' , $id )->delete();//employee_id
       
       return $this->create($request, $id);
   
    }
   }




















}
