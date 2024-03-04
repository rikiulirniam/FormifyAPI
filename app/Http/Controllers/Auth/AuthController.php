<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function index(){
        $user = Auth::guard('api')->user();

        if(!$user){
            return response()->json([
                'message' => 'error'
            ]);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json(['message' => "Email or password incorrect"], 401);
        }
        $user = Auth::user();
        $user->accessToken = $user->createToken(Str::random(60))->plainTextToken;

        return response()->json([
            'message' => 'Login Success',
            "user" => $user
        ]);

    }
    
    public function logout(Request $request){
        $user = Auth::guard('api')->user();
        if($user){

            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logout Success',
            ], 200);
        }
    }
}
