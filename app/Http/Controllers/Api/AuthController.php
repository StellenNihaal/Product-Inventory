<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    // ✅ Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // ✅ Revoke old tokens
        $user->tokens()->delete();

        // ✅ Create token based on role
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
        ]);
    }
    public function register(Request $request)
    {
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists.',
                'errors'  => [
                    'email' => ['This email is already registered.']
                ]
            ], 409);
        }
        $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:' . User::class
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults()
                ],
                'country' => ['nullable', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'],
            ],
            [
                'name.required'      => 'Name is required.',
                'email.required'     => 'Email is required.',
                'email.email'        => 'Please enter a valid email address.',
                'email.unique'       => 'This email is already registered.',
                'password.required'  => 'Password is required.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.min'       => 'Password must be at least 8 characters.',
                'password.mixed'     => 'Password must contain both uppercase and lowercase letters.',
                'password.numbers'   => 'Password must contain at least one number.',
                'password.symbols'   => 'Password must contain at least one special character.',
                'password.uncompromised' => 'This password has appeared in a data breach. Please choose a different password.',
            ]
        );


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'state' => $request->state,
        ]);


        // Fire registration event
        event(new Registered($user));


        // Create Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'country' => $user->country,
                'state' => $user->state,
            ],
        ], 201);
    }

    // ✅ Get logged in user
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user(),
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
