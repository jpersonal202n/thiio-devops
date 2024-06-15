<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AuthController;
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
Route::group(['prefix' => 'v1'],function() {
    Route::post('auth',[AuthController::class,'auth'])->name('auth');
});

Route::group(['prefix' => 'v1/','middleware' => 'auth:api'],function() {
   Route::post('users',[UserController::class,'store'])->name('users.store');
   Route::get('users/{uuid}',[UserController::class,'show'])->name('users.show');
   Route::delete('users/{uuid}',[UserController::class,'destroy'])->name('users.destroy');
});