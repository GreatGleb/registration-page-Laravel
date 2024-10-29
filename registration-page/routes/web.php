<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/registration', function () {
    return view('registration');
})->name('registration');

Route::get('/users', function () {
    return view('users');
})->name('users');

Route::get('/user/{id}', function ($id) {
    return view('user', ['id' => $id]);
})->name('user');

Route::get('/image/{filename}', function($filename) {
    $img = Storage::disk('public')->path('images/' . $filename);

    try {
        $photo = response( file_get_contents($img) )
            ->header('Content-Type','image/jpg');

        return $photo;
    } catch (\Exception $e) {
        return '';
    }
});
