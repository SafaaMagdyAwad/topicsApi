<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator as FacadesValidator;
class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator=FacadesValidator::make($request->all(),[
            "first_name"=>'required|string|max:255',
            "last_name"=>'required|string|max:255',
            "first_name"=>'required|string|max:255',
            "email"=>'required|email|unique:users,email|max:255',
            "password"=>'required|string|min:8|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                "message"=>$validator->messages(),
            ],300);
        }
        $user=User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'user_name'=>$request->user_name,
            'is_active'=>1,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        if($user){
            $token=$user->createToken($user->name."Auth-Token")->plainTextToken;//could be any random value
            return response()->json([
                "message"=>"registration success!",
                "token_type"=>"Bearer",
                "token"=>$token,
            ],200);
        }else{
            return response()->json([
                "message"=>"Somethog Went wrong!",
            ],500);
        }

    }
}
