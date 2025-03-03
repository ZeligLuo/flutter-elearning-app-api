<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create user
     * @param Request $request
     * @return User
     */

    public function createUser(Request $request) {
        try {
            // validated
            $validateUser = Validator::make($request->all(), [
                'avatar' => 'required',
                'type' => 'required',
                'name' => 'required',
                'email' => 'required',
                'open_id' => 'required',
                // 'password' => 'required|min:8'
            ]);

            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $validated = $validateUser->validated();

            $map=[];
            // email, phone, google, ...
            $map['type'] = $validated['type'];
            $map['open_id'] = $validated['open_id'];

            $user = User::where($map)->first();

            // whether user has already logged in or not
            // user does not exist then save the user to database 1st time
            if(empty($user->id)) {
                // this user has nerver been in database
                // then assign user to database
                $validated['token'] = md5(uniqid().rand(10000, 99999)); // this token is user id
                $validated['created_at'] = Carbon::now();
                // $validated['password'] = Hash::make($validated['password']); // encrypt pw
                $userId = User::insertGetId($validated); // return the id after saving
                $userInfo = User::where('id', '=', $userId)->first(); // user all info
                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;
                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userId)->update(['access_token'=>$accessToken]);

                return response()->json([
                    'code' => 200,
                    'msg' => 'User created successfully',
                    'data' => $userInfo
                ], 200);
            }

            // user has logged in previously
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token'=>$accessToken]);
            return response()->json([
                'code' => 200,
                'msg' => 'User created successfully',
                'data' => $user
            ], 200);

        } catch(\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // /**
    //  * Login user
    //  * @param Request $request
    //  * @return User
    //  */
    // public function loginUser(Request $request) {
    //     try {
    //         $validateUser = Validator::make($request->all(), [
    //             'email' => 'required|email',
    //             'password' => 'required'
    //         ]);

    //         if($validateUser->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'validation error',
    //                 'errors' => $validateUser->errors()
    //             ], 401);
    //         }

    //         if(!Auth::attempt($request->only(['email', 'password']))) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Wrong email or password.'
    //             ], 401);
    //         }

    //         $user = User::where('email', $request->email)->first();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User logged in successfully',
    //             'token' => $user->createToken("API TOKEN")->plainTextToken
    //         ], 200);
    //     } catch(\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }
}
