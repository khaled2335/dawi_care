<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\week_days;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SalaryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => 'api'],function ()  {
 
    Route::post('register' , [UserController::class,'register'] );
    Route::post('login' , [UserController::class,'login'] );
    Route::get('profile' , [UserController::class,'profile'] )->middleware('auth');
    Route::post('logout' , [UserController::class,'logout'] )->middleware('auth');
    Route::get('All_Users' , [UserController::class,'index'] )->middleware('auth');
    Route::post('Add_User' , [UserController::class,'create'] )->middleware('auth');
    Route::get('Show_User/{id}' , [UserController::class,'show'] )->middleware('auth');
    Route::post('Edit_User/{id}' , [UserController::class,'edit'] )->middleware('auth');
    Route::post('Delete_User/{id}' , [UserController::class,'destroy'] )->middleware('auth');
   });

################################      DOCTOR         ##########################################################

   Route::post('Add_Doctor' , [DocterController::class,'create'] )->middleware('auth');
   Route::get('All_doctors' , [DocterController::class,'index'] )->middleware('auth');
   Route::get('Show_doctor/{id}' , [DocterController::class,'show'] )->middleware('auth');
   Route::post('Edit_Doctor/{id}' , [DocterController::class,'edit'] )->middleware('auth');
   Route::post('Delete_Doctor/{id}' , [DocterController::class,'destroy'] )->middleware('auth');
################################      EMPLOYEE         ##########################################################
Route::post('Add_Employee' , [employeeController::class,'create'] )->middleware('auth');
Route::get('All_Employee' , [employeeController::class,'index'] )->middleware('auth');
Route::get('Show_Employee/{id}' , [employeeController::class,'show'] )->middleware('auth');
Route::post('Edit_Employee/{id}' , [employeeController::class,'edit'] )->middleware('auth');
Route::post('Delete_Employee/{id}' , [employeeController::class,'destroy'] )->middleware('auth');
################################      worked_days         ##########################################################
Route::post('switchday/{id}' , [week_days::class,'switchday'] );
Route::get('All_Week_day' , [week_days::class,'index'] )->middleware('auth');
Route::get('Show_Week_day/{id}' , [week_days::class,'show'] )->middleware('auth');
Route::post('Edit_Week_day/{id}' , [week_days::class,'edit'] )->middleware('auth');
Route::post('Edit_all_Week_day/{id}' , [week_days::class,'editall'] )->middleware('auth');
Route::post('Delete_Week_day/{id}' , [week_days::class,'destroy'] )->middleware('auth');
Route::post('numberofweekdays' , [week_days::class,'calculateAllWorkingDaysForYear'] );

################################    attendance        ##########################################################

Route::get('add_attendance' , [AttendanceController::class,'add_attendance'] );
Route::get('getattendance' , [AttendanceController::class,'index'] );
Route::get('get_doctor_attendence/{doctorId}' , [AttendanceController::class,'show'] );
Route::get('get_employee_attendence/{employeeId}' , [AttendanceController::class,'showemployee'] );
Route::post('attendencezero/{id}' , [AttendanceController::class,'attendencezero'] );
Route::post('deleteattendence/{id}' , [AttendanceController::class,'deleteattendence'] );
Route::post('takeattedence' , [AttendanceController::class,'takeattedence'] );

################################    clinic        ##########################################################
Route::post('add_clinic' , [ClinicController::class,'add_clinic'] );
Route::get('all_clinic' , [ClinicController::class,'all_clinic'] );
Route::get('show_clinic/{id}' , [ClinicController::class,'show_clinic'] );
Route::post('delete_clinic/{id}' , [ClinicController::class,'delete_clinic'] );
Route::post('edit_clinic/{id}' , [ClinicController::class,'edit_clinic'] );

################################    service     ##########################################################
Route::post('add_service' , [ServiceController::class,'add_service'] );
Route::get('all_service' , [ServiceController::class,'all_service'] );
Route::post('delete_service/{id}' , [ServiceController::class,'delete_service'] );
Route::post('edit_service/{id}' , [ServiceController::class,'edit_service'] );
Route::get('done_service' , [ServiceController::class,'doneService'] );
Route::post('done_service/{doctorId}/{attendenceId}' , [ServiceController::class,'doneServicePost'] );
Route::get('doneServiceDoctor/{doctorId}' , [ServiceController::class,'doneServiceDoctor'] );

################################    salaries     ##########################################################
Route::post('add_salary/{dayid}' , [SalaryController::class,'add_salary'] );
Route::get('all_salary' , [SalaryController::class,'all_salary'] );
Route::get('show_salary/{docid}' , [SalaryController::class,'show_salary'] );
Route::post('delete_salary/{id}' , [SalaryController::class,'delete_salary'] );
Route::post('edit_salary/{id}' , [SalaryController::class,'edit_salary'] );
Route::post('totalsalaryequation/{id}' , [SalaryController::class,'totalsalaryequation']);
Route::post('getPayed/{id}' , [SalaryController::class,'getPayed']);

