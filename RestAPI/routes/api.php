<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIRegisterController;

Route::post('/register', [APIRegisterController::class, 'register']);
Route::get('/user', [APIRegisterController::class, 'getUserData']);
Route::post('/update', [APIRegisterController::class, 'updateUserData']);
Route::post('/delete', [APIRegisterController::class, 'deleteUser']);
