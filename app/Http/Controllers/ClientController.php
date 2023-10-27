<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::with(['user'])->whereHas('user', function ($query) use ($request) {
            $query->where('firstname', 'LIKE', '%' . $request->q . '%');
        })->orWhereHas(
            'user',
            function ($query) use ($request) {
                $query->where('lastname', 'LIKE', '%' . $request->q . '%');
            }
        )->orWhereHas(
            'user',
            function ($query) use ($request) {
                $query->where('email', 'LIKE', '%' . $request->q . '%');
            }
        )->paginate($request->perPage);
        return response()->json(
            new ClientCollection($clients),
            200
        );
    }

    public function get($id)
    {
        $client = Client::with(['user'])->findOrFail($id);
        if ($client) {
            return response()->json([
                'client' => new ClientResource($client)
            ], 200);
        } else {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    }

    public function changeStatus($id)
    {
        $client = Client::findOrFail($id);
        if ($client) {
            $client->status = !$client->status;
            $client->save();
            return response()->json([
                'client' => new ClientResource($client)
            ], 200);
        } else {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        if ($client) {
            $client->user->delete();
            $client->delete();
            return response()->json([
                'message' => 'Client deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Client not found'
            ], 404);
        }
    }

    public function login(Request $request)
    {
        //validated Request body
        $request->validate([
            'email' =>  'required|string',
            'password' => 'required|string',
        ]);
        //Check if user exists and password is correct
        if (!Auth::attempt($request->only('email', 'password')) || $request->user()->role->name != 'client') {
            return response()->json([
                'message' => 'check your credentials',
            ], 401);
        }
        $user = $request->user();
        $client = Client::with(['user'])->where('user_id', $user->id)->first();
        if ($client->status == false) {
            return response()->json([
                'status' => $client->status,
                'message' => 'Your account is disabled',
            ], 401);
        }
        //Generate token
        $token = $user->createToken('auth_token')->plainTextToken;
        //Return client and token
        return response()->json([
            'client' => new ClientResource($client),
            'token' => $token
        ], 200);
    }

    public function register(ClientRequest $request)
    {
        //validated Request body
        $validated = $request->validated();
        //Create Client
        $user = User::create(array_merge($validated, [
            'password' => Hash::make($request->password),
            'role_id' => 2
        ]));
        $client = Client::create([
            'user_id' => $user->id
        ]);
        //Generate token
        $token = $user->createToken('client_token', ['client'])->plainTextToken;
        //Return user and token
        return response()->json([
            'client' => new ClientResource($client),
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        //Delete token
        $request->user()->currentAccessToken()->delete();
        //Return success message
        return response()->json([
            'status' => 'success',
            'message' => 'Client successfully logged out',
        ]);
    }
}