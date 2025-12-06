<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $response = Http::asForm()->post(url('/oauth/token'), [
    //         'grant_type' => 'password',
    //         'client_id' => env('PASSPORT_CLIENT_ID'),
    //         'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    //         'username' => $request->email,
    //         'password' => $request->password,
    //     ]);

    //     if ($response->failed()) {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }

    //     return $response->json();
    // }

    public function registerChecker(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'checker',
        ]);

        return response()->json([
            'message' => 'Checker created successfully',
            'user'    => $user,
        ], 201);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
