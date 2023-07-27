<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DoctorController extends Controller
{
    public function index()
    {
        return view('doctors.dashboard');
    }

    public function handleLogin(Request $request)
    {
        $attributes = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::guard('webdoctor')->attempt($attributes)) {
            return redirect('/doctor');
        }

        throw ValidationException::withMessages([
            'email' => 'Your provided credention does not match our record'
        ]);

        // $user = User::whereEmail
        // if (Auth::attempt($attributes)) {
        //     return redirect('/');
        // }
    }

    public function handleLogout(Request $request)
    {
        Auth::guard('webdoctor')->logout();

        return redirect('/');
    }
}
