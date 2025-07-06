<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if(!Auth::attempt($request->only('username', 'password'))){
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ]);
        }
        
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

     public function profile(){
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'message' => "User profile retrieved successfully",
            'user' => $user
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ]);
    }
}
