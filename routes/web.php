<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'redirectToProvider']);
Route::get('/callback', [AuthController::class, 'handleProviderCallback']);
Route::get('/', function () {
    return session('user') ? 'Logged In' : 'Not Logged In';
});
