<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|string|min:3',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->messages()
            ]);
        } else {

            $images = "";
            if ($request->hasFile('image')) {
                $images = $request->file('image')->store('post', 'public');
            } else {
                $images = "Null";
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'image' => $images,
                'token'=> rand(10000,99999)
            ]);
            $token = $user->createToken('myregistertoken')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }
    }

    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',

        ]);

        if ($validate->fails()) {
            return response()->json([
                'error' => $validate->messages()
            ]);
        } else {

            $user = User::where('email', $request->email)->first();
            if ($user && Hash::check($request->password, $user->password)) {
                $token = $user->createToken('mylogintoken')->plainTextToken;
                return response()->json([
                    'message' => "Login success",
                    'token' => $token
                ]);
            } else {
                return response()->json([
                    'message' => "Login failed! Invalid credentials",

                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Successfully Logout'
        ]);
    }
}
