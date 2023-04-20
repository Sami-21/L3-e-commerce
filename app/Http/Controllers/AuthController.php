<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        //validated Request body
        $request->validate([
            'email' =>  'required|string',
            'password' => 'required|string',
        ]);
        //Check if user exists and password is correct
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'check your credentials',
            ], 401);
        }
        //Generate token
        $token = auth()->user()->createToken('auth_token')->plainTextToken;
        //Return user and token
        return response()->json([
            'user' => auth()->user(),
            'token' => $token
        ], 200);
    }

    public function register(AuthRequest $request)
    {
        //validated Request body
        $validated = $request->validated();
        //Create user
        $user = User::create(array_merge($validated, [
            'password' => Hash::make($request->password),
            'role_id' => 2
        ]));
        Client::create([
            'user_id' => $user->id
        ]);
        //Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        //Return user and token
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function getUser($id)
    {
        $user = User::with(['role'])->findOrFail($id);
        return response()->json($user, 201);
    }

    public function logout(Request $request)
    {
        //Delete token
        $request->user()->currentAccessToken()->delete();
        //Return success message
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}
