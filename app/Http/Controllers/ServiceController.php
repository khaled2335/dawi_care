<?php

namespace App\Http\Controllers;
use App\Models\Service;
use App\Models\DoneService;

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
       public function doneService(){
   
        $Service = DoneService::with('clinic')->get();
        return response()->json(  $Service);
       }
       public function doneServicePost($doctorId,$attendenceId,Request $request){
   
                $rawData = $request->input('services');
            
                $elements = explode(',', $rawData); 
                
                    if (count($elements) % 3 !== 0) {
                    return response()->json(['error' => 'Data is not in valid trios'], 400);
                   }
                   
                   for ($i = 0; $i < count($elements); $i += 3) {
                        $serviceId = $elements[$i];                   
                        $service = Service::find($serviceId);
                        $doneService = new DoneService();
                        $doneService->service_id = $elements[$i] ;
                        $doneService->count = $elements[$i+1];
                        $doneService->total_cost = $elements[$i+2];  
                        $doneService->doctor_id = $doctorId;  
                        $doneService->clinic_id = $service->clinic_id;  
                        $doneService->attendence_id = $attendenceId;  
                        $doneService->save();
                }
        return response()->json(['success' => 'serves done added successfully'], 200);
       }

       public function doneServiceDoctor($doctorId){
   
        $docServices = DoneService::where('doctor_id',$doctorId)->get();
        return response()->json(  $docServices);
       }
       public function doneServiceClinic($clinicId){
   
        $clinicServices = DoneService::where('clinic_id',$clinicId)->get();
        return response()->json(  $clinicServices);
       }

       public function edit_service ($id,$clinicId,Request $request){

            $service = Service::find($id);
            $service->name = $request->name;
            $service->price = $request->price;
            $service->clinic_id = $clinicId;
            $service->save();
            return response()->json( ['status'=>'success','data'=> $service],200);

       }

}
