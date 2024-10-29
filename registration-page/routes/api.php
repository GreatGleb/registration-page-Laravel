<?php

use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\TokenController;
use App\Http\Controllers\API\PositionsController;
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

Route::middleware('auth:sanctum')->post('users', [RegistrationController::class, 'index'])->name('register');
Route::get('token', [TokenController::class, 'index'])->name('token');
Route::get('positions', [PositionsController::class, 'index'])->name('positions');
