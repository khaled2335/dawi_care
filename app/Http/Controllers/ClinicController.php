<?php

namespace App\Http\Controllers;
use App\Models\Clinic;
use App\Models\Service;


use Illuminate\Http\Request;

class ClinicController extends Controller
{
    

    public function all_clinic(){

     $clinics = Clinic::with('service')->get();
     
     return response()->json($clinics);
    }
    public function show_clinic($id){

     $clinics = Clinic::with('service')->findOrFail($id);
     
     return response()->json($clinics);
    }
    public function add_clinic(Request $request){

     $clinic = new Clinic;
     $clinic->name = $request->name;
     $res = $clinic->save();
     if ($res) {
                
        $rawData = $request->input('service');
   
        $elements = explode(',', $rawData); 
        
            if (count($elements) % 2 !== 0) {
            return response()->json(['error' => 'Data is not in valid pairs'], 400);
           }
           
           for ($i = 0; $i < count($elements); $i += 4) {
         
               $Service1 = new Service();
               $Service1->name = $elements[$i];
               $Service1->price = $elements[$i + 1];
               $Service1->clinic_id = $clinic->id;
               $Service1->save();
    
               if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
                   $Service2 = new Service();
                   $Service2->name = $elements[$i + 2];
                   $Service2->price = $elements[$i + 3];
                   $Service2->clinic_id = $clinic->id;
                   $Service2->save();
               }
        }
    }
     return response()->json(['message' => 'clinic added successfully']);
    }

    public function delete_clinic($id,Request $request){

     $clinic = Clinic::findOrFail($id);
     $clinic->delete();
     return response()->json(['message' => 'clinic deleted successfully']);
    }
    public function edit_clinic($id,Request $request){

     $clinic = Clinic::findOrFail($id);
     $clinic->name = $request->name;
     $res = $clinic->save();
     if ($res) {
                
        $rawData = $request->input('service');
   
        $elements = explode(',', $rawData); 
                    
            if (count($elements) % 2 !== 0) {
            return response()->json(['error' => 'Data is not in valid pairs'], 400);
           }
           
           for ($i = 0; $i < count($elements); $i += 4) {
         
            $Service1 = new Service();
            $Service1->name = $elements[$i];
            $Service1->price = $elements[$i + 1];
            $Service1->clinic_id = $clinic->id;
            $Service1->save();
 
            if (isset($elements[$i + 2]) && isset($elements[$i + 3])) {
                $Service2 = new Service();
                $Service2->name = $elements[$i + 2];
                $Service2->price = $elements[$i + 3];
                $Service2->clinic_id = $clinic->id;
                $Service2->save();
            }
     }
    
    }
     return response()->json(['message' => 'clinic updated successfully']);

    }

}