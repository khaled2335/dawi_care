<?php

namespace App\Http\Controllers;
use App\Models\Service;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function all_service(){

        $Services = Service::get();
                
        return response()->json($Services);
       }

       public function delete_service($id,Request $request){
   
        $Service = Service::findOrFail($id);
        $Service->delete();
        return response()->json(['message' => 'Service deleted successfully']);
       }

       
      
}
