<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Mews\Purifier\Facades\Purifier;

class UserController extends Controller
{
    // Get All Users (creating index method)

    public function index(Request $request)
    {
        $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                'name',
                'email',
                AllowedFilter::exact('role'),
            ])
            ->allowedSorts([
                'id',
                'name',
                'email',
                'created_at',
            ])
            ->paginate($request->get('per_page', 10))
            ->appends(request()->query());


        return response([
            'success' => true,
            'data' => $query
        ]);
    }

    // Get Only One User By ID (creating show method)
    public function show(User $user)
    {

        return response(['success' => true, 'data' => $user]);
    }

    // Create A New User (creating store method)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], 
            'role' => $validated['role'],
        ]);

        // Assign role via spatie
        $user->assignRole($validated['role']);


        return response(['success' => true, 'data' => $user->load('roles')], 201);
    }


    // Update an existing User (creating update method)
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);


        $user->update($validated);

        return response(['success' => true, 'data' => $user]);
    }


    // Delete A User
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response(
            [
                'success' => true
            ],
            Response::HTTP_NO_CONTENT
        );
    }
}
