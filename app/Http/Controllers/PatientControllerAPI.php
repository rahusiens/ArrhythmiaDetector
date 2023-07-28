<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PatientControllerAPI extends Controller
{
    public function register(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'alpha_num', 'min:3', 'max:25'],
            'password' => ['required', 'min:8'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'emergency_phone' => ['required', 'string'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'error' => $validator->errors(),
                    'status' => '422'
                ],
            );
        }

        $patient = new Patient([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
            'age' => $request->age,
            'gender' => $request->gender,
        ]);

        $patient->save();

        return response()->json([
            'message' => 'Successfully created user!',
            'status' => '200',
            'user' => $patient
        ], 200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'error' => $validator->errors(),
                    'status' => '422'
                ]
            );
        }

        if (Auth::attempt($request->only('username', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('example')->accessToken;

            dd($token);
            return response()->json([
                'message' => 'Successfully logged in!',
                'status' => '200',
                'token' => $token,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid credentials',
                'status' => '401',
            ]);
        }

        // $user = User::whereEmail
        // if (Auth::attempt($attributes)) {
        //     return redirect('/');
        // }
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'emergency_phone' => ['required', 'string'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => 'Validation failed',
                    'error' => $validator->errors(),
                    'status' => '422'
                ],
            );
        }

        
        $id = Auth::user()->id;
        if (Auth::user()) {
            Patient::find($id)->update([
                'address' => $request->address,
                'phone' => $request->phone,
                'emergency_phone' => $request->emergency_phone,
                'age' => $request->age,
                'gender' => $request->gender,
            ]);
        }
        return response()->json([
            'message' => 'Successfully updated user!',
            'user' => Auth::user(),
        ], 200);
    }

    public function logout(Request $request)
    {
        $accessToken = Auth::guard('apipatient')->user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked' => true]);
        $accessToken->revoke();

        return response()->json([
            'message' => 'Log Out Successful',
            'status' => 200,
            'data' => 'Unauthorized',
        ]);
    }
}
