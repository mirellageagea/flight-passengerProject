<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Mews\Purifier\Facades\Purifier;



class FlightController extends Controller
{

    public function index(Request $request)
    {

        $cacheKey = 'flights.index.' . md5($request->fullUrl());

        // Try to get the result from cache or store it if not found
        $flights = Cache::remember($cacheKey, 60, function () use ($request) {

            $query = Flight::query();
            $query = QueryBuilder::for(\App\Models\Flight::class)
                ->allowedFilters([
                    'departure_city',
                    'arrival_city',
                    AllowedFilter::exact('id'),
                ])
                ->allowedSorts(['id', 'departure_city', 'arrival_city', 'departure_time', 'arrival_time'])
                ->paginate($request->get('per_page', 10))
                ->appends(request()->query());

            // Pagination
            // /api/flights?per_page=100
            return $query;
        });

        return response(['success' => true, 'data' => $flights]);
    }



    public function show(Flight $flight)
    {
        return response([
            'success' => true,
            'data' => $flight
        ]);
    }


    public function store(Request $request)
    {
        // Validation only (sanitization is now handled by middleware)
        $formfields = $request->validate([
            'number' => ['required', 'string', 'max:255'],
            'departure_city' => 'required|string|max:255',
            'arrival_city' => ['required', 'string', 'different:departure_city', 'max:255'],
            'departure_time' => ['required', 'date', 'after:now'],
            'arrival_time' => ['required', 'date', 'after:departure_time']
        ]);

        $flight = Flight::create($formfields);
        return response(['success' => true, 'data' => $flight], 201);
    }


    public function update(Request $request, Flight $flight)
    {
        // Validation only (sanitization handled by middleware)
        $formfields = $request->validate([
            'number' => ['nullable', 'string', 'max:255', 'unique:flights,number,' . $flight->id],
            'departure_city' => ['nullable', 'string', 'max:255'],
            'arrival_city' => ['nullable', 'string', 'different:departure_city', 'max:255'],
            'departure_time' => ['nullable', 'date', 'after:now'],
            'arrival_time' => ['nullable', 'date', 'after:departure_time'],
        ]);

        $flight->update($formfields);
        return response([
            'success' => true,
            'data' => $flight
        ]);
    }


    public function destroy(Flight $flight)
    {
        $flight->delete();
        return response([
            'success' => true,
            'data' => $flight
        ]);
    }


    // Get All Passengers For A Specific Flight
    // public function passengers($flightId)
    // {
    //     $flight = Flight::with('passengers')->findOrFail($flightId);
    //     return response(['success' => true, 'data' => $flight->passengers]);
    // }

}
