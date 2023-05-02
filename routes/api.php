<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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



// Route::resource('users', UserController::class);
// Route::post('/auth', [UserController::class,"login"]);
Route::post('/logout', [UserController::class, 'logout']);
Route::get('/checkauth', [UserController::class, 'checkAuthStatus']);


Route::group(['middleware' => 'auth.redirect'], function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::resource('users', UserController::class);
    Route::post('/auth', [UserController::class, 'login']);
});