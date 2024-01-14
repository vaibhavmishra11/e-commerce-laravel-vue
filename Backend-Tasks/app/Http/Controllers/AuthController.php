<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Auth;
use Log;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        Log::debug("Going to authenicate user asd $request->email");

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $request->session()->regenerate();
    }

    // public function login(Request $request)
    // {
    //     Log::debug("Going to authenticate user $request->email");

    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => 'Invalid login details'
    //         ], 401);
    //     }

    //     $token = $user->createToken('auth-token')->plainTextToken;

    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'Bearer',
    //         'data' => $user,

    //     ], 200);
    // }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        Auth::logout();
    }

    // public function logout(Request $request)
    // {
    //     $user = Auth::user();
    //     // Revoke the user's current token
    //     $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }
    public function me(Request $request)
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    public function register(Request $request)
    {
        try {
            Log::debug('Got request to register user');
            Log::debug($request->all());

            $this->validate($request, [
                'first_name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::create([
                'user_id' => Str::uuid(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),


            ]);
            // $user->notify(new \App\Notifications\WelcomeMailNotification($user));

            if ($user) {
                return response()->json([
                    'status' => true,
                    'message' => 'Registered Successfully',
                    'data' => $user,
                ], 200);

            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went wrong'
                ], 500);
            }
            //return view('users.login')->with('success','you have registered successfully');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('fail', 'something wrong');
        }

    }
}
