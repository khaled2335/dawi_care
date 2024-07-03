<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocterController;

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

   Route::post('Add_Doctor' , [DocterController::class,'create'] )->middleware('auth');
   Route::get('All_doctors' , [DocterController::class,'index'] )->middleware('auth');
   Route::get('Show_doctor/{id}' , [DocterController::class,'show'] )->middleware('auth');
   Route::post('Edit_Doctor/{id}' , [DocterController::class,'edit'] )->middleware('auth');
   Route::post('Delete_Doctor/{id}' , [DocterController::class,'destroy'] )->middleware('auth');
   