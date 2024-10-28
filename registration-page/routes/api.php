<?php

use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('register', [RegistrationController::class, 'index'])->name('register');
Route::get('getToken', [TokenController::class, 'index'])->name('getToken');
