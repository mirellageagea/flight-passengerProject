<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Get All Users (creating index method)
    public function index() {
         return response()->json(User::all());
    }

    // Get Only One User By ID (creating show method)
    public function show($id) {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Create A New User (creating store method)
    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // $user = User::create($validated);

        $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => $validated['password'],
         'role' => $validated['role'],  // keep this updated if you want to keep the column
    ]);

    // Assign role here
    $user->assignRole($validated['role']);

        //return response()->json($user, 201);
         return response()->json($user->load('roles'), 201);
    }

    // Update an existing User (creating update method)
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    // Delete A User
    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

}
