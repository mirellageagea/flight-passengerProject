<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

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
        ]);

    $perPage = $request->get('per_page', 10); // Default: 10 per page
    return response([
        'success' => true,
        'data' => $query->paginate($perPage)
    ]);
}

    // Get Only One User By ID (creating show method)
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response(['success' => true, 'data' =>$user]);
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
            'role' => $validated['role'],  // keep this updated if you want to keep the column
        ]);

        // Assign role here
        $user->assignRole($validated['role']);


        return response(['success' => true, 'data' => $user->load('roles')], 201);
    }


    // Update an existing User (creating update method)
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string'],
            'email' => ['sometimes', 'email', 'unique:users,email,' . $id],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);


        $user->update($validated);

        return response(['success' => true, 'data' =>$user]);
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
