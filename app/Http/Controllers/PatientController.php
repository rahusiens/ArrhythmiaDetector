<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        $patient = Patient::find(Auth::user()->id);
        return response()->json($patient);
    }

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
            /** @var \App\Models\Patient $user **/
            $user = Auth::user();

            $token = $user->createToken('Patient')->accessToken;

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
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'emergency_phone' => ['required', 'string'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors(),
                'status' => '422'
            ]);
        }

        $patient = Patient::where('username', Auth::user()->username)->first();

        $dataToUpdate = [
            'address' => $request->address,
            'phone' => $request->phone,
            'emergency_phone' => $request->emergency_phone,
            'age' => $request->age,
            'gender' => $request->gender,
        ];
        if ($request->has('password')) {
            $dataToUpdate['password'] = bcrypt($request->password);
        }

        $patient->update($dataToUpdate);

        return response()->json([
            'message' => 'Successfully updated user!',
            'user' => $patient,
        ], 200);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors(),
                'status' => '422'
            ]);
        }

        $patient = Patient::where('username', Auth::user()->username);
        $patient->update([
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Successfully updated user!',
            'user' => $patient,
        ], 200);
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\Patient $user **/
        $user = Auth::guard('apipatient')->user();
        $accessToken = $user->token();
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

    public function send_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => ['required'],
            'time' => ['required'],
            'lead1' => ['required'],
            'lead2' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'error' => $validator->errors(),
                'status' => '422'
            ]);
        }
        
        $record = new Record([
            'patient_id' => $request->patient_id,
            'time' => $request->time,
            'lead1' => $request->lead1,
            'lead2' => $request->lead2
        ]);
        
        $record->save();

        return response()->json([
            'message' => 'Successfully saved record!',
            'status' => '200',
        ], 200);
    }

    public function get_data(Request $request) {
        $user_id = Auth::user()->id;

        $records = Record::select('id', 'time', 'lead1', 'lead2')
            ->where('patient_id', $user_id)->get();
        return response()->json([
            'patient_id' => $user_id,
            'records' => $records
        ]);
    }
}
