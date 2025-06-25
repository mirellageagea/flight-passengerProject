<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;

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




// Admin-only routes
Route::middleware('auth:sanctum')->group(function () {
    
    // View all users and user by ID
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);

    // Admin-only routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Get All Flights From THe Database with pagination, filtering and sorting
    Route::get('/flights', [FlightController::class, 'index']);
    // Get All Passengers Belonging To A Requested Flight
    Route::get('/flights/{flight}/passengers', [FlightController::class, 'passengers']);

    // Get All Passengers From THe Database with pagination, filtering and sorting
    Route::get('/passengers', [PassengerController::class, 'index']);
    // Attach flights to a passenger
    Route::post('/passengers/{passenger}/flights', [PassengerController::class, 'attachFlights']);

    // Export Users to an excel sheet
    Route::get('/export-users', [ExportController::class, 'exportUsers']);


});


// User Login
Route::post('/login', [AuthController::class, 'login']);

// User Logout

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
});





// // Get All Passengers From THe Database with pagination, filtering and sorting
// Route::get('/passengers', [PassengerController::class, 'index']);

// // Get All Flights From THe Database with pagination, filtering and sorting
// Route::get('/flights', [FlightController::class, 'index']);

// // Get All Passengers Belonging To A Requested Flight
// Route::get('/flights/{flight}/passengers', [FlightController::class, 'passengers']);

// // Attach flights to a passenger
// Route::post('/passengers/{passenger}/flights', [PassengerController::class, 'attachFlights']);





// // Get All Users
// Route::get('/users', [UserController::class, 'index']);

// // Get Only One User By ID 
// Route::get('/users/{id}', [UserController::class, 'show']);

// // Create A New User
// Route::post('/users', [UserController::class, 'store']);

// // Update An existing User
// Route::put('/users/{id}', [UserController::class, 'update']);

// // Delete A User
// Route::delete('/users/{id}', [UserController::class, 'destroy']);
