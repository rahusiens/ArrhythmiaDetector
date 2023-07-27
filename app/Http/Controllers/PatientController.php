<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index() {
        return view('patients.dashboard');
    }

    public function register()
    {
        $patient = new Patient;
        return view('auth.register', ['patient' => $patient]);
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
