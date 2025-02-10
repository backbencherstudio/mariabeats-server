<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Franchaisor\FranchaisorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update', [AuthController::class, 'updateUser']);
    Route::post('/franchaisor', [FranchaisorController::class, 'store']);
    Route::get('/franchaisor/{id}', [FranchaisorController::class, 'show']);
    Route::patch('/franchaisor/{id}', [FranchaisorController::class, 'update']);
    Route::delete('/franchaisor/{id}', [FranchaisorController::class, 'destroy']);
});