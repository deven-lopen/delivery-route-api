<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\User;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'email' => ['The provided credentials are incorrect.'],
            ], 404);
        }

        return $user->createToken('login')->plainTextToken;
    }
}
