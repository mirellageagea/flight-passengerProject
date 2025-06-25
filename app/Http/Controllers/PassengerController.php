<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;

class PassengerController extends Controller
{

  public function index(Request $request)
    {
        $query = Passenger::query();

        //  Filtering
        if ($request->has('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('dob')) {
            $query->whereDate('dob', $request->dob);
        }

        //  Sorting
        if ($request->has('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($request->sort_by, $sortDir);
        }

        //  Pagination
        $perPage = $request->get('per_page', 10);
        return response()->json($query->paginate($perPage));
    }



public function attachFlights(Request $request, $passengerId)
{
    $passenger = Passenger::findOrFail($passengerId);

    // Validate that flight_ids is an array of integers
    $request->validate([
        'flight_ids' => 'required|array',
        'flight_ids.*' => 'integer|exists:flights,id',
    ]);

    // Attach flights (use syncWithoutDetaching to add without removing existing flights)
    $passenger->flights()->syncWithoutDetaching($request->flight_ids);



    // Return updated passenger with flights
    return response()->json($passenger->load('flights'));
}



}
