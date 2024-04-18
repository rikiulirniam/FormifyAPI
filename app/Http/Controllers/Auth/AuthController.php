<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index(){
        return response()->json([
            'user' => Auth::user()
        ]);
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Invalid Fields',
                'errors' => $validator->errors()
            ], 422);
        }

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'Email or Password Incorrect'
            ], 401);
        }

        $user = Auth::user();
        $user->accessToken = $user->createToken(Str::random(100))->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            'user' => $user
        ]);
    }
    public function logout(){
        if(Auth::user()){
            Auth::user()->tokens()->delete();
            return response()->json(['message' => 'Logout Success']);
        }
    }
}
