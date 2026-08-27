<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// POST /api/users
Route::post('/users', [UserController::class, 'store']);

// GET /api/users
Route::get('/users', [UserController::class, 'index']);

Route::get('/profile', [UserController::class, 'profile']);

// PUT /api/users/{id}
Route::put('/users/{id}', [UserController::class, 'update']);

// DELETE /api/users/{id}
Route::delete('/users/{id}', [UserController::class, 'destroy']);