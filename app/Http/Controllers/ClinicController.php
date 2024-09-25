<?php

namespace App\Http\Controllers;
use App\Models\Clinic;


use Illuminate\Http\Request;

class ClinicController extends Controller
{
    

    public function all_clinic(){

     $clinics = Clinic::get();
     
     return response()->json([$clinics]);
    }
    public function add_clinic(Request $request){

     $clinic = new Clinic;
     $clinic->name = $request->name;
     $clinic->save();
     return response()->json(['message' => 'clinic added successfully', 'clinic' => $clinic]);
    }
    public function delete_clinic($id,Request $request){

     $clinic = Clinic::findOrFail($id);
     $clinic->delete();
     return response()->json(['message' => 'clinic deleted successfully']);
    }










}
