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
        $input = $request->all();

        // Clean only 'name'
        if (isset($input['name'])) {
            $input['name'] = Purifier::clean($input['name'], ['HTML.Allowed' => '']);
        }

        $input['email'] = trim($input['email'] ?? '');
        $input['role'] = trim($input['role'] ?? '');

        // $validated = $request->validate([
        //     'name' => ['required', 'string'],
        //     'email' => ['required', 'email', 'unique:users'],
        //     'password' => ['required', 'string', 'min:6'],
        //     'role' => ['required', 'in:admin,user'],
        // ]);

        $validated = validator($input, [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
        ])->validate();

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

        $input = $request->all();

        // Purify only fields that may contain HTML/script tags
        if (isset($input['name'])) {
            $input['name'] = Purifier::clean($input['name'], ['HTML.Allowed' => '']);
        }

        // $validated = $request->validate([
        //     'name' => ['nullable', 'string'],
        //     'email' => ['nullable', 'email', 'unique:users,email,' . $id],
        //     'password' => ['nullable', 'string', 'min:6'],
        // ]);

        $validated = validator($input, [
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:6'],
        ])->validate();


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
