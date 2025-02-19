<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Country\CountryController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Featured\FeaturedController;
use App\Http\Controllers\Franchaisee\FranchaiseeController;
use App\Http\Controllers\Franchaisor\FranchaisorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/franchaisee-request', [FranchaiseeController::class, 'doFranchaiseeRequest']);
Route::post('/franchaisor-request', [FranchaisorController::class, 'franchaisorRequest']);
Route::get('/franchaisors', [FranchaisorController::class, 'index']);
Route::get('/franchaisor/{id}', [FranchaisorController::class, 'show']);
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/featured', [FeaturedController::class, 'index']);
Route::post('/otp-verify', [AuthController::class, 'otpVerify']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



Route::group(['middleware' => 'auth:sanctum', 'role:admin'], function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update', [AuthController::class, 'updateUser']);
    Route::get('/admin/franchaisors/export-data', [FranchaisorController::class, 'exportData']);
    Route::get('/admin/franchaisors', [FranchaisorController::class, 'index']);
    Route::post('/admin/franchaisor', [FranchaisorController::class, 'store']);
    Route::get('/admin/franchaisor/{id}', [FranchaisorController::class, 'show']);
    Route::post('/admin/franchaisor/{id}', [FranchaisorController::class, 'update']);
    Route::delete('/admin/franchaisor/{id}', [FranchaisorController::class, 'destroy']);
    Route::get('/admin/franchaisees/export-data', [FranchaiseeController::class, 'exportData']);
    Route::get('/admin/franchaisees', [FranchaiseeController::class, 'index']);
    Route::post('/admin/franchaisee', [FranchaiseeController::class, 'store']);
    Route::get('/admin/franchaisee/{id}', [FranchaiseeController::class, 'show']);
    Route::post('/admin/franchaisee/{id}', [FranchaiseeController::class, 'update']);
    Route::delete('/admin/franchaisee/{id}', [FranchaiseeController::class, 'destroy']);
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/franchaisee-requests', [FranchaiseeController::class, 'franchaiseeRequests']);
    Route::get('/admin/franchaisor-requests', [FranchaisorController::class, 'franchaisorRequests']);
    Route::patch('/admin/franchaisee-request/{id}', [FranchaiseeController::class, 'franchaiseeRequestUpdate']);
    Route::patch('/admin/franchaisor-request/{id}', [FranchaisorController::class, 'franchaisorRequestUpdate']);
    Route::delete('/admin/franchaisee-request/{id}', [FranchaiseeController::class, 'franchaiseeRequestDelete']);
    Route::delete('/admin/franchaisor-request/{id}', [FranchaisorController::class, 'franchaisorRequestDelete']);
    Route::resource('/admin/featured', FeaturedController::class);
});
