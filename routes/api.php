<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FreelancerController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::put('/update/{id}',[UserController::class,'update']);
    Route::post('/freelancers',[FreelancerController::class,'store']);
    Route::post('/create/service',[ServiceController::class,'store']);
    Route::put('/add/price/{id}',[ServiceController::class,'addprice']);
    Route::put('services/{id}/upload-pictures', [ServiceController::class, 'uploadPictures']);
    Route::post('/logout', [UserController::class, 'logout']);
});
    // Route::middleware('auth')->post('/freelancers', [FreelancerController::class, 'store']);
    Route::get('/checkauth', [UserController::class, 'checkAuthStatus']);
    Route::post('/auth', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'store']);
    Route::get('/edit/{id}', [UserController::class, 'show']);
    Route::get('/user/{id}/freelancer', [UserController::class, 'freelancer']);
    