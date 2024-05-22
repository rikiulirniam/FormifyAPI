<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index(){
        return response()->json([
            'user' => Auth::user()
        ]);
    }
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => "required|regex:/^[A-Za-z ]+$/|max:15",
            'email' => 'required|unique:users|email',
            'password' => 'required|min:5'
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => "Invalid fields",
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'Email or Password Incorrect'
            ], 401);
        }

        $user->accessToken = $user->createToken(Str::random(100))->plainTextToken;

        return response()->json([
            'message' => 'User Registered',
            'user' => $user
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
