<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;

class PassengerController extends Controller
{

    public function index(Request $request)
    {

        $query = QueryBuilder::for(Passenger::class)
            ->allowedFilters([
                'first_name',
                'email',
                'dob',
                AllowedFilter::exact('flight_id'), // /passengers?filter[flight_id]=1
            ])
            ->allowedSorts([
                'id',
                'first_name',
                'last_name',
                'email',
                'dob',
                'created_at'
            ]);

        //  Pagination
        $perPage = $request->get('per_page', 10);
        return response(['success' => true, 'data' => $query->paginate($perPage)]);
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
        return response(['success' => true, 'data' => $passenger->load('flights')]);
    }
    

    public function show(Passenger $passenger)
    {
        return response()->json([
            'success' => true,
            'data' => $passenger
        ]);
    }


    public function store(Request $request)
    {
        $formfields = $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'flight_id' => ['required', 'exists:flights,id'],
            'email' => ['required', 'email', 'unique:passengers,email'],
            'password' => ['required', 'min:8'],
            'dob' => ['required', 'date', 'before:today'],
            'passport_expiry_date' => ['required', 'date', 'after:today'],
        ]);

        $passenger = Passenger::create($formfields);
        return response(['success' => true, 'data' => $passenger], 201);
    }


    public function update(Request $request, Passenger $passenger)
    {
        $formfields = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'flight_id' => ['sometimes', 'required', 'exists:flights,id'],
            'email' => ['sometimes', 'required', 'email', 'unique:passengers,email,' . $passenger->id],
            'password' => ['sometimes', 'required', 'min:8'],
            'dob' => ['sometimes', 'required', 'date', 'before:today'],
            'passport_expiry_date' => ['sometimes', 'required', 'date', 'after:today'],
            'achievement_badge' => ['sometimes', 'integer', 'min:0'],
        ]);


        $passenger->update($formfields);
        return response([
            'success' => true,
            'data'  => $passenger,
        ]);
    }


    public function destroy(Passenger $passenger)
    {
        $passenger->delete();
        return response(
            [
                'success' => true
            ],
            Response::HTTP_NO_CONTENT
        );
    }
}
