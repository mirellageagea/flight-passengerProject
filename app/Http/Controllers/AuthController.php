<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    // User Login and issue token
    public function login(Request $request)
    {
        // Validate user input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login
        if (!auth()->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Your provided credentials could not be verified.',
            ]);
        }

        // Get authenticated user
        $user = auth()->user();

        // Destroy all existing tokens (log out everywhere else)
        $user->tokens()->delete();

        $user = User::where('email', $credentials['email'])->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            //'user' => $user->only(['id', 'name', 'email', 'role']),
        ]);
    }


    // User Logout
    public function logout(Request $request)
    {
        Log::info('User logging out:', ['user_id' => $request->user()->id]);
        $request->user()->tokens()->delete();

        return response(['success' => 'Logged out']);
    }
}
