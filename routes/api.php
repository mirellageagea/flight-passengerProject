<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportPassengerController;
use App\Http\Controllers\PassengerImageController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Login
Route::post('/login', [AuthController::class, 'login']);

// User Logout
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

// Authentication
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // View all or by ID
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('flights', FlightController::class)->only(['index', 'show']);
    Route::apiResource('passengers', PassengerController::class)->only(['index', 'show']);

    
    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        //  Users apiResource
        Route::apiResource('users', UserController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:30,1');

        // Passengers apiResource
        Route::apiResource('passenger', PassengerController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:5,1');

        // Flights apiResource
        Route::apiResource('flights', FlightController::class)->only(['store', 'update', 'destroy'])->middleware('throttle:5,1');

        // Import passengers from Excel
        Route::post('/import-passengers', [ImportPassengerController::class, 'import'])->middleware('throttle:5,1');

        // Export Users to an excel sheet
        Route::get('/export-users', [ExportController::class, 'exportUsers']);

        // Upload an Image(original and thumbnail)
        Route::post('/passengers/{passenger}/upload-image', [PassengerImageController::class, 'upload']);
    });


    // Get All Passengers Belonging To A Requested Flight
    Route::get('/flights/{flight}/passengers', [FlightController::class, 'passengers']);
    // Attach flights to a passenger
    Route::post('/passengers/{passenger}/flights', [PassengerController::class, 'attachFlights']);
});

