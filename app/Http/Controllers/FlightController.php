<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{

   public function index(Request $request)
    {
        $query = Flight::query();

        // Filtering
        if ($request->has('departure_city')) {
            $query->where('departure_city', 'like', '%' . $request->departure_city . '%');
        }

        if ($request->has('arrival_city')) {
            $query->where('arrival_city', 'like', '%' . $request->arrival_city . '%');
        }


        // Sorting
        if ($request->has('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($request->sort_by, $sortDir);
        }


        // Pagination
        $perPage = $request->get('per_page', 10);
        return response()->json($query->paginate($perPage));
    }


    // Get All Passengers For A Specific Flight
    public function passengers($flightId)
       {
        $flight = Flight::with('passengers')->findOrFail($flightId);
        return response()->json($flight->passengers);
      }
}
