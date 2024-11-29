<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('authToken');
        $token->token->expires_at = now()->addDays(2);
        $token->token->save();


        return response()->json(['user' => $user, 'token' => $token->accessToken], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Auth::user()->createToken('authToken');
        $token->token->expires_at = now()->addDays(2);
        $token->token->save();

        return response()->json(['user' => Auth::user(), 'token' => $token->accessToken], 200);
    }

    public function logout()
    {
        Auth::guard('api')->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user()
    {
        $authUser =   Auth::guard('api')->user();

        $user = User::with('basicInfo')->find($authUser->id);

        return response()->json($user);
    }
}
