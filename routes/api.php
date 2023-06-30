<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\MessageController;
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
    Route::get('/profile/services', [ServiceController::class, 'displayinprofile']);
    Route::put('/edit/service/overview/{id}', [ServiceController::class, 'editservice']);
    Route::put('/edit/service/price/{id}', [ServiceController::class, 'updatePrice']);
    Route::get('/service/{id}/edit/overview', [ServiceController::class, 'getService']);
    Route::delete('/service/delete/{id}', [ServiceController::class, 'deleteService']);
    Route::delete('/profile/services/delete', [ServiceController::class, 'deleteSelected']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::get('/commands/of/freelancer', [CommandController::class, 'getCommandsRelatedToFreelancerService']);
    Route::get('/commands/of/user', [CommandController::class, 'getCommandsOfCustomers']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/commands/delevery/date/{command}', [CommandController::class, 'update']);
    Route::post('/commands/{id}/upload-files', [CommandController::class, 'uploadFiles']);
    Route::post('/store/messages', [MessageController::class, 'store']);
    Route::get('/get/messages/{receiverId}', [MessageController::class, 'index']);
    Route::get('/messages/list/users', [MessageController::class, 'chat']);
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages']);
    Route::get('/messages/last/{otherUserId}', [MessageController::class, 'getLastMessage']);
});
    Route::get('/search/services', [ServiceController::class, 'searchServices']);
    Route::get('/search', [ServiceController::class, 'searchServiceHomepage']);
    Route::get('/count/services/category', [ServiceController::class, 'countServicesInCategory']);
    Route::get('/services/best', [ServiceController::class, 'retrieveBestServices']);
    Route::post('/send-password-reset-email', 'App\Http\Controllers\UserController@sendResetLinkEmail')->name('password.reset');
    // Route::get('/list/services', [ServiceController::class, 'index']);
    Route::get('/service/{id}', [ServiceController::class, 'show']);
    // Route::middleware('auth')->post('/freelancers', [FreelancerController::class, 'store']);
    Route::get('/checkauth', [UserController::class, 'checkAuthStatus']);
    Route::post('/auth', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'store']);
    Route::get('/edit/{id}', [UserController::class, 'show']);
    Route::get('/user/{id}/freelancer', [UserController::class, 'freelancer']);
    Route::get('/user/{id}', [UserController::class, 'userInProfile']);
    Route::get('/user/{id}/services', [UserController::class, 'getServices']);
