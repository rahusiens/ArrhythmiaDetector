<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    public function index() {
        return view('patients.dashboard');
    }

    public function index_api() {
        return response()->json();
    } 

    public function register()
    {
        $patient = new Patient;
        return view('auth.register', ['patient' => $patient]);
    }
    
    public function register_api()
    {
        $patient = new Patient;
        return response()->json($patient);
    }
    
    public function handleRegister(Request $request)
    {  
        $attributes = $request->validate([
            'username' => ['required', 'alpha_num', 'min:3', 'max:25'],
            'password' => ['required', 'min:8'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'emergency_phone' => ['required', 'string'],
            'age' => ['required', 'numeric'],
            'gender' => ['required', 'string'],
        ]);

        $attributes['password'] = Hash::make($request->password);
        Patient::create($attributes);

        return redirect('/')->with('success', 'Registration Success');
    }

    public function handleRegister_api(Request $request)
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

    public function login()
    {
        return view('auth.login');
    }

    public function handleLogin(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($attributes)) {
            return redirect('/patient');
        }

        // $user = User::whereEmail
        // if (Auth::attempt($attributes)) {
        //     return redirect('/');
        // }
    }

    public function edit(Patient $patient)
    {
        $patient = Auth::user();
        return view('patients.edit', ['patient' => $patient]);
    }

    public function handleLogout(Request $request)
    {
        Auth::logout();

        return redirect('/');
    }
}
