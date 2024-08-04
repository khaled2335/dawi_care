<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\employeeController;
use App\Http\Controllers\week_days;
use App\Http\Controllers\employee_weekdays_controller;
use App\Http\Controllers\AttendanceController;

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
Route::post('Add_Week_day/{id}' , [week_days::class,'create'] )->middleware('auth');
Route::get('All_Week_day' , [week_days::class,'index'] )->middleware('auth');
Route::get('Show_Week_day/{id}' , [week_days::class,'show'] )->middleware('auth');
Route::post('Edit_Week_day/{id}' , [week_days::class,'edit'] )->middleware('auth');
Route::post('Edit_all_Week_day/{id}' , [week_days::class,'editall'] )->middleware('auth');
Route::post('Delete_Week_day/{id}' , [week_days::class,'destroy'] )->middleware('auth');
################################      employee_worked_days         ##########################################################
Route::post('Add_employee_weekdays/{id}' , [employee_weekdays_controller::class,'create'] )->middleware('auth');
Route::get('All_employee_weekdays' , [employee_weekdays_controller::class,'index'] )->middleware('auth');
Route::get('Show_employee_weekdays/{id}' , [employee_weekdays_controller::class,'show'] )->middleware('auth');
Route::post('Edit_employee_weekdays/{id}' , [employee_weekdays_controller::class,'edit'] )->middleware('auth');
Route::post('Edit_all_employee_weekdays/{id}' , [employee_weekdays_controller::class,'editall'] )->middleware('auth');
Route::post('Delete_employee_weekdays/{id}' , [employee_weekdays_controller::class,'destroy'] )->middleware('auth');
################################    attendance        ##########################################################

Route::get('add_attendance' , [AttendanceController::class,'add_attendance'] );
Route::get('getattendance' , [AttendanceController::class,'index'] );
Route::get('get_doctor_attendence/{doctorId}' , [AttendanceController::class,'show'] );
Route::get('get_employee_attendence/{employeeId}' , [AttendanceController::class,'showemployee'] );
Route::post('attendencezero/{id}' , [AttendanceController::class,'attendencezero'] );
Route::post('deleteattendence/{id}' , [AttendanceController::class,'deleteattendence'] );