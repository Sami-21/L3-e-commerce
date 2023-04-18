<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(AuthRequest $request)
    {
        $request->validate([
            [
                'firstname' => 'required|max:255|string',
                'lastname' => 'required|max:255|string',
                'email' => 'required|email|unique:user',
                'password' => 'required|string',
                'password_confirmation' => 'required|string|same:password',
            ]
        ]);

        $user =  new User([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => 2
        ]);

        if ($user->save()) {
            return response()->json([
                'message' => 'client saved with success'
            ]);
        } else {
            return response()->json([
                'message' => 'error occured when saving new client'
            ]);
        }
    }
}
