<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    function createUser(Request $request) {
        try{
            $validator = Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'required|email|unique:users,email',
                'password'=>'required',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $user = User::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'password'=> Hash::make($request->password)
            ]);
    
            return response()->json([
                'status'=>true,
                'message'=>trans('messages.user created successfully',['model'=>'User']),
                'token'=>$user->createToken("API TOKEN")->plainTextToken
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create User
     * @param Request $request
     * @return User
     */
    function loginUser(Request $request) {
        try{
            $validator = Validator::make($request->all(),[
                'email'=>'required|email',
                'password'=>'required',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message'=> 'validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'status' =>false,
                    'message'=>trans('auth.failed')
                ],401);
            }
            
            $user = User::where('email',$request->email)->first();

            return response()->json([
                'status'=>true,
                'message'=>trans('messages.user logged in successfully'),
                'token'=>$user->createToken("API TOKEN")->plainTextToken
            ], 200);
        }catch(\Throwable $th){
            return response()->json([
                'status' => false,
                'message'=> $th->getMessage()
            ], 500);
        }
    }
}
